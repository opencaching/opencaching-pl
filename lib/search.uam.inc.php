<?php

/* * *************************************************************************
  ./lib/search.uam.inc.php
  -------------------
  begin                : November 2 2005
  copyright            : (C) 2005 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder ??

  uam search output
  based on search.ov2.inc.php and half working uam code by sp2ong
  by Ar't (c)2007

 * ************************************************************************** */
// setlocale(LC_CTYPE, 'pl_PL');

/* * **************************************************************************

  8 Naglowek pliku = BB 22 D5 3F + 4 bajty ilosc rekordow 1D 00 00 00 = 29 rekordow
  Rekordy (kazdy 362 znaki)
  8 wspol w uk 1992, 4 bajty Y potem 4 bajty X
  1 Priorytet punktu (0-4)
  64 nazwa punktu
  255 Opis
  1 Czy widoczny na mapie (0 nie 1 tak)
  1 Numer kategorii (99 uzytkownika) ma byc 99
  32 Nazwa kategori usera np Geocaching

 * ************************************************************************** */
//  require_once("./lib/cs2cs.inc.php");
require_once("./lib/wgs21992.php");
set_time_limit(1800);
global $content, $bUseZip, $sqldebug, $hide_coords, $usr;

$uamSize[1] = 'o'; //'Other'
$uamSize[2] = 'm'; //'Micro'
$uamSize[3] = 's'; //'Small'
$uamSize[4] = 'r'; //'Regular'
$uamSize[5] = 'l'; //'Large'
$uamSize[6] = 'l'; //'Large'
$uamSize[7] = 'v'; //'Virtual'
// known by gpx
$uamType[1] = 'O'; //'Other'
$uamType[2] = 'T'; //'Traditional'
$uamType[3] = 'M'; //'Multi'
$uamType[4] = 'V'; //'Virtual'
$uamType[5] = 'W'; //'Webcam'
$uamType[6] = 'E'; //'Event'
// unknown ... converted
$uamType[7] = 'Q'; //'Quiz'
$uamType[8] = 'M'; //'Math'
$uamType[9] = 'M'; //'Mobile'
$uamType[10] = 'D'; //'Drive-in'

if ($usr || !$hide_coords) {
    //prepare the output
    $caches_per_page = 20;

    $sql = 'SELECT ';

    if (isset($lat_rad) && isset($lon_rad)) {
        $sql .= getSqlDistanceFormula($lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
    } else {
        if ($usr === false) {
            $sql .= '0 distance, ';
        } else {
            //get the users home coords
            $rs_coords = sql("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
            $record_coords = sql_fetch_array($rs_coords);

            if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0))) {
                $sql .= '0 distance, ';
            } else {
                //TODO: load from the users-profile
                $distance_unit = 'km';

                $lon_rad = $record_coords['longitude'] * 3.14159 / 180;
                $lat_rad = $record_coords['latitude'] * 3.14159 / 180;

                $sql .= getSqlDistanceFormula($record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
            }
            mysql_free_result($rs_coords);
        }
    }
    $sql .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`user_id` `user_id`, ';
    if ($usr === false) {
        $sql .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id FROM `caches` ';
    } else {
        $sql .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`, `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id FROM `caches`
                        LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = '
                . $usr['userid'];
    };
    $sql .= '   WHERE `caches`.`cache_id` IN (' . $sqlFilter . ')';

    $sortby = $options['sort'];
    if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance')) {
        $sql .= ' ORDER BY distance ASC';
    } else if ($sortby == 'bycreated') {
        $sql .= ' ORDER BY date_created DESC';
    } else { // by name
        $sql .= ' ORDER BY name ASC';
    }

    //startat?
    $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
    if (!is_numeric($startat))
        $startat = 0;

    if (isset($_REQUEST['count']))
        $count = $_REQUEST['count'];
    else
        $count = $caches_per_page;

    $maxlimit = 1000000000;

    if ($count == 'max')
        $count = $maxlimit;
    if (!is_numeric($count))
        $count = 0;
    if ($count < 1)
        $count = 1;
    if ($count > $maxlimit)
        $count = $maxlimit;

    $sqlLimit = ' LIMIT ' . $startat . ', ' . $count;


    // cleanup (old gpxcontent lingers if gpx-download is cancelled by user)
    sql('DROP TEMPORARY TABLE IF EXISTS `wptcontent`');
    // temporäre tabelle erstellen
    sql('CREATE TEMPORARY TABLE `wptcontent` ' . $sql . $sqlLimit);

    $rsCount = sql('SELECT COUNT(*) `count` FROM `wptcontent`');
    $rCount = sql_fetch_array($rsCount);
    mysql_free_result($rsCount);

    if ($rCount['count'] == 1) {
        $rsName = sql('SELECT `caches`.`wp_oc` `wp_oc` FROM `wptcontent`, `caches` WHERE `wptcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
        $rName = sql_fetch_array($rsName);
        mysql_free_result($rsName);

        $sFilebasename = $rName['wp_oc'];
    } else {
        if ($options['searchtype'] == 'bywatched') {
            $sFilebasename = 'watched_caches';
        } elseif ($options['searchtype'] == 'bylist') {
            $sFilebasename = 'cache_list';
        } else {
            $rsName = sql('SELECT `queries`.`name` `name` FROM `queries` WHERE `queries`.`id`= &1 LIMIT 1', $options['queryid']);
            $rName = sql_fetch_array($rsName);
            mysql_free_result($rsName);
            if (isset($rName['name']) && ($rName['name'] != '')) {
                $sFilebasename = trim($rName['name']);
                $sFilebasename = str_replace(" ", "_", $sFilebasename);
            } else {
                $sFilebasename = 'ocpl' . $options['queryid'];
            }
        }
    }


    $bUseZip = ($rCount['count'] > 50);
    $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
    $bUseZip = false;
    if ($bUseZip == true) {
        $content = '';
        require_once($rootpath . 'lib/phpzip/ss_zip.class.php');
        $phpzip = new ss_zip('', 6);
    }

    // ok, ausgabe starten

    if ($sqldebug == false) {
        if ($bUseZip == true) {
            header('content-type: application/zip');
            header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
        } else {
            header('Content-type: application/uam');
            header('Content-Disposition: attachment; filename=' . $sFilebasename . '.uam');
        }
    }

    // ok, ausgabe ...

    /*
      cacheid
      name
      lon
      lat

      archivedflag
      type
      size
      difficulty
      terrain
      username
     */

    $sql = 'SELECT `wptcontent`.`cache_id` `cacheid`, `wptcontent`.`longitude` `longitude`, `wptcontent`.`latitude` `latitude`, `wptcontent`.cache_mod_cords_id, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`wp_oc` `wp_oc`, `cache_type`.`short` `typedesc`, `cache_size`.`pl` `sizedesc`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username` , `caches`.`size` `size`, `caches`.`type` `type`  FROM `wptcontent`, `caches`, `cache_type`, `cache_size`, `user` WHERE `wptcontent`.`cache_id`=`caches`.`cache_id` AND `wptcontent`.`type`=`cache_type`.`id` AND `wptcontent`.`size`=`cache_size`.`id` AND `wptcontent`.`user_id`=`user`.`user_id`';
    $rs = sql($sql, $sqldebug);

    append_output(pack("ccccl", 0xBB, 0x22, 0xD5, 0x3F, $rCount['count']));

    while ($r = sql_fetch_array($rs)) {
        $lat = $r['latitude'];
        $lon = $r['longitude'];
        //      $utm = cs2cs_1992($lat, $lon);
        $utm = wgs2u1992($lat, $lon);
        $y = (int) $utm[0];
        $x = (int) $utm[1];

        //modified coords
        if ($r['cache_mod_cords_id'] > 0) {  //check if we have user coords
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
    mysql_free_result($rs);

    if ($sqldebug == true)
        sqldbg_end();

    // phpzip versenden
    if ($bUseZip == true) {
        $phpzip->add_data($sFilebasename . '.uam', $content);
        echo $phpzip->save($sFilebasename . '.zip', 'b');
    }
}
exit;

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
    global $content, $bUseZip, $sqldebug;
    if ($sqldebug == true)
        return;

    if ($bUseZip == true)
        $content .= $str;
    else
        echo $str;
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

function PlConvert($source, $dest, $tekst)
{
    $source = strtoupper($source);
    $dest = strtoupper($dest);
    if ($source == $dest)
        return $tekst;

    $chars['POLSKAWY'] = array('a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z', 'A', 'C', 'E', 'L', 'N', 'O', 'S', 'Z', 'Z');
    $chars['ISO-8859-2'] = array("\xB1", "\xE6", "\xEA", "\xB3", "\xF1", "\xF3", "\xB6", "\xBC", "\xBF", "\xA1", "\xC6", "\xCA", "\xA3", "\xD1", "\xD3", "\xA6", "\xAC", "\xAF");
    $chars['WINDOWS-1250'] = array("\xB9", "\xE6", "\xEA", "\xB3", "\xF1", "\xF3", "\x9C", "\x9F", "\xBF", "\xA5", "\xC6", "\xCA", "\xA3", "\xD1", "\xD3", "\x8C", "\x8F", "\xAF");
    $chars['UTF-8'] = array('ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż', 'Ą', 'Ć', 'Ę', 'Ł', 'Ń', 'Ó', 'Ś', 'Ź', 'Ż');
    $chars['ENTITIES'] = array('ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż', 'Ą', 'Ć', 'Ę', 'Ł', 'Ń', 'Ó', 'Ś', 'Ź', 'Ż');

    if (!isset($chars[$source]))
        return false;
    if (!isset($chars[$dest]))
        return false;

    return str_replace($chars[$source], $chars[$dest], $tekst);
}

?>
