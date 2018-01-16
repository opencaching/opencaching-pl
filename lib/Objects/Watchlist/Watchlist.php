<?php
/**
 * Contains \lib\Objects\Watchlist\Watchlist class definition
 */
namespace lib\Objects\Watchlist;

use lib\Objects\BaseObject;
use lib\Objects\User\UserWatchedCache;
use Utils\Database\OcDb;
use Utils\Log\Log;

/**
 * DAO for performing watchlist operations
 */
class Watchlist extends BaseObject
{
    /** Operation regarding cache owner logs, used in watches_waiting table */
    const WATCHTYPE_OWNER = 1;
    /** Operation regarding watched caches logs, used in watches_waiting table */
    const WATCHTYPE_WATCH = 2;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Retieves unprocessed (with owner_notified set to 0 ) cache logs,
     * including related data necessary for report item formatting
     *
     * @return WatchlistGeoCacheLog[] list of log instances
     */
    public function getUnprocessedCacheLogs()
    {
        $buffered = $this->db->getMysqlQueryBuffering();
        if (!is_null($buffered) && $buffered) {
            $this->db->setMysqlQueryBuffering(true);
        }
        $stmt = $this->db->simpleQuery(
            "SELECT cl.id, u.username, cl.type, cl.text, cl.date, c.cache_id,
                 c.user_id, c.wp_oc, c.name,
             IF(ISNULL(cr.cache_id), 0, 1) AS recommended
             FROM caches c, user u, cache_logs cl
             LEFT OUTER JOIN cache_rating cr ON
                 cl.cache_id=cr.cache_id AND cl.user_id=cr.user_id
             WHERE cl.cache_id=c.cache_id AND u.user_id = cl.user_id
                 AND cl.deleted=0 AND cl.owner_notified=0"
        );

        $result = $this->db->dbFetchAllAsObjects($stmt, function ($row) {
            return $this->getWatchlistGeoCacheLog($row);
        });
        $stmt->closeCursor();
        if (!is_null($buffered)) {
            $this->db->setMysqlQueryBuffering($buffered);
        }
        return $result;
    }

    /**
     * Creates new instance of {@see WatchlistGeoCacheLog} from query result row
     *
     * @param array $row query result row as an associative array
     *
     * @return WatchlistGeoCacheLog created instance
     */
    private function getWatchlistGeoCacheLog($row)
    {
        return new WatchlistGeoCacheLog(
            $row['id'],
            $row['username'],
            $row['type'],
            $row['text'],
            new \DateTime($row['date']),
            $row['cache_id'],
            $row['user_id'],
            $row['wp_oc'],
            $row['name'],
            $row['recommended']
        );
    }

    /**
     * Calls methods to store report item text related to given log data
     * and to notify about log processed and updates log as notified for owner
     *
     * @param WarchlistGeoCacheLog $log the log to process
     * @param string $itemText the formatted item to store
     * @param boolean $useLogEntries true if logentries should be updated too,
     *     false otherwise
     */
    public function storeAndNotifyLog(
        WatchlistGeoCacheLog $log,
        $itemText,
        $useLogentries
    ) {
        if (!empty($itemText)) {
            $this->updateWatchesWaiting($log, $itemText);
            $this->updateWatchesNotified($log);
            $this->db()->exec(
                "UPDATE cache_logs SET owner_notified=1 WHERE id="
                . intval($log->getLogId())
            );
            if ($useLogentries) {
                Log::logentry(
                    'watchlist',
                    1,
                    $log->getCacheOwnerId(),
                    $log->getLogId(),
                    0,
                    $itemText,
                    array()
                );
            }
        }
    }

    /**
     * Stores report item text related to given log data for the cache owner and
     * the cache watching users
     *
     * @param WarchlistGeoCacheLog $log the log to process
     * @param string $itemText the formatted item to store
     */
    private function updateWatchesWaiting(
        WatchlistGeoCacheLog $log,
        $itemText
    ) {
        $quotedText = $this->db->quote($itemText);
        $this->db->exec(
            "INSERT IGNORE INTO watches_waiting (
                 user_id, object_id, object_type, date_added,
                 watchtext, watchtype)
             VALUES("
                 . intval($log->getCacheOwnerId())
                 . "," . intval($log->getLogId())
                 . ", 1, NOW(), " . $quotedText . ", " . self::WATCHTYPE_OWNER
                 . ")"
        );
        $this->db->exec(
            "INSERT IGNORE INTO watches_waiting (
                 user_id, object_id, object_type, date_added,
                 watchtext, watchtype)
             SELECT
                 cw.user_id, cl.id, 1, NOW(), " . $quotedText
                 . ", " . self::WATCHTYPE_WATCH . " 
             FROM cache_watches cw, cache_logs cl
             WHERE cl.cache_id=cw.cache_id AND cl.id="
             . intval($log->getLogId())
        );
    }

    /**
     * Saves notifications about processing the cache log for its owner and
     * the cache watching users
     *
     * @param WarchlistGeoCacheLog $log the log to process
     */
    private function updateWatchesNotified(WatchlistGeoCacheLog $log)
    {
        $this->db->exec(
            "INSERT IGNORE INTO watches_notified (
                 user_id, object_id, object_type, date_processed)
             VALUES("
                 . intval($log->getCacheOwnerId())
                 . "," . intval($log->getLogId())
                 . ", 1, NOW())"
        );
        $this->db->exec(
            "INSERT IGNORE INTO watches_notified (
                 user_id, object_id, object_type, date_processed)
             SELECT cw.user_id, cl.id, 1, NOW()
             FROM cache_watches cw, cache_logs cl, watches_waiting ww
             WHERE cl.cache_id=cw.cache_id AND cl.id=ww.object_id
                 AND ww.user_id=cw.user_id AND ww.watchtype="
                 . self::WATCHTYPE_WATCH . " AND cl.id="
                 . intval($log->getLogId())
        );
    }

    /**
     * Retrieves data of all users having watchlist report items waiting to send
     * along with the items, or users having report sending option set to daily
     * or weekly. Puts retrieved items into owner and watch arrays.
     *
     * @return WachlistWatcher[] list of watchers
     */
    public function getWatchersAndWaitings()
    {
        $buffered = $this->db->getMysqlQueryBuffering();
        if (!is_null($buffered) && $buffered) {
            $this->db->setMysqlQueryBuffering(true);
        }
        $stmt = $this->db->simpleQuery(
            "SELECT
                 u.user_id, u.username, u.email, u.watchmail_mode,
                 u.watchmail_hour, u.watchmail_day, u.watchmail_nextmail,
                 ww.watchtext, ww.watchtype
             FROM user u LEFT OUTER JOIN watches_waiting ww ON
                 ww.user_id = u.user_id AND ww.watchtype IN ("
                 . self::WATCHTYPE_OWNER . ", " . self::WATCHTYPE_WATCH . ")
             WHERE
                 (u.watchmail_mode = "
                     . UserWatchedCache::SEND_NOTIFICATION_HOURLY . " 
                  AND ww.id IS NOT NULL)
                 OR
                 (u.watchmail_mode IN ("
                     . UserWatchedCache::SEND_NOTIFICATION_DAILY
                     . ", " . UserWatchedCache::SEND_NOTIFICATION_WEEKLY . ")
                  AND u.watchmail_nextmail < NOW()
                 )
             ORDER BY u.user_id, ww.id DESC"
        );

        $currentWatcher = null;
        $result = [];
        while ($row = $this->db->dbResultFetch($stmt, OcDb::FETCH_ASSOC)) {
            if (
                $currentWatcher == null
                || $currentWatcher->getUserId() != $row['user_id']
            ) {
                if ($currentWatcher != null) {
                    $result[] = $currentWatcher;
                }
                $nextmail = $row['watchmail_nextmail'];
                $currentWatcher =  new WatchlistWatcher(
                    $row['user_id'],
                    $row['username'],
                    $row['email'],
                    $row['watchmail_mode'],
                    $row['watchmail_hour'],
                    $row['watchmail_day'],
                    is_null($nextmail) ? null: new \DateTime($nextmail)
                );
            }
            if ($row['watchtype'] != null && (
                (
                    $currentWatcher->getWatchmailMode() ==
                    UserWatchedCache::SEND_NOTIFICATION_HOURLY
                ) || (
                    $currentWatcher->getWatchmailNext() != null
                    && $currentWatcher->getWatchmailNext()->getTimestamp() > 0
                )
            )) {
                if ($row['watchtype'] == self::WATCHTYPE_OWNER) {
                    $currentWatcher->addOwnerLog($row['watchtext']);
                } elseif ($row['watchtype'] == self::WATCHTYPE_WATCH) {
                    $currentWatcher->addWatchLog($row['watchtext']);
                }
            }
        }
        $stmt->closeCursor();
        if ($buffered != null) {
            $this->db->setMysqlQueryBuffering($buffered);
        }
        if ($currentWatcher != null) {
            $result[] = $currentWatcher;
        }
        return $result;
    }

    /**
     * Removes from database already sent log items and updates users next mail
     * send date and time where applicable
     *
     * @param WatchlistWatcher[] $watchers list of watchers
     * @param string $dbFormat database date and time format
     * @param boolean $useLogEntries true if logentries should be updated too,
     *     false otherwise
     */
    public function clearWaitingsAndUpdateWatchers(
        $watchers,
        $dbFormat,
        $useLogentries
    ) {
        if (is_array($watchers) and sizeof($watchers) > 0) {
            foreach ($watchers as $watcher) {
                if ($watcher instanceof WatchlistWatcher) {
                    $this->db->exec(
                        "DELETE FROM watches_waiting WHERE watchtype IN ("
                             . self::WATCHTYPE_OWNER
                             . ", " . self::WATCHTYPE_WATCH . ")
                         AND user_id = " . intval($watcher->getUserId())
                    );
                    if ($watcher->getWatchmailNext() != null
                        && $watcher->getWatchmailNext()->getTimestamp() > 0
                    ) {
                        $this->db->exec(
                            "UPDATE user SET watchmail_nextmail = "
                                . $this->db->quote(
                                     $watcher->getWatchmailNext()
                                         ->format($dbFormat)
                                 ) . " 
                             WHERE user_id = " . intval($watcher->getUserId())
                        );
                    }
                    if ($useLogentries) {
                        Log::logentry(
                            'watchlist',
                            2,
                            $watcher->getUserId(),
                            0,
                            0,
                            'Sending mail to ' . $watcher->getEmail(),
                            array('status' => $watcher->getSendStatus())
                        );
                    }
                }
            }
        }
    }
}
