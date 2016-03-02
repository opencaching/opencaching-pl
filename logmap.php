<?php

global $dateFormat;
require_once ('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        $tplname = 'logmap';

        $db = new dataBase;
        $query = "SELECT `cache_logs`.`id` FROM `cache_logs`, `caches` WHERE `cache_logs`.`cache_id`=`caches`.`cache_id` AND `cache_logs`.`deleted`=0 AND `caches`.`status` IN (1, 2, 3) AND `cache_logs`.`type` IN (1,2,3,4,5) ORDER BY  `cache_logs`.`date_created` DESC LIMIT 100";
        $db->simpleQuery($query);
        $cacheLogsCount = $db->rowCount();

        $log_ids = '';
        if ($cacheLogsCount == 0)
            $log_ids = '0';

        for ($i = 0; $i < $cacheLogsCount; $i++) {
            $record = $db->dbResultFetch();
            if ($i > 0) {
                $log_ids .= ', ' . $record['id'];
            } else {
                $log_ids = $record['id'];
            }
        }

        $query = "SELECT cache_logs.id, cache_logs.cache_id AS cache_id,
                         cache_logs.type AS log_type,
                         cache_logs.date AS log_date,
                         cache_logs.user_id AS luser_id,
                         caches.name AS cache_name,
                         caches.wp_oc AS wp,
                         user.username AS username,
                         `caches`.`latitude` `latitude`,
                         `caches`.`longitude` `longitude`,
                         caches.type AS cache_type,
                         cache_type.icon_small AS cache_icon_small,
                         log_types.icon_small AS icon_small
                         FROM (cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id)) INNER JOIN user ON (cache_logs.user_id = user.user_id) INNER JOIN log_types ON (cache_logs.type = log_types.id) INNER JOIN cache_type ON (caches.type = cache_type.id)
                         WHERE cache_logs.deleted=0 AND cache_logs.id IN ( $log_ids )
                         AND cache_logs.cache_id=caches.cache_id
                         AND caches.status<> 4 AND caches.status<> 5 AND caches.status<> 6
                         GROUP BY cache_logs.id
                         ORDER BY cache_logs.date_created DESC";
        $db->simpleQuery($query);
        $point = "";
        for ($i = 0; $i < $db->rowCount(); $i++) {
            $record = $db->dbResultFetch();
            $username = $record['username'];
            $y = $record['longitude'];
            $x = $record['latitude'];
            $log_date = htmlspecialchars(date($dateFormat, strtotime($record['log_date'])), ENT_COMPAT, 'UTF-8');
            $cache_name = common::cleanupText($record['cache_name']);
            $point .= "addMarker(" . $x . "," . $y . ",icon" . $record['log_type'] . ",'" . $record['cache_icon_small'] . "','" . $record['wp'] . "','" . $cache_name . "','" . $record['id'] . "','" . $record['icon_small'] . "','" . $record['luser_id'] . "','" . $username . "','" . $log_date . "');\n";
        }

        /* SET YOUR MAP CODE HERE */
        tpl_set_var('cachemap_header', '<script src="//maps.googleapis.com/maps/api/js?sensor=false&amp;language=' . $lang . '" type="text/javascript"></script>');
        tpl_set_var('points', $point);
        tpl_set_var('mapzoom', 6);
        tpl_set_var('mapcenterLat', $main_page_map_center_lat);
        tpl_set_var('mapcenterLon', $main_page_map_center_lon);
    }
}
tpl_BuildTemplate();
?>
