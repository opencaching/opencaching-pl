<?php
use Utils\Gis\Gis;

require_once('lib/calculation.inc.php');

class localCachesInc
{

    /**
     * Create the temp. table contains caches closer to point lat/lon than distance [in km].
     * Caches ignored by given userId are skipped from the list;
     *
     * @param unknown $dbcLocCache
     * @param unknown $lon
     * @param unknown $lat
     * @param unknown $distance
     * @param unknown $user_id
     */
    public static function createLocalCaches( $dbcLocCache, $lon, $lat, $distance, $user_id )
    {
        $max_lat_diff = Gis::distanceInDegreesLat($distance);
        $max_lon_diff = Gis::distanceInDegreesLon($distance, $lat);

        $dbcLocCache->simpleQuery(
            'CREATE TEMPORARY TABLE local_caches ENGINE=MEMORY
            SELECT
            (' . getSqlDistanceFormula($lon, $lat, $distance, 1) . ')     AS `distance`,
            `caches`.`cache_id`         AS `cache_id`,
            `caches`.`wp_oc`            AS `wp_oc`,
            `caches`.`type`             AS `type`,
            `caches`.`name`             AS `name`,
            `caches`.`longitude`        AS `longitude`,
            `caches`.`latitude`         AS `latitude`,
            `caches`.`date_hidden`      AS `date_hidden`,
            `caches`.`date_created`     AS `date_created`,
            `caches`.`country`          AS `country`,
            `caches`.`difficulty`       AS `difficulty`,
            `caches`.`terrain`          AS `terrain`,
            `caches`.`founds`           AS `founds`,
            `caches`.`status`           AS `status`,
            `caches`.`user_id`          AS `user_id`
            FROM `caches`
            WHERE `caches`.`cache_id` NOT IN (SELECT `cache_ignore`.`cache_id` FROM `cache_ignore` WHERE `cache_ignore`.`user_id`=\'' . $user_id . '\')
                AND caches.status<>4 AND caches.status<>5
                AND caches.status <>6
                AND `longitude` > ' . ($lon - $max_lon_diff) . '
                AND `longitude` < ' . ($lon + $max_lon_diff) . '
                AND `latitude` > ' . ($lat - $max_lat_diff) . '
                AND `latitude` < ' . ($lat + $max_lat_diff) . '
            HAVING `distance` < ' . $distance);

        $dbcLocCache->simpleQuery(
            'ALTER TABLE local_caches
            ADD PRIMARY KEY ( `cache_id` ),
            ADD INDEX(`cache_id`),
            ADD INDEX (`wp_oc`),
            ADD INDEX(`type`),
            ADD INDEX(`name`),
            ADD INDEX(`user_id`),
            ADD INDEX(`date_hidden`),
            ADD INDEX(`date_created`)' );

    }
}
