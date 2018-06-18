<?php
/**
 * This script publishes the cache if its activation_date was set
 */

use Utils\Database\XDb;
use lib\Objects\GeoCache\GeoCache;
use Utils\EventHandler\EventHandler;

$rootpath = '../../';
require_once($rootpath . 'lib/ClassPathDictionary.php');
require_once($rootpath . 'lib/settingsGlue.inc.php');

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
    $cache = GeoCache::fromCacheIdFactory($cacheid);
    GeoCache::touchCache($cacheid);
    EventHandler::cacheNew($cache);
    unset($cache);
}
XDb::xFreeResults($rsPublish);