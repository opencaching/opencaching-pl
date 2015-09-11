<?php

global $lang, $rootpath, $usr, $dateFormat;

if (!isset($rootpath))
    $rootpath = '';

//include template handling
require_once($rootpath . 'lib/common.inc.php');
require_once($rootpath . 'lib/calculation.inc.php');
require_once($rootpath . 'lib/cache_icon.inc.php');
require_once($stylepath . '/lib/icons.inc.php');

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {

        //get user record
        $user_id = $usr['userid'];
        tpl_set_var('userid', $user_id);

        if (isset($_REQUEST['logs'])) {
            $logs = $_REQUEST['logs'];
        } else {
            $logs = 1;
        }

        //get the news
        $tplname = 'mojaokolica';

        function cleanup_text($str)
        {
            $from[] = '<p>&nbsp;</p>';
            $to[] = '';
            $str = strip_tags($str, "<li>");
            $from[] = '&nbsp;';
            $to[] = ' ';
            $from[] = '<p>';
            $to[] = '';
            $from[] = '\n';
            $to[] = '';
            $from[] = '\r';
            $to[] = '';
            $from[] = '</p>';
            $to[] = "";
            $from[] = '<br>';
            $to[] = "";
            $from[] = '<br />';
            $to[] = "";
            $from[] = '<br/>';
            $to[] = "";

            $from[] = '<li>';
            $to[] = " - ";
            $from[] = '</li>';
            $to[] = "";

            $from[] = '&oacute;';
            $to[] = 'o';
            $from[] = '&quot;';
            $to[] = '"';
            $from[] = '&[^;]*;';
            $to[] = '';

            $from[] = '&';
            $to[] = '';
            $from[] = '\'';
            $to[] = '';
            $from[] = '"';
            $to[] = '';
            $from[] = '<';
            $to[] = '';
            $from[] = '>';
            $to[] = '';
            $from[] = ']]>';
            $to[] = ']] >';
            $from[] = '';
            $to[] = '';

            for ($i = 0; $i < count($from); $i++)
                $str = str_replace($from[$i], $to[$i], $str);

            return filterevilchars($str);
        }

        function filterevilchars($str)
        {
            return str_replace('[\\x00-\\x09|\\x0A-\\x0E-\\x1F]', '', $str);
        }

        function get_zoom($latitude, $lonMin, $lonMax, $latMin, $latMax)
        {
            /* In the following code, px and py are the width of the map in the
              webpage, latCenter represents the latitude of the center, and
              latMax etc are the obvious parameters.  Then one reasonable choice
              of the zoom (in javascript notation) is
             */
            $s = 1.35;
            $px = 350;
            $py = 350;
            $latcCnter = $latitude;
            $xZoom = -(log(($lonMax - $lonMin) / ($px * $s)) / log(2));
            $yZoom = -(log((($latMax - $latMin) * (1 / cos(($latcCnter * PI / 180)))) / ($py * $s)) / log(2));
            $zoom = min(floor($xZoom), floor($yZoom));
            return $zoom;
        }

        function cacheToLocation($cache_id)
        {
            global $lang;
            $res = sql("SELECT cache_loc.country, adm1, adm2 FROM cache_loc INNER
                        JOIN caches ON (cache_loc.cache_id = caches.cache_id)
                        WHERE cache_loc.cache_id = &1
                        AND caches.latitude = cache_loc.latitude AND caches.longitude = cache_loc.longitude
                        AND lang = '&2'", $cache_id, $lang);

            $rec = sql_fetch_array($res);
            if (!$rec) {
                $res = sql("SELECT latitude, longitude FROM caches WHERE caches.cache_id = &1", $cache_id);
                $rec = sql_fetch_array($res);
                if (!$rec)
                    return;
                $loc = coordToLocationOk($rec['latitude'], $rec['longitude']);
                sql("INSERT INTO cache_loc VALUES(&1, &2, &3, '&4', '&5', '&6', '&7') ON DUPLICATE KEY UPDATE latitude = &2, longitude = &3", $cache_id, $rec['latitude'], $rec['longitude'], $lang, $loc[0], $loc[1], $loc[2]);

                return $loc;
            } else
                return array($rec['country'], $rec['adm1'], $rec['adm2']);
        }

        function get_marker_positions($latitude, $longitude, $radius, $user_id)
        {
            $markerpos = array();
            $markers = array();
            $rs = sql("
        SELECT SQL_BUFFER_RESULT `caches`.`cache_id`, `caches`.`longitude`, `caches`.`latitude`, `caches`.`type`
        FROM local_caches `caches`
        WHERE
            `caches`.`type` != 6 AND
            `caches`.`status` = 1 AND
            `caches`.`date_hidden` <= NOW() AND
            `caches`.`date_created` <= NOW()
        ORDER BY IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) DESC, `caches`.`cache_id` DESC
        LIMIT 0, 10", $latitude, $longitude, $radius);

            for ($i = 0; $i < mysql_num_rows($rs); $i++) {
                $record = sql_fetch_array($rs);
                $lat = $record['latitude'];
                $lon = $record['longitude'];
                $type = $record['type'];
                $markers[] = array('lat' => $lat, 'lon' => $lon, 'type' => $type);
            }

            $markerpos['plain_cache_num'] = count($markers);

            $rs = sql("
        SELECT SQL_BUFFER_RESULT `caches`.`cache_id`, `caches`.`longitude`, `caches`.`latitude`, `caches`.`type`
        FROM    local_caches `caches`
        WHERE
        `caches`.`date_hidden` >= curdate() AND
            `caches`.`type` = 6 AND
            `caches`.`status` = 1
        ORDER BY `caches`.`date_hidden` ASC
        LIMIT 0, 10", $latitude, $longitude, $radius);

            for ($i = 0; $i < mysql_num_rows($rs); $i++) {
                $record = sql_fetch_array($rs);
                $lat = $record['latitude'];
                $lon = $record['longitude'];
                $type = $record['type'];
                $markers[] = array('lat' => $lat, 'lon' => $lon, 'type' => $type);
            }

            $markerpos['markers'] = $markers;

            return $markerpos;
        }

        function create_map_url($markerpos, $index, $latitude, $longitude)
        {
            global $googlemap_key;

            $markers = $markerpos['markers'];
            if (empty($markerpos['markers'])) {
                $dzoom = "&zoom=8";
            } else {
                $dzoom = "";
            }
            $markers_str = "markers=color:blue|size:small|";
            $markers_ev_str = "&markers=color:orange|size:small|";
            $sel_marker_str = "";
            foreach ($markers as $i => $marker) {
                $lat = sprintf("%.3f", $marker['lat']);
                $lon = sprintf("%.3f", $marker['lon']);
                $type = strtoupper(typeToLetter($marker['type']));
                if (strcmp($type, 'E') == 0)
                    if ($i != $index)
                        $markers_ev_str .= "$lat,$lon|";
                    else
                        $sel_marker_str = "&markers=color:orange|label:$type|$lat,$lon|";
                else
                if ($i != $index)
                    $markers_str .= "$lat,$lon|";
                else
                    $sel_marker_str = "&markers=color:blue|label:$type|$lat,$lon|";
            }

            $google_map = "http://maps.google.com/maps/api/staticmap?center=" . $latitude . "," . $longitude . $dzoom . "&size=350x350&maptype=roadmap&key=" . $googlemap_key . "&sensor=false&" . $markers_str . $markers_ev_str . $sel_marker_str;

            return $google_map;
        }

        tpl_set_var('more_caches', '');
        tpl_set_var('more_ftf', '');
        tpl_set_var('more_topcache', '');
        tpl_set_var('more_logs', '');

        $latitude = sqlValue("SELECT `latitude` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);
        $longitude = sqlValue("SELECT `longitude` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);

        if (($longitude == NULL && $latitude == NULL) || ($longitude == 0 && $latitude == 0)) {
            tpl_set_var('info', '<br><div class="notice" style="line-height: 1.4em;font-size: 120%;"><b>' . tr("myn_info") . '</b></div><br>');
        } else {
            tpl_set_var('info', '');
        }

        if ($latitude == NULL || $latitude == 0)
            $latitude = 52.24522;
        if ($longitude == NULL || $longitude == 0)
            $longitude = 21.00442;

        $distance = sqlValue("SELECT `notify_radius` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);
        if ($distance == 0)
            $distance = 50;
        $distance_unit = 'km';
        $radius = $distance;
        tpl_set_var('distance', $distance);

        //get the users home coords
//          $rs_coords = sql("SELECT `latitude` `lat`, `longitude` `lon` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
//          $record_coords = sql_fetch_array($rs_coords);

        $lat = $latitude;
        $lon = $longitude;
        $lon_rad = $lon * 3.14159 / 180;
        $lat_rad = $lat * 3.14159 / 180;


        //all target caches are between lat - max_lat_diff and lat + max_lat_diff
        $max_lat_diff = $distance / 111.12;

        //all target caches are between lon - max_lon_diff and lon + max_lon_diff
        //TODO: check!!!
        $max_lon_diff = $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180)) * 6378 * 3.14159);
        sql('DROP TEMPORARY TABLE IF EXISTS local_caches');
        sql('CREATE TEMPORARY TABLE local_caches ENGINE=MEMORY
                                        SELECT
                                            (' . getSqlDistanceFormula($lon, $lat, $distance, $multiplier[$distance_unit]) . ') AS `distance`,
                                            `caches`.`cache_id` AS `cache_id`,
                                            `caches`.`wp_oc` AS `wp_oc`,
                                            `caches`.`type` AS `type`,
                                            `caches`.`name` AS `name`,
                                            `caches`.`longitude` `longitude`,
                                            `caches`.`latitude` `latitude`,
                                            `caches`.`date_hidden` `date_hidden`,
                                            `caches`.`date_created` `date_created`,
                                            `caches`.`country` `country`,
                                            `caches`.`difficulty` `difficulty`,
                                            `caches`.`terrain` `terrain`,
                                            `caches`.`founds` `founds`,
                                            `caches`.`status` `status`,
                                            `caches`.`user_id` `user_id`
                                        FROM `caches`
                                        WHERE `caches`.`cache_id` NOT IN (SELECT `cache_ignore`.`cache_id` FROM `cache_ignore` WHERE `cache_ignore`.`user_id`=\'' . $user_id . '\') AND caches.status<>4 AND caches.status<>5 AND caches.status <>6
                                            AND `longitude` > ' . ($lon - $max_lon_diff) . '
                                            AND `longitude` < ' . ($lon + $max_lon_diff) . '
                                            AND `latitude` > ' . ($lat - $max_lat_diff) . '
                                            AND `latitude` < ' . ($lat + $max_lat_diff) . '
                                        HAVING `distance` < ' . $distance);
        sql('ALTER TABLE local_caches ADD PRIMARY KEY ( `cache_id` ),
                ADD INDEX(`cache_id`), ADD INDEX (`wp_oc`), ADD INDEX(`type`), ADD INDEX(`name`), ADD INDEX(`user_id`), ADD INDEX(`date_hidden`), ADD INDEX(`date_created`)');




        // Read coordinates of the newest caches
        $markerpositions = get_marker_positions($latitude, $longitude, $radius, $user_id);
        // Generate include file for map with new caches
        tpl_set_var('local_cache_map', '<img src="' . create_map_url($markerpositions, -1, $latitude, $longitude) . '" basesrc="' . create_map_url($markerpositions, -1, $latitude, $longitude) . '" id="main-cachemap" name="main-cachemap" alt="mapa" />');

//  $file_content = '<img src="' . create_map_url($markerpositions, -1,$latitude,$longitude) . '" basesrc="' . create_map_url($markerpositions, -1,$latitude,$longitude) . '" id="main-cachemap" name="main-cachemap" alt="mapa" />';
//  $n_file = fopen($dynstylepath . "local_cachemap.inc.php", 'w');
//  fwrite($n_file, $file_content);
//  fclose($n_file);
        //start_newcaches.include
        $rs = sql("SELECT `user`.`user_id` `user_id`,
                `user`.`username` `username`,
                `caches`.`cache_id` `cache_id`,
                `caches`.`name` `name`,
                `caches`.`longitude` `longitude`,
                `caches`.`latitude` `latitude`,
                `caches`.`date_hidden` `date_hidden`,
                `caches`.`date_created` `date_created`,
                IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) AS `date`,
                `caches`.`country` `country`,
                `caches`.`difficulty` `difficulty`,
                `caches`.`terrain` `terrain`,
                `cache_type`.`icon_large` `icon_large`
        FROM local_caches `caches` INNER JOIN `user` ON (`caches`.`user_id`=`user`.`user_id`), `cache_type`
        WHERE         `caches`.`type`!=6
              AND `caches`.`status`=1
              AND `caches`.`type`=`cache_type`.`id`
                AND `caches`.`date_created` <= NOW()
                AND `caches`.`date_hidden` <= NOW()
            ORDER BY `date` DESC, `caches`.`cache_id` DESC
            LIMIT 0 , 10");

        $rsc = sql("SELECT `user`.`user_id` `user_id`,
                `user`.`username` `username`,
                `caches`.`cache_id` `cache_id`,
                `caches`.`name` `name`,
                `caches`.`longitude` `longitude`,
                `caches`.`latitude` `latitude`,
                `caches`.`date_hidden` `date_hidden`,
                `caches`.`date_created` `date_created`,
                IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) AS `date`,
                `caches`.`country` `country`,
                `caches`.`difficulty` `difficulty`,
                `caches`.`terrain` `terrain`,
                `cache_type`.`icon_large` `icon_large`
            FROM local_caches `caches` INNER JOIN `user` ON (`caches`.`user_id`=`user`.`user_id`), `cache_type`
            WHERE `caches`.`type`!=6
                AND `caches`.`status`=1
                AND `caches`.`type`=`cache_type`.`id`
                AND `caches`.`date_created` <= NOW()
                AND `caches`.`date_hidden` <= NOW()
            ORDER BY `date` DESC, `caches`.`cache_id` DESC");
        if (mysql_num_rows($rsc) > 10) {
            tpl_set_var('more_caches', '<a class="links" href="myn_newcaches.php">[' . tr("show_more") . '...]</a>');
        }
        mysql_free_result($rsc);

        if (mysql_num_rows($rs) == 0) {
            $file_content = "<p>&nbsp;&nbsp;&nbsp;&nbsp;<b>" . tr('list_of_caches_is_empty') . "</b></p><br>";
        } else {

            $cacheline = '<li class="newcache_list_multi" style="margin-bottom:8px;">' .
                    '<img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" />&nbsp;{date}&nbsp;' .
                    '<a id="newcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}" onmouseover="Lite({nn})" onmouseout="Unlite()" maphref="{smallmapurl}">{cachename}</a>&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" title="user" />&nbsp;&nbsp;<a class="links" href="viewprofile.php?userid={userid}">{username}</a></b>';

            $file_content = '<ul style="font-size: 11px;">';
            for ($i = 0; $i < mysql_num_rows($rs); $i++) {
                $record = sql_fetch_array($rs);

                //        $loc = cacheToLocation($record['cache_id']);

                $cacheicon = 'tpl/stdstyle/images/' . getSmallCacheIcon($record['icon_large']);

                $thisline = $cacheline;
                $thisline = mb_ereg_replace('{nn}', $i, $thisline);
//      $thisline = mb_ereg_replace('{location}',join(" > ", array_slice($loc, 0, 2)), $thisline);
                $thisline = mb_ereg_replace('{date}', htmlspecialchars(date($dateFormat, strtotime($record['date'])), ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
                $thisline = mb_ereg_replace('{cache_count}', $i, $thisline);
                $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
                $thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);
                $thisline = mb_ereg_replace('{smallmapurl}', create_map_url($markerpositions, $i, $latitude, $longitude), $thisline);

                $file_content .= $thisline . "\n";
            }
        }
        $file_content .= '</ul>';

        tpl_set_var('new_caches', $file_content);
        mysql_free_result($rs);

        //start_ftfcaches.include
        $rs = sql("SELECT `user`.`user_id` `user_id`,
                `user`.`username` `username`,
                `caches`.`cache_id` `cache_id`,
                `caches`.`name` `name`,
                `caches`.`longitude` `longitude`,
                `caches`.`latitude` `latitude`,
                `caches`.`date_hidden` `date`,
                `caches`.`date_created` `date_created`,
                `caches`.`country` `country`,
                `caches`.`difficulty` `difficulty`,
                `caches`.`terrain` `terrain`,
                `cache_type`.`icon_large` `icon_large`
        FROM local_caches `caches` INNER JOIN `user` ON (`caches`.`user_id`=`user`.`user_id`), `cache_type`
        WHERE `caches`.`type`!=6
              AND `caches`.`status`=1
              AND `caches`.`type`=`cache_type`.`id`
              AND `caches`.`founds`=0
            ORDER BY `date` DESC, `caches`.`cache_id` DESC
            LIMIT 0 , 10");
        $rsftf = sql("SELECT `user`.`user_id` `user_id`,
                `user`.`username` `username`,
                `caches`.`cache_id` `cache_id`,
                `caches`.`name` `name`,
                `caches`.`longitude` `longitude`,
                `caches`.`latitude` `latitude`,
                `caches`.`date_hidden` `date`,
                `caches`.`date_created` `date_created`,
                `caches`.`country` `country`,
                `caches`.`difficulty` `difficulty`,
                `caches`.`terrain` `terrain`,
                `cache_type`.`icon_large` `icon_large`
        FROM local_caches `caches` INNER JOIN `user` ON (`caches`.`user_id`=`user`.`user_id`), `cache_type`
        WHERE `caches`.`type`!=6
              AND `caches`.`status`=1
              AND `caches`.`type`=`cache_type`.`id`
              AND `caches`.`founds`=0
            ORDER BY `date` DESC, `caches`.`cache_id` DESC");
        if (mysql_num_rows($rsftf) > 10) {
            tpl_set_var('more_ftf', '<a class="links" href="myn_ftf.php">[' . tr("show_more") . '...]</a>');
        }
        mysql_free_result($rsftf);


        if (mysql_num_rows($rs) == 0) {
            $file_content = "<p>&nbsp;&nbsp;&nbsp;&nbsp;<b>" . tr('list_of_caches_is_empty') . "</b></p><br>";
        } else {

            $cacheline = '<li class="newcache_list_multi" style="margin-bottom:8px;">' .
                    '<img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" />&nbsp;{date}&nbsp;' .
                    '<a id="newcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}" onmouseover="Lite({nn})" onmouseout="Unlite()" maphref="{smallmapurl}">{cachename}</a>&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" title="user" />&nbsp;&nbsp;<a class="links" href="viewprofile.php?userid={userid}">{username}</a></b>';

            $file_content = '<ul style="font-size: 11px;">';
            for ($i = 0; $i < mysql_num_rows($rs); $i++) {
                $record = sql_fetch_array($rs);

//          $loc = cacheToLocation($record['cache_id']);

                $cacheicon = 'tpl/stdstyle/images/' . getSmallCacheIcon($record['icon_large']);

                $thisline = $cacheline;
//      $thisline = mb_ereg_replace('{nn}', $i, $thisline);
//      $thisline = mb_ereg_replace('{location}',join(" > ", array_slice($loc, 0, 2)), $thisline);
                $thisline = mb_ereg_replace('{date}', htmlspecialchars(date($dateFormat, strtotime($record['date'])), ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
                $thisline = mb_ereg_replace('{cache_count}', $i, $thisline);
                $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
                $thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);
//      $thisline = mb_ereg_replace('{smallmapurl}', create_map_url($markerpositions, $i,$latitude,$longitude), $thisline);

                $file_content .= $thisline . "\n";
            }
        }
        $file_content .= '</ul>';

        tpl_set_var('ftf_caches', $file_content);
        mysql_free_result($rs);

        //start_topcaches.include
        $rstr = sql("SELECT `user`.`user_id` `user_id`,
                `user`.`username` `username`,
                `caches`.`cache_id` `cache_id`,
                `caches`.`name` `name`,
                `caches`.`longitude` `longitude`,
                `caches`.`latitude` `latitude`,
                `caches`.`date_hidden` `date`,
                `caches`.`date_created` `date_created`,
                `caches`.`country` `country`,
                `caches`.`difficulty` `difficulty`,
                `caches`.`terrain` `terrain`,
                `cache_type`.`icon_large` `icon_large`,
                count(`cache_rating`.`cache_id`) `toprate`
        FROM local_caches `caches` INNER JOIN `user` ON (`caches`.`user_id`=`user`.`user_id`) LEFT JOIN `cache_rating` ON (`caches`.`cache_id`=`cache_rating`.`cache_id`), `cache_type`
        WHERE `caches`.`type`!=6
            AND `cache_rating`.`cache_id`=`caches`.`cache_id`
              AND `caches`.`status`=1
              AND `caches`.`type`=`cache_type`.`id`
              GROUP BY `caches`.`cache_id`
            ORDER BY `toprate` DESC, `caches`.`name` ASC LIMIT 0 , 10");

        $rstr1 = sql("SELECT `user`.`user_id` `user_id`,
                `user`.`username` `username`,
                `caches`.`cache_id` `cache_id`,
                `caches`.`name` `name`,
                `caches`.`longitude` `longitude`,
                `caches`.`latitude` `latitude`,
                `caches`.`date_hidden` `date`,
                `caches`.`date_created` `date_created`,
                `caches`.`country` `country`,
                `caches`.`difficulty` `difficulty`,
                `caches`.`terrain` `terrain`,
                `cache_type`.`icon_large` `icon_large`,
                count(`cache_rating`.`cache_id`) `toprate`
        FROM local_caches `caches` INNER JOIN `user` ON (`caches`.`user_id`=`user`.`user_id`) LEFT JOIN `cache_rating` ON (`caches`.`cache_id`=`cache_rating`.`cache_id`), `cache_type`
        WHERE `caches`.`type`!=6
            AND `cache_rating`.`cache_id`=`caches`.`cache_id`
              AND `caches`.`status`=1
              AND `caches`.`type`=`cache_type`.`id`
              GROUP BY `caches`.`cache_id`
            ORDER BY `toprate` DESC, `caches`.`name` ASC");
        if (mysql_num_rows($rstr1) > 10) {
            tpl_set_var('more_topcaches', '<a class="links" href="myn_topcaches.php">[' . tr("show_more") . '...]</a>');
        }
        mysql_free_result($rstr1);

        if (mysql_num_rows($rstr) == 0) {
            $file_content = "<p>&nbsp;&nbsp;&nbsp;&nbsp;<b>" . tr('list_of_caches_is_empty') . "</b></p><br>";
        } else {

            $cacheline = '<li class="newcache_list_multi" style="margin-bottom:8px;">' .
                    '<img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" />&nbsp;{date}&nbsp;' .
                    '<a id="newcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}" onmouseover="Lite({nn})" onmouseout="Unlite()" maphref="{smallmapurl}">{cachename}</a>&nbsp;<span style="font-weight:bold;color: green;">[{toprate}]</span>&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" title="user" />&nbsp;&nbsp;<a class="links" href="viewprofile.php?userid={userid}">{username}</a></b>';

            $file_content = '<ul style="font-size: 11px;">';
            for ($i = 0; $i < mysql_num_rows($rstr); $i++) {
                $record = sql_fetch_array($rstr);

//          $loc = cacheToLocation($record['cache_id']);

                $cacheicon = 'tpl/stdstyle/images/' . getSmallCacheIcon($record['icon_large']);

                $thisline = $cacheline;
//      $thisline = mb_ereg_replace('{nn}', $i, $thisline);
//      $thisline = mb_ereg_replace('{location}',join(" > ", array_slice($loc, 0, 2)), $thisline);
                $thisline = mb_ereg_replace('{date}', htmlspecialchars(date($dateFormat, strtotime($record['date'])), ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
//      $thisline = mb_ereg_replace('{cache_count}',$i, $thisline);
                $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
                $thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);
                $thisline = mb_ereg_replace('{toprate}', $record['toprate'], $thisline);
//      $thisline = mb_ereg_replace('{smallmapurl}', create_map_url($markerpositions, $i,$latitude,$longitude), $thisline);

                $file_content .= $thisline . "\n";
            }
        }
        $file_content .= '</ul>';

        tpl_set_var('top_caches', $file_content);
        mysql_free_result($rstr);

        //nextevents.include

        $rss = sql("SELECT `user`.`user_id` `user_id`,
                `user`.`username` `username`,
                `caches`.`cache_id` `cache_id`,
                `caches`.`name` `name`,
                `caches`.`longitude` `longitude`,
                `caches`.`latitude` `latitude`,
                `caches`.`date_hidden` `date_hidden`,
                `caches`.`date_created` `date_created`,
                `caches`.`country` `country`,
                `caches`.`difficulty` `difficulty`,
                `caches`.`terrain` `terrain`,
                `cache_type`.`icon_large` `icon_large`
        FROM `caches`, `user`, `cache_type`,local_caches
        WHERE `caches`.`cache_id`=local_caches.`cache_id` AND
        `caches`.`user_id`=`user`.`user_id`
              AND `caches`.`type`=6
              AND `caches`.`status`=1
              AND `caches`.`type`=`cache_type`.`id`
                AND `caches`.`date_hidden` >= curdate()
            ORDER BY `date_hidden` ASC
            LIMIT 0 , 10");



        $file_content = '';
        if (mysql_num_rows($rss) == 0) {
            $file_content = "<p>&nbsp;&nbsp;&nbsp;&nbsp;<b>" . tr('list_of_events_is_empty') . "</b></p><br>";
        } else {
            $cacheline = '<li class="newcache_list_multi" style="margin-bottom:8px;"><img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" />&nbsp;{date}&nbsp;<a id="newcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}" onmouseover="Lite({nn})" onmouseout="Unlite()" maphref="{smallmapurl}">{cachename}</a>&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" title="user" />&nbsp;&nbsp;<a class="links" href="viewprofile.php?userid={userid}">{username}</a></li>';
            $file_content = '<ul style="font-size: 11px;">';
            for ($i = 0; $i < mysql_num_rows($rss); $i++) {
                $record = sql_fetch_array($rss);
//          $loc = coordToLocation($record['latitude'], $record['longitude']);

                $thisline = $cacheline;
                $thisline = mb_ereg_replace('{nn}', $i + $markerpositions['plain_cache_num'], $thisline);
//          $thisline = mb_ereg_replace('{kraj}',$loc['kraj'], $thisline);
//          $thisline = mb_ereg_replace('{woj}',$loc['woj'], $thisline);
//          $thisline = mb_ereg_replace('{miasto}',$loc['miasto'], $thisline);
//          $thisline = mb_ereg_replace('{dziubek}',$loc['dziubek'], $thisline);
                $thisline = mb_ereg_replace('{date}', htmlspecialchars(date($dateFormat, strtotime($record['date_hidden'])), ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
                $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
                $thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{locationstring}', $locationstring, $thisline);
                $thisline = mb_ereg_replace('{cacheicon}', 'tpl/stdstyle/images/cache/22x22-event.png', $thisline);
                $thisline = mb_ereg_replace('{smallmapurl}', create_map_url($markerpositions, $i + $markerpositions['plain_cache_num'], $latitude, $longitude), $thisline);

                $file_content .= $thisline . "\n";
            }
            $file_content .= '</ul>';
        }

        tpl_set_var('new_events', $file_content);
        mysql_free_result($rss);

        // Read just log IDs first - this gets easily optimized
        $log_ids = '';
        $rsids = sql("SELECT cache_logs.id FROM cache_logs
        WHERE cache_logs.deleted = 0 AND
            cache_logs.cache_id IN (SELECT cache_id FROM local_caches) AND
            cache_logs.date_created >= DATE_SUB(NOW(), INTERVAL 31 DAY)
        ORDER BY cache_logs.date_created DESC
        LIMIT 0, 10");
        for ($i = 0; $i < mysql_num_rows($rsids); $i++) {
            $idrec = sql_fetch_array($rsids);
            if (!empty($log_ids)) {
                $log_ids .= ",";
            }
            $log_ids .= $idrec['id'];
        }
        mysql_free_result($rsids);

        // Ugly hack to avoid modifying code below.
        // When there are no logs to display -> pass dummy value as log id.
        // starypatyk, 2011.04.08
        if (empty($log_ids)) {
            $log_ids = "-1";
        }

        // Now use a set of log IDs to retrieve all other necessary information
        $rsl = sql("SELECT cache_logs.id, cache_logs.cache_id AS cache_id,
                              cache_logs.type AS log_type,
                              cache_logs.date AS log_date,
                   cache_logs.text AS log_text,
                  cache_logs.text_html AS text_html,
                              local_caches.name AS cache_name,
                              user.username AS user_name,
                              user.user_id AS user_id,
                              local_caches.wp_oc AS wp_name,
                              local_caches.type AS cache_type,
                              cache_type.icon_small AS cache_icon_small,
                              log_types.icon_small AS icon_small,
                              IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`,
                            COUNT(gk_item.id) AS geokret_in
                            FROM
                                (local_caches INNER JOIN cache_logs ON (local_caches.cache_id = cache_logs.cache_id))
                                INNER JOIN user ON (cache_logs.user_id = user.user_id)
                                INNER JOIN log_types ON (cache_logs.type = log_types.id)
                                INNER JOIN cache_type ON (local_caches.type = cache_type.id)
                                LEFT JOIN `cache_rating` ON (`cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`)
                                LEFT JOIN   gk_item_waypoint ON (gk_item_waypoint.wp = local_caches.wp_oc)
                                LEFT JOIN   gk_item ON (gk_item.id = gk_item_waypoint.id AND
                            gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5)
                            WHERE   cache_logs.id IN (" . $log_ids . ")
                            GROUP BY cache_logs.id
                            ORDER BY cache_logs.date_created DESC LIMIT 0, 10");
        $rsll = sql("SELECT cache_logs.id FROM cache_logs
    WHERE cache_logs.deleted = 0 AND cache_logs.cache_id IN (SELECT cache_id FROM local_caches)
    ORDER BY cache_logs.date_created DESC LIMIT 0, 11");

        if (mysql_num_rows($rsll) > 10) {
            tpl_set_var('more_logs', '<a class="links" href="myn_newlogs.php">[' . tr("show_more") . '...]</a>');
        } else {
            tpl_set_var('more_logs', "");
        }
        mysql_free_result($rsll);

        $file_content = '';

        if (mysql_num_rows($rsl) == 0) {
            $file_content = "<p>&nbsp;&nbsp;&nbsp;&nbsp;<b>" . tr('list_of_latest_logs_is_empty') . "</b></p><br>";
        } else {
            $cacheline = '<li class="newcache_list_multi" style="margin-bottom:8px;"><img src="{gkicon}" class="icon16" alt="" title="gk" />&nbsp;&nbsp;<img src="{rateicon}" class="icon16" alt="" title="rate" />&nbsp;&nbsp;<img src="{logicon}" class="icon16" alt="" title="log" />&nbsp;&nbsp;<a id="newcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}" onmouseover="Lite({nn})" onmouseout="Unlite()" maphref="{smallmapurl}"><img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" /></a>&nbsp;{date}&nbsp;<a id="newlog{nn}" class="links" href="viewlogs.php?logid={logid}" onmouseover="Tip(\'{log_text}\', PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()">{cachename}</a>&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" title="user" />&nbsp;&nbsp;<a class="links" href="viewprofile.php?userid={userid}">{username}</a><br/></li>';
            $file_content = '<ul style="font-size: 11px;">';
            for ($i = 0; $i < mysql_num_rows($rsl); $i++) {
                $log_record = sql_fetch_array($rsl);

                $thisline = $cacheline;

                $cacheicon = 'tpl/stdstyle/images/' . $log_record['cache_icon_small'];
                if ($log_record['geokret_in'] != '0') {
                    $thisline = mb_ereg_replace('{gkicon}', "images/gk.png", $thisline);
                } else {
                    $thisline = mb_ereg_replace('{gkicon}', "images/rating-star-empty.png", $thisline);
                }

                //$rating_picture
                if ($log_record['recommended'] == 1 && $log_record['log_type'] == 1) {
                    $thisline = mb_ereg_replace('{rateicon}', "images/rating-star.png", $thisline);
                } else {
                    $thisline = mb_ereg_replace('{rateicon}', "images/rating-star-empty.png", $thisline);
                }
                // ukrywanie autora komentarza COG przed zwykłym userem
                // (Łza)
                if ($log_record['log_type'] == 12 && !$usr['admin']) {
                    $log_record['user_id'] = '0';
                    $log_record['user_name'] = 'Centrum Obsługi Geocachera ';
                }
                // koniec ukrywania autora komentarza COG przed zwykłym userem

                $thisline = mb_ereg_replace('{date}', htmlspecialchars(date($dateFormat, strtotime($log_record['log_date'])), ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{cacheid}', urlencode($log_record['cache_id']), $thisline);
                $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($log_record['cache_name'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{userid}', urlencode($log_record['user_id']), $thisline);
                $thisline = mb_ereg_replace('{logid}', htmlspecialchars($log_record['id'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{username}', htmlspecialchars($log_record['user_name'], ENT_COMPAT, 'UTF-8'), $thisline);
//          $thisline = mb_ereg_replace('{locationstring}', $locationstring, $thisline);

                $logtext = '<b>' . $log_record['user_name'] . '</b>: &nbsp;';
                if ($log_record['encrypt'] == 1 && $log_record['cache_owner'] != $usr['userid'] && $log_record['luser_id'] != $usr['userid']) {
                    $logtext .= "<img src=\'/tpl/stdstyle/images/free_icons/lock.png\' alt=\`\` /><br/>";
                }
                if ($log_record['encrypt'] == 1 && ($log_record['cache_owner'] == $usr['userid'] || $log_record['luser_id'] == $usr['userid'])) {
                    $logtext .= "<img src=\'/tpl/stdstyle/images/free_icons/lock_open.png\' alt=\`\` /><br/>";
                }
                $data_text = cleanup_text(str_replace("\r\n", " ", $log_record['log_text']));
                $data_text = str_replace("\n", " ", $data_text);
                if ($log_record['encrypt'] == 1 && $log_record['cache_owner'] != $usr['userid'] && $log_record['luser_id'] != $usr['userid']) {//crypt the log ROT13, but keep HTML-Tags and Entities
                    $data_text = str_rot13_html($data_text);
                } else {
                    $logtext .= "<br/>";
                }
                $logtext .=$data_text;
                $thisline = mb_ereg_replace('{log_text}', $logtext, $thisline);
                $thisline = mb_ereg_replace('{logicon}', "tpl/stdstyle/images/" . $log_record['icon_small'], $thisline);

                $thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);
                $thisline = mb_ereg_replace('{smallmapurl}', create_map_url($markerpositions, $i + $markerpositions['plain_cache_num'], $latitude, $longitude), $thisline);

                $file_content .= $thisline . "\n";
            }
            $file_content .= '</ul>';
        }
        tpl_set_var('new_logs', $file_content);
        mysql_free_result($rsl);
    }
}

// AJAX Chat -shoutbox
function getShoutBoxContent()
{
    // URL to the chat directory:
    if (!defined('AJAX_CHAT_URL')) {
        define('AJAX_CHAT_URL', './chat/');
    }

    // Path to the chat directory:
    if (!defined('AJAX_CHAT_PATH')) {
        define('AJAX_CHAT_PATH', realpath(dirname($_SERVER['SCRIPT_FILENAME']) . '/chat') . '/');
    }

    // Validate the path to the chat:
    if (@is_file(AJAX_CHAT_PATH . 'lib/classes.php')) {

        // Include Class libraries:
        require_once(AJAX_CHAT_PATH . 'lib/classes.php');

        // Initialize the shoutbox:
        $ajaxChat = new CustomAJAXChatShoutBox();

        // Parse and return the shoutbox template content:
        return $ajaxChat->getShoutBoxContent();
    }

    return null;
}

//make the template and send it out
tpl_BuildTemplate();
?>
