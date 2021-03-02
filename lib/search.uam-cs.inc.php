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

use src\Utils\Text\TextConverter;
use src\Models\ApplicationContainer;

ob_start();

require_once (__DIR__.'/../lib/calculation.inc.php');

set_time_limit(1800);
global $content, $bUseZip, $hide_coords, $dbcSearch;

$loggedUser = ApplicationContainer::GetAuthorizedUser();

$uamSize[1] = 'u'; // 'Not specified'
$uamSize[2] = 'm'; // 'Micro'
$uamSize[3] = 's'; // 'Small'
$uamSize[4] = 'r'; // 'Regular'
$uamSize[5] = 'l'; // 'Large'
$uamSize[6] = 'x'; // 'Large'
$uamSize[7] = '-'; // 'No container'
$uamSize[8] = 'n'; // 'Nano'

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

if ($loggedUser || ! $hide_coords) {
    // prepare the output
    $caches_per_page = 20;

    $query = 'SELECT ';

    if (isset($lat_rad) && isset($lon_rad)) {
        $query .= getSqlDistanceFormula($lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
    } else {
        if (!$loggedUser) {
            $query .= '0 distance, ';
        } else {
            // get the users home coords
            $rs_coords = XDb::xSql(
                "SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`= ? LIMIT 1", $loggedUser->getUserId());
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
        require_once (__DIR__.'/../src/Libs/PhpZip/ss_zip.class.php');
        $phpzip = new ss_zip('', 6);
    }

    $s = $dbcSearch->simpleQuery(
        'SELECT `wptcontent`.`cache_id` `cacheid`, `wptcontent`.`longitude` `longitude`, `wptcontent`.`latitude` `latitude`, `caches`.`date_hidden` `date_hidden`,
                `caches`.`name` `name`, `caches`.`wp_oc` `wp_oc`, `cache_type`.`short` `typedesc`,
                `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username` , `caches`.`size` `size`,
                `caches`.`type` `type`
        FROM `wptcontent`, `caches`, `cache_type`, `user`
        WHERE `wptcontent`.`cache_id`=`caches`.`cache_id` AND `wptcontent`.`type`=`cache_type`.`id`
            AND `wptcontent`.`user_id`=`user`.`user_id`');

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


function cs2cs_core2($lat, $lon, $to) {

    $descriptorspec = array(
        0 => array("pipe", "r"),     // stdin is a pipe that the child will read from
        1 => array("pipe", "w"),     // stdout is a pipe that the child will write to
        2 => array("pipe", "w")      // stderr is a pipe that the child will write to
    );

    if (mb_eregi('^[a-z0-9_ ,.\+\-=]*$', $to) == 0) {
        die("invalid arguments in command: " . $to ."\n");
    }

    $command = "cs2cs" . " +proj=latlong +ellps=WGS84 +to " . $to;

    $process = proc_open($command, $descriptorspec, $pipes);

    if (is_resource($process)) {

        fwrite($pipes[0], $lon . " " . $lat);
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        //
        // $procstat = proc_get_status($process);
        //
        // neither proc_close nor proc_get_status return reasonable results with PHP5 and linux 2.6.11,
        // see http://bugs.php.net/bug.php?id=32533
        //
        // as temporary (?) workaround, check stderr output.
        // (Vinnie, 2006-02-09)

        if ($stderr) {
            die("proc_open() failed:<br />command='$command'<br />stderr='" . $stderr . "'");
        }

        proc_close($process);

        return mb_split("\t|\n| ", TextConverter::mb_trim($stdout));

    } else {
        die("proc_open() failed, command=$command\n");
    }
}

function cs2cs_1992($lat, $lon) {
    return cs2cs_core2($lat, $lon, "+proj=tmerc +k=0.9993 +ellps=GRS80 +lat_0=0 +lon_0=19 +x_0=500000 +y_0=-5300000 +units=m");
}
