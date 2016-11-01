<?php
/**
 *
 * This script updates lists of caches at
 *  - natura2000 arras
 *  - PL parks
 *
 * It should be called daily from CRON
 */
use Utils\Database\XDb;
$rootpath = '../../../';
require_once($rootpath . 'lib/common.inc.php');


$rsCache = XDb::xSql(
    "SELECT `cache_id`, `latitude`, `longitude` FROM `caches` WHERE `need_npa_recalc`=1");

while ($rCache = XDb::xFetchArray($rsCache)) {

    if( !is_numeric($rCache['longitude']) || !is_numeric($rCache['latitude']) ){
        continue;
    }

    XDb::xSql(
        "DELETE FROM `cache_npa_areas`
        WHERE `cache_id`= ? AND `calculated`=1
        LIMIT 1", $rCache['cache_id']);



    // Natura 2000
    $rsLayers = XDb::xSql(
        "SELECT `id`, AsText(`shape`) AS `geometry`
        FROM `npa_areas`
        WHERE WITHIN(
            GEOMFROMTEXT(
                POINT(" . $rCache['longitude'] . ', ' . $rCache['latitude'] . ")
            ), `shape`
        )" );

    while ($rLayers = XDb::xFetchArray($rsLayers)) {

        if ( gis::ptInLineRing(
                $rLayers['geometry'],
                'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')')
            ) {
            XDb::xSql(
                "INSERT INTO `cache_npa_areas` (`cache_id`, `npa_id`, `calculated`)
                VALUES ( ?, ?, 1)
                ON DUPLICATE KEY UPDATE `calculated`=1",
                $rCache['cache_id'], $rLayers['id']);

        }
    }
    XDb::xFreeResults($rsLayers);

    // Parki PL
    $rsLayers = XDb::xSql(
        "SELECT `id`, AsText(`shape`) AS `geometry` FROM `parkipl`
        WHERE WITHIN(
            GEOMFROMTEXT(
                POINT(" . $rCache['longitude'] . ', ' . $rCache['latitude'] . ")
            ), `shape`
        )" );

    while ($rLayers = XDb::xFetchArray($rsLayers)) {
        if ( gis::ptInLineRing(
                $rLayers['geometry'],
                'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')')
            ) {
            XDb::xSql(
                "INSERT INTO `cache_npa_areas` (`cache_id`, `parki_id`, `calculated`)
                VALUES ( ?, ?, 1)
                ON DUPLICATE KEY UPDATE `calculated`=1",
                $rCache['cache_id'], $rLayers['id']);
        }
    }
    XDb::xFreeResults($rsLayers);
    // End of Parki PL

    XDb::xSql("UPDATE `caches` SET `need_npa_recalc`=0 WHERE `cache_id`= ? LIMIT 1", $rCache['cache_id']);
}
XDb::xFreeResults($rsCache);

