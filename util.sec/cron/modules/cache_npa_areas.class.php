<?php

/**************************************************************************
 *  You can find the license in the docs directory
 *
 *  Cleanup the table sys_temptables from entries of dead threads
 *
 *                         run it once a day
 *
 ***************************************************************************/

$rootpath = '../../../';
//  require_once($rootpath.'lib2/logic/gis.class.php');
require_once($rootpath . 'lib/gis/gis.class.php');
require_once($rootpath . 'lib/clicompatbase.inc.php');
require_once($rootpath . 'lib/common.inc.php');

class cache_npa_areas {

    function run() {
        db_connect();
        $rsCache = sql("SELECT `cache_id`, `latitude`, `longitude` FROM `caches` WHERE `need_npa_recalc`=1");
        while ($rCache = mysql_fetch_assoc($rsCache)) {
            $sql = sql("DELETE FROM `cache_npa_areas` WHERE `cache_id`='&1' AND `calculated`=1", $rCache['cache_id']);
            mysql_query($sql);
            // Natura 2000
            $rsLayers = sql("SELECT `id`, AsText(`shape`) AS `geometry` FROM `npa_areas` WHERE WITHIN(GeomFromText('&1'), `shape`)", 'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')');
            while ($rLayers = mysql_fetch_assoc($rsLayers)) {
                if (gis::ptInLineRing($rLayers['geometry'], 'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')')) {
                    $sql = sql("INSERT INTO `cache_npa_areas` (`cache_id`, `npa_id`, `calculated`) VALUES ('&1', '&2', 1) ON DUPLICATE KEY UPDATE `calculated`=1", $rCache['cache_id'], $rLayers['id']);
                    mysql_query($sql);
                }
            }
            mysql_free_result($rsLayers);

            // Parki PL
            $rsLayers = sql("SELECT `id`, AsText(`shape`) AS `geometry` FROM `parkipl` WHERE WITHIN(GeomFromText('&1'), `shape`)", 'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')');

            while ($rLayers = mysql_fetch_assoc($rsLayers)) {
                if (gis::ptInLineRing($rLayers['geometry'], 'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')')) {
                    $sql = sql("INSERT INTO `cache_npa_areas` (`cache_id`, `parki_id`, `calculated`) VALUES ('&1', '&2', 1) ON DUPLICATE KEY UPDATE `calculated`=1", $rCache['cache_id'], $rLayers['id']);
                    mysql_query($sql);
                }
            }
            mysql_free_result($rsLayers);
            // End of Parki PL

            $sql = sql("UPDATE `caches` SET `need_npa_recalc`=0 WHERE `cache_id`='&1'", $rCache['cache_id']);
            mysql_query($sql);
        }
        mysql_free_result($rsCache);
        db_disconnect();
    }
}

$cache_npa = new cache_npa_areas();
$cache_npa->run();
