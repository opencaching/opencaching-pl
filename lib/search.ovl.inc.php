<?php
/**
 * This script is used (can be loaded) by /search.php
 */

use Utils\Database\XDb;

    global $content, $bUseZip, $hide_coords, $usr, $dbcSearch;
    set_time_limit(1800);
    $ovlLine = "[Symbol {symbolnr1}]\r\nTyp=6\r\nGroup=1\r\nWidth=20\r\nHeight=20\r\nDir=100\r\nArt=1\r\nCol=3\r\nZoom=1\r\nSize=103\r\nArea=2\r\nXKoord={lon}\r\nYKoord={lat}\r\n[Symbol {symbolnr2}]\r\nTyp=2\r\nGroup=1\r\nCol=3\r\nArea=1\r\nZoom=1\r\nSize=130\r\nFont=1\r\nDir=100\r\nXKoord={lonname}\r\nYKoord={latname}\r\nText={mod_suffix}{cachename}\r\n";
    $ovlFoot = "[Overlay]\r\nSymbols={symbolscount}\r\n";

    if( $usr || !$hide_coords )
    {
        //prepare the output
        $caches_per_page = 20;

        $query = 'SELECT ';

        if (isset($lat_rad) && isset($lon_rad))
        {
            $query .= getSqlDistanceFormula($lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
        }
        else
        {
            if ($usr === false)
            {
                $query .= '0 distance, ';
            }
            else
            {
                //get the users home coords
                $rs_coords = XDb::xSql(
                    "SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`= ? ", $usr['userid']);

                $record_coords = XDb::xFetchArray($rs_coords);

                if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0)))
                {
                    $query .= '0 distance, ';
                }
                else
                {
                    //TODO: load from the users-profile
                    $distance_unit = 'km';

                    $lon_rad = $record_coords['longitude'] * 3.14159 / 180;
                    $lat_rad = $record_coords['latitude'] * 3.14159 / 180;

                    $query .= getSqlDistanceFormula($record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
                }
                XDb::xFreeResults($rs_coords);
            }
        }
        if ($usr === false)
        {
            $query .= ' `caches`.`cache_id`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id, `caches`.`type` `type`
                    FROM `caches` ';
        }
        else
        {
            $query .= ' `caches`.`cache_id`, IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`,
                            `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id, `caches`.`type` `type` FROM `caches`
                        LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = '
                            . $usr['userid'];
        }
        $query .= ' WHERE `caches`.`cache_id` IN (' . $queryFilter . ')';

        $sortby = $options['sort'];
        if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance'))
        {
            $query .= ' ORDER BY distance ASC';
        }
        else if ($sortby == 'bycreated')
        {
            $query .= ' ORDER BY date_created DESC';
        }
        else // by name
        {
            $query .= ' ORDER BY name ASC';
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

        $queryLimit = ' LIMIT ' . $startat . ', ' . $count;

        $dbcSearch->simpleQuery( 'CREATE TEMPORARY TABLE `ovlcontent` ' . $query . $queryLimit);
        $dbcSearch->reset();

        $dbcSearch->simpleQuery( 'SELECT COUNT(*) `count` FROM `ovlcontent`');
        $rCount = $dbcSearch->dbResultFetch();
        $dbcSearch->reset();

        if ($rCount['count'] == 1)
        {
            $rsName = $dbcSearch->simpleQuery('SELECT `caches`.`wp_oc` `wp_oc` FROM `ovlcontent`, `caches` WHERE `ovlcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
            $rName = $rCount = $dbcSearch->dbResultFetch();
            $dbcSearch->reset();

            $sFilebasename = $rName['wp_oc'];
        }
        else {
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
        if ($bUseZip == true)
        {
            $content = '';
            require_once($rootpath . 'lib/phpzip/ss_zip.class.php');
            $phpzip = new ss_zip('',6);
        }

        if ($bUseZip == true)
        {
            header("content-type: application/zip");
            header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
        }
        else
        {
            header("Content-type: application/ovl");
            header("Content-Disposition: attachment; filename=" . $sFilebasename . ".ovl");
        }

        $nr = 1;
        $rs = $dbcSearch->simpleQuery( 'SELECT `ovlcontent`.`cache_id` `cacheid`, `ovlcontent`.`longitude` `longitude`, `ovlcontent`.`latitude` `latitude`, `ovlcontent`.cache_mod_cords_id, `caches`.`name` `name`, `ovlcontent`.`type` `type` FROM `ovlcontent`, `caches` WHERE `ovlcontent`.`cache_id`=`caches`.`cache_id`');
        while($r = $dbcSearch->dbResultFetch())
        {
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

            append_output($thisline);
            ob_flush();
            $nr += 2;
        }
        $dbcSearch->reset();
        unset($dbc);
        $ovlFoot = mb_ereg_replace('{symbolscount}', $nr - 1, $ovlFoot);
        append_output($ovlFoot);

        // phpzip versenden
        if ($bUseZip == true)
        {
            $phpzip->add_data($sFilebasename . '.ovl', $content);
            echo $phpzip->save($sFilebasename . '.zip', 'b');
        }
    }
    exit;

    function convert_string($str)
    {
        $newstr = iconv("UTF-8", "utf-8", $str);
        if ($newstr == false)
            return $str;
        else
            return $newstr;
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
