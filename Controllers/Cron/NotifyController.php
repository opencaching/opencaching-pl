<?php
namespace Controllers\Cron;

use Controllers\BaseController;
use Utils\Email\EmailSender;
use lib\Objects\Notify\Notify;
use lib\Objects\User\User;

class NotifyController extends BaseController
{

    const NOTIFY_LOCK = "/tmp/notification-run_notify.lock";

    const NOTIFY_FLAG = "/tmp/notification-run_notify.date";

    private $lockFile;

    /* @var \DateTime */
    private $lastRun;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->processNotifyQueue();
    }

    private function processNotifyQueue()
    {
        if (! $this->setLock()) {
            die("Another instance is running or problem with lock file");
        }
        $this->lastRun = $this->getFlagTime();
        $this->touchFlag();
        
        $notifiesWaiting = Notify::getUniqueUserIdNotifiesList(Notify::TYPE_NEWCACHE);
        foreach ($notifiesWaiting as $uniqueUser) {
            $itemUser = new User(array(
                'userId' => $uniqueUser['user_id']
            ));
            if ($this->checkIfShouldSendToUser($itemUser)) {
                $this->sendNotifiesAndClean($itemUser);
            }
            unset($itemUser);
        }
        $this->unsetLock();
    }

    /**
     * Check if should send notify for user just now
     *
     * @param User $user
     * @return boolean
     */
    private function checkIfShouldSendToUser(User $user)
    {
        $right_time = new \DateTime();
        $hour_now = $right_time->format('H');
        
        switch ($user->getWatchmailMode()) {
            case '1': // Send notifications every hour
                $right_time->setTime(intval($hour_now), 0, 0);
                break;
            case '0': // Notify once per day
                if ($user->getWatchmailHour() > $hour_now) {
                    $right_time->sub(new \DateInterval('P1D'));
                }
                $right_time->setTime(intval($user->getWatchmailHour()), 0, 0);
                break;
            case '2': // Notify once per week
                if ($user->getWatchmailHour() > $hour_now) {
                    $right_time->sub(new \DateInterval('P1D'));
                }
                $right_time->setTime(intval($user->getWatchmailHour()), 0, 0);
                if (intval($user->getWatchmailDay()) >= 1 && intval($user->getWatchmailDay()) <= 7) { // Check for sure
                    while (intval($right_time->format('N')) != intval($user->getWatchmailDay())) {
                        $right_time->sub(new \DateInterval('P1D'));
                    }
                }
                break;
        }
        return $this->lastRun < $right_time;
    }

    private function sendNotifiesAndClean(User $user)
    {
        $notifiesList = Notify::getAllNotifiesForUserId($user->getUserId(), Notify::TYPE_NEWCACHE);
        EmailSender::sendNewCacheNotify(__DIR__ . '/../../tpl/stdstyle/email/', $notifiesList, $user);
        Notify::deleteNotifiesForUserId($user->getUserId(), Notify::TYPE_NEWCACHE);
    }

    /**
     * Make lock to prevent multiple instances run at once
     *
     * @return boolean - false if other instance are running
     */
    private function setLock()
    {
        $this->lockFile = fopen(self::NOTIFY_LOCK, "w");
        if (! flock($this->lockFile, LOCK_EX | LOCK_NB)) {
            fclose($this->lockFile);
            return false;
        }
        return true;
    }

    /**
     * Unset lock made by setLock();
     */
    private function unsetLock()
    {
        flock($this->lockFile, LOCK_UN);
        fclose($this->lockFile);
    }

    /**
     * Change mtime of NOTIFY_FLAG - used to check previous run
     *
     * @return boolean
     */
    private function touchFlag()
    {
        return touch(self::NOTIFY_FLAG);
    }

    /**
     * Returns DateTime object of mtime Flag file
     *
     * @return \DateTime - last modification time of NOTIFY_FLAG
     */
    private function getFlagTime()
    {
        $mTime = new \DateTime();
        if (file_exists(self::NOTIFY_FLAG)) {
            $mTime->setTimestamp(filemtime(self::NOTIFY_FLAG));
        } else {
            $mTime->setTimestamp(0);
        }
        return $mTime;
    }
}