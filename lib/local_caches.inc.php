<?php
require_once('lib/calculation.inc.php');

class localCachesInc
{
    public static function createLocalCaches( $dbcLocCache, $lon, $lat, $distance, $user_id )
    {
        $max_lat_diff = $distance / 111.12;
        $max_lon_diff = $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180)) * 6378 * 3.14159);
        
        
        $sqlstr = 'CREATE TEMPORARY TABLE local_caches ENGINE=MEMORY
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
            HAVING `distance` < ' . $distance;
        
        $dbcLocCache->simpleQuery( $sqlstr );
        
        
        $sqlstr = 'ALTER TABLE local_caches
            ADD PRIMARY KEY ( `cache_id` ),
            ADD INDEX(`cache_id`),
            ADD INDEX (`wp_oc`),
            ADD INDEX(`type`),
            ADD INDEX(`name`),
            ADD INDEX(`user_id`),
            ADD INDEX(`date_hidden`),
            ADD INDEX(`date_created`)';
        
        $dbcLocCache->simpleQuery( $sqlstr );
        
    }
}

?>