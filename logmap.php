<?php

use Utils\Database\OcDb;
use lib\Objects\GeoCache\GeoCacheLog;

global $dateFormat, $googlemap_key;
require_once ('./lib/common.inc.php');

//user logged in?
if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}

$tplname = 'logmap';

$db = OcDb::instance();
$s = $db->simpleQuery(
    "SELECT `cache_logs`.`id` FROM `cache_logs`, `caches`
    WHERE `cache_logs`.`cache_id`=`caches`.`cache_id`
        AND `cache_logs`.`deleted`=0 AND `caches`.`status` IN (1, 2, 3)
        AND `cache_logs`.`type` IN (1,2,3,4,5)
    ORDER BY  `cache_logs`.`date_created` DESC
    LIMIT 100");


$log_ids = $db->dbFetchAllAsObjects($s, function ($row){
    return $row['id'];
});

if(!empty($log_ids)){

    $log_ids = implode(',', $log_ids);

    $s = $db->simpleQuery(
        "SELECT cache_logs.id, cache_logs.cache_id AS cache_id,
                cache_logs.type AS log_type, cache_logs.date AS log_date,
                cache_logs.user_id AS luser_id, caches.name AS cache_name,
                caches.wp_oc AS wp, user.username AS username,
                `caches`.`latitude` `latitude`, `caches`.`longitude` `longitude`,
                caches.type AS cache_type, cache_type.icon_small AS cache_icon_small,
                log_types.icon_small AS icon_small
        FROM (cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id))
            INNER JOIN user ON (cache_logs.user_id = user.user_id)
            INNER JOIN log_types ON (cache_logs.type = log_types.id)
            INNER JOIN cache_type ON (caches.type = cache_type.id)
        WHERE cache_logs.deleted=0 AND cache_logs.id IN ( $log_ids )
            AND cache_logs.cache_id=caches.cache_id
            AND caches.status<> 4 AND caches.status<> 5 AND caches.status<> 6
        GROUP BY cache_logs.id
        ORDER BY cache_logs.date_created DESC");
    $point = "";
    while( $record = $db->dbResultFetch($s) ){
        $username = $record['username'];
        $y = $record['longitude'];
        $x = $record['latitude'];
        $log_date = htmlspecialchars(
            date($dateFormat, strtotime($record['log_date'])), ENT_COMPAT, 'UTF-8');

        $cache_name = GeoCacheLog::cleanLogTextForToolTip($record['cache_name']);
        $point .= "addMarker(" . $x . "," . $y . ",icon" . $record['log_type'] . ",'" .
                $record['cache_icon_small'] . "','" . $record['wp'] . "','" .
                addslashes($cache_name) . "','" . $record['id'] . "','" .
                $record['icon_small'] . "','" . $record['luser_id'] . "','" .
                addslashes($username) . "','" . $log_date . "');\n";
    }


}else{ // there is no log_ids...
    $point = "";
}
/* SET YOUR MAP CODE HERE */
tpl_set_var('cachemap_header', '<script src="https://maps.googleapis.com/maps/api/js?key=' .
    $googlemap_key . '&amp;language=' . $lang . '" type="text/javascript"></script>');

tpl_set_var('mapzoom', 6);
tpl_set_var('points', $point);
tpl_set_var('mapcenterLat', $main_page_map_center_lat);
tpl_set_var('mapcenterLon', $main_page_map_center_lon);


tpl_BuildTemplate();

