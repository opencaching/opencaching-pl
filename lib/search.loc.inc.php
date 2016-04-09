<?php

    global $content, $bUseZip, $hide_coords, $usr, $dbcSearch;
    set_time_limit(1800);
$locHead = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<loc version="1.0" src="'.$absolute_server_URI.'">' . "\n";

$locLine = '
<waypoint>
    <name id="{{waypoint}}"><![CDATA[{mod_suffix}{cachename} '.tr('from').' {owner}, {type_text} ({difficulty}/{terrain})]]></name>
    <coord lat="{lat}" lon="{lon}"/>
    <type>Geocache</type>
    <link text="Cache Details">'.$absolute_server_URI.'viewcache.php?cacheid={cacheid}</link>
</waypoint>
';

$locFoot = '</loc>';

$cacheTypeText[1] = "".tr('cacheType_5')."";
$cacheTypeText[2] = "".tr('cacheType_1')."";
$cacheTypeText[3] = "".tr('cacheType_2')."";
$cacheTypeText[4] = "".tr('cacheType_8')."";
$cacheTypeText[5] = "".tr('cacheType_7')."";
$cacheTypeText[6] = "".tr('cacheType_6')."";
$cacheTypeText[7] = "".tr('cacheType_3')."";
$cacheTypeText[8] = "".tr('cacheType_4')."";
$cacheTypeText[10] = "".tr('cacheType_10')."";


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
                $rs_coords = sql("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
                $record_coords = sql_fetch_array($rs_coords);

                if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0)))
                {
                    $sql .= '0 distance, ';
                }
                else
                {
                    //TODO: load from the users-profile
                    $distance_unit = 'km';

                    $lon_rad = $record_coords['longitude'] * 3.14159 / 180;
                    $lat_rad = $record_coords['latitude'] * 3.14159 / 180;

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
        $sql .= ' WHERE `caches`.`cache_id` IN (' . $queryFilter . ')';

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

        $sqlLimit = ' LIMIT ' . $startat . ', ' . $count;

        // cleanup (old gpxcontent lingers if gpx-download is cancelled by user)
        $dbcSearch->simpleQuery( 'DROP TEMPORARY TABLE IF EXISTS `loccontent`');
        $dbcSearch->reset();
        // temporäre tabelle erstellen
        $dbcSearch->simpleQuery( 'CREATE TEMPORARY TABLE `loccontent` ' . $sql . $sqlLimit);
        $dbcSearch->reset();

        $dbcSearch->simpleQuery( 'SELECT COUNT(*) `count` FROM `loccontent`');
        $rCount = $dbcSearch->dbResultFetch();
        $dbcSearch->reset();

        if ($rCount['count'] == 1)
        {
            $dbcSearch->simpleQuery('SELECT `caches`.`wp_oc` `wp_oc` FROM `loccontent`, `caches` WHERE `loccontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
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
                    $sFilebasename = str_replace(" ", "_", $sFilebasename);
                } else {
                    $sFilebasename = "$short_sitename" . $options['queryid'];
                }
            }
        }

        $bUseZip = ($rCount['count'] > 200000000000);
        $bUseZip = $bUseZip || ($_REQUEST['zip'] == '1');
        $bUseZip = false;
        if ($bUseZip == true)
        {
            $content = '';
            require_once($rootpath . 'lib/phpzip/ss_zip.class.php');
            $phpzip = new ss_zip('',6);
        }

        if ($bUseZip == true)
        {
            header("content-type: application/zip");
            header('Content-Disposition: attachment; filename='. $sFilebasename . '.zip');
        }
        else
        {
            header("Content-type: application/loc");
            header("Content-Disposition: attachment; filename=" . $sFilebasename . ".loc");
        }

        append_output($locHead);

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

        $rs = $dbcSearch->simpleQuery( 'SELECT `loccontent`.`cache_id` `cacheid`, `loccontent`.`longitude` `longitude`, `loccontent`.`latitude` `latitude`, `loccontent`.cache_mod_cords_id, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`wp_oc` `waypoint`, `cache_type`.`short` `typedesc`, `cache_type`.`id` `type_id`, `cache_size`.`pl` `sizedesc`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username` FROM `loccontent`, `caches`, `cache_type`, `cache_size`, `user` WHERE `loccontent`.`cache_id`=`caches`.`cache_id` AND `loccontent`.`type`=`cache_type`.`id` AND `loccontent`.`size`=`cache_size`.`id` AND `loccontent`.`user_id`=`user`.`user_id`');
        while($r = $dbcSearch->dbResultFetch())
        {
            $thisline = $locLine;

            $lat = sprintf('%01.5f', $r['latitude']);
            $thisline = mb_ereg_replace('{lat}', $lat, $thisline);

            $lon = sprintf('%01.5f', $r['longitude']);
            $thisline = mb_ereg_replace('{lon}', $lon, $thisline);

            $thisline = mb_ereg_replace('{{waypoint}}', $r['waypoint'], $thisline);
            $thisline = mb_ereg_replace('{cachename}', PLConvert('UTF-8','POLSKAWY',$r['name']), $thisline);

            //modified coords
            if ($r['cache_mod_cords_id'] > 0) {  //check if we have user coords
                $thisline = str_replace('{mod_suffix}', '<F>', $thisline);
            } else {
                $thisline = str_replace('{mod_suffix}', '', $thisline);
            }

//          if (($r['status'] == 2) || ($r['status'] == 3))
//          {
//              if ($r['status'] == 2)
//                  $thisline = mb_ereg_replace('{archivedflag}', 'Czasowo niedostepna!, ', $thisline);
//              else
//                  $thisline = mb_ereg_replace('{archivedflag}', 'Zarchiwizowana!, ', $thisline);
//          }
//          else
//              $thisline = mb_ereg_replace('{archivedflag}', '', $thisline);

            $thisline = mb_ereg_replace('{type_text}', $cacheTypeText[$r['type_id']], $thisline);
            $thisline = mb_ereg_replace('{{size}}', PLConvert('UTF-8','POLSKAWY',tr('cacheType_'.$r['type_id'])), $thisline);

            $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
            $thisline = mb_ereg_replace('{difficulty}', $difficulty, $thisline);

            $terrain = sprintf('%01.1f', $r['terrain'] / 2);
            $thisline = mb_ereg_replace('{terrain}', $terrain, $thisline);

            $thisline = mb_ereg_replace('{owner}', $r['username'], $thisline);
            $thisline = mb_ereg_replace('{cacheid}', $r['cacheid'], $thisline);

            append_output($thisline);
            ob_flush();
        }
        $dbcSearch->reset();
        unset($dbc);
        append_output($locFoot);

        // phpzip versenden
        if ($bUseZip == true)
        {
            $phpzip->add_data($sFilebasename . '.loc', $content);
            echo $phpzip->save($sFilebasename . '.zip', 'b');
        }

        exit;
        }

        function xmlentities($str)
        {
            $from[0] = '&'; $to[0] = '&amp;';
            $from[1] = '<'; $to[1] = '&lt;';
            $from[2] = '>'; $to[2] = '&gt;';
            $from[3] = '"'; $to[3] = '&quot;';
            $from[4] = '\''; $to[4] = '&apos;';

            for ($i = 0; $i <= 4; $i++)
                $str = mb_ereg_replace($from[$i], $to[$i], $str);
                    $str = preg_replace('/[[:cntrl:]]/', '', $str);

            return $str;
        }

        function append_output($str)
        {
            global $content, $bUseZip;

            if ($bUseZip == true)
                $content .= $str;
            else
                echo $str;
        }

