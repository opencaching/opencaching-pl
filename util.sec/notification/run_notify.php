<?php
/**
 *
 * This script sends emails notification about new caches
 * It should be called from CRON quite often (to not delay messages)
 *
 */

use Utils\Database\OcDb;
use Utils\Log\Log;
use Utils\Gis\Gis;
use lib\Objects\GeoCache\GeoCacheCommons;

$rootpath = __dir__ . '/../../';
require_once($rootpath . 'lib/common.inc.php');

/* take datetime format from settings.inc.php */
$sDateformat = $datetimeFormat;

define('NOTIFY_NEW_CACHES', 1); //TODO: unify with value from eventhandler.inc.php

// Check if another instance of the script is running
$lock_file = fopen("/tmp/notification-run_notify.lock", "w");
if (!flock($lock_file, LOCK_EX | LOCK_NB)) { // Another instance of the script is running - exit
    echo "Another instance of run_notify.php is currently running.\nExiting.\n";
    fclose($lock_file);
    exit;
}

// No other instance - do normal processing

$db = OcDb::instance();

/* init caches container */
$cacheCntainer = cache::instance();
$cacheTypes = $cacheCntainer->getCacheTypes();
$cacheTypeIcons = $cacheCntainer->getCacheTypeIcons();

$id = 0;
do {
    $s = $db->multiVariableQuery(
        "SELECT `notify_waiting`.`id`, `notify_waiting`.`cache_id`, `notify_waiting`.`type`,
                `user`.`username`, user.user_id as cache_owner_id,
                user.hidden_count as hidden, user.notfounds_count as dnf, user.founds_count as found,
                `user2`.`email`, `user2`.`username` as `recpname`, `user2`.`latitude` as `lat1`, `user2`.`longitude` as `lon1`, `user2`.`user_id` as `recid`,
                `caches`.`name` as `cachename`, `caches`.`date_hidden`, `caches`.`latitude` as `lat2`, `caches`.`longitude` as `lon2`, `caches`.`wp_oc`,
                `caches`.`type` as `cachetype`,
                `caches`.`size` as `cachesize`
        FROM `notify_waiting`, `caches`, `user`, `user` `user2`
        WHERE `notify_waiting`.`cache_id`=`caches`.`cache_id`
            AND `notify_waiting`.`user_id`=`user2`.`user_id`
            AND `caches`.`user_id`=`user`.`user_id`
            AND `notify_waiting`.`id` > :1
        ORDER BY `notify_waiting`.`id`
        LIMIT 0,100", $id);

    $rsNotify = $db->dbResultFetchAll($s);

    foreach ($rsNotify as $rNotify) {
        $id = $rNotify['id'];
        /* send out everything that has to be sent */
        if (process_new_cache($rNotify) == 0) {
            $db->multiVariableQuery("DELETE FROM `notify_waiting` WHERE `id` =:1", $rNotify['id']);
        }
    }
    if (count($rsNotify) > 0) {
        sleep(5);
    } else {
        break;
    }
} while (true);

// Release lock
fclose($lock_file);

function process_new_cache($notify)
{
    global $emailaddr, $octeamEmailsSignature, $absolute_server_URI, $site_name;
    global $dateFormat, $cacheTypes, $cacheTypeIcons;


    switch ($notify['type']) {
        case NOTIFY_NEW_CACHES: // Type: new cache
            $mailbody = file_get_contents(__DIR__ . '/notifyNewcacheEmail.html');
            break;
        default:
            echo "Unknown Notification Typ: " . $notify['type'] . "<br />";
            return 0;
    }

    $thunderSection = ' (<img src="' . $absolute_server_URI . 'tpl/stdstyle/images/blue/thunder_ico.png" alt="user activity" width="9" height="9" border="0" title="' . tr('viewlog_aktywnosc') . ' [' . $notify['found'] . '+' . $notify['dnf'] . '+' . $notify['hidden'] . ']"/>' . ($notify['hidden'] + $notify['found'] + $notify['dnf']) . ') ';
    $mailbody = mb_ereg_replace('{username}', htmlspecialchars($notify['recpname'], ENT_COMPAT, 'UTF-8'), $mailbody);
    $mailbody = mb_ereg_replace('{date}', date($dateFormat, strtotime($notify['date_hidden'])), $mailbody);
    $mailbody = mb_ereg_replace('{cacheid}', $notify['cache_id'], $mailbody);
    $mailbody = mb_ereg_replace('{wp_oc}', $notify['wp_oc'], $mailbody);
    $mailbody = mb_ereg_replace('{user}', htmlspecialchars($notify['username'], ENT_COMPAT, 'UTF-8'), $mailbody);
    $mailbody = mb_ereg_replace('{cachename}', htmlspecialchars($notify['cachename'], ENT_COMPAT, 'UTF-8'), $mailbody);
    $mailbody = mb_ereg_replace('{distance}', round(Gis::distance($notify['lat1'], $notify['lon1'], $notify['lat2'], $notify['lon2'], 1), 1), $mailbody);
    $mailbody = mb_ereg_replace('{unit}', 'km', $mailbody);
    $mailbody = mb_ereg_replace('{bearing}', Gis::bearing2Text(Gis::calcBearing($notify['lat1'], $notify['lon1'], $notify['lat2'], $notify['lon2'])), $mailbody);
    $mailbody = mb_ereg_replace('{cachetype}', tr($cacheTypes[$notify['cachetype']]['translation']), $mailbody);
    $mailbody = mb_ereg_replace('{cachesize}', tr(GeoCacheCommons::CacheSizeTranslationKey($notify['cachesize'])), $mailbody);
    $mailbody = mb_ereg_replace('{server}', $absolute_server_URI, $mailbody);
    $mailbody = mb_ereg_replace('{sitename}', $site_name, $mailbody);
    $mailbody = mb_ereg_replace('{notify_newCache_01}', tr('notify_newCache_01'), $mailbody);
    $mailbody = mb_ereg_replace('{notify_newCache_02}', tr('notify_newCache_02'), $mailbody);
    $mailbody = mb_ereg_replace('{notify_newCache_03}', tr('notify_newCache_03'), $mailbody);
    $mailbody = mb_ereg_replace('{notify_newCache_04}', tr('notify_newCache_04'), $mailbody);
    $mailbody = mb_ereg_replace('{notify_newCache_05}', tr('notify_newCache_05'), $mailbody);
    $mailbody = mb_ereg_replace('{notify_newCache_06}', tr('notify_newCache_06'), $mailbody);
    $mailbody = mb_ereg_replace('{notify_newCache_07}', tr('notify_newCache_07'), $mailbody);
    $mailbody = mb_ereg_replace('{notify_newCache_08}', tr('notify_newCache_08'), $mailbody);
    $mailbody = mb_ereg_replace('{notify_newCache_09}', tr('notify_newCache_09'), $mailbody);
    $mailbody = mb_ereg_replace('{octeamEmailsSignature}', $octeamEmailsSignature, $mailbody);
    $mailbody = mb_ereg_replace('{runwatch04}', tr('runwatch04'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch08}', tr('runwatch08'), $mailbody);
    $mailbody = mb_ereg_replace('{runwatch09}', tr('runwatch09'), $mailbody);
    $mailbody = mb_ereg_replace('{wp_oc}', $notify['wp_oc'], $mailbody);
    $mailbody = mb_ereg_replace('{cache_owner_id}', $notify['cache_owner_id'], $mailbody);
    $mailbody = mb_ereg_replace('{caheIcon}', $cacheTypeIcons[$notify['cachetype']]['iconSet'][1]['iconSmall'], $mailbody);
    $mailbody = mb_ereg_replace('{thunderSection}', $thunderSection, $mailbody);

    $subject = mb_ereg_replace('{cachename}', $notify['cachename'], tr('notify_newCache_13'));

    /* begin send out everything that has to be sent */
    $email_headers = "Content-Type: text/html; charset=utf-8\r\n";
    $email_headers .= 'From: "' . $emailaddr . '" <' . $emailaddr . '>';

    $mailadr = $notify['email'];

    $status = mb_send_mail($mailadr, $subject, $mailbody, $email_headers);
    if(!$status){
        error_log(__FILE__.':'.__LINE__.': Mail sending failure: to:'.$mailadr);
    }
    Log::logentry('notify_newcache', 5, $notify['recid'], $notify['cache_id'], 0, 'Sending mail to ' . $mailadr, array('status' => $status));

    return 0;
}
