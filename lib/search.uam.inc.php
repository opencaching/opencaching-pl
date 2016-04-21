<?php

/**
 * This script is used (can be loaded) by /search.php
 */

use Utils\Database\XDb;

set_time_limit(1800);
global $content, $bUseZip, $hide_coords, $usr, $dbcSearch;

$uamSize[1] = 'o'; // 'Other'
$uamSize[2] = 'm'; // 'Micro'
$uamSize[3] = 's'; // 'Small'
$uamSize[4] = 'r'; // 'Regular'
$uamSize[5] = 'l'; // 'Large'
$uamSize[6] = 'l'; // 'Large'
$uamSize[7] = 'v'; // 'Virtual'

// known by gpx
$uamType[1] = 'O'; // 'Other'
$uamType[2] = 'T'; // 'Traditional'
$uamType[3] = 'M'; // 'Multi'
$uamType[4] = 'V'; // 'Virtual'
$uamType[5] = 'W'; // 'Webcam'
$uamType[6] = 'E'; // 'Event'

// unknown ... converted
$uamType[7] = 'Q'; // 'Quiz'
$uamType[8] = 'M'; // 'Math'
$uamType[9] = 'M'; // 'Mobile'
$uamType[10] = 'D'; // 'Drive-in'

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
    $query .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`user_id` `user_id`, ';
    if ($usr === false) {
        $query .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id FROM `caches` ';
    } else {
        $query .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`, `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id FROM `caches`
                        LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = ' . $usr['userid'];
    }

    $query .= '   WHERE `caches`.`cache_id` IN (' . $queryFilter . ')';

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
    $dbcSearch->simpleQuery('DROP TEMPORARY TABLE IF EXISTS `wptcontent`');

    $dbcSearch->simpleQuery('CREATE TEMPORARY TABLE `wptcontent` ' . $query . $queryLimit);

    $s = $dbcSearch->simpleQuery('SELECT COUNT(*) `count` FROM `wptcontent`');
    $rCount = $dbcSearch->dbResultFetchOneRowOnly($s);

    if ($rCount['count'] == 1) {
        $s = $dbcSearch->simpleQuery(
            'SELECT `caches`.`wp_oc` `wp_oc` FROM `wptcontent`, `caches`
            WHERE `wptcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
        $rName = $rCount = $dbcSearch->dbResultFetchOneRowOnly($s);

        $sFilebasename = $rName['wp_oc'];
    } else {
        if ($options['searchtype'] == 'bywatched') {
            $sFilebasename = 'watched_caches';
        } elseif ($options['searchtype'] == 'bylist') {
            $sFilebasename = 'cache_list';
        } else {
            $rsName = XDb::xSql(
                'SELECT `queries`.`name` `name` FROM `queries` WHERE `queries`.`id`= ? LIMIT 1',
                $options['queryid']);

            $rName = XDb::xFetchArray($rsName);
            XDb::xFreeResults($rsName);
            if (isset($rName['name']) && ($rName['name'] != '')) {
                $sFilebasename = trim($rName['name']);
                $sFilebasename = str_replace(" ", "_", $sFilebasename);
            } else {
                $sFilebasename = 'ocpl' . $options['queryid'];
            }
        }
    }

    $bUseZip = ( isset($rCount['count']) && $rCount['count'] > 50 );
    $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
    $bUseZip = false;
    if ($bUseZip == true) {
        $content = '';
        require_once ($rootpath . 'lib/phpzip/ss_zip.class.php');
        $phpzip = new ss_zip('', 6);
    }

    if ($bUseZip == true) {
        header('content-type: application/zip');
        header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
    } else {
        header('Content-type: application/uam');
        header('Content-Disposition: attachment; filename=' . $sFilebasename . '.uam');
    }


    $s = $dbcSearch->simpleQuery(
        'SELECT `wptcontent`.`cache_id` `cacheid`, `wptcontent`.`longitude` `longitude`, `wptcontent`.`latitude` `latitude`, `wptcontent`.cache_mod_cords_id,
                `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`wp_oc` `wp_oc`, `cache_type`.`short` `typedesc`, `cache_size`.`pl` `sizedesc`,
                `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username` , `caches`.`size` `size`, `caches`.`type` `type`
        FROM `wptcontent`, `caches`, `cache_type`, `cache_size`, `user`
        WHERE `wptcontent`.`cache_id`=`caches`.`cache_id`
            AND `wptcontent`.`type`=`cache_type`.`id` AND `wptcontent`.`size`=`cache_size`.`id`
            AND `wptcontent`.`user_id`=`user`.`user_id`');

    append_output(pack("ccccl", 0xBB, 0x22, 0xD5, 0x3F, $rCount['count']));

    while ($r = $dbcSearch->dbResultFetch($s)) {
        $lat = $r['latitude'];
        $lon = $r['longitude'];
        // $utm = cs2cs_1992($lat, $lon);
        $utm = wgs2u1992($lat, $lon);
        $y = (int) $utm[0];
        $x = (int) $utm[1];

        // modified coords
        if ($r['cache_mod_cords_id'] > 0) { // check if we have user coords
            $r['mod_suffix'] = '[F]';
        } else {
            $r['mod_suffix'] = '';
        }

        $name = PLConvert('UTF-8', 'POLSKAWY', $r['mod_suffix'] . $r['name']);
        $username = PLConvert('UTF-8', 'POLSKAWY', $r['username']);
        $type = $uamType[$r['type']];
        $size = $uamSize[$r['size']];
        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
        $cacheid = $r['wp_oc'];

        $descr = "$name by $username [$difficulty/$terrain]";
        $poiname = "$cacheid $type$size";

        $record = pack("llca64a255cca32", $x, $y, 2, $poiname, $descr, 1, 99, 'Geocaching');

        append_output($record);
        ob_flush();
    }

    // phpzip versenden
    if ($bUseZip == true) {
        $phpzip->add_data($sFilebasename . '.uam', $content);
        echo $phpzip->save($sFilebasename . '.zip', 'b');
    }
}
exit();

function convert_string($str)
{
    $newstr = iconv("UTF-8", "ASCII//TRANSLIT", $str);
                if ($newstr == false)
                                return "--- charset error ---";
                else
                                return $newstr;
}

function append_output($str)
{
    global $content, $bUseZip;

    if ($bUseZip == true)
        $content .= $str;
    else
        echo $str;
}

function wgs2u1992($lat, $lon)
{

    //double Brad , Lrad, Lorad ,k, C, firad, Xmer, Ymer, Xgk, Ygk;
    // stale
    $E = 0.0818191910428;
    $Pi = 3.141592653589793238462643;
    $Pi_2 = 1.570796327;  //3.141592653589793238462643 / 2  // Pi / 2
    $Pi_4 = 0.7853981634; // 3.141592653589793238462643 / 4  // Pi / 4
    $Pi__180 = 0.01745329252; // 3.141592653589793238462643 / 180
    $Ro = 6367449.14577;
    $a2 = 0.0008377318247344;
    $a4 = 0.0000007608527788826;
    $a6 = 0.000000001197638019173;
    $a8 = 0.00000000000244337624251;


    // uklad UTM
    //#define mo  0.9996   //wspo#udnik skali na po#udniku #rodkowym
    //#define Lo  (double)((((int)(lon/6)) * 6) + 3) // po#udnik #rodkowy
    // zone = (int)(lon+180/6)+1
    //#define FE  500000   //False Easting
    //#define FN  0 //False Northing
    // uklad 1992
    $mo = 0.9993;   //wspo#udnik #rodkowy
    $Lo = 19.0;
    $FE = 500000;   //False Easting
    $FN = -5300000; //False Northing


    $Brad = $lat * $Pi / 180; //Pi / 180;
    $Lrad = $lon * $Pi / 180; // Pi / 180;
    $Lorad = $Lo * $Pi / 180; // Pi / 180;
    //k = ((1 - E * sin(Brad)) / (1 + E * sin(Brad))) ^ (E / 2); // pasc
    //k = pow(((1 - E * sin(Brad)) / (1 + E * sin(Brad))) , (E / 2)); // c
    $k = exp(($E / 2) * log((1 - $E * sin($Brad)) / (1 + $E * sin($Brad))));

    $C = $k * tan(($Brad / 2) + ($Pi_4));

    $firad = (2 * atan($C)) - ($Pi_2);

    $Xmer = atan(sin($firad) / (cos($firad) * cos($Lrad - $Lorad)));
    $Ymer = 0.5 * log((1 + cos($firad) * sin($Lrad - $Lorad)) / (1 - cos($firad) * sin($Lrad - $Lorad)));

    $Xgk = $Ro * ($Xmer + ($a2 * sin(2 * $Xmer) * cosh(2 * $Ymer)) + ($a4 * sin(4 * $Xmer) * cosh(4 * $Ymer)) + ($a6 * sin(6 * $Xmer) * cosh(6 * $Ymer)) + ($a8 * sin(8 * $Xmer) * cosh(8 * $Ymer)));
    $Ygk = $Ro * ($Ymer + ($a2 * cos(2 * $Xmer) * sinh(2 * $Ymer)) + ($a4 * cos(4 * $Xmer) * sinh(4 * $Ymer)) + ($a6 * cos(6 * $Xmer) * sinh(6 * $Ymer)) + ($a8 * cos(8 * $Xmer) * sinh(8 * $Ymer)));

    $X = $mo * $Xgk + $FN;
    $Y = $mo * $Ygk + $FE;

    return (array($X, $Y));
}

/*
Funkcja do konwersji polskich znakow miedzy roznymi systemami kodowania.
Zwraca skonwertowany tekst.
Argumenty:
$source - string - źródłowe kodowanie
$dest - string - źródłowe kodowanie
$tekst - string - tekst do konwersji
Obsługiwane formaty kodowania to:
POLSKAWY (powoduje zamianę polskich liter na ich łacińskie odpowiedniki)
ISO-8859-2
WINDOWS-1250
UTF-8
ENTITIES (zamiana polskich znaków na encje html)
Przyklad:
echo(PlConvert('UTF-8','ISO-8859-2','Zażółć gęślą jaźń.'));
*/
function PLConvert($source,$dest,$tekst)
{
    $source=strtoupper($source);
    $dest=strtoupper($dest);
    if($source==$dest) return $tekst;
    $chars['POLSKAWY']    =array('a','c','e','l','n','o','s','z','z','A','C','E','L','N','O','S','Z','Z');
    $chars['ISO-8859-2']  =array("\xB1","\xE6","\xEA","\xB3","\xF1","\xF3","\xB6","\xBC","\xBF","\xA1","\xC6","\xCA","\xA3","\xD1","\xD3","\xA6","\xAC","\xAF");
    $chars['WINDOWS-1250']=array("\xB9","\xE6","\xEA","\xB3","\xF1","\xF3","\x9C","\x9F","\xBF","\xA5","\xC6","\xCA","\xA3","\xD1","\xD3","\x8C","\x8F","\xAF");
    $chars['UTF-8']       =array('ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż');
    $chars['ENTITIES']    =array('ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż');
    if(!isset($chars[$source])) return false;
    if(!isset($chars[$dest])) return false;
        $tekst = str_replace('a', 'a', $tekst);
        $tekst = str_replace('é', 'e', $tekst);
    return str_replace($chars[$source],$chars[$dest],$tekst);
}
