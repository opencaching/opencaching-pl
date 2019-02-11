<?php
/**
 * Contains \Controllers\Cron\WatchlistController class definition
 */
namespace src\Controllers\Cron;

use src\Controllers\BaseController;
use src\Utils\Email\Email;
use src\Utils\Lock\Lock;
use src\Models\User\UserNotify;
use src\Models\Watchlist\Watchlist;
use src\Models\Watchlist\WatchlistItem;
use src\Models\Watchlist\WatchlistReport;
use src\Models\Watchlist\WatchlistWatcher;

/**
 * Initiates and performs operations included in watchlist processing: new logs
 * retreiving, report items preparing, report emails sending, new email send
 * date and time computing
 */
class WatchlistController extends BaseController
{
    /**
     * @var array watchlist configuration associative array from
     * {@see \src\Models\OcConfig\OcConfig}
     */
    private $watchlistConfig;
    /** @var \src\Models\Watchlist\Watchlist class instance */
    private $watchlist;
    /** @var \src\Models\Watchlist\WatchlistItem class instance */
    private $watchlistItem;
    /** @var \src\Models\Watchlist\WatchlistReport class instance */
    private $watchlistReport;

    /** @var resource diagnostic file handle, used only if enabled in config */
    private $diagFileHandle;
    /** @var mixed diagnostic start time, used only if enabled in config */
    private $diagStartTime;

    /**
     * Gets config and creates watchlist classes instances
     */
    public function __construct()
    {
        parent::__construct();
        $this->watchlistConfig = $this->ocConfig->getWatchlistConfig();
        if ($this->watchlistConfig == null) {
            $this->watchlistConfig = [];
        }
        $this->watchlist = new Watchlist;
        $this->watchlistItem = new WatchlistItem();
        $this->watchlistReport = new WatchlistReport();
    }

    /**
     * Controller default and the only one entry point
     * @see \src\Controllers\BaseController::index()
     */
    public function index()
    {
        $this->processWatchlist();
    }

    /**
     * Not callable from router
     * @see \src\Controllers\BaseController::isCallableFromRouter()
     */
    public function isCallableFromRouter($actionName)
    {
        return false;
    }

    /**
     * Main function, performs all processing in exclusive lock to prevent
     * concurrent execution.
     */
    private function processWatchlist()
    {
        $lockHandle = Lock::tryLock($this, Lock::EXCLUSIVE | Lock::NONBLOCKING);
        if ($lockHandle) {
            if (isset($this->watchlistConfig['diag_file'])) {
                $diagFilePath = $this->watchlistConfig['diag_file'];
            } else {
                $diagFilePath = "";
            }
            if (mb_strlen($diagFilePath) > 0) {
                $this->diagFileHandle = fopen($diagFilePath, "a");
            }

            $this->processNewLogs();
            $this->sendReportsAndUpdateChecks();

            if ($this->diagFileHandle != null) {
                fclose($this->diagFileHandle);
            }

            Lock::unlock($lockHandle);
        } else {
            print "Another instance of " . get_class($this)
                . " is currently running.\nExiting.\n";
        }
    }

    /**
     * Retrieves new, unprocessed cache logs from Watchlist instance and
     * for each formats report item and stores it for owner and watches,
     * including notifications.
     */
    private function processNewLogs()
    {
        if ($this->diagFileHandle != null) {
            $this->diagStartTime = microtime(true);
            fprintf(
                $this->diagFileHandle,
                "start;%s\n",
                $this->getDbFormattedTime()
            );
        }

        $newLogs = $this->watchlist->getUnprocessedCacheLogs();
        foreach ($newLogs as $log) {
            $this->watchlist->storeAndNotifyLog(
                $log,
                $this->watchlistItem->prepare($log),
                isset($this->watchlistConfig['use_logentries'])
                    && $this->watchlistConfig['use_logentries']
            );
        }
        unset($newLogs);

        if ($this->diagFileHandle != null) {
            fprintf(
                $this->diagFileHandle,
                "after-owner-notifies-cache-watches;%s;%lf\n",
                $this->getDbFormattedTime(),
                microtime(true) - $this->diagStartTime
            );
            $this->diagStartTime = microtime(true);
        }
    }

    /**
     * Retrieves watchers and owners having stored report items to send
     * along with watchers the next mail send date and time needs to be updated
     * for. For each watcher sends email where applicable and computes new send
     * mail date and time. Finally removes sent items from store.
     */
    private function sendReportsAndUpdateChecks()
    {
        $watchers = $this->watchlist->getWatchersAndWaitings();
        foreach ($watchers as $watcher) {
            if (
                (sizeof($watcher->getOwnerLogs()) > 0
                || sizeof($watcher->getWatchLogs()) > 0)
                && UserNotify::getUserLogsNotify($watcher->getUserId())
            ) {
                if (!Email::isValidEmailAddr($watcher->getEmail())) {
                    // Only users with valid addresses will receive notifications.
                    $sendStatus = true;
                } else {
                    $sendStatus = $this->watchlistReport->prepareAndSend($watcher);
                }
                $watcher->setSendStatus($sendStatus);
            }
            $watcher->setWatchmailNext(
                $this->computeWatchmailNextDateTime($watcher)
            );
        }
        $this->watchlist->clearWaitingsAndUpdateWatchers(
            $watchers,
            $this->ocConfig->getDbDateTimeFormat(),
            isset($this->watchlistConfig['use_logentries'])
                && $this->watchlistConfig['use_logentries']
        );
        unset($watchers);

        if ($this->diagFileHandle != null) {
            fprintf(
                $this->diagFileHandle,
                "after-send-out;%s;%lf\n",
                $this->getDbFormattedTime(),
                microtime(true) - $this->diagStartTime
            );
        }
    }

    /**
     * Computes new send mail date and time for watcher, where watchmail mode is
     * set to daily or weekly.
     *
     * @param WatchlistWatcher $watcher watcher to compute
     *     new date and time for
     *
     * @return \DateTime computed new send mail date and time
     */
    private function computeWatchmailNextDateTime(WatchlistWatcher $watcher)
    {
        $result = null;
        if (
            $watcher->getWatchmailMode() !=
                UserNotify::SEND_NOTIFICATION_HOURLY
        ) {
            $now = new \DateTime();
            if (
                $watcher->getWatchmailMode() ==
                    UserNotify::SEND_NOTIFICATION_DAILY
            ) {
                $result = $now ->
                    setDate(
                        $now->format('Y'),
                        $now->format('n'),
                        $now->format('j') + 1
                    )->
                    setTime($watcher->getWatchmailHour(), 0, 0);
            } elseif (
                $watcher->getWatchmailMode() ==
                    UserNotify::SEND_NOTIFICATION_WEEKLY
            ) {
                $weekday = $now->format('w');
                if ($weekday == 0) {
                    $weekday = 7;
                }
                $weekAdjust = ($weekday >= $watcher->getWatchmailDay() ? 0 : 7);
                $result = $now ->
                    setDate(
                        $now->format('Y'),
                        $now->format('n'),
                        $now->format('j') - $weekday
                            + $watcher->getWatchmailDay() + $weekAdjust
                    )->
                    setTime($watcher->getWatchmailHour(), 0, 0);
            }
        }
        return $result;
    }

    /**
     * Formats time using db format defined in OcConfig. Used only in
     * diagnostic file.
     *
     * @param \DateTime $time instance to format, current date and time is used
     *     if null
     *
     * @return string formatted date and time
     */
    private function getDbFormattedTime(\DateTime $time = null)
    {
        if ($time == null) {
            $time = new \DateTime();
        }
        return $time->format($this->ocConfig->getDbDateTimeFormat());
    }
}
