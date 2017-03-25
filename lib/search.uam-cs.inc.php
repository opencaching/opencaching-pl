<?php

/**
 * This script is used (can be loaded) by /search.php
 */

/**
 * **************************************************************************
 *
 * 8 Naglowek pliku = BB 22 D5 3F + 4 bajty ilosc rekordow 1D 00 00 00 = 29 rekordow
 * Rekordy (kazdy 362 znaki)
 * 8 wspol w uk 1992, 4 bajty Y potem 4 bajty X
 * 1 Priorytet punktu (0-4)
 * 64 nazwa punktu
 * 255 Opis
 * 1 Czy widoczny na mapie (0 nie 1 tak)
 * 1 Numer kategorii (99 uzytkownika) ma byc 99
 * 32 Nazwa kategori usera np Geocaching
 *
 * **************************************************************************
 */

ob_start();

require_once ("./lib/cs2cs.inc.php");

set_time_limit(1800);
global $content, $bUseZip, $hide_coords, $usr, $dbcSearch;

$uamSize[1] = 'n'; // 'Nano'
$uamSize[2] = 'm'; // 'Micro'
$uamSize[3] = 's'; // 'Small'
$uamSize[4] = 'r'; // 'Regular'
$uamSize[5] = 'l'; // 'Large'
$uamSize[6] = 'x'; // 'Large'
$uamSize[7] = '-'; // 'No container'
$uamSize[8] = 'u'; // 'Not specified'

// known by gpx
$uamType[1] = 'O'; // 'Other'
$uamType[2] = 'T'; // 'Traditional'
$uamType[3] = 'M'; // 'Multi'
$uamType[4] = 'V'; // 'Virtual'
$uamType[5] = 'W'; // 'Webcam'
$uamType[6] = 'E'; // 'Event'

// by OC
$uamType[7] = 'Q'; // 'Puzzle / formerly Quiz'
$uamType[8] = 'M'; // 'Moving'
$uamType[9] = 'P'; // 'Podcast'
$uamType[10] = 'U'; // 'Own/user's cache'

if ($usr || ! $hide_coords) {
    // prepare the output
    $caches_per_page = 20;

    $query = 'SELECT ';

    if (isset($lat_rad) && isset($lon_rad)) {
        $query .= getSqlDistanceFormula($lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
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

                $query .= getSqlDistanceFormula($record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
            }
            XDb::xFreeResults($rs_coords);
        }
    }
    $query .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, `caches`.`user_id` `user_id`
                                                                    FROM `caches`
                                                                    WHERE `caches`.`cache_id` IN (' . $queryFilter . ')';

    $sortby = $options['sort'];
    if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance')) {
        $query .= ' ORDER BY distance ASC';
    } else
        if ($sortby == 'bycreated') {
            $query .= ' ORDER BY date_created DESC';
        } else { // by name

            $query .= ' ORDER BY name ASC';
        }

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

    $dbcSearch->simpleQuery('CREATE TEMPORARY TABLE `wptcontent` ' . $query . $queryLimit);

    $s = $dbcSearch->simpleQuery('SELECT COUNT(*) `count` FROM `wptcontent`');
    $rCount = $dbcSearch->dbResultFetchOneRowOnly($s);

    if ($rCount['count'] == 1) {
        $s = $dbcSearch->simpleQuery(
            'SELECT `caches`.`wp_oc` `wp_oc` FROM `wptcontent`, `caches`
            WHERE `wptcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
        $rName = $dbcSearch->dbResultFetchOneRowOnly($s);

        $sFilebasename = $rName['wp_oc'];
    } else
        $sFilebasename = 'search' . $options['queryid'];

    $bUseZip = ($rCount['count'] > 50);
    $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
    $bUseZip = false;
    if ($bUseZip == true) {
        $content = '';
        require_once ($rootpath . 'lib/phpzip/ss_zip.class.php');
        $phpzip = new ss_zip('', 6);
    }

    $s = $dbcSearch->simpleQuery(
        'SELECT `wptcontent`.`cache_id` `cacheid`, `wptcontent`.`longitude` `longitude`, `wptcontent`.`latitude` `latitude`, `caches`.`date_hidden` `date_hidden`,
                `caches`.`name` `name`, `caches`.`wp_oc` `wp_oc`, `cache_type`.`short` `typedesc`, `cache_size`.`pl` `sizedesc`,
                `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username` , `caches`.`size` `size`,
                `caches`.`type` `type`
        FROM `wptcontent`, `caches`, `cache_type`, `cache_size`, `user`
        WHERE `wptcontent`.`cache_id`=`caches`.`cache_id` AND `wptcontent`.`type`=`cache_type`.`id`
            AND `wptcontent`.`size`=`cache_size`.`id` AND `wptcontent`.`user_id`=`user`.`user_id`');

    echo pack("ccccl", 0xBB, 0x22, 0xD5, 0x3F, $rCount['count']);

    while ($r = $dbcSearch->dbResultFetch($s)) {
        $lat = $r['latitude'];
        $lon = $r['longitude'];
        $utm = cs2cs_1992($lat, $lon);
        $x = (int) $utm[0];
        $y = (int) $utm[1];
        $name = convert_string($r['name']);
        $username = convert_string($r['username']);
        $type = $uamType[$r['type']];
        $size = $uamSize[$r['size']];
        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
        $cacheid = $r['wp_oc'];

        $descr = "$name by $username [$difficulty/$terrain]";
        $poiname = "$cacheid $type$size";

        $record = pack("llca64a255cca32", $x, $y, 2, $poiname, $descr, 1, 99, 'Geocaching');

        echo $record;
        // DO NOT USE HERE:
        // ob_flush();
    }
    XDb::xSql('DROP TABLE `wptcontent` ');

    // compress using phpzip
    if ($bUseZip == true) {
        $content = ob_get_clean();
        $phpzip->add_data($sFilebasename . '.uam', $content);
        $out = $phpzip->save($sFilebasename . '.zip', 'b');
        
        header('content-type: application/zip');
        header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
        echo $out;
        ob_end_flush();
    } else {
        header('Content-type: application/uam');
        header('Content-Disposition: attachment; filename=' . $sFilebasename . '.uam');
        ob_end_flush();
    }

}

exit();
