<?php
/**
 * This script publishes the cache if its activation_date was set
 */

use Utils\Database\XDb;

$rootpath = '../../';
require_once __DIR__ . '/../../lib/ClassPathDictionary.php';
require_once('settings.inc.php');
require_once($rootpath . 'lib/eventhandler.inc.php');

$rsPublish = XDb::xSql(
                "SELECT `cache_id`, `user_id`
                FROM `caches`
                WHERE `status` = 5
                  AND `date_activate` != 0
                  AND `date_activate` <= NOW()");

while ($rPublish = XDb::xFetchArray($rsPublish)) {
    $userid = $rPublish['user_id'];
    $cacheid = $rPublish['cache_id'];

    // update cache status to active
    XDb::xSql("UPDATE `caches` SET `status`=1, `date_activate`=NULL, `last_modified`=NOW() WHERE `cache_id`= ? ", $cacheid);

    // send events
    touchCache($cacheid);
    event_new_cache($userid);
    event_notify_new_cache($cacheid);
}
XDb::xFreeResults($rsPublish);

// update last_modified=NOW() for every object depending on that cacheid
function touchCache($cacheid)
{
    XDb::xSql(
        "UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`= ? ", $cacheid);
    XDb::xSql(
        "UPDATE `caches`, `cache_logs` SET `cache_logs`.`last_modified`=NOW()
        WHERE `caches`.`cache_id`=`cache_logs`.`cache_id`
            AND `caches`.`cache_id`= ? AND `cache_logs`.`deleted`= ? ", $cacheid, 0);
    XDb::xSql(
        "UPDATE `caches`, `cache_desc` SET `cache_desc`.`last_modified`=NOW()
        WHERE `caches`.`cache_id`=`cache_desc`.`cache_id` AND `caches`.`cache_id`= ?", $cacheid);
    XDb::xSql(
        "UPDATE `caches`, `pictures` SET `pictures`.`last_modified`=NOW()
        WHERE `caches`.`cache_id`=`pictures`.`object_id` AND `pictures`.`object_type`=2 AND `caches`.`cache_id`= ? ", $cacheid);
    XDb::xSql(
        "UPDATE `caches`, `cache_logs`, `pictures` SET `pictures`.`last_modified`=NOW()
        WHERE `caches`.`cache_id`=`cache_logs`.`cache_id` AND `cache_logs`.`id`=`pictures`.`object_id`
            AND `pictures`.`object_type`=1 AND `caches`.`cache_id`= ?
            AND `cache_logs`.`deleted`= ? ", $cacheid, 0);
    XDb::xSql(
        "UPDATE `caches`, `mp3` SET `mp3`.`last_modified`=NOW()
        WHERE `caches`.`cache_id`=`mp3`.`object_id` AND `mp3`.`object_type`=2 AND `caches`.`cache_id`= ? ", $cacheid);
    XDb::xSql(
        "UPDATE `caches`, `cache_logs`, `mp3` SET `mp3`.`last_modified`=NOW()
        WHERE `caches`.`cache_id`=`cache_logs`.`cache_id` AND `cache_logs`.`id`=`mp3`.`object_id`
            AND `mp3`.`object_type`=1 AND `caches`.`cache_id`= ?
            AND `cache_logs`.`deleted`= ? ", $cacheid, 0);
}