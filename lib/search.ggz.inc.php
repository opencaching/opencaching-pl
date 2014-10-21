<?php
        /***************************************************************************
                ./lib/search.zip.inc.php
        -------------------
                        begin                : January 28 2012
                        copyright            : (C) 2012 The OpenCaching Group
                        forum contact at     : http://forum.opencaching.pl

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

                Unicode Reminder ??

                Garmin zip search output (gpx + images for garmin devices)
                based on search.*.inc.php
                by Limak (opencaching.pl)

        ****************************************************************************/
        setlocale(LC_TIME, 'pl_PL.UTF-8');
        global $content, $bUseZip, $sqldebug, $usr, $hide_coords, $lang;

        set_time_limit(1800);


        if( $usr || !$hide_coords ) {
                    //prepare the output
                    $caches_per_page = 20;

                    $sql = 'SELECT ';

                    if (isset($lat_rad) && isset($lon_rad))
                    {
                                    $sql .= getCalcDistanceSqlFormula($usr !== false,$lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
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

                                                                    $sql .= getCalcDistanceSqlFormula($usr !== false,$record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
                                                    }
                                                    mysql_free_result($rs_coords);
                                    }
                    }
                    $sql .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`,
                        `caches`.`user_id` `user_id`, ';
                    if ($usr === false)
                    {
                        $sql .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id FROM `caches` ';
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

                    $sqlLimit = ' LIMIT ' . $startat . ', ' . $count;

        // cleanup (old zipcontent lingers if zip-download is cancelled by user)
        sql('DROP TEMPORARY TABLE IF EXISTS `zipcontent`');
                    // temporäre tabelle erstellen
                    sql('CREATE TEMPORARY TABLE `zipcontent` ' . $sql . $sqlLimit);
                    // echo $sql;
                    $rsCount = sql('SELECT COUNT(*) `count` FROM `zipcontent`');
                    $rCount = sql_fetch_array($rsCount);
                    mysql_free_result($rsCount);

                    $caches_count = $rCount['count'];

                    if ($rCount['count'] == 1)
                    {
                                    $rsName = sql('SELECT `caches`.`wp_oc` `wp_oc` FROM `zipcontent`, `caches` WHERE `zipcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
                                    $rName = sql_fetch_array($rsName);
                                    mysql_free_result($rsName);

                                    $sFilebasename = $rName['wp_oc'];
                    }
        else {
            if ($options['searchtype'] == 'bywatched') {
                $sFilebasename = 'watched_caches';
            } elseif ($options['searchtype'] == 'bylist') {
                $sFilebasename = 'cache_list';
            } elseif ($options['searchtype'] == 'bypt') {
                $sFilebasename = $options['gpxPtFileName'];
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

        //$bUseZip = ($rCount['count'] > 50);
        //$bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
        $bUseZip = false;

        // ok, ausgabe ...


                    // =======================================
                    // I don't know what code above doing (it's horrible and I don't have enough time to analyze this code),
                    // so I just modify existing piece of code from other output search.*.inc.php file.
                    // == Limak (28.01.2012) ==

                    // change this, only if OKAPI changes this value (in okapi/caches/formatters/garmin.php file)!
                    //if(isset($_REQUEST['okapidebug'])) 
                    $okapi_max_caches = 500; 
                    // else $okapi_max_caches = 50;

                    //zippart param in request is used for split ZIP files
                    if(!isset($_REQUEST['zippart'])) $_REQUEST['zippart'] = 0;
                    $zippart = abs(intval($_REQUEST['zippart'])) + 0;
                    $startat = ($zippart-1)*$okapi_max_caches;

                    // too much caches for one zip file - generate webpage instead
                    if(($caches_count > $okapi_max_caches) && ($zippart==0 || $startat>=$caches_count))
                    {
                        $tplname = 'garminzip';

                        tpl_set_var('zip_total_cache_count', $caches_count);
                        tpl_set_var('zip_max_count', $okapi_max_caches);

                        $links_content = '';
                        $forlimit=intval($caches_count/$okapi_max_caches)+1;
                        for($i=1;$i<=$forlimit;$i++)
                        {
                        $zipname='ocpl'.$options['queryid'].'.ggz?startat=0&count=max&zip=1&zippart='.$i.(isset($_REQUEST['okapidebug'])?'&okapidebug':'');
                        $links_content .= '<li><a class="links" href="'.$zipname.'" title="Garmin GGZ file (part '.$i.')">'.$sFilebasename.'-'.$i.'.ggz</a></li>';
                        }
                        tpl_set_var('zip_links', $links_content);
                        tpl_BuildTemplate();
                    }
                    else // caches are less or equals then okapi_max_caches in one ZIP file limit - okey, return ZIP file
                    {
                        // use 'LIMIT' only if it's needed
                        if($caches_count > $okapi_max_caches) $ziplimit = ' LIMIT '.$startat.','.$okapi_max_caches;
                        else $ziplimit = '';
                        // OKAPI need only waypoints
                        $rs = sql('SELECT `caches`.`wp_oc` `wp_oc` FROM `zipcontent`, `caches` WHERE `zipcontent`.`cache_id`=`caches`.`cache_id`'.$ziplimit);

                        $waypoints_tab = array();
                        while($r = sql_fetch_array($rs))
                        {
                            $waypoints_tab[] = $r['wp_oc'];
                        }
                        $waypoints = implode("|",$waypoints_tab);

                        mysql_free_result($rs);

                        // I don't know what this line doing, but other 'search.*.inc.php' files include this.
                        if ($sqldebug == true) sqldbg_end();

                        if (!isset($_SESSION))
                            session_start();  # prevent downloading multiple parts at once

                        // Including OKAPI's Facade. This is the only valid (and fast) interface to access
                        // OKAPI services from within OC code.
                        require_once($rootpath.'okapi/facade.php');

                        try
                        {
                            $okapi_response =  \okapi\Facade::service_call('services/caches/formatters/ggz',
                                $usr['userid'],
                                array(
                                    'cache_codes' => $waypoints, 
                                    'langpref' => $lang,
                                    'ns_ground' => 'true',
                                    'ns_ox' => 'true',
                                    'images' => 'ox:all',
                                    'attrs' => 'ox:tags',
                                    'trackables' => 'desc:count',
                                    'alt_wpts' => 'true',
                                    'recommendations' => 'desc:count',
                                    'latest_logs' => 'true',
                                    'lpc' => 'all',
                                    'my_notes' => isset($usr) ? "desc:text" : "none",
                                    'location_source'=> 'alt_wpt:user-coords', 
                                    'location_change_prefix' => '(F)'));

                            // Modifying OKAPI's default HTTP Response headers.
                            //$okapi_response->content_type = 'application/zip';
                            //$okapi_response->content_disposition = 'attachment; filename=' . $sFilebasename . (($zippart!=0)?'-'.$zippart:'') . '.zip';

                            // This outputs headers and the ZIP file.
                            $okapi_response->display();
                        }
                        catch (\okapi\BadRequest $e)
                        {
                            # In case of bad requests, simply output OKAPI's error response.
                            # In case of other, internal errors, don't catch the error. This
                            # will cause OKAPI's default error hangler to kick in (so the admins
                            # will get informed).

                            header('Content-Type: text/plain');
                            echo $e;
                            exit;
                        }
                        exit;

                    }
                }

                    // =======================================

?>
