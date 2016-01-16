<?php


if (!isset($rootpath))
    $rootpath = '';

global $titled_cache_period_prefix;

//include template handling
require_once($rootpath . 'lib/common.inc.php');
require_once($rootpath . 'lib/calculation.inc.php');
require_once($rootpath . 'lib/cache_icon.inc.php');
require_once($stylepath . '/lib/icons.inc.php');
//require_once($rootpath . '/powerTrail/PowerTrailBase.php');
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
        $tplname = 'myneighborhood';


        $fixed_google_map_link = '';
        $marker_offset = 0;
        
        $dbcLocCache = new dataBase();

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
            $res = sql("SELECT cache_loc.country, adm1, adm2 FROM cache_loc INNER JOIN caches ON (cache_loc.cache_id = caches.cache_id)
                        WHERE cache_loc.cache_id = &1
                            AND caches.latitude = cache_loc.latitude
                            AND caches.longitude = cache_loc.longitude
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
            }
            else {
                return array($rec['country'], $rec['adm1'], $rec['adm2']);
            }
        }

        function get_marker_positions($latitude, $longitude, $radius, $user_id)
        {
            global $dbcLocCache;
            
            $markerpos = array();
            $markers = array();
            $sqlstr = "
                      SELECT SQL_BUFFER_RESULT `caches`.`cache_id`, `caches`.`longitude`, `caches`.`latitude`, `caches`.`type`
                      FROM local_caches `caches`
                      WHERE `caches`.`type` != 6
                            AND `caches`.`status` = 1
                            AND `caches`.`date_hidden` <= NOW()
                            AND `caches`.`date_created` <= NOW()
                      ORDER BY IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) DESC, `caches`.`cache_id` DESC
                      LIMIT 0, 10";

            $dbcLocCache->simpleQuery( $sqlstr );
            
            for ($i = 0; $i < $dbcLocCache->rowCount(); $i++) {
                $record = $dbcLocCache->dbResultFetch();
                $lat = $record['latitude'];
                $lon = $record['longitude'];
                $type = $record['type'];
                $markers[] = array('lat' => $lat, 'lon' => $lon, 'type' => $type, 'kind' => 'new');
            }

            $markerpos['plain_cache_num'] = count($markers);

            $sqlstr = "
                      SELECT SQL_BUFFER_RESULT  `caches`.`cache_id`,
                                                `caches`.`longitude`,
                                                `caches`.`latitude`,
                                                `caches`.`type`
                        FROM    local_caches `caches`
                        WHERE  `caches`.`date_hidden` >= curdate()
                            AND `caches`.`type` = 6
                            AND `caches`.`status` = 1
                        ORDER BY `caches`.`date_hidden` ASC
                        LIMIT 0, 10";
            
            $dbcLocCache->simpleQuery( $sqlstr );
            
            
            for ($i = 0; $i < $dbcLocCache->rowCount(); $i++) {
                $record = $dbcLocCache->dbResultFetch();
                $lat = $record['latitude'];
                $lon = $record['longitude'];
                $type = $record['type'];
                $markers[] = array('lat' => $lat, 'lon' => $lon, 'type' => $type, 'kind' => 'event');
            }
            $markerpos['plain_cache_num2'] = count($markers);

            $sqlstr = "
                      SELECT SQL_BUFFER_RESULT  `caches`.`cache_id`,
                                                `caches`.`longitude`,
                                                `caches`.`latitude`,
                                                `caches`.`type`
                        FROM    local_caches `caches`
                        WHERE  `caches`.`type` != 6
                            AND `caches`.`status` = 1
                            AND `caches`.`founds` = 0
                        ORDER BY `caches`.`date_hidden` DESC, `caches`.`cache_id` DESC
                        LIMIT 0, 10"; 
            
            $dbcLocCache->simpleQuery( $sqlstr );
            
            
            
            for ($i = 0; $i < $dbcLocCache->rowCount(); $i++) {
                $record = $dbcLocCache->dbResultFetch();
                $lat = $record['latitude'];
                $lon = $record['longitude'];
                $type = $record['type'];
                $markers[] = array('lat' => $lat, 'lon' => $lon, 'type' => $type, 'kind' => 'ftf');
            }

            $markerpos['markers'] = $markers;

            return $markerpos;
        }

        function create_map_url($markerpos, $index, $latitude, $longitude)
        {
            global $googlemap_key;
            global $fixed_google_map_link;

            $markers = $markerpos['markers'];
            if (empty($markerpos['markers'])) {
                $dzoom = "&amp;zoom=8";
            } else {
                $dzoom = "";
            }
            $markers_str = "&amp;markers=color:blue|size:small|";
            $markers_ev_str = "&amp;markers=color:orange|size:small|";
            $markers_ftf_str = "&amp;markers=color:0xFFDF00|size:small|";
            $sel_marker_str = "";
            foreach ($markers as $i => $marker) {
                $lat = sprintf("%.3f", $marker['lat']);
                $lon = sprintf("%.3f", $marker['lon']);
                $type = strtoupper(typeToLetter($marker['type']));
                $kind = $marker['kind'];
                if (strcmp($kind, 'event') == 0)
                    if ($i != $index)
                        $markers_ev_str .= "$lat,$lon|";
                    else
                        $sel_marker_str = "&amp;markers=color:orange|label:$type|$lat,$lon|";
                else if (strcmp($kind, 'ftf') == 0)
                    if ($i != $index)
                        $markers_ftf_str .= "$lat,$lon|";
                    else
                        $sel_marker_str = "&amp;markers=color:0xFFDF00|label:$type|$lat,$lon|";
                else if (strcmp($kind, 'new') == 0)
                    if ($i != $index)
                        $markers_str .= "$lat,$lon|";
                    else
                        $sel_marker_str = "&amp;markers=color:blue|label:$type|$lat,$lon|";
            }
            $google_map = "http://maps.google.com/maps/api/staticmap?center=" . $latitude . "," . $longitude . $dzoom . "&amp;size=350x350&amp;maptype=roadmap&amp;key=" . $googlemap_key . "&amp;sensor=false" . $markers_ftf_str . $markers_str . $markers_ev_str . $sel_marker_str;

            if ($index == -1) {
                $fixed_google_map_link = $google_map; // store fixed map link to be used with Top Reco and New logs items
            };
            return $google_map;
        }

        function add_single_marker($c_kind, $c_type, $c_lat, $c_long)
        {
            global $fixed_google_map_link;
            $lat = sprintf("%.3f", $c_lat);
            $lon = sprintf("%.3f", $c_long);
            $type = strtoupper(typeToLetter($c_type));
            $kind = $c_kind;
            $single_marker = '';
            if (strcmp($kind, 'TopR') == 0) {
                $single_marker = "&amp;markers=color:0x29BD1A|label:$type|$lat,$lon|";
            } else if (strcmp($kind, 'NewL') == 0) {
                $single_marker = "&amp;markers=color:black|label:$type|$lat,$lon|";
            };
            return $fixed_google_map_link . $single_marker;
        }

        ;

        tpl_set_var('more_caches', '');
        tpl_set_var('more_ftf', '');
        tpl_set_var('more_topcache', '');
        tpl_set_var('more_logs', '');

        $latitude = sqlValue("SELECT `latitude` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);
        $longitude = sqlValue("SELECT `longitude` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);

        if (($longitude == NULL && $latitude == NULL) || ($longitude == 0 && $latitude == 0)) {
            tpl_set_var('info', '<br /><div class="notice" style="line-height: 1.4em;font-size: 120%;"><b>' . tr("myn_info") . '</b></div><br />');
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
        $lat = $latitude;
        $lon = $longitude;
        $lon_rad = $lon * 3.14159 / 180;
        $lat_rad = $lat * 3.14159 / 180;

        localCachesInc::createLocalCaches($dbcLocCache, $lon, $lat, $distance, $user_id);
        
        // Read coordinates of the newest caches
        $markerpositions = get_marker_positions($latitude, $longitude, $radius, $user_id);
        // Generate include file for map with new caches
        tpl_set_var('local_cache_map', '<img src="' . create_map_url($markerpositions, -1, $latitude, $longitude) . '" basesrc="' . create_map_url($markerpositions, -1, $latitude, $longitude) . '" id="main-cachemap" name="main-cachemap" alt="mapa" />');

        /* ===================================================================================== */
        /*                          Najnowsze skrzynki                                           */
        /* ===================================================================================== */

        //start_newcaches.include
        $sqlstr = "SELECT    `user`.`user_id`            AS `user_id`,
                            `user`.`username`           AS `username`,
                            `caches`.`cache_id`         AS `cache_id`,
                            `caches`.`name`             AS `name`,
                            `caches`.`longitude`        AS `longitude`,
                            `caches`.`latitude`         AS `latitude`,
                            `caches`.`date_hidden`      AS `date_hidden`,
                            `caches`.`date_created`     AS `date_created`,
                            IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) AS `date`,
                            `caches`.`country`          AS `country`,
                            `caches`.`difficulty`       AS `difficulty`,
                            `caches`.`distance`         AS `distance`,
                            `caches`.`terrain`          AS `terrain`,
                            `cache_type`.`icon_large`   AS `icon_large`,
                            `caches`.`type`             AS `cache_type`
                 FROM local_caches `caches` INNER JOIN `user` ON (`caches`.`user_id`=`user`.`user_id`), `cache_type`
                 WHERE     `caches`.`type`!=6
                    AND `caches`.`status`=1
                    AND `caches`.`type`=`cache_type`.`id`
                    AND `caches`.`date_created` <= NOW()
                    AND     `caches`.`date_hidden` <= NOW()
                 ORDER BY `date` DESC, `caches`.`cache_id` DESC
                 LIMIT 0 , 11";
        
        $dbcLocCache->simpleQuery( $sqlstr );

        if ( $dbcLocCache->rowCount() > 10) {
            tpl_set_var('more_caches', '<a class="links" href="myn_newcaches.php">[' . tr("show_more") . '...]</a>');
            $limit = 10;
        } else
            $limit = $dbcLocCache->rowCount();
        
        if ($limit == 0) {
            $file_content = "<p>&nbsp;&nbsp;&nbsp;&nbsp;<b>" . tr('list_of_caches_is_empty') . "</b></p><br />";
        } else {
            $cacheline = '
                <tr>
                    <td class="myneighborhood tab_icon"> <img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" /></td>

                    <td class="myneighborhood tab_date"> {date}</td>
                    <td class="myneighborhood"> <a id="mapcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}" onmouseover="Lite({nn})" onmouseout="Unlite()" maphref="{smallmapurl}">{cachename}</a> &nbsp; (&nbsp;{distance}&nbsp;km&nbsp;) </td>
                    <td class="myneighborhood tab_arrow"> <img src="tpl/stdstyle/images/blue/arrow.png" alt="" title="user" /></td>
                    <td class="myneighborhood"> <a class="links" href="viewprofile.php?userid={userid}">{username}</a></td>
                </tr>';

            $file_content = '<table class="myneighborhood">';

            for ($i = 0; $i < $limit; $i++) {
                $record = $dbcLocCache->dbResultFetch();

                $cacheicon = myninc::checkCacheStatusByUser($record, $user_id);
                $thisline = $cacheline;
                $thisline = mb_ereg_replace('{nn}', $i, $thisline);
                $thisline = mb_ereg_replace('{date}', htmlspecialchars(date($dateFormat, strtotime($record['date'])), ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
                $thisline = mb_ereg_replace('{cache_count}', $i, $thisline);
                $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
                $thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{distance}', number_format($record['distance'], 1, ',', ''), $thisline);
                $thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);
                $thisline = mb_ereg_replace('{smallmapurl}', create_map_url($markerpositions, $i, $latitude, $longitude), $thisline);

                $file_content .= $thisline . "\n";
            }
            $file_content .= '</table>';
            $marker_offset = $marker_offset + $i;
        }

        tpl_set_var('new_caches', $file_content);
        /* ===================================================================================== */
        /*                          Lista wydarzeń                                               */
        /* ===================================================================================== */

        //nextevents.include

        $sqlstr = "SELECT   `user`.`user_id`            AS `user_id`,
                            `user`.`username`           AS `username`,
                            `caches`.`cache_id`         AS `cache_id`,
                            `caches`.`name`             AS `name`,
                            `caches`.`longitude`        AS `longitude`,
                            `caches`.`latitude`         AS `latitude`,
                            `caches`.`date_hidden`      AS `date_hidden`,
                            `caches`.`date_created`     AS `date_created`,
                            `caches`.`country`          AS `country`,
                            `caches`.`type`             AS `cache_type`,
                            `caches`.`difficulty`       AS `difficulty`,
                            `local_caches`.`distance`   AS `distance`,
                            `caches`.`terrain`          AS `terrain`,
                            `cache_type`.`icon_large`   AS `icon_large`

                  FROM `caches`, `user`, `cache_type`,local_caches
                  WHERE `caches`.`cache_id`=local_caches.`cache_id`
                        AND `caches`.`user_id`=`user`.`user_id`
                        AND `caches`.`type`=6
                        AND `caches`.`status`=1
                        AND `caches`.`type`=`cache_type`.`id`
                        AND `caches`.`date_hidden` >= curdate()
                  ORDER BY `date_hidden` ASC
                  LIMIT 0 , 10";

        $dbcLocCache->simpleQuery( $sqlstr );
        
        $file_content = '';
        if ( $dbcLocCache->rowCount() == 0) {
            $file_content = "<p>&nbsp;&nbsp;&nbsp;&nbsp;<b>" . tr('list_of_events_is_empty') . "</b></p><br />";
        } else {
            $cacheline = '
            <tr>
                <td class="myneighborhood tab_icon"> <img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" /></td>
                <td class="myneighborhood tab_date"> {date} </td>
                <td class="myneighborhood"> <a id="mapcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}" onmouseover="Lite({nn})" onmouseout="Unlite()" maphref="{smallmapurl}">{cachename}</a>  (&nbsp;{distance}&nbsp;km&nbsp;) </td>
                <td class="myneighborhood tab_arrow"> <img src="tpl/stdstyle/images/blue/arrow.png" alt="" title="user" /></td>
                <td class="myneighborhood"> <a class="links" href="viewprofile.php?userid={userid}">{username}</a></td>
            </tr>';
            $file_content = '<table  class="myneighborhood">';

            for ($i = 0; $i < $dbcLocCache->rowCount(); $i++) {
                $record = $dbcLocCache->dbResultFetch();
                $cacheicon = myninc::checkCacheStatusByUser($record, $user_id);             // $cacheicon =is_event_attended($record['cache_id'], $user_id) ? $cacheTypesIcons[6]['iconSet'][1]['iconSmallFound'] : $cacheTypesIcons[6]['iconSet'][1]['iconSmallFound'] ;
                $thisline = $cacheline;
                $thisline = mb_ereg_replace('{nn}', $i + $markerpositions['plain_cache_num'], $thisline);
                $thisline = mb_ereg_replace('{date}', htmlspecialchars(date($dateFormat, strtotime($record['date_hidden'])), ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
                $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
                $thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{distance}', number_format($record['distance'], 1, ',', ''), $thisline);
                $thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);
                $thisline = mb_ereg_replace('{smallmapurl}', create_map_url($markerpositions, $i + $markerpositions['plain_cache_num'], $latitude, $longitude), $thisline);
                $file_content .= $thisline . "\n";
            }
            $file_content .= '</table>';
            $marker_offset = $marker_offset + $i;
        }

        tpl_set_var('new_events', $file_content);

        /* ===================================================================================== */
        /*                          Skrzynki z FTF                                               */
        /* ===================================================================================== */

        //start_ftfcaches.include
        $sqlstr = "SELECT    `user`.`user_id`            AS `user_id`,
                            `user`.`username`           AS `username`,
                            `caches`.`cache_id`         AS `cache_id`,
                            `caches`.`name`             AS `name`,
                            `caches`.`longitude`        AS `longitude`,
                            `caches`.`type`             AS `cache_type`,
                            `caches`.`latitude`         AS `latitude`,
                            `caches`.`date_hidden`      AS `date`,
                            `caches`.`date_created`     AS `date_created`,
                            `caches`.`country`          AS `country`,
                            `caches`.`difficulty`       AS `difficulty`,
                            `caches`.`distance`         AS `distance`,
                            `caches`.`terrain`          AS`terrain`,
                            `cache_type`.`icon_large`   AS `icon_large`
                 FROM local_caches `caches` INNER JOIN `user` ON (`caches`.`user_id`=`user`.`user_id`), `cache_type`
                 WHERE `caches`.`type`!=6
                    AND `caches`.`status`=1
                    AND `caches`.`type`=`cache_type`.`id`
                    AND `caches`.`founds`=0
                 ORDER BY `date` DESC, `caches`.`cache_id` DESC
                 LIMIT 0 , 11";
        
        $dbcLocCache->simpleQuery( $sqlstr );
        
        if ( $dbcLocCache->rowCount() > 10) {
            tpl_set_var('more_ftf', '<a class="links" href="myn_ftf.php">[' . tr("show_more") . '...]</a>');
            $limit = 10;
        } else
            $limit = $dbcLocCache->rowCount();

        if ($limit == 0) {
            $file_content = "<p>&nbsp;&nbsp;&nbsp;&nbsp;<b>" . tr('list_of_caches_is_empty') . "</b></p><br />";
        } else {
            $cacheline = '
                <tr>
                    <td class="myneighborhood tab_icon"> <img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" /></td>
                    <td class="myneighborhood tab_date"> {date}</td>
                    <td class="myneighborhood"> <a id="mapcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}" onmouseover="Lite({nn})" onmouseout="Unlite()" maphref="{smallmapurl}">{cachename}</a>  (&nbsp;{distance}&nbsp;km&nbsp;) </td>
                    <td class="myneighborhood tab_arrow"> <img src="tpl/stdstyle/images/blue/arrow.png" alt="" title="user" /></td>
                    <td class="myneighborhood tab_user"> <a class="links" href="viewprofile.php?userid={userid}">{username}</a></td>
                </tr>';
            $file_content = '<table  class="myneighborhood"> ';

            for ($i = 0; $i < $limit; $i++) {
                $record = $dbcLocCache->dbResultFetch();
                $cacheicon = myninc::checkCacheStatusByUser($record, $user_id);
                $thisline = $cacheline;
                $thisline = mb_ereg_replace('{nn}', $i + $markerpositions['plain_cache_num2'], $thisline);
                $thisline = mb_ereg_replace('{date}', htmlspecialchars(date($dateFormat, strtotime($record['date'])), ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
                $thisline = mb_ereg_replace('{cache_count}', $i, $thisline);
                $thisline = mb_ereg_replace('{smallmapurl}', create_map_url($markerpositions, $i + $markerpositions['plain_cache_num2'], $latitude, $longitude), $thisline);
                $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
                $thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{distance}', number_format($record['distance'], 1, ',', ''), $thisline);
                $thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);

                $file_content .= $thisline . "\n";
            }
            $file_content .= '</table>';
            $marker_offset = $marker_offset + $i;
        }

        tpl_set_var('ftf_caches', $file_content);

        /* ===================================================================================== */
        /*                          Najnowsze komentarze                                         */
        /* ===================================================================================== */

        // Read just log IDs first - this gets easily optimized
        $sqlstr = "SELECT cache_logs.id FROM cache_logs
         WHERE cache_logs.deleted = 0
        AND cache_logs.cache_id IN (SELECT cache_id FROM local_caches)
        ORDER BY cache_logs.date_created DESC
        LIMIT 0, 11";
        $dbcLocCache->simpleQuery( $sqlstr );
        
        if ( $dbcLocCache->rowCount() > 10) {
        tpl_set_var('more_logs', '<a class="links" href="myn_newlogs.php">[' . tr("show_more") . '...]</a>');
        } else {
            tpl_set_var('more_logs', "");
        }
        
        
        
        
        $log_ids = '';
        $sqlstr = "SELECT cache_logs.id FROM cache_logs
                       WHERE cache_logs.deleted = 0
                            AND cache_logs.cache_id IN (SELECT cache_id FROM local_caches)
                            AND cache_logs.date_created >= DATE_SUB(NOW(), INTERVAL 31 DAY)
                       ORDER BY cache_logs.date_created DESC
                       LIMIT 0, 10";
        
        $dbcLocCache->simpleQuery( $sqlstr );
        
        for ($i = 0; $i < $dbcLocCache->rowCount(); $i++) {
            $idrec = $dbcLocCache->dbResultFetch();
            if (!empty($log_ids)) {
                $log_ids .= ",";
            }
            $log_ids .= $idrec['id'];
        }

        // Ugly hack to avoid modifying code below.
        // When there are no logs to display -> pass dummy value as log id.
        // starypatyk, 2011.04.08
        if (empty($log_ids)) {
            $log_ids = "-1";
        }

        // Now use a set of log IDs to retrieve all other necessary information
        $sqlstr = "SELECT  cache_logs.id,
                            cache_logs.cache_id        AS cache_id,
                            cache_logs.type            AS log_type,
                            cache_logs.date            AS log_date,
                            cache_logs.text            AS log_text,
                            cache_logs.text_html       AS text_html,
                            local_caches.name          AS cache_name,
                            user.username              AS user_name,
                            user.user_id               AS luser_id,
                            local_caches.wp_oc         AS wp_name,
                            local_caches.type          AS cache_type,
                            `local_caches`.`longitude` AS `longitude`,
                            `local_caches`.`latitude`  AS `latitude`,
                            caches.user_id AS user_id,

                            log_types.icon_small       AS icon_small,
                            IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`,
                            COUNT(gk_item.id)          AS geokret_in,
                            `PowerTrail`.`id` AS PT_ID,
                            `PowerTrail`.`name` AS PT_name,
                            `PowerTrail`.`type` As PT_type,
                            `PowerTrail`.`image` AS PT_image
                   FROM caches, (local_caches INNER JOIN cache_logs ON (local_caches.cache_id = cache_logs.cache_id))
                        INNER JOIN user             ON (cache_logs.user_id = user.user_id)
                        INNER JOIN log_types        ON (cache_logs.type = log_types.id)
                        INNER JOIN cache_type       ON (local_caches.type = cache_type.id)
                        LEFT JOIN `cache_rating`    ON (`cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`)
                        LEFT JOIN gk_item_waypoint  ON (gk_item_waypoint.wp = local_caches.wp_oc)
                        LEFT JOIN gk_item           ON (gk_item.id = gk_item_waypoint.id AND gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5)
                        LEFT JOIN `powerTrail_caches` ON (`cache_logs`.`cache_id` = `powerTrail_caches`.`cacheId`)
                        LEFT JOIN `PowerTrail` ON (`PowerTrail`.`id` = `powerTrail_caches`.`PowerTrailId`  AND `PowerTrail`.`status` = 1 )
                   WHERE    cache_logs.id IN (" . $log_ids . ")
                   AND caches.cache_id = cache_logs.cache_id

                   GROUP BY cache_logs.id
                   ORDER BY cache_logs.date_created DESC LIMIT 0, 10";

        $dbcLocCache->simpleQuery( $sqlstr );
                
        
        $file_content = '';

        if ( $dbcLocCache->rowCount() == 0) {
            $file_content = "<p>&nbsp;&nbsp;&nbsp;&nbsp;<b>" . tr('list_of_latest_logs_is_empty') . "</b></p><br />";
        } else {
            $cacheline = '
                <tr>
                    <td class="myneighborhood tab_icon" > <img src="{gkicon}" class="icon16" alt="" title="gk" /></td>
                    <td class="myneighborhood tab_icon" > <img src="{rateicon}" class="icon16" alt="" title="rate" /></td>
                    <td class="myneighborhood tab_icon" > {GPicon}</td>
                    <td class="myneighborhood tab_icon" > <img src="{logicon}" class="icon16" alt="" title="log" /></td>
                    <td class="myneighborhood tab_icon" > <a id="logcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}"><img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" /> </a></td>
                    <td class="myneighborhood tab_date" > {date} </td>
                    <td class="myneighborhood"> <a id="mapcache{nn}" class="links" href="viewlogs.php?logid={logid}" onmouseover="Tip(\'{log_text}\', PADDING,5, WIDTH,280,SHADOW,true); Lite({nn});" onmouseout="UnTip();Unlite();"  maphref="{smallmapurl}">{cachename}</a></td>
                    <td class="myneighborhood tab_arrow" > <img src="tpl/stdstyle/images/blue/arrow.png" alt="" title="user" /></td>
                    <td class="myneighborhood tab_user" > <a class="links" href="viewprofile.php?userid={userid}"> {username}</a> </td>
                </tr>';
            $file_content = '<table  class="myneighborhood">';
            //PowerTrail vel GeoPath variables
            $pt_cache_intro_tr = tr('pt_cache');
            $pt_icon_title_tr = tr('pt139');

            for ($i = 0; $i < $dbcLocCache->rowCount(); $i++) {
                $log_record = $dbcLocCache->dbResultFetch();
                $cacheicon = myninc::checkCacheStatusByUser($log_record, $user_id);
                $thisline = $cacheline;

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

                // PowerTrail vel GeoPath icon
                if (isset($log_record['PT_ID'])) {
                    $PT_icon = icon_geopath_small($log_record['PT_ID'], $log_record['PT_image'], $log_record['PT_name'], $log_record['PT_type'], $pt_cache_intro_tr, $pt_icon_title_tr);
                    $thisline = mb_ereg_replace('{GPicon}', $PT_icon, $thisline);
                } else {
                    $thisline = mb_ereg_replace('{GPicon}', '<img src="images/rating-star-empty.png" class="icon16" alt="' . $pt_icon_title_tr . '" title="' . $pt_icon_title_tr . '" />', $thisline);
                }


                // ukrywanie autora komentarza COG przed zwykłym userem
                // (Łza)
                if ($log_record['log_type'] == 12 && !$usr['admin']) {
                    $log_record['user_id'] = '0';
                    $log_record['user_name'] = tr('cog_user_name');
                }
                // koniec ukrywania autora komentarza COG przed zwykłym userem

                $thisline = mb_ereg_replace('{nn}', $i + $marker_offset, $thisline);
                $thisline = mb_ereg_replace('{date}', htmlspecialchars(date($dateFormat, strtotime($log_record['log_date'])), ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{cacheid}', urlencode($log_record['cache_id']), $thisline);
                $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($log_record['cache_name'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{userid}', urlencode($log_record['luser_id']), $thisline);
                $thisline = mb_ereg_replace('{logid}', htmlspecialchars($log_record['id'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{username}', htmlspecialchars($log_record['user_name'], ENT_COMPAT, 'UTF-8'), $thisline);

                $logtext = '<b>' . $log_record['user_name'] . '</b>: &nbsp;';
                $data_text = common::cleanupText(str_replace("\r\n", " ", $log_record['log_text']));
                $data_text = str_replace("\n", " ", $data_text);
                $logtext .= "<br/>";

                $logtext .=$data_text;
                $thisline = mb_ereg_replace('{log_text}', htmlspecialchars($logtext, ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{logicon}', "tpl/stdstyle/images/" . $log_record['icon_small'], $thisline);
                $thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);
                $thisline = mb_ereg_replace('{smallmapurl}', add_single_marker('NewL', $log_record['cache_type'], $log_record['latitude'], $log_record['longitude']), $thisline);

                $file_content .= $thisline . "\n";
            }
            $file_content .= '</table>';
            $marker_offset = $marker_offset + $i;
        }
        tpl_set_var('new_logs', $file_content);
    }

    /* ===================================================================================== */
    /*                          TOP rekomendowanych skrzynek                                 */
    /* ===================================================================================== */

    //start_topcaches.include
    $sqlstr = "SELECT  `user`.`user_id`                    AS `user_id`,
                            `user`.`username`                   AS `username`,
                            `caches`.`cache_id`                 AS `cache_id`,
                            `caches`.`name`                     AS `name`,
                            `caches`.`longitude`                AS `longitude`,
                            `caches`.`latitude`                 AS `latitude`,
                            `caches`.`date_hidden`              AS `date`,
                            `caches`.`date_created`             AS `date_created`,
                            `caches`.`country`                  AS `country`,
                            `caches`.`difficulty`               AS `difficulty`,
                            `caches`.`terrain`                  AS `terrain`,
                            `cache_type`.`icon_large`           AS `icon_large`,
                            `caches`.`type`                     AS `cache_type`,
                            count(`cache_rating`.`cache_id`)    AS `toprate`
                   FROM local_caches `caches`
                        INNER JOIN `user`           ON (`caches`.`user_id`=`user`.`user_id`)
                        LEFT JOIN `cache_rating`    ON (`caches`.`cache_id`=`cache_rating`.`cache_id`), `cache_type`
                   WHERE `caches`.`type`!=6
                        AND `cache_rating`.`cache_id`=`caches`.`cache_id`
                        AND `caches`.`status`=1
                        AND `caches`.`type`=`cache_type`.`id`
                   GROUP BY `caches`.`cache_id`
                   ORDER BY `toprate` DESC, `caches`.`name` ASC LIMIT 0 , 11";
    
    $dbcLocCache->simpleQuery( $sqlstr );
    
    if ( $dbcLocCache->rowCount() > 10) {
        tpl_set_var('more_topcaches', '<a class="links" href="myn_topcaches.php">[' . tr("show_more") . '...]</a>');
        $limit = 10;
    } else
        $limit = $dbcLocCache->rowCount();

    if ($limit == 0) {
        $file_content = "<p>&nbsp;&nbsp;&nbsp;&nbsp;<b>" . tr('list_of_caches_is_empty') . "</b></p><br />";
    } else {
        $cacheline = '
                <tr>
                    <td class="myneighborhood tab_icon"> <img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" /></td>

                    <td class="myneighborhood tab_date"> {date} </td>
                    <td class="myneighborhood"> <a id="mapcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}" onmouseover="Lite({nn})" onmouseout="Unlite()" maphref="{smallmapurl}">{cachename}</a><span style="font-weight:bold;color: green;">&nbsp;[{toprate}]</span></td>

                    <td class="myneighborhood tab_arrow"> <img src="tpl/stdstyle/images/blue/arrow.png" alt="" title="user" /></td>
                    <td class="myneighborhood tab_user"> <a class="links" href="viewprofile.php?userid={userid}">{username}</a></td>
                </tr>';

        $file_content = '<table  class="myneighborhood">';

        for ($i = 0; $i < $limit; $i++) {
            $record = $dbcLocCache->dbResultFetch();
            $cacheicon = myninc::checkCacheStatusByUser($record, $user_id);
            $thisline = $cacheline;
            $thisline = mb_ereg_replace('{nn}', $i + $marker_offset, $thisline);   //TODO: dynamic number
            $thisline = mb_ereg_replace('{date}', htmlspecialchars(date($dateFormat, strtotime($record['date'])), ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
            $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
            $thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);
            //    $thisline = mb_ereg_replace('{cacheicon_found}',    $cacheicon_found,                                                                   $thisline);
            $thisline = mb_ereg_replace('{toprate}', $record['toprate'], $thisline);
            $thisline = mb_ereg_replace('{smallmapurl}', add_single_marker('TopR', $record['cache_type'], $record['latitude'], $record['longitude']), $thisline);

            $file_content .= $thisline . "\n";
        }
        $file_content .= '</table>';
        $marker_offset = $i + $marker_offset;
    }

    tpl_set_var('top_caches', $file_content);
    
    /* ===================================================================================== */
    /*                          Skrzynki tygodnia                                            */
    /* ===================================================================================== */    
    
    $sqlstr  = "SELECT 
                user.user_id        AS user_id,
                user.username       AS username,
                caches.cache_id     AS cache_id,
                caches.name         AS name,
                cache_titled.date_alg AS date,
                caches.type         AS cache_type
                                   
        FROM cache_titled
        JOIN local_caches on cache_titled.cache_id = local_caches.cache_id
        JOIN caches ON cache_titled.cache_id = caches.cache_id
        JOIN user ON caches.user_id = user.user_id
        WHERE caches.status=1
        ORDER BY cache_titled.date_alg DESC LIMIT 0 , 11";
    
    $dbcLocCache->simpleQuery( $sqlstr );
    
    if ( $dbcLocCache->rowCount() > 9) {
        tpl_set_var('more_titledCaches', '<a class="links" href="cache_titled.php?type=Local">[' . tr("show_more") . '...]</a>');
        $limit = 10;
    } else {
        tpl_set_var('more_titledCaches', '');
        $limit = $dbcLocCache->rowCount();
    }
    
    if ($limit == 0) {
        $Title_titledCaches = "";
        $End_titledCaches = "";
        $file_content = "";
    } else {
        $ntitled_cache = $titled_cache_period_prefix.'_titled_caches';
        $Title_titledCaches = '<br>
        <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/TitledCache.png" class="icon32" alt="" title="Event" align="middle" />&nbsp;'.tr($ntitled_cache).'</p>';
        
        $End_titledCaches = "<br /><br /><br />";
        
        $cacheline = '
                <tr>
                    <td class="myneighborhood tab_icon"> <img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" /></td>
        
                    <td class="myneighborhood tab_date"> {date} </td>
                    <td class="myneighborhood"> <a class="links" href="viewcache.php?cacheid={cacheid}" >{cachename}</a></td>
        
                    <td class="myneighborhood tab_arrow"> <img src="tpl/stdstyle/images/blue/arrow.png" alt="" title="user" /></td>
                    <td class="myneighborhood tab_user"> <a class="links" href="viewprofile.php?userid={userid}">{username}</a></td>
                </tr>';
        $file_content = '<table  class="myneighborhood">';
        
        for ($i = 0; $i < $limit; $i++) {
            $record = $dbcLocCache->dbResultFetch();
            $cacheicon = myninc::checkCacheStatusByUser($record, $user_id);
            $thisline = $cacheline;
            $thisline = mb_ereg_replace('{nn}', $i + $marker_offset, $thisline);   //TODO: dynamic number
            $thisline = mb_ereg_replace('{date}', htmlspecialchars(date($dateFormat, strtotime($record['date'])), ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
            $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
            $thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);
        
            $file_content .= $thisline . "\n";
        }
        $file_content .= '</table>';
        $marker_offset = $i + $marker_offset;
    }

    
    tpl_set_var('Title_titledCaches', $Title_titledCaches);
    tpl_set_var('End_titledCaches', $End_titledCaches);
    tpl_set_var('titledCaches', $file_content);
    
    
    
    unset( $dbcLocCache );
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
