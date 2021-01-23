<?php
/**
 * This script is used (can be loaded) by /search.php
 */

ob_start();

use src\Utils\Database\XDb;
use src\Utils\I18n\I18n;
use src\Models\ApplicationContainer;

global $content, $bUseZip, $hide_coords, $dbcSearch;

set_time_limit(1800);

require_once (__DIR__.'/../lib/calculation.inc.php');

$loggedUser = ApplicationContainer::GetAuthorizedUser();

$wptSize[1] = 'Not specified';
$wptSize[2] = 'Micro';
$wptSize[3] = 'Small';
$wptSize[4] = 'Regular';
$wptSize[5] = 'Large';
$wptSize[6] = 'Extra Large';
$wptSize[7] = 'No container';
$wptSize[8] = 'Nano';

$wptType[1] = 'Unknown Cache';
$wptType[2] = 'Traditional Cache';
$wptType[3] = 'Multi-Cache';
$wptType[4] = 'Virtual Cache';
$wptType[5] = 'Webcam Cache';
$wptType[6] = 'Event Cache';
$wptType[7] = 'Puzzle';
$wptType[8] = 'Moving Cache';
$wptType[9] = 'Podcast';
$wptType[10] = 'Own Cache';

if( $loggedUser || !$hide_coords ) {
    //prepare the output
    $caches_per_page = 20;

    $query = 'SELECT ';

    if (isset($lat_rad) && isset($lon_rad)) {
        $query .= getCalcDistanceSqlFormula(is_object($loggedUser), $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
    } elseif (!$loggedUser) {
        $query .= '0 distance, ';
    } else {
        //get the users home coords
        $rs_coords = XDb::xSql(
            "SELECT `latitude`, `longitude` FROM `user`
            WHERE `user_id`= ? ", $loggedUser->getUserId());

        $record_coords = XDb::xFetchArray($rs_coords);

        if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0))) {
            $query .= '0 distance, ';
        } else {
            //TODO: load from the users-profile
            $distance_unit = 'km';
            $lon_rad = $record_coords['longitude'] * 3.14159 / 180;
            $lat_rad = $record_coords['latitude'] * 3.14159 / 180;

            $query .= getCalcDistanceSqlFormula(
                is_object($loggedUser), $record_coords['longitude'], $record_coords['latitude'],
                0, $multiplier[$distance_unit]) . ' `distance`, ';
        }
        XDb::xFreeResults($rs_coords);
    }

    $query .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`,
        `caches`.`user_id` `user_id`, ';
    if (!$loggedUser) {
        $query .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id FROM `caches` ';
    } else {
        $query .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`,
            `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.latitude,0) as cache_mod_cords_id FROM `caches`
            LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = '
            . $loggedUser->getUserId();
    }
    $query .= ' WHERE `caches`.`cache_id` IN (' . $queryFilter . ')';

    $sortby = $options['sort'];
    if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance')) {
        $query .= ' ORDER BY distance ASC';
    } elseif ($sortby == 'bycreated') {
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

    // cleanup (old gpxcontent lingers if gpx-download is cancelled by user)
    $dbcSearch->simpleQuery( 'DROP TEMPORARY TABLE IF EXISTS `wptcontent`');

    // create temporary table
    $dbcSearch->simpleQuery( 'CREATE TEMPORARY TABLE `wptcontent` ' . $query . $queryLimit);

    $s = $dbcSearch->simpleQuery( 'SELECT COUNT(*) `count` FROM `wptcontent`');
    $rCount = $dbcSearch->dbResultFetchOneRowOnly($s);

    if ($rCount['count'] == 1) {
        $s = $dbcSearch->simpleQuery(
            'SELECT `caches`.`wp_oc` `wp_oc` FROM `wptcontent`, `caches`
            WHERE `wptcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
        $rName = $dbcSearch->dbResultFetchOneRowOnly($s);

        $sFilebasename = $rName['wp_oc'];
    } elseif ($options['searchtype'] == 'bywatched') {
        $sFilebasename = 'watched_caches';
    } elseif ($options['searchtype'] == 'bylist') {
        $sFilebasename = 'cache_list';
    } else {
        $rsName = XDb::xSql(
            'SELECT `queries`.`name` `name` FROM `queries`
            WHERE `queries`.`id`= ? LIMIT 1', $options['queryid']);
        $rName = XDb::xFetchArray($rsName);
        XDb::xFreeResults($rsName);
        if (isset($rName['name']) && ($rName['name'] != '')) {
            $sFilebasename = trim(convert_string($rName['name']));
            $sFilebasename = str_replace(" ", "_", $sFilebasename);
        } else {
            $sFilebasename = 'search' . $options['queryid'];
        }
    }

    $bUseZip = ($rCount['count'] > 50);
    $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
    $bUseZip = false;
    if ($bUseZip == true) {
        $content = '';
        require_once(__DIR__.'/../src/Libs/PhpZip/ss_zip.class.php');
        $phpzip = new ss_zip('',6);
    }

    $stmt = XDb::xSql(
        'SELECT `wptcontent`.`cache_id` `cacheid`, IF(wptcontent.cache_id IN
                (SELECT cache_id FROM cache_logs WHERE deleted=0 AND user_id='.$loggedUser->getUserId().' AND (type=1 OR type=8)),1,0)
                as found, `wptcontent`.`longitude` `longitude`, `wptcontent`.`latitude` `latitude`, `wptcontent`.cache_mod_cords_id,
                `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`wp_oc` `wp_oc`, `cache_type`.`short` `typedesc`,
                `wptcontent`.`size` `size`,`caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username` ,
                `caches`.`status` `status`, `caches`.`type` `type` FROM `wptcontent`, `caches`, `cache_type`, `user`
        WHERE `wptcontent`.`cache_id`=`caches`.`cache_id`
            AND `wptcontent`.`type`=`cache_type`.`id`
            AND `wptcontent`.`user_id`=`user`.`user_id`' );

    echo "OziExplorer Waypoint File Version 1.1\r\n";
    echo "WGS 84\r\n";
    echo "Reserved 2\r\n";
    echo "Reserved 3\r\n";

    while($r = XDb::xFetchArray($stmt) ) {
        $lat = sprintf('%01.6f', $r['latitude']);
        $lon = sprintf('%01.6f', $r['longitude']);

        //modified coords
        if ($r['cache_mod_cords_id'] > 0) {  //check if we have user coords
            $r['mod_suffix']= '[F]';
        } else {
            $r['mod_suffix']= '';
        }

        $name = convert_string(str_replace(',','',$r['mod_suffix'].$r['name']));
        $username = convert_string(str_replace(',','',$r['username']));
        $type = $wptType[$r['type']];
        $size = $wptSize[$r['size']];
        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
        $cacheid = $r['wp_oc'];
        $id = $r['cacheid'];

        $date_hidden = $r['date_hidden'];
        $userid = XDb::xMultiVariableQueryValue("SELECT user_id FROM caches WHERE cache_id = :1 LIMIT 1",0, $id);

        $kolor = 16776960;
        if ($loggedUser && $userid == $loggedUser->getUserId()) {
            $kolor = 65280;
        }
        if ($r['status'] == 3 || $r['status'] == 2) {
            $kolor = 255;
        }
        if ($r['found']) {
            $kolor = 65535;
        }
        $sss=

        $r['ozi_filips']= XDb::xMultiVariableQueryValue(
            "SELECT ozi_filips FROM user WHERE user_id= :1 LIMIT 1", null, $loggedUser->getUserId());

        if($r['ozi_filips']!=""||$r['ozi_filips']!=null) {
            $attach = $r['ozi_filips']."\\op\\".$r['wp_oc'][2]."\\".$r['wp_oc'][3]."\\".$r['wp_oc'][4].$r['wp_oc'][5].".html";
        } else {
            $attach = "";
        }
        // remove double slashes
        $attach = str_replace("\\\\", "\\", $attach);
        $line = "$cacheid / D:$difficulty / T:$terrain / Size:$size";

        $record  = "-1,$name,$lat,$lon,,117,1,4,0,$kolor,$line,0,0,0, -777,8,0,17,0,10.0,2,$attach,,\r\n";

        echo $record;
        // DO NOT USE HERE:
        // ob_flush();
    }

    // compress using phpzip
    if ($bUseZip == true) {
        $content = ob_get_clean();
        $phpzip->add_data($sFilebasename . '.wpt', $content);
        $out = $phpzip->save($sFilebasename . '.zip', 'b');

        header('content-type: application/zip');
        header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
        echo $out;
        ob_end_flush();
    } else {
        header('Content-type: application/wpt');
        header('Content-Disposition: attachment; filename=' . $sFilebasename . '.wpt');
        ob_end_flush();
    }

    exit;
}
