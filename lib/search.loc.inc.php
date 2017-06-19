<?php
/**
 * This script is used (can be loaded) by /search.php
 */

ob_start();

use Utils\Database\XDb;
use lib\Objects\GeoCache\GeoCacheCommons;

global $content, $bUseZip, $hide_coords, $usr, $dbcSearch;
set_time_limit(1800);

require_once ('lib/calculation.inc.php');


$locHead = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<loc version="1.0" src="' . $absolute_server_URI . '">' . "\n";

$locLine = '
<waypoint>
    <name id="{{waypoint}}"><![CDATA[{mod_suffix}{cachename} ' . tr('from') . ' {owner}, {type_text}, {size_text} ({difficulty}/{terrain})]]></name>
    <coord lat="{lat}" lon="{lon}"/>
    <type>Geocache</type>
    <link text="Cache Details">' . $absolute_server_URI . 'viewcache.php?wp={cache_wp}</link>
</waypoint>
';

$locFoot = '</loc>';

$cacheTypeText[1] = "" . tr('cacheType_5') . "";
$cacheTypeText[2] = "" . tr('cacheType_1') . "";
$cacheTypeText[3] = "" . tr('cacheType_2') . "";
$cacheTypeText[4] = "" . tr('cacheType_8') . "";
$cacheTypeText[5] = "" . tr('cacheType_7') . "";
$cacheTypeText[6] = "" . tr('cacheType_6') . "";
$cacheTypeText[7] = "" . tr('cacheType_3') . "";
$cacheTypeText[8] = "" . tr('cacheType_4') . "";
$cacheTypeText[9] = "" . tr('cacheType_9') . "";
$cacheTypeText[10] = "" . tr('cacheType_10') . "";

if ($usr || ! $hide_coords) {
    // prepare the output
    $caches_per_page = 20;

    $query = 'SELECT ';

    if (isset($lat_rad) && isset($lon_rad)) {
        $query .= getCalcDistanceSqlFormula($usr !== false, $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
    } else {
        if ($usr === false) {
            $query .= '0 distance, ';
        } else {
            // get the users home coords
            $rs_coords = XDb::xSql("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`= ? LIMIT 1", $usr['userid']);
            $record_coords = XDb::xFetchArray($rs_coords);

            if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0))) {
                $query .= '0 distance, ';
            } else {
                // TODO: load from the users-profile
                $distance_unit = 'km';

                $lon_rad = $record_coords['longitude'] * 3.14159 / 180;
                $lat_rad = $record_coords['latitude'] * 3.14159 / 180;

                $query .= getCalcDistanceSqlFormula($usr !== false, $record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
            }
            XDb::xFreeResults($rs_coords);
        }
    }

    $query .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`user_id` `user_id`, ';
    if ($usr === false) {
        $query .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id
                    FROM `caches` ';
    } else {
        $query .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`,
                            `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id FROM `caches`
                        LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = ' . $usr['userid'];
    }
    $query .= ' WHERE `caches`.`cache_id` IN (' . $queryFilter . ')';

    $sortby = $options['sort'];
    if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance')) {
        $query .= ' ORDER BY distance ASC';
    } else
        if ($sortby == 'bycreated') {
            $query .= ' ORDER BY date_created DESC';
        } else // by name
{
            $query .= ' ORDER BY name ASC';
        }

    // startat?
    $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
    if (! is_numeric($startat))
        $startat = 0;

    if (isset($_REQUEST['count']))
        $count = $_REQUEST['count'];
    else
        $count = $caches_per_page;
    $maxlimit = 1000000000;

    if ($count == 'max')
        $count = $maxlimit;
    if (! is_numeric($count))
        $count = 0;
    if ($count < 1)
        $count = 1;
    if ($count > $maxlimit)
        $count = $maxlimit;

    $queryLimit = ' LIMIT ' . $startat . ', ' . $count;

    // cleanup (old gpxcontent lingers if gpx-download is cancelled by user)
    $dbcSearch->simpleQuery('DROP TEMPORARY TABLE IF EXISTS `loccontent`');

    $dbcSearch->simpleQuery('CREATE TEMPORARY TABLE `loccontent` ' . $query . $queryLimit);

    $s = $dbcSearch->simpleQuery('SELECT COUNT(*) `count` FROM `loccontent`');
    $rCount = $dbcSearch->dbResultFetchOneRowOnly($s);


    if ($rCount['count'] == 1) {
        $s = $dbcSearch->simpleQuery(
            'SELECT `caches`.`wp_oc` `wp_oc` FROM `loccontent`, `caches`
            WHERE `loccontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
        $rName = $dbcSearch->dbResultFetchOneRowOnly($s);


        $sFilebasename = $rName['wp_oc'];
    } else {
        if ($options['searchtype'] == 'bywatched') {
            $sFilebasename = 'watched_caches';
        } elseif ($options['searchtype'] == 'bylist') {
            $sFilebasename = 'cache_list';
        } else {
            $rsName = XDb::xSql('SELECT `queries`.`name` `name` FROM `queries` WHERE `queries`.`id`= ? LIMIT 1', $options['queryid']);

            $rName = XDb::xFetchArray($rsName);
            XDb::xFreeResults($rsName);

            if (isset($rName['name']) && ($rName['name'] != '')) {
                $sFilebasename = trim($rName['name']);
                $sFilebasename = str_replace(" ", "_", $sFilebasename);
            } else {
                $sFilebasename = "search" . $options['queryid'];
            }
        }
    }

    $bUseZip = ($rCount['count'] > 200000000000);
    $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && $_REQUEST['zip'] == '1');
    $bUseZip = false;
    if ($bUseZip == true) {
        $content = '';
        require_once ($rootpath . 'lib/phpzip/ss_zip.class.php');
        $phpzip = new ss_zip('', 6);
    }

    echo $locHead;

    $s = $dbcSearch->simpleQuery(
        'SELECT `loccontent`.`cache_id` `cacheid`, `loccontent`.`longitude` `longitude`, `loccontent`.`latitude` `latitude`,
                    `loccontent`.cache_mod_cords_id, `caches`.`date_hidden` `date_hidden`,
                    `caches`.`name` `name`, `caches`.`wp_oc` `waypoint`,
                    `cache_type`.`short` `typedesc`, `cache_type`.`id` `type_id`, `cache_size`.`id` `size_id`, `caches`.`terrain` `terrain`,
                    `caches`.`difficulty` `difficulty`, `user`.`username` `username` FROM `loccontent`, `caches`, `cache_type`, `cache_size`, `user`
        WHERE `loccontent`.`cache_id`=`caches`.`cache_id`
            AND `loccontent`.`type`=`cache_type`.`id`
            AND `loccontent`.`size`=`cache_size`.`id`
            AND `loccontent`.`user_id`=`user`.`user_id`');

    while ($r = $dbcSearch->dbResultFetch($s)) {
        $thisline = $locLine;

        $lat = sprintf('%01.5f', $r['latitude']);
        $thisline = mb_ereg_replace('{lat}', $lat, $thisline);

        $lon = sprintf('%01.5f', $r['longitude']);
        $thisline = mb_ereg_replace('{lon}', $lon, $thisline);

        $thisline = mb_ereg_replace('{{waypoint}}', $r['waypoint'], $thisline);
        $thisline = mb_ereg_replace('{cachename}', $r['name'], $thisline);

        // modified coords
        if ($r['cache_mod_cords_id'] > 0) { // check if we have user coords
            $thisline = str_replace('{mod_suffix}', '<F>', $thisline);
        } else {
            $thisline = str_replace('{mod_suffix}', '', $thisline);
        }

        $thisline = mb_ereg_replace('{type_text}', $cacheTypeText[$r['type_id']], $thisline);
        $thisline = mb_ereg_replace('{size_text}',
            tr(GeoCacheCommons::CacheSizeTranslationKey($r['size_id'])), $thisline);

        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
        $thisline = mb_ereg_replace('{difficulty}', $difficulty, $thisline);

        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
        $thisline = mb_ereg_replace('{terrain}', $terrain, $thisline);

        $thisline = mb_ereg_replace('{owner}', $r['username'], $thisline);
        $thisline = mb_ereg_replace('{cache_wp}', $r['waypoint'], $thisline);

        echo $thisline;
        // DO NOT USE HERE:
        // ob_flush();
    }

    echo $locFoot;

    // compress using phpzip
    if ($bUseZip == true) {
        $content = ob_get_clean();
        $phpzip->add_data($sFilebasename . '.loc', $content);
        $out = $phpzip->save($sFilebasename . '.zip', 'b');

        header("content-type: application/zip");
        header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
        echo $out;
        ob_end_flush();
    } else {
        header("Content-type: application/loc");
        header("Content-Disposition: attachment; filename=" . $sFilebasename . ".loc");
        ob_end_flush();
    }

}

exit();
