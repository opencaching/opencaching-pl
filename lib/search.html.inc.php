<?php
    /***************************************************************************
                                                            ./lib/search.html.inc.php
                                                                -------------------
            begin                : July 25 2004
            copyright            : (C) 2004 The OpenCaching Group
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

        Unicode Reminder ăĄă˘

        (X)HTML search output

        TODO: (1) save the options in the database
              (2) sort the results and the make the final query

    ****************************************************************************/

    global $sqldebug; $usr; $lang; $hide_coords;

    require_once($stylepath . '/lib/icons.inc.php');
    require_once('lib/cache_icon.inc.php');
    set_time_limit(1800);
    //prepare the output
    $tplname = 'search.result.caches';
    $caches_per_page = 20;

    //build sql-list
    $countselect = mb_eregi_replace('^SELECT `cache_id`', 'SELECT COUNT(`cache_id`) `count`', $sqlFilter);
    $countselect = mb_eregi_replace('^SELECT `caches` `cache_id`', 'SELECT COUNT(`caches`.`cache_id`) `count`', $countselect);
    $countselect = mb_eregi_replace('^SELECT `caches`.`cache_id` `cache_id`', 'SELECT COUNT(`caches`.`cache_id`) `count`', $countselect);
    $countselect = mb_eregi_replace('^SELECT `result_caches`.`cache_id`', 'SELECT COUNT(`result_caches`.`cache_id`) `count`', $countselect);
    $countselect = mb_eregi_replace('^SELECT `result_caches`.`cache_id` `cache_id`', 'SELECT COUNT(`result_caches`.`cache_id`) `count`', $countselect);
    $rs = sql($countselect, $sqldebug);
    $r = sql_fetch_array($rs);
    $resultcount = $r['count'];
    mysql_free_result($rs);

    tpl_set_var('results_count', $r['count']);

    //build lines
    $cache_line = tpl_do_translate(read_file($stylepath . '/search.result.caches.row.tpl.php'));
    $caches_output = '';

    /*
        $lat_rad
        $lon_rad
        $distance_unit
    */
    $distance_unit = 'km';

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
    $sql .= '   `caches`.`name` `name`, `caches`.`status` `status`, `caches`.`wp_oc` `wp_oc`,
                `caches`.`difficulty` `difficulty`, `caches`.`terrain` `terrain`, `caches`.`desc_languages` `desc_languages`,
                `caches`.`date_created` `date_created`, `caches`.`type` `type`, `caches`.`cache_id` `cache_id`,
                `user`.`username` `username`, `user`.`user_id` `user_id`,
                `cache_type`.`icon_large` `icon_large`,
                `caches`.`founds` `founds`, `caches`.`topratings` `toprating`, ';
    if ($usr === false)
    {
        $sql .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id
                FROM `caches`, ';
    }
    else
    {
        $sql .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`,
                            `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id FROM `caches`
                        LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = '
                            . $usr['userid'] . ', ';
    }
    $sql .= ' `user`, `cache_type`
        WHERE `caches`.`user_id`=`user`.`user_id`
        AND `caches`.`cache_id` IN (' . $sqlFilter . ')
        AND `cache_type`.`id`=`caches`.`type` ';
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
    if (!is_numeric($caches_per_page)) $caches_per_page = 20;
    $startat = floor($startat / $caches_per_page) * $caches_per_page;
    $sql .= ' LIMIT ' . $startat . ', ' . $caches_per_page;
    $rs_caches = sql($sql, $sqldebug);
    $tr_Coord_have_been_modified = tr('srch_Coord_have_been_modified');
    $tr_Recommended =  tr('srch_Recommended');
    $tr_Send_to_GPS =tr('srch_Send_to_GPS');
    if (!isset($dbc)) {$dbc = new dataBase();};

    for ($i = 0; $i < mysql_num_rows($rs_caches); $i++)
    {

        $caches_record = sql_fetch_array($rs_caches);
        //modified coords
        if ($caches_record['cache_mod_cords_id'] > 0) {  //check if we have user coords
            $caches_record['coord_modified'] = true; //mark as coords midified
        } else {
            $caches_record['coord_modified'] = false;
        }

        $tmpline = $cache_line;

        list($iconname, $inactive) = getCacheIcon($usr['userid'], $caches_record['cache_id'], $caches_record['status'],
                                         $caches_record['user_id'], $caches_record['icon_large']);

        $tmpline = str_replace('{icon_large}', $iconname, $tmpline);
        // sp2ong

    $ratingA = $caches_record['toprating'];
    if ($ratingA > 0) $ratingimg='<img src="images/rating-star.png" alt="'.$tr_Recommended.'" title="'.$tr_Recommended.'" />'; else $ratingimg='';
    $tmpline = str_replace('{ratpic}', $ratingimg, $tmpline);
$login=0;
    if ($usr == false ) {
    $tmpline = str_replace('{long}',tr('please_login'), $tmpline);
    $tmpline = str_replace('{lat}',tr('to_see_coords'), $tmpline);
} else {
    $tmpline = str_replace('{long}', htmlspecialchars(help_lonToDegreeStr($caches_record['longitude'])), $tmpline);
    if ($caches_record['coord_modified'] == true) {
        $tmpline = str_replace('{mod_cord_style}', 'style="color:orange;" alt ="'.$tr_Coord_have_been_modified.'" title="'.$tr_Coord_have_been_modified.'"', $tmpline);
        $tmpline = str_replace('{mod_suffix}','[F]',$tmpline);
    } else {
        $tmpline = str_replace('{mod_cord_style}','',$tmpline);
        $tmpline = str_replace('{mod_suffix}','',$tmpline);
    }

    $tmpline = str_replace('{lat}', htmlspecialchars(help_latToDegreeStr($caches_record['latitude'])), $tmpline);
};
        $tmpline = str_replace('{cachetype}', htmlspecialchars(cache_type_from_id($caches_record['type'], $lang), ENT_COMPAT, 'UTF-8'), $tmpline);

        // sp2ong short_desc ermitteln TODO: nicht die erste sondern die richtige wĂ¤hlen
        $tmpline = str_replace('{wp_oc}', htmlspecialchars($caches_record['wp_oc'], ENT_COMPAT, 'UTF-8'), $tmpline);;
        $tmpline = str_replace('{latitude}', htmlspecialchars($caches_record['latitude'], ENT_COMPAT, 'UTF-8'), $tmpline);;
        $tmpline = str_replace('{longitude}', htmlspecialchars($caches_record['longitude'], ENT_COMPAT, 'UTF-8'), $tmpline);;

        $rsdesc = sql("SELECT `short_desc` FROM `cache_desc` WHERE `cache_id`='&1' AND `language`='$lang' LIMIT 1", $caches_record['cache_id']);
        $desc_record = sql_fetch_array($rsdesc);
        mysql_free_result($rsdesc);

        $tmpline = str_replace('{short_desc}', htmlspecialchars($desc_record['short_desc'], ENT_COMPAT, 'UTF-8'), $tmpline);

        $dDiff = abs(dateDiff('d', $caches_record['date_created'], date('Y-m-d')));
        if ($dDiff < $caches_olddays)
            $tmpline = str_replace('{new}', $caches_newstring, $tmpline);
        else
            $tmpline = str_replace('{new}', '', $tmpline);

        $tmpline = str_replace('{diffpic}', icon_difficulty("diff", $caches_record['difficulty']), $tmpline);
        $tmpline = str_replace('{terrpic}', icon_difficulty("terr", $caches_record['terrain']), $tmpline);

        // das letzte found suchen
        $sql = 'SELECT `cache_logs`.`id` `id`, `cache_logs`.`type` `type`, `cache_logs`.`date` `date`, `log_types`.`icon_small` `icon_small`
                FROM `cache_logs`, `log_types`
                WHERE `cache_logs`.`deleted`=0
                AND `cache_logs`.`cache_id`=\'' . sql_escape($caches_record['cache_id']) . '\'
                AND `log_types`.`id`=`cache_logs`.`type`
                ORDER BY `cache_logs`.`date` DESC LIMIT 6';
        $result = sql($sql);

        $sql_liczniki = 'SELECT count(cache_logs.type) as typy, cache_logs.type as type
                FROM `cache_logs`, `log_types`
                WHERE `cache_logs`.`cache_id`=\'' . sql_escape($caches_record['cache_id']) . '\'
                AND `cache_logs`.`deleted`=0
                AND `log_types`.`id`=`cache_logs`.`type`
                GROUP BY cache_logs.type
                ORDER BY cache_logs.type ASC';
        $result_liczniki = sql($sql_liczniki);

        $typy = array(0=>0,1=>0,2=>0);
        $typy_i=0;

        while ($row = sql_fetch_array($result_liczniki))
        {
            $typy[($row['type']-1)] = $row['typy'];
        }
        $tmpline = str_replace('{logtypes1}', "(<span style='color:green'>".$typy[0]."</span>/<span style='color:red'>".$typy[1]."</span>/<span style='color:black'>".$typy[2]."</span>)", $tmpline);

        if ($row = sql_fetch_array($result))
        {
            $tmpline = str_replace('{logimage1}',
                icon_log_type($row['icon_small'], ""). '<a href=\'viewlogs.php?cacheid='.htmlspecialchars($caches_record['cache_id'], ENT_COMPAT, 'UTF-8').'#'.htmlspecialchars($row['id'], ENT_COMPAT, 'UTF-8').'\'>{gray_s}' .date($logdateformat, strtotime($row['date'])) . '{gray_e}</a>', $tmpline);
            $tmpline = str_replace('{logdate1}', "", $tmpline);
        }
        else
        {
            $tmpline = str_replace('{logimage1}', "<img src='images/trans.gif' border='0' width='16' height='16' />", $tmpline);
            $tmpline = str_replace('{logdate1}', "--.--.----", $tmpline);
        }

        $lastlogs = "";
        while ($row = sql_fetch_array($result))
        {
            $lastlogs .= '<a href=\'viewlogs.php?cacheid=' . urlencode($caches_record['cache_id']) . '#' . htmlspecialchars($row['id'], ENT_COMPAT, 'UTF-8') . '\'>' . icon_log_type($row['icon_small'], '') . '</a>&nbsp;';
        }
        $tmpline = str_replace('{lastlogs}', $lastlogs, $tmpline);

        // und jetzt noch die Richtung ...
        if ($caches_record['distance'] > 0 && ($usr || !$hide_coords))
        {
            $tmpline = str_replace('{direction}', Bearing2Text(calcBearing($lat_rad / 3.14159 * 180, $lon_rad / 3.14159 * 180, $caches_record['latitude'], $caches_record['longitude']), 1), $tmpline);
        }
        else
            $tmpline = str_replace('{direction}', '', $tmpline);

        $desclangs = '';
        $aLangs = mb_split(',', $caches_record['desc_languages']);
        foreach ($aLangs AS $thislang)
        {
            $desclangs .= '<a href="viewcache.php?cacheid=' . urlencode($caches_record['cache_id']) . '&amp;desclang=' . urlencode($thislang) . '" style="text-decoration:none;"><b><font color="blue">' . htmlspecialchars($thislang, ENT_COMPAT, 'UTF-8') . '</font></b></a> ';
        }
        $tmpline = str_replace('{desclangs}', $desclangs, $tmpline);
        if($usr || !$hide_coords) {



            if ($caches_record['coord_modified'] == true) {
                $mod_suffix_garmin = '(F)';
            } else {
                $mod_suffix_garmin ='';
            };
            $tmpline = str_replace('{sendtogps}', ("<a href=\"#\" onclick=\"javascript:window.open('garmin.php?lat=".$caches_record['latitude']."&amp;long=".$caches_record['longitude']."&amp;wp=".$caches_record['wp_oc']."&amp;name=".urlencode($mod_suffix_garmin.$caches_record['name'])."&amp;popup=y','Send_To_GPS','width=450,height=160,resizable=no,scrollbars=0')\"><img src='/images/garmin.jpg' alt='Send to GPS' title='".$tr_Send_to_GPS."' border='0' /></a>"), $tmpline);
        } else {


            $tmpline = str_replace('{sendtogps}', "", $tmpline);
        };
        $tmpline = str_replace('{cachename}', htmlspecialchars($caches_record['name'], ENT_COMPAT, 'UTF-8'), $tmpline);
        $tmpline = str_replace('{urlencode_cacheid}', htmlspecialchars(urlencode($caches_record['cache_id']), ENT_COMPAT, 'UTF-8'), $tmpline);
        $tmpline = str_replace('{urlencode_userid}', htmlspecialchars(urlencode($caches_record['user_id']), ENT_COMPAT, 'UTF-8'), $tmpline);
        $tmpline = str_replace('{username}', htmlspecialchars($caches_record['username'], ENT_COMPAT, 'UTF-8'), $tmpline);
        if( $usr || !$hide_coords)
            $tmpline = str_replace('{distance}', htmlspecialchars(sprintf("%01.1f", $caches_record['distance']), ENT_COMPAT, 'UTF-8'), $tmpline);
        else
            $tmpline = str_replace('{distance}', "", $tmpline);
        $tmpline = str_replace('{position}', $i + $startat + 1, $tmpline);

        // backgroundcolor of line
        if (($i % 2) == 1)  $bgcolor = $bgcolor2;
        else                $bgcolor = $bgcolor1;

        if($inactive)
        {
            //$bgcolor = $bgcolor_inactive;
            $tmpline = str_replace('{gray_s}', "<span class='text_gray'>", $tmpline);
            $tmpline = str_replace('{gray_e}', "</span>", $tmpline);
        }
        else
        {
            $tmpline = str_replace('{gray_s}', "", $tmpline);
            $tmpline = str_replace('{gray_e}', "", $tmpline);
        }

        $tmpline = str_replace('{bgcolor}', $bgcolor, $tmpline);


        $caches_output .= $tmpline;
    }
    unset($dbc);
    tpl_set_var('results', $caches_output);

    //more than one page?
    if ($startat > 0)
    {
        $pages = '<a href="search.php?queryid=' . $options['queryid'] . '&amp;startat=0">{first_img}</a> <a href="search.php?queryid=' . $options['queryid'] . '&amp;startat=' . ($startat - $caches_per_page) . '">{prev_img}</a> ';
    }
    else
    {
        $pages = '{first_img_inactive} {prev_img_inactive} ';
    }

    $frompage = ($startat / $caches_per_page) - 3;
    if ($frompage < 1) $frompage = 1;

    $maxpage = ceil($resultcount / $caches_per_page);

    $topage = $frompage + 8;
    if ($topage > $maxpage) $topage = $maxpage;

    for ($i = $frompage; $i <= $topage; $i++)
    {
        if (($startat / $caches_per_page + 1) == $i)
        {
            $pages .= ' <b>' . $i . '</b>';
        }
        else
        {
            $pages .= ' <a href="search.php?queryid=' . $options['queryid'] . '&amp;startat=' . (($i - 1) * $caches_per_page) . '">' . $i . '</a>';
        }
    }

    if ($startat / $caches_per_page < ($maxpage - 1))
    {
        $pages .= ' <a href="search.php?queryid=' . $options['queryid'] . '&amp;startat=' . ($startat + $caches_per_page) . '">{next_img}</a> <a href="search.php?queryid=' . $options['queryid'] . '&amp;startat=' . (($maxpage - 1) * $caches_per_page) . '">{last_img}</a> ';
    }
    else
    {
        $pages .= ' {next_img_inactive} {last_img_inactive}';
    }

    $pages = mb_ereg_replace('{prev_img}', $prev_img, $pages);
    $pages = mb_ereg_replace('{next_img}', $next_img, $pages);
    $pages = mb_ereg_replace('{last_img}', $last_img, $pages);
    $pages = mb_ereg_replace('{first_img}', $first_img, $pages);

    $pages = mb_ereg_replace('{prev_img_inactive}', $prev_img_inactive, $pages);
    $pages = mb_ereg_replace('{next_img_inactive}', $next_img_inactive, $pages);
    $pages = mb_ereg_replace('{first_img_inactive}', $first_img_inactive, $pages);
    $pages = mb_ereg_replace('{last_img_inactive}', $last_img_inactive, $pages);

    //'<a href="search.php?queryid=' . $options['queryid'] . '&amp;startat=20">20</a> 40 60 80 100';
    //$caches_per_page
    //count($caches) - 1
    tpl_set_var('pages', $pages);

    // speichern-link
    if ($usr === false)
        tpl_set_var('safelink', '');
    else
        tpl_set_var('safelink', str_replace('{queryid}', $options['queryid'], $safelink));

    // downloads
    if( $usr || !$hide_coords)
        tpl_set_var('queryid', $options['queryid']);
    tpl_set_var('startat', $startat);

    tpl_set_var('startatp1', $startat + 1);

    //if (($resultcount - $startat) < 500)
        tpl_set_var('endat', $startat + $resultcount - $startat);
    //else
        //tpl_set_var('endat', $startat + 500);

    // kompatibilitĂ¤t!
    if ($distance_unit == 'sm')
        tpl_set_var('distanceunit', 'mi');
    else if ($distance_unit == 'nm')
        tpl_set_var('distanceunit', 'sm');
    else
        tpl_set_var('distanceunit', $distance_unit);

    if ($usr !== false){
        $queryid = $options['queryid'];
        $google_kml_link = $absolute_server_URI . "search.php?queryid=$queryid&output=kml&startat=$startat";
        $google_kml_link .= requestSigner::get_signature_text();
        
        $google_kml_link_all = $google_kml_link.'&count=max&zip=1';

        $domain = substr(trim($absolute_server_URI,'/'),-2,2); // It should be done better

        $google_maps_link = "http://maps.google.$domain/maps?f=q&hl=$lang&geocode=&q=";
        $google_maps_link_all = "http://maps.google.$domain/maps?f=q&hl=$lang&geocode=&ie=UTF8&z=7&q=";

        
        $google_maps_link = htmlentities($google_maps_link).urlencode($google_kml_link);
        $google_maps_link_all = htmlentities($google_maps_link_all).urlencode($google_kml_link_all);
        
        tpl_set_var('google_maps_link', $google_maps_link);
        tpl_set_var('google_maps_link_all', $google_maps_link_all);
    }

    if ($sqldebug == true)
        sqldbg_end();
    else
        tpl_BuildTemplate();
?>
