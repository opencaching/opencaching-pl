<?php
/**
 * This script is used (can be loaded) by /search.php
 */

ob_start();

use src\Utils\Database\XDb;
use src\Models\ApplicationContainer;

global $content, $hide_coords, $dbcSearch;
set_time_limit(1800);

$loggedUser = ApplicationContainer::GetAuthorizedUser();

require_once (__DIR__.'/../lib/calculation.inc.php');

$ovlLine = "[Symbol {symbolnr1}]\r\nTyp=6\r\nGroup=1\r\nWidth=20\r\nHeight=20\r\nDir=100\r\nArt=1\r\nCol=3\r\nZoom=1\r\nSize=103\r\nArea=2\r\nXKoord={lon}\r\nYKoord={lat}\r\n[Symbol {symbolnr2}]\r\nTyp=2\r\nGroup=1\r\nCol=3\r\nArea=1\r\nZoom=1\r\nSize=130\r\nFont=1\r\nDir=100\r\nXKoord={lonname}\r\nYKoord={latname}\r\nText={mod_suffix}{cachename}\r\n";
$ovlFoot = "[Overlay]\r\nSymbols={symbolscount}\r\n";

if( $loggedUser || !$hide_coords ) {
    //prepare the output
    $caches_per_page = 20;

    $query = 'SELECT ';

    if (isset($lat_rad) && isset($lon_rad)) {
        $query .= getSqlDistanceFormula($lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
    } else {
        if (!$loggedUser) {
            $query .= '0 distance, ';
        } else {
            //get the users home coords
            $rs_coords = XDb::xSql(
                "SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`= ? ", $loggedUser->getUserId());

            $record_coords = XDb::xFetchArray($rs_coords);

            if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0))) {
                $query .= '0 distance, ';
            } else {
                //TODO: load from the users-profile
                $distance_unit = 'km';

                $lon_rad = $record_coords['longitude'] * 3.14159 / 180;
                $lat_rad = $record_coords['latitude'] * 3.14159 / 180;

                $query .= getSqlDistanceFormula($record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
            }
            XDb::xFreeResults($rs_coords);
        }
    }
    if (!$loggedUser) {
        $query .= ' `caches`.`cache_id`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id, `caches`.`type` `type`
                FROM `caches` ';
    } else {
        $query .= ' `caches`.`cache_id`, IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`,
                        `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.latitude,0) as cache_mod_cords_id, `caches`.`type` `type` FROM `caches`
                    LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = '
                        . $loggedUser->getUserId();
    }
    $query .= ' WHERE `caches`.`cache_id` IN (' . $queryFilter . ')';

    $sortby = $options['sort'];
    if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance')) {
        $query .= ' ORDER BY distance ASC';
    } else if ($sortby == 'bycreated') {
        $query .= ' ORDER BY date_created DESC';
    } else { // by name
        $query .= ' ORDER BY name ASC';
    }

    if (isset($_REQUEST['startat'])) {
        $startat = XDb::quoteOffset($_REQUEST['startat']);
    } else {
        $startat = 0;
    }

    if (isset($_REQUEST['count'])) {
        $count = XDb::quoteLimit($_REQUEST['count']);
    } else {
        $count = $caches_per_page;
    }

    $queryLimit = ' LIMIT ' . $startat . ', ' . $count;

    $dbcSearch->simpleQuery( 'CREATE TEMPORARY TABLE `ovlcontent` ' . $query . $queryLimit);

    $s = $dbcSearch->simpleQuery( 'SELECT COUNT(*) AS `count` FROM `ovlcontent`');
    $rCount = $dbcSearch->dbResultFetchOneRowOnly($s);

    if ($rCount['count'] == 1) {
        $s = $dbcSearch->simpleQuery(
            'SELECT `caches`.`wp_oc` `wp_oc` FROM `ovlcontent`, `caches`
            WHERE `ovlcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
        $rName = $rCount = $dbcSearch->dbResultFetchOneRowOnly($s);


        $sFilebasename = $rName['wp_oc'];
    } else {
        if ($options['searchtype'] == 'bywatched') {
            $sFilebasename = 'watched_caches';
        } elseif ($options['searchtype'] == 'bylist') {
            $sFilebasename = 'cache_list';
        } else {
            $rsName = XDb::xSql(
                'SELECT `queries`.`name` `name` FROM `queries` WHERE `queries`.`id`= ? LIMIT 1', $options['queryid']);

            $rName = XDb::xFetchArray($rsName);
            XDb::xFreeResults($rsName);
            if (isset($rName['name']) && ($rName['name'] != '')) {
                $sFilebasename = trim(convert_string($rName['name']));
                $sFilebasename = str_replace(" ", "_", $sFilebasename);
            } else {
                $sFilebasename = 'search' . $options['queryid'];
            }
        }
    }

    $nr = 1;
    $s = $dbcSearch->simpleQuery(
        'SELECT `ovlcontent`.`cache_id` `cacheid`, `ovlcontent`.`longitude` `longitude`,
                `ovlcontent`.`latitude` `latitude`, `ovlcontent`.cache_mod_cords_id,
                `caches`.`name` `name`, `ovlcontent`.`type` `type` FROM `ovlcontent`, `caches`
        WHERE `ovlcontent`.`cache_id`=`caches`.`cache_id`');
    while($r = $dbcSearch->dbResultFetch($s)) {
        $thisline = $ovlLine;

        $lat = sprintf('%01.5f', $r['latitude']);
        $thisline = mb_ereg_replace('{lat}', $lat, $thisline);
        $thisline = mb_ereg_replace('{latname}', $lat, $thisline);

        $lon = sprintf('%01.5f', $r['longitude']);
        $thisline = mb_ereg_replace('{lon}', $lon, $thisline);
        $thisline = mb_ereg_replace('{lonname}', $lon, $thisline);
        //modified coords
        if ($r['cache_mod_cords_id'] > 0) {  //check if we have user coords
            $thisline = str_replace('{mod_suffix}', '<F>', $thisline);
        } else {
            $thisline = str_replace('{mod_suffix}', '', $thisline);
        }

        $thisline = mb_ereg_replace('{cachename}', convert_string($r['name']), $thisline);
        $thisline = mb_ereg_replace('{symbolnr1}', $nr, $thisline);
        $thisline = mb_ereg_replace('{symbolnr2}', $nr + 1, $thisline);

        $nr += 2;

        echo $thisline;
        // DO NOT USE HERE:
        // ob_flush();
    }

    $ovlFoot = mb_ereg_replace('{symbolscount}', $nr - 1, $ovlFoot);
    echo $ovlFoot;

    // compress using phpzip
    header("Content-type: application/ovl");
    header("Content-Disposition: attachment; filename=" . $sFilebasename . ".ovl");
    ob_end_flush();
}

exit();
