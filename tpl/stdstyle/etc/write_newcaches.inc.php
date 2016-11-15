<?php

use Utils\Database\XDb;
setlocale(LC_TIME, 'pl_PL.UTF-8');

global $lang, $rootpath,$googlemap_key;

if (!isset($rootpath))
    $rootpath = __DIR__ . '/../../../';

//include template handling
require_once($rootpath . 'lib/common.inc.php');
require_once($rootpath . 'lib/cache_icon.inc.php');

// Map parameters
$map_center_lat = $main_page_map_center_lat;
$map_center_lon = $main_page_map_center_lon;
$map_zoom = $main_page_map_zoom;
$map_width = $main_page_map_width;
$map_height = $main_page_map_height;

// Read coordinates of the newest caches
$markerpositions = get_marker_positions();

// Generate include file for map with new caches
$google_map = sprintf("https://maps.google.com/maps/api/staticmap?center=%F,%F&zoom=%d&size=%dx%d&maptype=roadmap&key=%s", $map_center_lat, $map_center_lon, $map_zoom, $map_width, $map_height, $googlemap_key);
$file_content = '<img src="' . $google_map . '" id="main-cachemap" alt="{{map}}" />';

// Calculate positions for small and large images highlighting recent caches and events
$markers = $markerpositions['markers'];
$small_markers = '';
$big_markers = '';
foreach ($markers as $i => $marker) {
    $markerposleft = lon_offset($marker['lon'], $map_center_lon, $map_width, $map_zoom);
    $markerpostop = lat_offset($marker['lat'], $map_center_lat, $map_height, $map_zoom);
    $type = strtoupper(typeToLetter($marker['type']));
    if (strcmp($type, 'E') == 0) {
        $small_marker = 'mark-small-orange.png';
        $big_marker = 'marker-orangeE.png';
    } else {
        $small_marker = 'mark-small-blue.png';
        $big_marker = 'marker-blue' . $type . '.png';
    }
    $small_markers .= '<img id="smallmark' . $marker['nn'] . '" style="position: absolute; left: ' . ($markerposleft - 7) . 'px; top: ' . ($markerpostop - 21) . 'px; border: none; background-color: transparent;" alt="" src="/images/markers/' . $small_marker . '">';
    $big_markers .= '<img id="bigmark' . $marker['nn'] . '" style="position: absolute; left: ' . ($markerposleft - 11) . 'px; top: ' . ($markerpostop - 36) . 'px; border: none; background-color: transparent; visibility: hidden;" alt="" src="/images/markers/' . $big_marker . '">';
}

$file_content .= $small_markers . $big_markers;
$n_file = fopen($dynstylepath . "main_cachemap.inc.php", 'w');
fwrite($n_file, $file_content);
fclose($n_file);

//start_newcaches.include
$rs = XDb::xSql(
    "SELECT `user`.`user_id` `user_id`, `user`.`username` `username`, `caches`.`cache_id` `cache_id`,
            `caches`.`name` `name`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`,
            `caches`.`wp_oc` `wp`, `caches`.`date_hidden` `date_hidden`, `caches`.`date_created` `date_created`,
            IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`,
            `caches`.`date_created`) AS `date`, `caches`.`country` `country`, `caches`.`difficulty` `difficulty`,
            `caches`.`terrain` `terrain`, `cache_type`.`icon_large` `icon_large`,
            IFNULL(`cache_location`.`adm1`, '') AS `adm1`,
            IFNULL(`cache_location`.`code1`, '') AS `code1`,
            IFNULL(`cache_location`.`adm2`, '') AS `adm2`,
            IFNULL(`cache_location`.`adm3`, '') AS `adm3`,
            IFNULL(`cache_location`.`code3`, '') AS `code3`,
            IFNULL(`cache_location`.`adm4`, '') AS `adm4`
    FROM (`caches` LEFT JOIN `cache_location` ON `caches`.`cache_id` = `cache_location`.`cache_id`)
        INNER JOIN countries ON (caches.country = countries.short), `cache_type`, `user`
    WHERE `caches`.`user_id`=`user`.`user_id`
        AND `caches`.`type`!=6
        AND `caches`.`status`=1
        AND `caches`.`type`=`cache_type`.`id`
        AND `caches`.`date_hidden` <= NOW()
        AND `caches`.`date_created` <= NOW()
    ORDER BY IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) DESC, `caches`.`cache_id` DESC
    LIMIT 0 , 10");


$cacheline = '<li class="newcache_list_multi" style="margin-bottom:8px;">' .
        '<img src="{cacheicon}" class="icon16" alt="" title="Cache" />&nbsp;{date}&nbsp;' .
        '<a id="newcache{nn}" class="links" href="viewcache.php?wp={wp}" onmouseover="Lite({nn})" onmouseout="Unlite({nn})">{cachename}</a>&nbsp;
            hidden_by&nbsp;<a class="links" href="viewprofile.php?userid={userid}">{username}</a><br/>' .
        '<p class="content-title-noshade"><b>{kraj} {dziubek} {woj}</b></p></li>';

$file_content = '<ul style="font-size: 11px;">';

$i = -1;
while( $record = XDb::xFetchArray($rs) ){
    $i++;

    if (substr(@tr($record['code1']), -5) == '-todo')
        $countryTranslation = $record['adm1'];
    else
        $countryTranslation = tr($record['code1']);
    $regionTranslation = $record['adm3'];

    if ($record['adm3'] != "")
        $dziubek = ">";
    else
        $dziubek = "";

    $cacheicon = 'tpl/stdstyle/images/' . getSmallCacheIcon($record['icon_large']);

    $thisline = $cacheline;
    $thisline = mb_ereg_replace('{nn}', $i, $thisline);
    $thisline = mb_ereg_replace('{kraj}', $countryTranslation, $thisline);
    $thisline = mb_ereg_replace('{woj}', $regionTranslation, $thisline);
    $thisline = mb_ereg_replace('{dziubek}', $dziubek, $thisline);
    $thisline = mb_ereg_replace('{date}', htmlspecialchars(date("d-m-Y", strtotime($record['date'])), ENT_COMPAT, 'UTF-8'), $thisline);
    $thisline = mb_ereg_replace('{wp}', urlencode($record['wp']), $thisline);
    $thisline = mb_ereg_replace('{cache_count}', $i, $thisline);
    $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
    $thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
    $thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
    $thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);

    $file_content .= $thisline . "\n";
}

$file_content .= '</ul>';
$n_file = fopen($dynstylepath . "start_newcaches.inc.php", 'w');
fwrite($n_file, $file_content);
fclose($n_file);

//nextevents.include
$rs = XDb::xSql(
    "SELECT `user`.`user_id` `user_id`, `user`.`username` `username`,
            `caches`.`cache_id` `cache_id`, `caches`.`wp_oc` `wp`, `caches`.`name` `name`,
            `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`,
            `caches`.`date_created` `date_created`, `caches`.`country` `country`,
            `caches`.`difficulty` `difficulty`, `caches`.`terrain` `terrain`,
            `caches`.`date_hidden`,
            IFNULL(`cache_location`.`adm1`, '') AS `adm1`,
            IFNULL(`cache_location`.`code1`, '') AS `code1`,
            IFNULL(`cache_location`.`adm2`, '') AS `adm2`,
            IFNULL(`cache_location`.`adm3`, '') AS `adm3`,
            IFNULL(`cache_location`.`code3`, '') AS `code3`,
            IFNULL(`cache_location`.`adm4`, '') AS `adm4`
    FROM (`caches` LEFT JOIN `cache_location` ON `caches`.`cache_id` = `cache_location`.`cache_id`)
        INNER JOIN countries ON (caches.country = countries.short), `user`
    WHERE `user`.`user_id`=`caches`.`user_id`
        AND `caches`.`date_hidden` >= curdate()
        AND `caches`.`type` = 6
        AND `caches`.`status` = 1
    ORDER BY `date_hidden` ASC
    LIMIT 0 , 10");

$file_content = '';
if ( ! $record = XDb::xFetchArray($rs)) {
    $file_content = '';
} else {
    $cacheline = '<li class="newcache_list_multi" style="margin-bottom:8px;"><img src="{cacheicon}" class="icon16" alt="" title="Cache" />&nbsp;{date}&nbsp;<a id="newcache{nn}" class="links" href="viewcache.php?wp={wp}" onmouseover="Lite({nn})" onmouseout="Unlite({nn})">{cachename}</a>&nbsp;hidden_by&nbsp;<a class="links" href="viewprofile.php?userid={userid}">{username}</a><br/><p class="content-title-noshade"><b>{kraj} {dziubek} {woj}</b></p></li>';
    $file_content = '<ul style="font-size: 11px;">';

    $i=-1;
    do{
        $i++;
        $dziubek2 = "";
        if (substr(@tr($record['code3']), -5) == '-todo')
            $regionTranslation = $record['adm3'];
        else
            $regionTranslation = tr($record['code3']);
        if (substr(@tr($record['code1']), -5) == '-todo')
            $countryTranslation = $record['adm1'];
        else
            $countryTranslation = tr($record['code1']);

        if ($record['adm3'] != "") {
            $dziubek = ">";
        } else {
            $dziubek = "";
        }

        $thisline = $cacheline;
        $thisline = mb_ereg_replace('{nn}', $i + $markerpositions['plain_cache_num'], $thisline);
        $thisline = mb_ereg_replace('{kraj}', $countryTranslation, $thisline);
        $thisline = mb_ereg_replace('{woj}', $regionTranslation, $thisline);
        $thisline = mb_ereg_replace('{dziubek}', $dziubek, $thisline);
        $thisline = mb_ereg_replace('{date}', htmlspecialchars(date("d-m-Y", strtotime($record['date_hidden'])), ENT_COMPAT, 'UTF-8'), $thisline);
        $thisline = mb_ereg_replace('{wp}', urlencode($record['wp']), $thisline);
        $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
        $thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
        $thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
        $thisline = mb_ereg_replace('{cacheicon}', 'tpl/stdstyle/images/cache/22x22-event.png', $thisline);

        $file_content .= $thisline . "\n";

    }while( $record = XDb::xFetchArray($rs) );

    $file_content .= '</ul>';
}

$n_file = fopen($dynstylepath . "nextevents.inc.php", 'w');
fwrite($n_file, $file_content);
fclose($n_file);

function compare_lats($a, $b)
{
    return $a['lat'] < $b['lat'];
}

function get_marker_positions()
{
    $markerpos = array();
    $markers = array();

    $rs = XDb::xSql(
        "SELECT `cache_id`, `longitude`, `latitude`, `type`
        FROM `caches`
        WHERE `type` != 6
            AND `status` = 1
            AND `date_hidden` <= NOW()
            AND `date_created` <= NOW()
        ORDER BY IF((`date_hidden`>`date_created`), `date_hidden`, `date_created`) DESC, `cache_id` DESC
        LIMIT 0, 10");

    $i= -1;
    while( $record = XDb::xFetchArray($rs) ){
        $i++;
        $lat = $record['latitude'];
        $lon = $record['longitude'];
        $type = $record['type'];
        $markers[] = array('lat' => $lat, 'lon' => $lon, 'type' => $type, 'nn' => $i);
    }

    $markerpos['plain_cache_num'] = count($markers);

    $rs = XDb::xSql(
        "SELECT `cache_id`, `longitude`, `latitude`, `type`
        FROM `caches`
        WHERE `date_hidden` >= curdate()
            AND `type` = 6 AND `status` = 1
        ORDER BY `date_hidden` ASC
        LIMIT 0, 10");

    $i = -1;
    while( $record = XDb::xFetchArray($rs) ){
        $i++;
        $lat = $record['latitude'];
        $lon = $record['longitude'];
        $type = $record['type'];
        $markers[] = array('lat' => $lat, 'lon' => $lon, 'type' => $type, 'nn' => $i + $markerpos['plain_cache_num']);
    }

    // Sort all markers by latitude (starting from top) - this makes them overlap nicer
    usort($markers, "compare_lats");

    $markerpos['markers'] = $markers;

    return $markerpos;
}

// Convert coordinates to pixels in Google coordinate system (spherical Mercator) at provided zoom level
function LToX($x, $offset, $radius)
{
    return round($offset + $radius * $x * M_PI / 180);
}

function LToY($y, $offset, $radius)
{
    return round($offset - $radius * log((1 + sin($y * M_PI / 180)) / (1 - sin($y * M_PI / 180))) / 2);
}

function lon_offset($currlon, $baselon, $imgwidth, $zoom_lev)
{
    $offset = 268435456 >> (21 - $zoom_lev); // 268435456 --> half of the earth circumference's in pixels at zoom level 21
    $radius = $offset / M_PI;

    return LToX($currlon, $offset, $radius) - LToX($baselon, $offset, $radius) + ($imgwidth / 2);
}

function lat_offset($currlat, $baselat, $imgheight, $zoom_lev)
{
    $offset = 268435456 >> (21 - $zoom_lev); // 268435456 --> half of the earth circumference's in pixels at zoom level 21
    $radius = $offset / M_PI;

    return LToY($currlat, $offset, $radius) - LToY($baselat, $offset, $radius) + ($imgheight / 2);
}


