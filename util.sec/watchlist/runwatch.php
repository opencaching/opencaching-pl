<?php
use Utils\Database\OcDb;
use Utils\Email\EmailSender;
use lib\Objects\OcConfig\OcConfig;
use Utils\Log\Log;
use Utils\Log\LogEntry;

// TODO: think about better way to set paths and include settings
$GLOBALS['rootpath'] = "../../";
require_once($GLOBALS['rootpath'] . 'lib/common.inc.php');

WatchlistCronJobController::run();

/**
 * Purposes of this class:
 *  - prepare and send reports for watched caches as well as the owned ones
 *  - update next timedate scheduled for sending reports
 *
 */
class WatchlistCronJobController
{
    /**
     * Current class instance in singleton pattern
     */
    private static $instance;

    /**
     * SQL query used for retrieving new logs and their watchers at once
     */
    private static $newLogsSql =
        "SELECT cl.id log_id, c.user_id owner_id, cl.cache_id cache_id, cl.text text, u.username logger,
            c.wp_oc wp, c.name cachename, cl.type type, cl.date logdate, cw.user_id watcher_id,
            IF(ISNULL(cr.cache_id), 0, 1) AS recommended
         FROM user u, caches c, cache_logs cl
              LEFT OUTER JOIN(cache_rating cr) ON (cl.cache_id=cr.cache_id AND cl.user_id=cr.user_id)
              LEFT OUTER JOIN(cache_watches cw) ON (cw.cache_id=cl.cache_id)
              LEFT OUTER JOIN(watches_notified wn) ON (wn.user_id=cw.user_id AND wn.object_id=cl.id AND wn.object_type=1)
         WHERE cl.cache_id=c.cache_id AND u.user_id = cl.user_id AND cl.deleted=0 AND cl.owner_notified=0 AND wn.id IS NULL
         ORDER BY log_id, cache_id, watcher_id";

    /**
     * SQL query used for retrieving watchers and waiting watches at once
     */
    private static $watchToSendSql =
        "SELECT u.user_id user_id, u.username username, u.email email, u.watchmail_mode watchmail_mode, u.watchmail_hour watchmail_hour,
            u.watchmail_day watchmail_day, u.watchmail_nextmail watchmail_nextmail, ww.watchtext watchtext, ww.watchtype watchtype
         FROM user u LEFT OUTER JOIN (watches_waiting ww) ON (ww.user_id = u.user_id)
         WHERE (u.watchmail_mode = 1 AND ww.id IS NOT NULL) OR (u.watchmail_mode IN (0, 2) AND u.watchmail_nextmail < NOW())
         ORDER BY u.user_id, ww.id DESC";

    /**
     * SQL query used for insert data into watches_waiting - starting part
     */
    private static $watchWaitingSql = "INSERT IGNORE INTO watches_waiting (user_id, object_id, object_type, date_added, watchtext, watchtype) VALUES";
    /**
     * SQL query used for insert data into watches_notified - starting part
     */
    private static $watchNotifySql = "INSERT IGNORE INTO watches_notified (user_id, object_id, object_type, date_processed) VALUES";
    /**
     * SQL query used for updating owner_notified in cache_logs - starting part
     */
    private static $ownerNotifiedSql = "UPDATE cache_logs SET owner_notified=1 WHERE id IN (";
    /**
     * SQL query used for deleting rows by user_id from watches_waiting - starting part
     */
    private static $watchWaitingDelSql = "DELETE FROM watches_waiting WHERE watchtype IN (1, 2) AND user_id IN (";
    /**
     * SQL query used for updating nextmail for user_id in user - starting part
     */
    private static $nextmailUpdateSql = "INSERT IGNORE INTO user (user_id, watchmail_nextmail) VALUES";
    /**
     * SQL query used for updating nextmail for user_id in user - final part
     */
    private static $nextmailUpdateSqlSuffix = " ON DUPLICATE KEY UPDATE watchmail_nextmail=VALUES(watchmail_nextmail)";

    /**
     * Maximum size of data string for insert/update queries, defined in settings, default value 4096
     */
    private $maxDataSize;
    /**
     * Maximum size of LogEntry array before it is passed to database, defined in settings, default value 200
     */
    private $maxLogEntries;
    /**
     * If true, logentries are generated during processing
     */
    private $generateLockEntries;
    /**
     * Date format, compatible with SQL queries (can be refactored to static)
     */
    private $dateFormat;
    /**
     * Source text for log report item, the same for each log; initialized as an atrribute in purpose to load it only once
     */
    private $srcWatchListItemText;
    /**
     * OcDb instance
     */
    private $db;
    /**
     * Diagnosis log file handle
     */
    private $diagLogFile;
    /**
     * Diagnosis stage starting time, used for time measurement
     */
    private $diagStartTime;
    /**
     * Data string for delete watches_waiting, promoted to class level to share between methods
     */
    private $watchWaitingDeletes;
    /**
     * Data string for update nextmail, promoted to class level to share between methods
     */
    private $nextmailUpdates;
    /**
     * LogEntry array for watchlist event 2, promoted to class level to share between methods
     */
    private $sendLogEntries;

    /**
     * The only public entry to the class, performs the whole process
     */
    public static function run()
    {
        $watchlistConfig = OcConfig::instance()->watchlistConfig();
        $lockFile = null;
        $lockFilePath = isset($watchlistConfig['lock_file'])?$watchlistConfig['lock_file']:'/tmp/watchlist-runwatch.lock';
        if (mb_strlen($lockFilePath) > 0) {
            $lockFile = fopen($lockFilePath, "w");
            if (!flock($lockFile, LOCK_EX | LOCK_NB)) {
                // Another instance of the script is running - exit
                echo "Another instance of ".basename(__FILE__)." is currently running.\nExiting.\n";
                fclose($lockFile);
                return;
            }
        }
        if (self::$instance == null) {
            self::$instance = new WatchlistCronJobController($watchlistConfig);
        }
        $instance = self::$instance;
        $instance->processNewLogs();
        $instance->processWaitingAndWatchers();

        if ($lockFile != null) {
            fclose($lockFile);
        }
    }

    /**
     * Private constructor - singleton pattern
     *
     * @param array $watchlistConfig the config array, defined in settings ({@see /lib/settingsDefault.inc.php})
     */
    private function __construct(array $watchlistConfig)
    {
        $this->maxDataSize = !empty($watchlistConfig['max_sqldata'])?intval($watchlistConfig['max_sqldata']):4096;
        $this->generateLogEntries = !empty($watchlistConfig['use_logentries'])?$watchlistConfig['use_logentries']:false;
        $this->maxLogEntries = !empty($watchlistConfig['max_logentries'])?intval($watchlistConfig['max_logentries']):200;
        $this->dateFormat = 'Y-m-d H:i:s';
        $this->srcWatchlistItemText = EmailSender::prepareWatchlistItemSrc();
        $this->srcWatchlistItemText = mb_ereg_replace('{absolute_server_URI}', OcConfig::instance()->getAbsolute_server_URI(), $this->srcWatchlistItemText);
        $this->db = OcDb::instance();
        $diagFilePath = isset($watchlistConfig['diag_file'])?$watchlistConfig['diag_file']:'/var/log/ocpl/runwatch.log';
        if (mb_strlen($diagFilePath) > 0) {
            $this->diagLogFile = fopen($diagFilePath, "a");
        }
        $this->watchWaitingDeletes = "";
        $this->nextmailUpdates = "";
        $this->sendLogEntries = array();
    }

    /**
     * Stage I - retrieves new logs, prepares text items for each, stores them in watches_waiting,
     * inserts notifications to watches_notified table and updates owner_notified
     */
    private function processNewLogs()
    {
        if ($this->diagLogFile != null) {
            $this->diagStartTime = microtime(true);
            fprintf($this->diagLogFile, "start;%s\n", date($this->dateFormat));
        }
        $newLogs = $this->db->query(self::$newLogsSql);
        if ($newLogs) {
            $logEntries = array();
            $watchWaitingInserts="";
            $watchNotifyInserts="";
            $ownerNotifiedUpdates="";
            $currentLogId = -1;
            foreach ($newLogs as $row) {
                $resultWatchText = EmailSender::prepareWatchlistItem(
                    $row["logdate"], $row["logger"], $row["type"], $row["wp"], $row["cachename"], $row["text"], $row["recommended"],
                    $this->srcWatchlistItemText);
                if ($currentLogId != $row["log_id"]) {
                    $now = date($this->dateFormat);

                    $watchWaitingInserts = $this->processBulkSql(
                        self::$watchWaitingSql, $watchWaitingInserts, "",
                        "(".intval($row["owner_id"]).",".intval($row["log_id"]).",1,".$this->db->quote($now).",". $this->db->quote($resultWatchText).",1)",
                        $this->maxDataSize);

                    $watchNotifyInserts = $this->processBulkSql(
                        self::$watchNotifySql, $watchNotifyInserts, "",
                        "(".intval($row["owner_id"]).",".intval($row["log_id"]).",1,".$this->db->quote($now).")",
                        $this->maxDataSize);

                    $ownerNotifiedUpdates = $this->processBulkSql(self::$ownerNotifiedSql, $ownerNotifiedUpdates, ")", "".intval($row["log_id"]), $this->maxDataSize);
                    if ($this->generateLogEntries) {
                        array_push($logEntries, new LogEntry('watchlist', 1, $row["owner_id"], $row["log_id"], 0, $resultWatchText, array(), $now));
                        if (sizeof($logEntries) > $this->maxLogEntries) {
                            Log::logentries($logEntries);
                            $logEntries = array();
                        }
                    }
                    $currentLogId = $row["log_id"];
                }
                if ($row["watcher_id"] != null) {
                    $now = date($this->dateFormat);

                    $watchWaitingInserts = $this->processBulkSql(
                        self::$watchWaitingSql, $watchWaitingInserts, "",
                        "(".intval($row["watcher_id"]).",".intval($row["log_id"]).",1,".$this->db->quote($now).",".$this->db->quote($resultWatchText).",2)",
                        $this->maxDataSize);

                    $watchNotifyInserts = $this->processBulkSql(
                        self::$watchNotifySql, $watchNotifyInserts, "",
                        "(".intval($row["watcher_id"]).",".intval($row["log_id"]).",1,".$this->db->quote($now).")",
                        $this->maxDataSize);
                }
            }
            $newLogs->closeCursor();
            $this->processBulkSql(self::$watchWaitingSql, $watchWaitingInserts, "", "", 0);
            $this->processBulkSql(self::$watchNotifySql, $watchNotifyInserts, "", "", 0);
            $this->processBulkSql(self::$ownerNotifiedSql, $ownerNotifiedUpdates, ")", "", 0);
            if ($this->generateLogEntries && sizeof($logEntries) > 0) {
                Log::logentries($logEntries);
            }
        }
        if ($this->diagLogFile != null) {
            fprintf($this->diagLogFile, "after-owner-notifies-cache-watches;%s;%lf\n", date("Y-m-d H:i:s"), microtime(true) - $this->diagStartTime);
            $this->diagStartTime = microtime(true);
        }
    }

    /**
     * Stage II - retrieves items waiting to send, groups them in owwer and watch list,
     * calls processWatchUser to perform sending mail
     */
    private function processWaitingAndWatchers()
    {
        $watchToSend = $this->db->query(self::$watchToSendSql);
        if ($watchToSend) {
            $currentUserId = -1;
            $currentUsername = "";
            $currentUsermail = "";
            $currentWatchmailMode = "";
            $currentWatchmailHour = "";
            $currentWatchmailDay = "";
            $currentWatchmailNextMail = "";
            $ownerLogs = "";
            $watchLogs = "";
            foreach ($watchToSend as $row) {
                if ($currentUserId != $row["user_id"]) {
                    $this->processWatchUser(
                        $currentUserId, $currentUsername, $currentUsermail, $currentWatchmailMode,
                        $currentWatchmailHour, $currentWatchmailDay, $ownerLogs, $watchLogs);

                    $currentUserId = $row["user_id"];
                    $currentUsername = $row["username"];
                    $currentUsermail = $row["email"];
                    $currentWatchmailMode = $row['watchmail_mode'];
                    $currentWatchmailHour = $row['watchmail_hour'];
                    $currentWatchmailDay = $row['watchmail_day'];
                    $currentWatchmailNextMail = $row['watchmail_nextmail'];
                    $ownerLogs = "";
                    $watchLogs = "";
                }
                if ($row['watchtext'] != null && ($currentWatchmailMode == 1 || $currentWatchmailNextMail != '0000-00-00 00:00:00')) {
                    if ($row["watchtype"] == 1) {
                        $ownerLogs .= $row["watchtext"];
                    } elseif ($row["watchtype"] == 2) {
                        $watchLogs .= $row["watchtext"];
                    }
                }
            }
            $watchToSend->closeCursor();
            if ($currentUserId >= 0) {
                $this->processWatchUser(
                    $currentUserId, $currentUsername, $currentUsermail, $currentWatchmailMode,
                    $currentWatchmailHour, $currentWatchmailDay, $ownerLogs, $watchLogs);
            }
            $this->processBulkSql(self::$watchWaitingDelSql, $this->watchWaitingDeletes, ")", "", 0);
            $this->processBulkSql(self::$nextmailUpdateSql, $this->nextmailUpdates, self::$nextmailUpdateSqlSuffix, "", 0);
            if ($this->generateLogEntries && sizeof($this->sendLogEntries) > 0) {
                Log::logentries($this->sendLogEntries);
            }
        }
        if ($this->diagLogFile !=null) {
            fprintf($this->diagLogFile, "after-send-out;%s;%lf\n", date("Y-m-d H:i:s"), microtime(true) - $this->diagStartTime);
            fclose($this->diagLogFile);
        }
    }

    /**
     * Commisions sending email to single user either/or computes new nextmail datetime if applicable
     *
     * @param int $userid the id of processed user
     * @param string $username the processed user name
     * @param string $usermail the mail of processed user
     * @param int $watchmailMode the mode of periodic sending mail to user
     * @param int $watchmailHour the hour to send mail to user if applicable
     * @param int $watchmailDay the week day to send mail to user if applicable
     * @param string $ownerLogs the logs regarding the user own caches
     * @param string $watchLogs the logs regarding caches watched by user
     */
    private function processWatchUser($userid, $username, $usermail, $watchmailMode, $watchmailHour, $watchmailDay, $ownerLogs, $watchLogs)
    {
        if (mb_strlen($ownerLogs) > 0 || mb_strlen($watchLogs) > 0) {
            $now = date($this->dateFormat);
            $status = EmailSender::sendWatchlistMail($username, $usermail, $ownerLogs, $watchLogs);
            $this->watchWaitingDeletes = $this->processBulkSql(self::$watchWaitingDelSql, $this->watchWaitingDeletes, ")", $userid, $this->maxDataSize);
            if ($this->generateLogEntries) {
                array_push($this->sendLogEntries, new LogEntry('watchlist', 2, $userid, 0, 0, 'Sending mail to ' . $usermail, array('status' => $status), $now));
                if (sizeof($this->sendLogEntries) > $this->maxLogEntries) {
                    Log::logentries($this->sendLogEntries);
                    $this->sendLogEntries = array();
                }
            }
        }
        if ($userid >= 0 && ($watchmailMode == 0 || $watchmailMode == 2)) {
            if ($watchmailMode == 0) {
                $nextmail = date($this->dateFormat, mktime($watchmailHour, 0, 0, date('n'), date('j') + 1, date('Y')));
            } elseif ($watchmailMode == 2) {
                $weekday = date('w');
                if ($weekday == 0){
                    $weekday = 7;
                }
                if ($weekday >= $watchmailDay) {
                    // We are on or after specified day in the week - next run should be next week
                    $nextmail = date($this->dateFormat, mktime($watchmailHour, 0, 0, date('n'), date('j') - $weekday + $watchmailDay + 7, date('Y')));
                } else {
                    // We are still before specified day in the week - next run should be this week
                    $nextmail = date($this->dateFormat, mktime($watchmailHour, 0, 0, date('n'), date('j') - $weekday + $watchmailDay + 0, date('Y')));
                }
            }
            $this->nextmailUpdates = $this->processBulkSql(
                self::$nextmailUpdateSql, $this->nextmailUpdates, self::$nextmailUpdateSqlSuffix,
                "(".intval($userid).",".$this->db->quote($nextmail).")", $this->maxDataSize);
        }
    }

    /**
     * Builds SQL data string and executes the bulk query if data string size exceeds maxDataSize
     *
     * @param string $sqlPrefix starting part of SQL query to execute
     * @param string $sqlDataString current SQL data string contents
     * @param string $sqlSuffix final part of SQL query to execute
     * @param string $sqlEntry the data to compose with existing SQL data string
     * @param int $maxDataSize maximum size of resulting SQL data string, when exceeded the query is executed
     *
     * @return string the resulting SQL data string for future use, may be empty if query has been executed
     */
    private function processBulkSql($sqlPrefix, $sqlDataString, $sqlSuffix, $sqlEntry, $maxDataSize)
    {
        if (mb_strlen($sqlEntry) > 0) {
            if (mb_strlen($sqlDataString) > 0) {
                $sqlDataString .= ",";
            }
            $sqlDataString .= $sqlEntry;
        }
        if (mb_strlen($sqlDataString) > $maxDataSize) {
            $this->db->exec($sqlPrefix.$sqlDataString.$sqlSuffix);
            $sqlDataString = "";
        }
        return $sqlDataString;
    }

}
