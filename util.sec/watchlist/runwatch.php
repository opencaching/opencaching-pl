<?php
/**
 *
 * This script sends emails notification about new logs to cache owner and watchers
 * It should be called from CRON quite often (to not delay messages)
 *
 */
use Utils\Database\XDb;
use Utils\Log\Log;

$rootpath = '../../';

require_once($rootpath . 'lib/consts.inc.php');
require_once($rootpath . 'lib/common.inc.php');


$sDateformat = 'Y-m-d H:i:s';
$mailsubject = tr('runwatch03') . ' ' . $site_name . ': ' . date('Y-m-d H:i:s');
$nologs = tr('runwatch15');


// Check if another instance of the script is running
$lock_file = fopen("/tmp/watchlist-runwatch.lock", "w");
if (!flock($lock_file, LOCK_EX | LOCK_NB)) {
    // Another instance of the script is running - exit
    echo "Another instance of runwatch.php is currently running.\nExiting.\n";
    fclose($lock_file);
    exit;
}

// No other instance - do normal processing

$diag_log_file = fopen("/var/log/ocpl/runwatch.log", "a");
$diag_start_time = microtime(true);
fprintf($diag_log_file, "start;%s\n", date("Y-m-d H:i:s"));

/* Stage I: Notify
 * - cache owners
 * - cache watchers
 * about new logs in their caches
 */
$rsNewLogs = XDb::xSql(
    "SELECT cache_logs.id log_id, caches.user_id user_id, cache_logs.cache_id cache_id
    FROM cache_logs, caches
    WHERE cache_logs.deleted=0
        AND cache_logs.cache_id=caches.cache_id
        AND cache_logs.owner_notified=0");

while( $rNewLog = XDb::xFetchArray($rsNewLogs) ){

    //foreach cache with new log entry..

    $rNewLog_log_id = $rNewLog['log_id'];
    $rNewLog_user_id = $rNewLog['user_id'];
    $rNewLog_cache_id = $rNewLog['cache_id'];

    // Notify owner
    $rsNotified = XDb::xMultiVariableQueryValue(
        "SELECT COUNT(`id`) FROM watches_notified
        WHERE user_id= :1
            AND object_id= :2
            AND object_type=1",
        -1, $rNewLog_user_id, $rNewLog_log_id);

    if ( $rsNotified == 0) {

        XDb::xSql(
            "INSERT IGNORE INTO `watches_notified` (`user_id`, `object_id`, `object_type`, `date_processed`)
            VALUES ( ?,  ?, 1, NOW())",
            $rNewLog_user_id, $rNewLog_log_id);

        process_owner_log($rNewLog_user_id, $rNewLog_log_id);
    }

    // Notify watchers
    $rscw = XDb::xSql(
        "SELECT user_id FROM cache_watches WHERE cache_id = ?", $rNewLog_cache_id);

    while( $rcw = XDb::xFetchArray($rscw) ){

        $rcw_user_id = $rcw['user_id'];

        // check if this notification was send before...
        $rsNotified = XDb::xMultiVariableQueryValue(
            "SELECT COUNT(`id`) FROM watches_notified
            WHERE user_id= :1
                AND object_id= :2
                AND object_type=1",
            -1, $rcw_user_id, $rNewLog_log_id);

        if ( $rsNotified == 0) {
            XDb::xSql(
                "INSERT IGNORE INTO `watches_notified` (`user_id`, `object_id`, `object_type`, `date_processed`)
                VALUES ( ?, ?, 1, NOW())",
                $rcw_user_id, $rNewLog_log_id);

            process_log_watch($rcw_user_id, $rNewLog_log_id);
        }
    }
    XDb::xFreeResults($rscw);

    XDb::xSql("UPDATE cache_logs SET owner_notified=1 WHERE id= ? LIMIT 1", $rNewLog_log_id);
}

XDb::xFreeResults($rsNewLogs);
/* end owner notifies and cache watches */

fprintf($diag_log_file, "after-owner-notifies-cache-watches;%s;%lf\n", date("Y-m-d H:i:s"), microtime(true) - $diag_start_time);
$diag_start_time = microtime(true);


/* Stage II: begin send out everything that has to be sent */

/* Stage IIA: send messages to users who have requested immediate notification */

$currUserID = '';
$currUserName = '';
$currUserEMail = '';
$currUserOwnerLogs = '';
$currUserWatchLogs = '';

$rsWatchesUsers = XDb::xSql(
    'SELECT watches_waiting.id AS id, watches_waiting.watchtext AS watchtext, watches_waiting.watchtype AS watchtype,
            `user`.user_id AS user_id, `user`.username AS username, `user`.email AS email
    FROM `user`, watches_waiting
    WHERE `user`.user_id = watches_waiting.user_id
        AND `user`.watchmail_mode = 1
    ORDER BY watches_waiting.user_id, watches_waiting.id DESC');

while( $rWatchUser = XDb::xFetchArray($rsWatchesUsers) ){

    if ($currUserID != $rWatchUser['user_id']) {
        // Time to send all gathered info for the previous user (if any)
        send_mail_and_clean_watches_waiting($currUserID, $currUserName, $currUserEMail, $currUserOwnerLogs, $currUserWatchLogs);

        // After sending e-mail prepare the stage for the next user
        $currUserID = $rWatchUser['user_id'];
        $currUserName = $rWatchUser['username'];
        $currUserEMail = $rWatchUser['email'];
        $currUserOwnerLogs = '';
        $currUserWatchLogs = '';
    }

    if ($rWatchUser['watchtype'] == '1') {
        $currUserOwnerLogs .= $rWatchUser['watchtext'];
    }
    if ($rWatchUser['watchtype'] == '2') {
        $currUserWatchLogs .= $rWatchUser['watchtext'];
    }
}
XDb::xFreeResults($rsWatchesUsers);

// Send all gathered info for the last user (if any)
send_mail_and_clean_watches_waiting($currUserID, $currUserName, $currUserEMail, $currUserOwnerLogs, $currUserWatchLogs);


/* Stage IIB: check/send messages to users who have requested daily/weekly notification */

$rsUsers = XDb::xSql(
    'SELECT user_id, username, email, watchmail_mode, watchmail_hour, watchmail_day, watchmail_nextmail
    FROM `user` WHERE watchmail_mode IN (0, 2) AND watchmail_nextmail < NOW()');

while( $rUser = XDb::xFetchArray($rsUsers) ){

    if ($rUser['watchmail_nextmail'] != '0000-00-00 00:00:00') {

        $r['count'] = XDb::xMultiVariableQueryValue(
            "SELECT COUNT(*) count FROM watches_waiting WHERE user_id= :1 ", 0, $rUser['user_id']);

        if ($r['count'] > 0) {
            $currUserID = $rUser['user_id'];
            $currUserName = $rUser['username'];
            $currUserEMail = $rUser['email'];
            $currUserOwnerLogs = '';
            $currUserWatchLogs = '';

            $rsWatchesOwner = XDb::xSql(
                "SELECT id, watchtext FROM watches_waiting
                WHERE user_id= ? AND watchtype=1
                ORDER BY id DESC", $rUser['user_id']);

            while( $rWatch = XDb::xFetchArray($rsWatchesOwner) ){
                $currUserOwnerLogs .= $rWatch['watchtext'];
            }
            XDb::xFreeResults($rsWatchesOwner);

            $rsWatchesLog = XDb::xSql(
                "SELECT id, watchtext FROM watches_waiting
                WHERE user_id= ? AND watchtype=2
                ORDER BY id DESC",
                $rUser['user_id']);

            while ( $rWatch = XDb::xFetchArray($rsWatchesLog) ){
                $currUserWatchLogs .= $rWatch['watchtext'];
            }

            // send mail
            send_mail_and_clean_watches_waiting($currUserID, $currUserName, $currUserEMail, $currUserOwnerLogs, $currUserWatchLogs);
        }
    }

    if ($rUser['watchmail_mode'] == 0)
        $nextmail = date($sDateformat, mktime($rUser['watchmail_hour'], 0, 0, date('n'), date('j') + 1, date('Y')));
    elseif ($rUser['watchmail_mode'] == 2) {
        $weekday = date('w');
        if ($weekday == 0)
            $weekday = 7;

        if ($weekday >= $rUser['watchmail_day']){
            // We are on or after specified day in the week - next run should be next week
            $nextmail = date($sDateformat, mktime($rUser['watchmail_hour'], 0, 0, date('n'), date('j') - $weekday + $rUser['watchmail_day'] + 7, date('Y')));
        } else {
            // We are still before specified day in the week - next run should be this week
            $nextmail = date($sDateformat, mktime($rUser['watchmail_hour'], 0, 0, date('n'), date('j') - $weekday + $rUser['watchmail_day'] + 0, date('Y')));
        }
    }

    XDb::xSql("UPDATE user SET watchmail_nextmail= ? WHERE user_id= ? LIMIT 1", $nextmail, $rUser['user_id']);
}
XDb::xFreeResults($rsUsers);

/* end send out everything that has to be sent */


fprintf($diag_log_file, "after-send-out;%s;%lf\n", date("Y-m-d H:i:s"), microtime(true) - $diag_start_time);
$diag_start_time = microtime(true);
fclose($diag_log_file);

// Release lock
fclose($lock_file);

/**
 * This function prepares message to cache owner about new log entry
 * @param unknown $user_id
 * @param unknown $log_id
 */
function process_owner_log($user_id, $log_id)
{
    global $absolute_server_URI, $octeamEmailsSignature;


    $rsLog = XDb::xSql(
        "SELECT cache_logs.cache_id cache_id, cache_logs.text text, cache_logs.text_html text_html,
                cache_logs.date logdate, user.username username, user.hidden_count ch, user.founds_count cf,
                user.notfounds_count cn, caches.wp_oc wp,caches.name cachename, cache_logs.type type,
                IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`
        FROM `cache_logs`
            LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id`
            AND `cache_logs`.`user_id`=`cache_rating`.`user_id`, `user`, `caches`
        WHERE `cache_logs`.`deleted`=0 AND (cache_logs.user_id = user.user_id)
            AND (cache_logs.cache_id = caches.cache_id)
            AND (cache_logs.id = ? )
        LIMIT 1", $log_id);

    $rLog = XDb::xFetchArray($rsLog);
    XDb::xFreeResults($rsLog);

    $userActivity = $rLog['ch'] + $rLog['cf'] + $rLog['cn'];
    $watchtext = file_get_contents(dirname(__FILE__) . '/item.email.html');
    $logtext = $rLog['text'];
    $logtext = preg_replace("/<img[^>]+\>/i", "", $logtext);

    $logtypeParams = getLogtypeParams($rLog['type']);
    if (isset($logtypeParams['username'])) {
        $rLog['username'] = $logtypeParams['username'];
    }

    if ($rLog['recommended'] != 0 && $rLog['type'] == 1) {
        $recommended = ' + ' . tr('recommendation');
    } else {
        $recommended = '';
    }
    $watchtext = mb_ereg_replace('{date}', date('Y-m-d H:i', strtotime($rLog['logdate'])), $watchtext);
    $watchtext = mb_ereg_replace('{wp}', $rLog['wp'], $watchtext);
    $watchtext = mb_ereg_replace('{text}', $logtext, $watchtext);
    $watchtext = mb_ereg_replace('{user}', $rLog['username'], $watchtext);
    $watchtext = mb_ereg_replace('{logtype}', $logtypeParams['logtype'] . $recommended, $watchtext);
    $watchtext = mb_ereg_replace('{cachename}', $rLog['cachename'], $watchtext);
    $watchtext = mb_ereg_replace('{logtypeColor}', $logtypeParams['logtypeColor'], $watchtext);
    $watchtext = mb_ereg_replace('{runwatch01}', tr('runwatch01'), $watchtext);
    $watchtext = mb_ereg_replace('{runwatch02}', tr('runwatch02'), $watchtext);
    $watchtext = mb_ereg_replace('{absolute_server_URI}', $absolute_server_URI, $watchtext);
    $watchtext = mb_ereg_replace('{emailSign}', $octeamEmailsSignature, $watchtext);
    $watchtext = mb_ereg_replace('{userActivity}', $userActivity, $watchtext);

    XDb::xSql(
        "INSERT IGNORE INTO watches_waiting (`user_id`, `object_id`, `object_type`, `date_added`, `watchtext`, `watchtype`)
        VALUES ( ?, ?, 1, NOW(), ?, 1)",
        $user_id, $log_id, $watchtext);

    Log::logentry('watchlist', 1, $user_id, $log_id, 0, $watchtext, array());
}

/**
 * This function prepares message to text watcher about new log entry
 * @param unknown $user_id
 * @param unknown $log_id
 */
function process_log_watch($user_id, $log_id)
{
    global $absolute_server_URI;

    $rsLog = XDb::xSql(
        "SELECT cache_logs.cache_id cache_id, cache_logs.text text, cache_logs.text_html text_html,
                cache_logs.date logdate, user.username username, user.hidden_count ch, user.founds_count cf,
                user.notfounds_count cn, caches.wp_oc wp,caches.name cachename, cache_logs.type type,
                IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`
        FROM `cache_logs`
            LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id`
            AND `cache_logs`.`user_id`=`cache_rating`.`user_id`, `user`, `caches`
        WHERE `cache_logs`.`deleted`=0 AND (cache_logs.user_id = user.user_id)
            AND (cache_logs.cache_id = caches.cache_id)
            AND (cache_logs.id = ?)
        LIMIT 1", $log_id);

    $rLog = XDb::xFetchArray($rsLog);
    XDb::xFreeResults($rsLog);

    $logtypeParams = getLogtypeParams($rLog['type']);
    if (isset($logtypeParams['username'])) {
        $rLog['username'] = $logtypeParams['username'];
    }

    if ($rLog['recommended'] != 0 && $rLog['type'] == 1) {
        $recommended = ' + ' . tr('recommendation');
    } else {
        $recommended = '';
    }

    $watchtext = file_get_contents(dirname(__FILE__) . '/item.email.html');
    $logtext = $rLog['text'];

    $logtext = preg_replace("/<img[^>]+\>/i", "", $logtext);

    $watchtext = mb_ereg_replace('{date}', date('Y-m-d H:i', strtotime($rLog['logdate'])), $watchtext);
    $watchtext = mb_ereg_replace('{wp}', $rLog['wp'], $watchtext);
    $watchtext = mb_ereg_replace('{text}', $logtext, $watchtext);
    $watchtext = mb_ereg_replace('{user}', $rLog['username'], $watchtext);
    $watchtext = mb_ereg_replace('{logtype}', $logtypeParams['logtype'] . $recommended, $watchtext);
    $watchtext = mb_ereg_replace('{cachename}', $rLog['cachename'], $watchtext);
    $watchtext = mb_ereg_replace('{logtypeColor}', $logtypeParams['logtypeColor'], $watchtext);
    $watchtext = mb_ereg_replace('{runwatch02}', tr('runwatch02'), $watchtext);
    $watchtext = mb_ereg_replace('{absolute_server_URI}', $absolute_server_URI, $watchtext);
    $watchtext = mb_ereg_replace('{userActivity}', $userActivity, $watchtext);

    XDb::xSql(
        "INSERT IGNORE INTO watches_waiting (`user_id`, `object_id`, `object_type`, `date_added`, `watchtext`, `watchtype`)
        VALUES (?, ?, 1, NOW(), ?, 2)",
        $user_id, $log_id, $watchtext);
}

function send_mail_and_clean_watches_waiting($currUserID, $currUserName, $currUserEMail, $currUserOwnerLogs, $currUserWatchLogs)
{
    global $nologs, $watchlistMailfrom, $mailsubject, $absolute_server_URI, $octeamEmailsSignature;

    if ($currUserID == '')
        return;

    $email_headers = 'MIME-Version: 1.0' . "\r\n";
    $email_headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $email_headers .= 'From: "' . $watchlistMailfrom . '" <' . $watchlistMailfrom . '>';

    $mailbody = file_get_contents(dirname(__FILE__) . '/watchlist.email.html');
    $mailbody = mb_ereg_replace('{username}', $currUserName, $mailbody);
    $mailbody = mb_ereg_replace('{absolute_server_URI}', $absolute_server_URI, $mailbody);

    if ($currUserOwnerLogs != '') {
        $logtexts = $currUserOwnerLogs;

        while ((mb_substr($logtexts, -1) == "\n") || (mb_substr($logtexts, -1) == "\r"))
            $logtexts = mb_substr($logtexts, 0, mb_strlen($logtexts) - 1);

        $mailbody = mb_ereg_replace('{ownerlogs}', $logtexts, $mailbody);
        $mailbody = mb_ereg_replace('{cachesOwnedDisplay}', 'block', $mailbody);
    } else {
        $mailbody = mb_ereg_replace('{ownerlogs}', $nologs, $mailbody);
        $mailbody = mb_ereg_replace('{cachesOwnedDisplay}', 'none', $mailbody);
    }

    if ($currUserWatchLogs != '') {
        $logtexts = $currUserWatchLogs;

        while ((mb_substr($logtexts, -1) == "\n") || (mb_substr($logtexts, -1) == "\r"))
            $logtexts = mb_substr($logtexts, 0, mb_strlen($logtexts) - 1);

        $mailbody = mb_ereg_replace('{watchlogs}', $logtexts, $mailbody);
        $mailbody = mb_ereg_replace('{cachesWatchedDisplay}', 'block', $mailbody);
    } else {
        $mailbody = mb_ereg_replace('{watchlogs}', $nologs, $mailbody);
        $mailbody = mb_ereg_replace('{cachesWatchedDisplay}', 'none', $mailbody);
    }

    $mailbody = mb_ereg_replace('{runwatch01}', tr('runwatch01'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch02}', tr('runwatch02'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch03}', tr('runwatch03'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch04}', tr('runwatch04'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch05}', tr('runwatch05'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch06}', tr('runwatch06'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch07}', tr('runwatch07'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch08}', tr('runwatch08'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch09}', tr('runwatch09'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch10}', tr('runwatch10'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch11}', tr('runwatch11'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch12}', tr('runwatch12'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch13}', tr('runwatch13'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch14}', tr('runwatch14'), $mailbody);
    $mailbody = mb_ereg_replace('{emailSign}', $octeamEmailsSignature, $mailbody);


    $mailadr = $currUserEMail;

    // $mailbody;
    $status = mb_send_mail($mailadr, $mailsubject, $mailbody, $email_headers);

    Log::logentry('watchlist', 2, $currUserID, 0, 0, 'Sending mail to ' . $mailadr, array('status' => $status));

    XDb::xSql("DELETE FROM watches_waiting WHERE user_id= ? AND watchtype IN (1, 2)", $currUserID);
}

function getLogtypeParams($logType)
{
    global $COGname;

    switch ($logType) {
        case '1':
            $logtypeParams['logtype'] = tr('logType1');
            $logtypeParams['logtypeColor'] = 'green';
            break;
        case '2':
            $logtypeParams['logtype'] = tr('logType2');
            $logtypeParams['logtypeColor'] = 'red';
            break;
        case '3':
            $logtypeParams['logtype'] = tr('logType3');
            $logtypeParams['logtypeColor'] = 'black';
            break;
        case '4':
            $logtypeParams['logtype'] = tr('logType4');
            ;
            $logtypeParams['logtypeColor'] = 'green';
            break;
        case '5':
            $logtypeParams['logtype'] = tr('logType5');
            ;
            $logtypeParams['logtypeColor'] = 'orange';
            break;
        case '6':
            $logtypeParams['logtype'] = tr('logType6');
            ;
            $logtypeParams['logtypeColor'] = 'red';
            break;
        case '7':
            $logtypeParams['logtype'] = tr('logType7');
            ;
            $logtypeParams['logtypeColor'] = 'green';
            break;
        case '8':
            $logtypeParams['logtype'] = tr('logType8');
            ;
            $logtypeParams['logtypeColor'] = 'green';
            break;
        case '9':
            $logtypeParams['logtype'] = tr('logType9');
            ;
            $logtypeParams['logtypeColor'] = 'red';
            break;
        case '10':
            $logtypeParams['logtype'] = tr('logType10');
            ;
            $logtypeParams['logtypeColor'] = 'green';
            break;
        case '11':
            $logtypeParams['logtype'] = tr('logType11');
            ;
            $logtypeParams['logtypeColor'] = 'red';
            break;
        case '12':
            $logtypeParams['logtype'] = tr('logType12');
            ;
            $logtypeParams['username'] = $COGname;
            $logtypeParams['logtypeColor'] = 'black';
            break;
        default:
            $logtypeParams['logtype'] = "";
            $logtypeParams['logtypeColor'] = 'black';
    }
    return $logtypeParams;
}


