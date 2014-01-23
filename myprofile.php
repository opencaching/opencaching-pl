<?php

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

     the users profile page

     used template(s): myprofile, myprofile_change
     parameter(s):     none

 ****************************************************************************/

    //prepare the templates and include all neccessary
    if (!isset($rootpath)) $rootpath = '';
    require_once('./lib/common.inc.php');

    //Preprocessing
    if ($error == false)
    {
        $description = "";
        //user logged in?
        if ($usr == false)
        {
            $target = urlencode(tpl_get_current_page());
            tpl_redirect('login.php?target='.$target);
        }
        else
        {
            tpl_set_var('desc_updated', '');
            if( isset($_POST['description']) )
            {
                $sql = "UPDATE user SET description = '".strip_tags(sql_escape($_POST['description']))."' WHERE user_id='".sql_escape($usr['userid'])."'";
                @mysql_query($sql);
                tpl_set_var('desc_updated',"<font color='green'>".tr('desc_updated')."</font>");

            }
            if( isset($_POST['submit']) )
            {
                $sql = "UPDATE user SET get_bulletin = ".intval(sql_escape($_POST['bulletin']))." WHERE user_id='".sql_escape($usr['userid'])."'";
                @mysql_query($sql);
            }
            $sql = "SELECT description, get_bulletin FROM user WHERE user_id = ".$usr['userid'];
            $query = @mysql_query($sql);
            $userinfo = @mysql_fetch_array($query);
            $description = $userinfo['description'];
            $bulletin = $userinfo['get_bulletin'];
            tpl_set_var('bulletin_label', $bulletin==1?(tr('bulletin_label_yes')):(tr('bulletin_label_no')));
            tpl_set_var('bulletin_value', $bulletin);
            tpl_set_var('is_checked', $bulletin==1?("checked"):(""));
            tpl_set_var('description',$description);

            $tplname = 'myprofile';
            require($stylepath . '/myprofile.inc.php');

            // check user can set as Geocaching guide
/*
            $rsnfc = sql("SELECT COUNT(`cache_logs`.`cache_id`) as num_fcaches FROM cache_logs,caches WHERE cache_logs.cache_id=caches.cache_id AND (caches.type='1' OR caches.type='2' OR caches.type='3' OR caches.type='7') AND cache_logs.type='1' AND cache_logs.deleted='0' AND `cache_logs`.`user_id` = ".sql_escape($usr['userid'])."");
            $rec = sql_fetch_array($rsnfc);
            $num_find_caches = $rec['num_fcaches'];

            $rsnc = sql("SELECT COUNT(`caches`.`cache_id`) as num_caches FROM `caches` WHERE `user_id` = ".sql_escape($usr['userid'])."
                                        AND (status = 1 OR status=2 OR status=3) AND (caches.type='1' OR caches.type='2' OR caches.type='3' OR caches.type='7')");
            $record = sql_fetch_array($rsnc);
            $num_caches = $record['num_caches'];
*/
            // Number of recommendations
            $nrec=sql("SELECT SUM(topratings) as nrecom FROM caches WHERE `caches`.`user_id`=&1",$usr['userid']);
            $nr = sql_fetch_array($nrec);
            $nrecom=$nr['nrecom'];

            //old
//          if ($num_caches>=5 && $num_find_caches>=5)
            if ($nrecom>=20)
                {
                    tpl_set_var('guide_start', '');
                    tpl_set_var('guide_end', '');
                    } else {
                    tpl_set_var('guide_start', '<!--');
                    tpl_set_var('guide_end', '-->');
                    }
            $rs = sql("SELECT `guru`,`username`, `email`, `country`, `latitude`, `longitude`, `date_created`, `pmr_flag`, `permanent_login_flag`, `no_htmledit_flag`, `notify_radius`, `ozi_filips` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
            $record = sql_fetch_array($rs);

            if ($record['guru']==1){
                    tpl_set_var('guides_start', '');
                    tpl_set_var('guides_end', '');
                    } else {
                    tpl_set_var('guides_start', '<!--');
                    tpl_set_var('guides_end', '-->');
                    }
            tpl_set_var('userid', $usr['userid']+0);
            tpl_set_var('profileurl', $absolute_server_URI.'viewprofile.php?userid=' . ($usr['userid']+0));
            tpl_set_var('statlink', $absolute_server_URI.'statpics/' . ($usr['userid']+0) . '.jpg');
            tpl_set_var('username', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('username_html', htmlspecialchars(htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), ENT_COMPAT, 'UTF-8'));
            tpl_set_var('email', htmlspecialchars($record['email'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('country', htmlspecialchars(db_CountryFromShort($record['country']), ENT_COMPAT, 'UTF-8'));
            tpl_set_var('coords', htmlspecialchars(help_latToDegreeStr($record['latitude']), ENT_COMPAT, 'UTF-8') . '<br />' . htmlspecialchars(help_lonToDegreeStr($record['longitude']), ENT_COMPAT, 'UTF-8'));
            tpl_set_var('registered_since', fixPlMonth(htmlspecialchars(strftime($dateformat, strtotime($record['date_created'])), ENT_COMPAT, 'UTF-8')));
            tpl_set_var('notify_radius', htmlspecialchars($record['notify_radius'] + 0, ENT_COMPAT, 'UTF-8'));

            /*GeoKretyApi - display if secid from geokrety is set; (by Łza) */
            $GKAPIKeyQuery = sql("SELECT `secid` FROM `GeoKretyAPI` WHERE `userID` ='&1'", $usr['userid']);
            $GKAPIKeyrecord = sql_fetch_array($GKAPIKeyQuery);
            tpl_set_var('GeoKretyApiSecid', $GKAPIKeyrecord['secid']);

            if (mysql_num_rows($GKAPIKeyQuery) > 0) tpl_set_var('GeoKretyApiIntegration', tr('yes'));
            else tpl_set_var('GeoKretyApiIntegration', tr('no'));
            /* end of GeoKretyApi*/

            if($record['notify_radius'] + 0 > 0)
            {
                tpl_set_var('notify', mb_ereg_replace('{radius}', $record['notify_radius'] + 0, $notify_radius_message));
            }
            else
            {
                tpl_set_var('notify', $no_notify_message);
            }

            //misc user options
            $using_pmr = $record['pmr_flag'];
            $using_permantent_login = $record['permanent_login_flag'];
            $no_htmledit = $record['no_htmledit_flag'];

            if (isset($_REQUEST['action']))
            {
                $action = $_REQUEST['action'];

                if ($action == 'change')
                {
                    //display the change form
                    $tplname = 'myprofile_change';
                    require_once($stylepath . '/myprofile_change.inc.php');
            // check user can set as Geocaching guide
/*
            $rsnfc = sql("SELECT COUNT(`cache_logs`.`cache_id`) as num_fcaches FROM cache_logs,caches WHERE cache_logs.cache_id=caches.cache_id AND (caches.type='1' OR caches.type='2' OR caches.type='3' OR caches.type='7') AND cache_logs.type='1' AND cache_logs.deleted='0' AND `cache_logs`.`user_id` = ".sql_escape($usr['userid'])."");
            $rec = sql_fetch_array($rsnfc);
            $num_find_caches = $rec['num_fcaches'];

            $rsnc = sql("SELECT COUNT(`caches`.`cache_id`) as num_caches FROM `caches` WHERE `user_id` = ".sql_escape($usr['userid'])."
                                        AND (status = 1 OR status=2 OR status=3) AND (caches.type='1' OR caches.type='2' OR caches.type='3' OR caches.type='7')");
            $record = sql_fetch_array($rsnc);
            $num_caches = $record['num_caches'];
    */
            // Number of recommendations
            $nrec=sql("SELECT SUM(topratings) as nrecom FROM caches WHERE `caches`.`user_id`=&1",$usr['userid']);
            $nr = sql_fetch_array($nrec);
            $nrecom=$nr['nrecom'];

            //old
//          if ($num_caches>=5 && $num_find_caches>=5)
            if ($nrecom>=20)
                    {
                    tpl_set_var('guide_start', '');
                    tpl_set_var('guide_end', '');
                    } else {
                    tpl_set_var('guide_start', '<!--');
                    tpl_set_var('guide_end', '-->');
                    }
                    if (isset($_POST['submit']) || isset($_POST['submit_all_countries']))
                    {
                        //load datas from form
                        $show_all_countries = $_POST['show_all_countries'];
                        $username = $_POST['username'];
                        $country = $_POST['country'];
                        $radius = $_POST['notify_radius'];
                        $ozi_path = strip_tags($_POST['ozi_path']);
                        tpl_set_var('ozi_path', $ozi_path);

                        $using_permantent_login = isset($_POST['using_permanent_login']) ? (int)$_POST['using_permanent_login'] : 0;
                        if ($using_permantent_login == 1)
                        {
                            tpl_set_var('permanent_login_sel', ' checked="checked"');
                        }
                        else
                        {
                            tpl_set_var('permanent_login_sel', '');
                        }

                        $using_pmr = isset($_POST['using_pmr']) ? (int)$_POST['using_pmr'] : 0;
                        if ($using_pmr == 1)
                        {
                            tpl_set_var('pmr_sel', ' checked="checked"');
                        }
                        else
                        {
                            tpl_set_var('pmr_sel', '');
                        }
                        $guide = isset($_POST['guide']) ? (int)$_POST['guide'] : 0;
                        if ($guide == 1)
                        {
                            tpl_set_var('guide_sel', ' checked="checked"');
                        }
                        else
                        {
                            tpl_set_var('guide_sel', '');
                        }
                        $no_htmledit = isset($_POST['no_htmledit']) ? (int)$_POST['no_htmledit'] : 0;
                        if ($no_htmledit == 1)
                        {
                            tpl_set_var('no_htmledit_sel', ' checked="checked"');
                        }
                        else
                        {
                            tpl_set_var('no_htmledit_sel', '');
                        }

                        $latNS = $_POST['latNS'];
                        if ($latNS == 'N')
                        {
                            tpl_set_var('latNsel', ' selected="selected"');
                            tpl_set_var('latSsel', '');
                        }
                        else
                        {
                            tpl_set_var('latSsel', ' selected="selected"');
                            tpl_set_var('latNsel', '');
                        }
                        $lonEW = $_POST['lonEW'];
                        if ($lonEW == 'E')
                        {
                            tpl_set_var('lonEsel', ' selected="selected"');
                            tpl_set_var('lonWsel', '');
                        }
                        else
                        {
                            tpl_set_var('lonWsel', ' selected="selected"');
                            tpl_set_var('lonEsel', '');
                        }

                        $lat_h = $_POST['lat_h'];
                        $lat_min = $_POST['lat_min'];
                        $lon_h = $_POST['lon_h'];
                        $lon_min = $_POST['lon_min'];

                        $GeoKretyApiSecid = mysql_real_escape_string($_POST['GeoKretyApiSecid']);

                        tpl_set_var('username', $username);

                        tpl_set_var('notify_radius', $radius);

                        //set user messages
                        tpl_set_var('username_message', '');
                        tpl_set_var('lat_message', '');
                        tpl_set_var('lon_message', '');
                        tpl_set_var('notify_message', '');

                        //validate data
                        $username_not_ok = mb_ereg_match(regex_username, $username) ? false : true;
                        if ($username_not_ok == false)
                        {
                            // username should not be formatted like an email-address
                            $username_not_ok = is_valid_email_address($username) ? true : false;
                        }

                        /*GeoKretyApi validate secid*/
                        if ((strlen($GeoKretyApiSecid) != 128))
                        {
                            tpl_set_var('secid_message', tr('GKApi11'));
                            $secid_not_ok = true;
                        }
                        else
                        {
                            $secid_not_ok = false;
                            tpl_set_var('secid_message', '');
                        }
                        if ($GeoKretyApiSecid == '')
                        {
                            $secid_not_ok = false;
                            tpl_set_var('secid_message', '');
                        }

                        //check coordinates
                        if ($lat_h!='' || $lat_min!='')
                        {
                            if (!mb_ereg_match('^[0-9]{1,2}$', $lat_h))
                            {
                                tpl_set_var('lat_message', $error_coords_not_ok);
                                $lat_h_not_ok = true;
                            }
                            else
                            {
                                if (($lat_h >= 0) && ($lat_h < 90))
                                {
                                    $lat_h_not_ok = false;
                                }
                                else
                                {
                                    tpl_set_var('lat_message', $error_coords_not_ok);
                                    $lat_h_not_ok = true;
                                }
                            }

                            if (is_numeric($lat_min))
                            {
                                if (($lat_min >= 0) && ($lat_min < 60))
                                {
                                    $lat_min_not_ok = false;
                                }
                                else
                                {
                                    tpl_set_var('lat_message', $error_coords_not_ok);
                                    $lat_min_not_ok = true;
                                }
                            }
                            else
                            {
                                tpl_set_var('lat_message', $error_coords_not_ok);
                                $lat_min_not_ok = true;
                            }

                            $latitude = $lat_h + $lat_min / 60;
                            if ($latNS == 'S') $latitude = -$latitude;
                        }
                        else
                        {
                            $latitude = NULL;
                            $lat_h_not_ok = false;
                            $lat_min_not_ok = false;
                        }

                        if ($lon_h!='' || $lon_min!='')
                        {
                            if (!mb_ereg_match('^[0-9]{1,3}$', $lon_h))
                            {
                                tpl_set_var('lon_message', $error_coords_not_ok);
                                $lon_h_not_ok = true;
                            }
                            else
                            {
                                if (($lon_h >= 0) && ($lon_h < 180))
                                {
                                    $lon_h_not_ok = false;
                                }
                                else
                                {
                                    tpl_set_var('lon_message', $error_coords_not_ok);
                                    $lon_h_not_ok = true;
                                }
                            }

                            if (is_numeric($lon_min))
                            {
                                if (($lon_min >= 0) && ($lon_min < 60))
                                {
                                    $lon_min_not_ok = false;
                                }
                                else
                                {
                                    tpl_set_var('lon_message', $error_coords_not_ok);
                                    $lon_min_not_ok = true;
                                }
                            }
                            else
                            {
                                tpl_set_var('lon_message', $error_coords_not_ok);
                                $lon_min_not_ok = true;
                            }

                            $longitude = $lon_h + $lon_min / 60;
                            if ($lonEW == 'W') $longitude = -$longitude;
                        }
                        else
                        {
                            $longitude = NULL;
                            $lon_h_not_ok = false;
                            $lon_min_not_ok = false;
                        }

                        $lon_not_ok = $lon_min_not_ok || $lon_h_not_ok;
                        $lat_not_ok = $lat_min_not_ok || $lat_h_not_ok;

                        //check if username is in the database
                        $username_exists = false;
                        $username_not_ok = mb_ereg_match(regex_username, $username) ? false : true;
                        if ($username_not_ok == false)
                        {
                            // username should not be formatted like an email-address
                            // exception: $username == $email
                            $username_not_ok = is_valid_email_address($username) ? true : false;
                        }
                        if ($username_not_ok)
                        {
                            tpl_set_var('username_message', $error_username_not_ok);
                        }
                        else
                        {
                            if ($username != $usr['username'])
                            {
                                $rs = sql("SELECT `username` FROM `user` WHERE `username`='&1'", $username);
                                if (mysql_num_rows($rs) > 0)
                                {
                                    $username_exists = true;
                                    tpl_set_var('username_message', $error_username_exists);
                                }
                            }
                        }

                        if ($radius != '')
                        {
                            $radius = $radius+0;
                            $radius_not_ok = (($radius >= 0) && ($radius <= 150)) ? false : true;
                            if ($radius_not_ok)
                            {
                                tpl_set_var('notify_message', $error_radius_not_ok);
                            }
                        }
                        else
                        {
                            $radius_not_ok = false;
                        }

                        //submit
                        if (isset($_POST['submit']))
                        {
                            //try to save
                            if (!($username_not_ok ||
                                $username_exists ||
                                $lon_not_ok ||
                                $lat_not_ok ||
                                $radius_not_ok ||
                                $secid_not_ok))
                            {


                                /*GeoKretyApi by Łza*/
                                /*insert or update in database user secid from Geokrety*/
                                if (strlen($GeoKretyApiSecid)== 128)
                                {
                                    mysql_query("insert into `GeoKretyAPI` (`userID`, `secid`) values (".$usr['userid'].",'$GeoKretyApiSecid') on duplicate key update `secid`='$GeoKretyApiSecid'") or die(mysql_error());
                                    tpl_set_var('GeoKretyApiIntegration', 'TAK');
                                }
                                if ($GeoKretyApiSecid == '')
                                {
                                    mysql_query("DELETE FROM `GeoKretyAPI` WHERE `userID` = ".$usr['userid'] );
                                    tpl_set_var('GeoKretyApiIntegration', 'NIE');
                                }

                                //in DB updaten
                                sql("UPDATE `user` SET `username`='&1', `last_modified`=NOW(), `latitude`='&2', `longitude`='&3', `pmr_flag`='&4', `country`='&5', `permanent_login_flag`='&6', `no_htmledit_flag`='&8' , `notify_radius`='&9', `ozi_filips`='&10',`guru`='&11' WHERE `user_id`='&7'", $username, $latitude, $longitude, $using_pmr, $country, $using_permantent_login, $usr['userid'], $no_htmledit, $radius, $ozi_path,$guide);

                                //wieder normal anzeigen
                                $tplname = 'myprofile';

                                //variablen updaten
                                tpl_set_var('country', htmlspecialchars(db_CountryFromShort($country), ENT_COMPAT, 'UTF-8'));
                                tpl_set_var('coords', htmlspecialchars(help_latToDegreeStr($latitude), ENT_COMPAT, 'UTF-8') . '<br />' . htmlspecialchars(help_lonToDegreeStr($longitude), ENT_COMPAT, 'UTF-8'));



                                if($radius + 0 > 0)
                                {
                                    tpl_set_var('notify', mb_ereg_replace('{radius}', $radius + 0, $notify_radius_message));
                                }
                                else
                                {
                                    tpl_set_var('notify', $no_notify_message);
                                }
                            }
                        }
                    }
                    else
                    {
                        //load from database
            $rs = sql("SELECT `guru`,`username`, `email`, `country`, `latitude`, `longitude`, `date_created`, `pmr_flag`, `permanent_login_flag`, `no_htmledit_flag`, `notify_radius`, `ozi_filips` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
            $record = sql_fetch_array($rs);
                            if ($record['guru']==1|| $guide == 1){
                    tpl_set_var('guides_start', '');
                    tpl_set_var('guides_end', '');
                    } else {
                    tpl_set_var('guides_start', '<!--');
                    tpl_set_var('guides_end', '-->');
                    }
                        $show_all_countries = 0;
                        $country = $record['country'];
                        $guide= $record['guru'];
                        $longitude = $record['longitude'];
                        $latitude = $record['latitude'];
                        $using_pmr = $record['pmr_flag'];
                        $using_permantent_login = $record['permanent_login_flag'];
                        $ozi_path = strip_tags($record['ozi_filips']);
                        tpl_set_var('ozi_path', $ozi_path);

                        if ($using_permantent_login == 1)
                        {
                            tpl_set_var('permanent_login_sel', ' checked="checked"');
                        }
                        else
                        {
                            tpl_set_var('permanent_login_sel', '');
                        }

                        if ($using_pmr == 1)
                        {
                            tpl_set_var('pmr_sel', ' checked="checked"');
                        }
                        else
                        {
                            tpl_set_var('pmr_sel', '');
                        }

                        if ($guide == 1)
                        {
                            tpl_set_var('guide_sel', ' checked="checked"');
                        }
                        else
                        {
                            tpl_set_var('guide_sel', '');
                        }
                        if ($no_htmledit == 1)
                        {
                            tpl_set_var('no_htmledit_sel', ' checked="checked"');
                        }
                        else
                        {
                            tpl_set_var('no_htmledit_sel', '');
                        }

                        if ($longitude < 0)
                        {
                            $lonEW = 'W';
                            $longitude = -$longitude;
                        }
                        else
                        {
                            $lonEW = 'E';
                        }
                        if ($latitude < 0)
                        {
                            $latNS = 'S';
                            $latitude = -$latitude;
                        }
                        else
                        {
                            $latNS = 'N';
                        }

                        if ($latNS == 'N')
                        {
                            tpl_set_var('latNsel', ' selected="selected"');
                            tpl_set_var('latSsel', '');
                        }
                        else
                        {
                            tpl_set_var('latSsel', ' selected="selected"');
                            tpl_set_var('latNsel', '');
                        }
                        if ($lonEW == 'E')
                        {
                            tpl_set_var('lonEsel', ' selected="selected"');
                            tpl_set_var('lonWsel', '');
                        }
                        else
                        {
                            tpl_set_var('lonWsel', ' selected="selected"');
                            tpl_set_var('lonEsel', '');
                        }

                        $lat_h = floor($latitude);
                        $lon_h = floor($longitude);

                        $lat_min = sprintf("%02.3f", round(($latitude - $lat_h) * 60, 3));
                        $lon_min = sprintf("%02.3f", round(($longitude - $lon_h) * 60, 3));

                        //set user messages
                        tpl_set_var('username_message', '');
                        tpl_set_var('lat_message', '');
                        tpl_set_var('lon_message', '');
                        tpl_set_var('notify_message', '');
                        tpl_set_var('secid_message', '');


                    }
                            if ($record['guru']==1 || $guide == 1){
                    tpl_set_var('guides_start', '');
                    tpl_set_var('guides_end', '');
                    } else {
                    tpl_set_var('guides_start', '<!--');
                    tpl_set_var('guides_end', '-->');
                    }
                    tpl_set_var('lat_h', $lat_h);
                    tpl_set_var('lon_h', $lon_h);
                    tpl_set_var('lat_min', $lat_min);
                    tpl_set_var('lon_min', $lon_min);

                    //load the country list
                    if ($country == 'XX')
                    {
                        $stmp = '<option value="XX" selected="selected">' . $no_answer . '</option>';
                    }
                    else
                    {
                        $stmp = '<option value="XX">' . $no_answer . '</option>';
                    }

                    if (isset($_POST['submit_all_countries']))
                    {
                        $show_all_countries = 1;
                    }

                    if(checkField('countries','list_default_'.$lang) )
                                                $lang_db = $lang ;
                                        else
                                                $lang_db = "en";

                    //Country in defaults ?
                    if (($show_all_countries == 0) && ($country != 'XX'))
                    {
                        $rs2 = sql("SELECT `list_default_" . sql_escape($lang_db) . "` FROM `countries` WHERE `short`='&1'", $country);
                        $record2 = sql_fetch_array($rs2);
                        if ($record2['list_default_' . $lang_db] == 0)
                        {
                            $show_all_countries = 1;
                        }
                        else
                        {
                            $show_all_countries = 0;
                        }
                        mysql_free_result($rs2);
                    }

                    if ($show_all_countries == 1)
                    {
                        $rs2 = sql("SELECT `&1`, `list_default_" . sql_escape($lang_db) . "`, `short`, `sort_" . sql_escape($lang_db) . "` FROM `countries` ORDER BY `sort_" . sql_escape($lang_db) . '` ASC', $lang_db);
                    }
                    else
                    {
                        $rs2 = sql("SELECT `&1`, `list_default_" . sql_escape($lang_db) . "`, `short`, `sort_" . sql_escape($lang_db) . "` FROM `countries` WHERE `list_default_" . sql_escape($lang_db) . "`=1 ORDER BY `sort_" . sql_escape($lang_db) . '` ASC', $lang_db);
                    }

                    for ($i = 0; $i < mysql_num_rows($rs2); $i++)
                    {
                        $record2 = sql_fetch_array($rs2);

                        if ($record2['short'] == $country)
                        {
                            $stmp .= '<option value="' . $record2['short'] . '" selected="selected">' . htmlspecialchars($record2[$lang_db], ENT_COMPAT, 'UTF-8') . "</option>\n";
                        }
                        else
                        {
                            $stmp .= '<option value="' . $record2['short'] . '">' . htmlspecialchars($record2[$lang_db], ENT_COMPAT, 'UTF-8') . "</option>\n";
                        }
                    }

                    tpl_set_var('countrylist', $stmp);
                    unset($stmp);
                    tpl_set_var('show_all_countries', $show_all_countries);

                    if ($show_all_countries == 0)
                    {
                        tpl_set_var('allcountriesbutton', '<input type="submit" class="formbuttons" name="submit_all_countries" value="' . $allcountries . '" />');
                    }
                    else
                    {
                        tpl_set_var('allcountriesbutton', '');
                    }
                }
            }

            //build useroptions
            $user_options = '';
            if ($using_permantent_login == 1)
            {
                $user_options .= $using_permantent_login_message . "\n";
            }
            if ($no_htmledit == 1)
            {
                $user_options .= $no_htmledit_message . "\n";
            }
            if ($using_pmr == 1)
            {
                $user_options .= $using_pmr_message . "\n";
            }
            if ($user_options == '') $user_options = '&nbsp;';
            tpl_set_var('user_options', $user_options);
            $ozi_path = strip_tags($record['ozi_filips']);
            if( isset($_POST['ozi_path']))
                tpl_set_var('ozi_path', strip_tags($_POST['ozi_path']));
            else
                tpl_set_var('ozi_path', $ozi_path);
        }
    }

    //make the template and send it out
    tpl_BuildTemplate();
?>
