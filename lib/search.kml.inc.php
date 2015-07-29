<?php
    /***************************************************************************
                                                            ./lib/search.kml.inc.php
                                                                -------------------
            begin                : November 1 2005
            copyright            : (C) 2005 The OpenCaching Group
            forum contact at     : http://www.opencaching.com/phpBB2

        ***************************************************************************/

    /***************************************************************************
        *
        *   This program is free software; you can redistribute it and/or modify
        *   it under the terms of the GNU General Public License as published by
        *   the Free Software Foundation; either version 2 of the License, or
        *   (at your option) any later version.
        *
        ***************************************************************************/

    /****************************************************************************

        kml search output


    ****************************************************************************/

    ob_start();

    global $bUseZip, $sqldebug, $usr, $hide_coords, $absolute_server_URI, $dbcSearch;
    set_time_limit(1800);
    $kmlLine =
'
<Placemark>
  <description><![CDATA[<a href="'.$absolute_server_URI.'viewcache.php?cacheid={cacheid}">'.tr('search_kml_01').'</a><br />'.tr('search_kml_02').' {username}<br />&nbsp;<br /><table cellspacing="0" cellpadding="0" border="0"><tr><td>{typeimgurl} </td><td>'.tr('search_kml_03').' {type}<br />'.tr('search_kml_04').' {{size}}</td></tr><tr><td colspan="2">'.tr('search_kml_05').' {difficulty} '.tr('search_kml_06').' 5.0<br />'.tr('search_kml_07').' {terrain} '.tr('search_kml_06').' 5.0</td></tr></table>]]></description>
   <name>{mod_suffix}{name}</name>
  <LookAt>
    <longitude>{lon}</longitude>
    <latitude>{lat}</latitude>
    <range>5000</range>
    <tilt>0</tilt>
    <heading>3</heading>
  </LookAt>
  <styleUrl>#{icon}</styleUrl>
  <Point>
    <coordinates>{lon},{lat},0</coordinates>
  </Point>
</Placemark>
';

    $kmlFoot = '</Folder></Document></kml>';

    $kmlTimeFormat = 'Y-m-d\TH:i:s\Z';

    if( $usr || !$hide_coords )
    {
        //prepare the output
        $caches_per_page = 20;

        $sql = 'SELECT ';

        if (isset($lat_rad) && isset($lon_rad))
        {
            $sql .= getCalcDistanceSqlFormula($usr !== false, $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
        }
        else
        {
            if ($usr === false)
            {
                $sql .= '0 distance, ';
            }
            else
            {
                //get the users home coords
                $rs_coords = sql('SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`=\'' . intval($usr['userid']) . '\'');
                $record_coords = mysql_fetch_array($rs_coords);

                if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0)))
                {
                    $sql .= '0 distance, ';
                }
                else
                {
                    //TODO: load from the users-profile
                    $distance_unit = 'km';

                    $sql .= getCalcDistanceSqlFormula($usr !== false, $record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
                }
                mysql_free_result($rs_coords);
            }
        }

        $sql .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`user_id` `user_id`, ';
        if ($usr === false)
        {
            $sql .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id
                    FROM `caches` ';
        }
        else
        {
            $sql .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`,
                            `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id FROM `caches`
                        LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = '
                            . $usr['userid'];
        }
        $sql .= ' WHERE `caches`.`cache_id` IN (' . $sqlFilter . ')';

        $sortby = $options['sort'];
        if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance'))
        {
            $sql .= ' ORDER BY distance ASC';
        }
        else if ($sortby == 'bycreated')
        {
            $sql .= ' ORDER BY date_created DESC';
        }
        else // by name
        {
            $sql .= ' ORDER BY name ASC';
        }

        //startat?
        $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
        if (!is_numeric($startat)) $startat = 0;

        if (isset($_REQUEST['count']))
            $count = $_REQUEST['count'];
        else
            $count = $caches_per_page;

        $maxlimit = 1000000000;

        if ($count == 'max') $count = $maxlimit;
        if (!is_numeric($count)) $count = 0;
        if ($count < 1) $count = 1;
        if ($count > $maxlimit) $count = $maxlimit;

        $sqlLimit .= ' LIMIT ' . $startat . ', ' . $count;

        // cleanup (old gpxcontent lingers if gpx-download is cancelled by user)
        $dbcSearch->simpleQuery( 'DROP TEMPORARY TABLE IF EXISTS `kmlcontent`');
        $dbcSearch->reset();

        // temporäre tabelle erstellen
        $dbcSearch->simpleQuery( 'CREATE TEMPORARY TABLE `kmlcontent` ' . $sql . $sqlLimit, $sqldebug);
        $dbcSearch->reset();

        $dbcSearch->simpleQuery( 'SELECT COUNT(*) `count` FROM `kmlcontent`');
        $rCount = $dbcSearch->dbResultFetch();
        $dbcSearch->reset();

        if ($rCount['count'] == 1)
        {
            $dbcSearch->simpleQuery( 'SELECT `caches`.`wp_oc` `wp_oc` FROM `kmlcontent`, `caches` WHERE `kmlcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
            $rName = $dbcSearch->dbResultFetch();
            $dbcSearch->reset();

            $sFilebasename = $rName['wp_oc'];
        }
        else {
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
                } else {
                    $sFilebasename = "$short_sitename" . $options['queryid'];
                }
            }
        }
        $sFilebasename = str_replace(" ", "_", $sFilebasename);

        $bUseZip = ($rCount['count'] > 50);
        $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
        // $bUseZip = false;
        if ($bUseZip == true)
        {
            require_once($rootpath . 'lib/phpzip/ss_zip.class.php');
            $phpzip = new ss_zip('',6);
        }

        // ok, ausgabe starten

        include ($stylepath . '/search.result.caches.kml.head.tpl.php');

        $dbcSearch->simpleQuery( 'SELECT MIN(`longitude`) `minlon`, MAX(`longitude`) `maxlon`, MIN(`latitude`) `minlat`, MAX(`latitude`) `maxlat` FROM `kmlcontent`', $sqldebug);
        $rMinMax = $dbcSearch->dbResultFetch();
        $dbcSearch->reset();

        $kmlDetailHead = str_replace('{minlat}', $rMinMax['minlat'], $kmlDetailHead);
        $kmlDetailHead = str_replace('{minlon}', $rMinMax['minlon'], $kmlDetailHead);
        $kmlDetailHead = str_replace('{maxlat}', $rMinMax['maxlat'], $kmlDetailHead);
        $kmlDetailHead = str_replace('{maxlon}', $rMinMax['maxlon'], $kmlDetailHead);
        $kmlDetailHead = str_replace('{{time}}', date($kmlTimeFormat), $kmlDetailHead);

        echo $kmlDetailHead;

        // ok, ausgabe ...

        /*
            wp
            name
            username
            type
            size
            lon
            lat
            icon
        */

        $dbcSearch->simpleQuery( 'SELECT `kmlcontent`.`cache_id` `cacheid`, `kmlcontent`.`longitude` `longitude`, `kmlcontent`.`latitude` `latitude`, `kmlcontent`.cache_mod_cords_id, `kmlcontent`.`type` `type`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `cache_type`.`pl` `typedesc`, `cache_size`.`pl` `sizedesc`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username` FROM `kmlcontent`, `caches`, `cache_type`, `cache_size`, `user` WHERE `kmlcontent`.`cache_id`=`caches`.`cache_id` AND `kmlcontent`.`type`=`cache_type`.`id` AND `kmlcontent`.`size`=`cache_size`.`id` AND `kmlcontent`.`user_id`=`user`.`user_id`', $sqldebug);
        while($r = $dbcSearch->dbResultFetch() )
        {
            $thisline = $kmlLine;

            // icon suchen
            switch ($r['type'])
            {
                case 2:
                    $icon = 'tradi';
                    $typeimgurl = '<img src="'.$absolute_server_URI.'tpl/stdstyle/images/cache/traditional.png" alt="'.tr('cacheType_1').'" title="'.tr('cacheType_1').'" />';
                    break;
                case 3:
                    $icon = 'multi';
                    $typeimgurl = '<img src="'.$absolute_server_URI.'tpl/stdstyle/images/cache/multi.png" alt="'.tr('cacheType_2').'" title="'.tr('cacheType_2').'" />';
                    break;
                case 4:
                    $icon = 'virtual';
                    $typeimgurl = '<img src="'.$absolute_server_URI.'tpl/stdstyle/images/cache/virtual.png" alt="'.tr('cacheType_8').'" title="'.tr('cacheType_8').'" />';
                    break;
                case 5:
                    $icon = 'webcam';
                    $typeimgurl = '<img src="'.$absolute_server_URI.'tpl/stdstyle/images/cache/webcam.png" alt="'.tr('cacheType_7').'" title="'.tr('cacheType_7').'" />';
                    break;
                case 6:
                    $icon = 'event';
                    $typeimgurl = '<img src="'.$absolute_server_URI.'tpl/stdstyle/images/cache/event.png" alt="'.tr('cacheType_6').'" title="'.tr('cacheType_6').'" />';
                    break;
                case 7:
                    $icon = 'myst';
                    $typeimgurl = '<img src="'.$absolute_server_URI.'tpl/stdstyle/images/cache/quiz.png" alt="'.tr('cacheType_3').'" title="'.tr('cacheType_3').'" />';
                    break;
                case 9:
                    $icon = 'moving';
                    $typeimgurl = '<img src="'.$absolute_server_URI.'tpl/stdstyle/images/cache/moving.png" alt="'.tr('cacheType_4').'" title="'.tr('cacheType_4').'" />';
                    break;
                default:
                    $icon = 'unknown';
                    $typeimgurl = '<img src="'.$absolute_server_URI.'tpl/stdstyle/images/cache/unknown.png" alt="'.tr('cacheType_5').'" title="'.tr('cacheType_5').'" />';
                    break;
            }
            $thisline = str_replace('{icon}', $icon, $thisline);
            $thisline = str_replace('{typeimgurl}', $typeimgurl, $thisline);

            $lat = sprintf('%01.5f', $r['latitude']);
            $thisline = str_replace('{lat}', $lat, $thisline);

            $lon = sprintf('%01.5f', $r['longitude']);
            $thisline = str_replace('{lon}', $lon, $thisline);

            $time = date($kmlTimeFormat, strtotime($r['date_hidden']));
            $thisline = str_replace('{{time}}', $time, $thisline);

            $thisline = str_replace('{name}', xmlentities(PlConvert("UTF-8", "POLSKAWY", $r['name'])), $thisline);
            //modified coords
            if ($r['cache_mod_cords_id'] > 0) {  //check if we have user coords
                $thisline = str_replace('{mod_suffix}', '[F]', $thisline);
            } else {
                $thisline = str_replace('{mod_suffix}', '', $thisline);
            }



            if (($r['status'] == 2) || ($r['status'] == 3))
            {
                if ($r['status'] == 2)
                    $thisline = str_replace('{archivedflag}', 'Tymczasowo niedostepna!, ', $thisline);
                else
                    $thisline = str_replace('{archivedflag}', 'Zarchiwizowana!, ', $thisline);
            }
            else
                $thisline = str_replace('{archivedflag}', '', $thisline);

            $thisline = str_replace('{type}', xmlentities(PlConvert("UTF-8", "POLSKAWY", $r['typedesc'])), $thisline);
            $thisline = str_replace('{{size}}', xmlentities(PlConvert("UTF-8", "POLSKAWY", $r['sizedesc'])), $thisline);

            $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
            $thisline = str_replace('{difficulty}', $difficulty, $thisline);

            $terrain = sprintf('%01.1f', $r['terrain'] / 2);
            $thisline = str_replace('{terrain}', $terrain, $thisline);

            $time = date($kmlTimeFormat, strtotime($r['date_hidden']));
            $thisline = str_replace('{{time}}', $time, $thisline);

            $thisline = str_replace('{username}', xmlentities(PlConvert("UTF-8", "POLSKAWY", $r['username'])), $thisline);
            $thisline = str_replace('{cacheid}', xmlentities($r['cacheid']), $thisline);

            echo $thisline;
            // ob_flush();
        }
        $dbcSearch->reset();
        unset($dbc);
        echo $kmlFoot;

        if ($sqldebug == true) outputSqlDebugForm();

        // phpzip versenden
        if ($bUseZip == true)
        {
            $content = ob_get_clean();
            $phpzip->add_data($sFilebasename . '.kml', $content);
            $out = $phpzip->save($sFilebasename . '.kmz', 'r');
            header('Content-Type: application/vnd.google-earth.kmz; charset=utf8');
            header('Content-Disposition: attachment; filename="' . $sFilebasename . '.kmz"');
            header('Pragma: no-cache');
            header('Cache-Control: no-store,no-cache,must-revalidate,post-check=0,pre-check=0,private');
            // header('Content-Transfer-Encoding: binary');
            echo $out;
            ob_end_flush();

        } else {
            header('Content-Type: application/vnd.google-earth.kml; charset=utf8');
            header('Content-Disposition: attachment; filename="' . $sFilebasename . '.kml"');
            ob_end_flush();
        }
    }
    exit;

    function xmlentities($str)
    {
        $from[0] = '&'; $to[0] = '&amp;';
        $from[1] = '<'; $to[1] = '&lt;';
        $from[2] = '>'; $to[2] = '&gt;';
        $from[3] = '"'; $to[3] = '&quot;';
        $from[4] = '\''; $to[4] = '&apos;';

        $str = str_replace($from, $to, $str);
            $str = preg_replace('/[[:cntrl:]]/', '', $str);

        return $str;
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
    function PlConvert($source,$dest,$tekst)
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

    return str_replace($chars[$source],$chars[$dest],$tekst);
    }

?>
