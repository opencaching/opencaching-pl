<?php
/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
     the users profile page

     used template(s): myprofile, myprofile_change
 ****************************************************************************/
//prepare the templates and include all neccessary
if (!isset($rootpath)){
    $rootpath = '';
}
require_once('./lib/common.inc.php');
//Preprocessing
if ($error == false) {
    $db = new dataBase;
    $description = "";
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target='.$target);
    } else {
        tpl_set_var('desc_updated', '');
        tpl_set_var('displayGeoPathSection', displayGeoPatchSection('table'));
        if(isset($_POST['description'])) {
            $sql = "UPDATE user SET description = :1 WHERE user_id=:2";
            $db->multiVariableQuery($sql, strip_tags($_POST['description']), (int) $usr['userid']);
            $db->reset();
            tpl_set_var('desc_updated',"<font color='green'>".tr('desc_updated')."</font>");
        }
        if(isset($_POST['submit'])) {
            $sql = "UPDATE user SET get_bulletin = :1 WHERE user_id = :2 ";
            $db->multiVariableQuery($sql,intval(sql_escape($_POST['bulletin'])), (int) $usr['userid']);
            $db->reset();
        }
        $sql = "SELECT description, get_bulletin FROM user WHERE user_id = :1 LIMIT 1";
        $db->multiVariableQuery($sql, (int) $usr['userid']);
        $userinfo = $db->dbResultFetchOneRowOnly();
        $description = $userinfo['description'];
        $bulletin = $userinfo['get_bulletin'];
        tpl_set_var('bulletin_label', $bulletin==1?(tr('bulletin_label_yes')):(tr('bulletin_label_no')));
        tpl_set_var('bulletin_value', $bulletin);
        tpl_set_var('is_checked', $bulletin==1?("checked"):(""));
        tpl_set_var('description',$description);
        $tplname = 'myprofile';
        $using_permantent_login_message = tr('no_auto_logout');
        $no_htmledit_message = tr('hide_html_editor');
        $notify_radius_message = tr('notify_new_caches_radius').' '.tr('radius').' km';
        $no_notify_message = tr('no_new_caches_notification');

        // check user can set as Geocaching guide
        // Number of recommendations
        $nrecom = $db->multiVariableQueryValue("SELECT SUM(topratings) as nrecom FROM caches WHERE `caches`.`user_id`= :1",0,$usr['userid']);
        if ($nrecom>=20) {
            tpl_set_var('guide_start', '');
            tpl_set_var('guide_end', '');
        } else {
            tpl_set_var('guide_start', '<!--');
            tpl_set_var('guide_end', '-->');
        }
        $sql="SELECT `guru`,`username`, `email`, `country`, `latitude`, `longitude`, `date_created`, `permanent_login_flag`, `power_trail_email`, `notify_radius`, `ozi_filips` FROM `user` WHERE `user_id`=:1 ";
        $db->multiVariableQuery($sql, $usr['userid']);
        $record = $db->dbResultFetchOneRowOnly();
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
        $GKAPIKeyQuery = "SELECT `secid` FROM `GeoKretyAPI` WHERE `userID` =:1";
        $db->multiVariableQuery($GKAPIKeyQuery, $usr['userid']);
        if ($db->rowCount() > 0) {
            tpl_set_var('GeoKretyApiIntegration', tr('yes'));
        } else {
            tpl_set_var('GeoKretyApiIntegration', tr('no'));
        }
        $GKAPIKeyrecord = $db->dbResultFetchOneRowOnly();
        tpl_set_var('GeoKretyApiSecid', $GKAPIKeyrecord['secid']);
        if($record['notify_radius'] + 0 > 0){
            tpl_set_var('notify', mb_ereg_replace('{radius}', $record['notify_radius'] + 0, $notify_radius_message));
        } else {
            tpl_set_var('notify', $no_notify_message);
        }

        //misc user options
        $using_permantent_login = $record['permanent_login_flag'];
        $geoPathsEmail = $record['power_trail_email'];

        if (isset($_REQUEST['action'])) {
            $action = $_REQUEST['action'];
            if ($action == 'change') { //display the change form
                $tplname = 'myprofile_change';
                $change_data = tr('change');
                $allcountries = tr('show_all');
                $no_answer = tr('no_choice');
                $error_username_not_ok = '<span class="errormsg">'.tr('username_incorrect').'</span>';
                $error_username_exists = '<span class="errormsg">'.tr('username_exists').'</span>';
                $error_coords_not_ok = '<span class="errormsg">'.tr('bad_coordinates').'</span>';
                $error_radius_not_ok = '<span class="errormsg">'.tr('bad_radius').'</span>';
                if ($nrecom>=20) {
                    tpl_set_var('guide_start', '');
                    tpl_set_var('guide_end', '');
                } else {
                    tpl_set_var('guide_start', '<!--');
                    tpl_set_var('guide_end', '-->');
                }
                $guide = isset($_POST['guide']) ? (int) $_POST['guide'] : 0;
                if (isset($_POST['submit']) || isset($_POST['submit_all_countries'])) {
                    //load datas from form
                    $show_all_countries = $_POST['show_all_countries'];
                    $username = $_POST['username'];
                    $country = $_POST['country'];
                    $radius = $_POST['notify_radius'];
                    $ozi_path = strip_tags($_POST['ozi_path']);
                    tpl_set_var('ozi_path', $ozi_path);

                    $using_permantent_login = isset($_POST['using_permanent_login']) ? (int)$_POST['using_permanent_login'] : 0;
                    if ($using_permantent_login == 1) {
                        tpl_set_var('permanent_login_sel', ' checked="checked"');
                    } else {
                        tpl_set_var('permanent_login_sel', '');
                    }

                    if ($guide == 1) {
                        tpl_set_var('guide_sel', ' checked="checked"');
                    } else {
                        tpl_set_var('guide_sel', '');
                    }
                    /* geoPaths - switch on/off notification email*/
                    $geoPathsEmail = isset($_POST['geoPathsEmail']) ? (int) $_POST['geoPathsEmail'] : 0;
                    if ($geoPathsEmail == 1) {
                        tpl_set_var('geoPathsEmailCheckboxChecked', ' checked="checked"');
                    } else {
                        tpl_set_var('geoPathsEmailCheckboxChecked', '');
                    }

                    $latNS = $_POST['latNS'];
                    if ($latNS == 'N') {
                        tpl_set_var('latNsel', ' selected="selected"');
                        tpl_set_var('latSsel', '');
                    } else {
                        tpl_set_var('latSsel', ' selected="selected"');
                        tpl_set_var('latNsel', '');
                    }
                    $lonEW = $_POST['lonEW'];
                    if ($lonEW == 'E') {
                        tpl_set_var('lonEsel', ' selected="selected"');
                        tpl_set_var('lonWsel', '');
                    } else  {
                        tpl_set_var('lonWsel', ' selected="selected"');
                        tpl_set_var('lonEsel', '');
                    }

                    $lat_h = $_POST['lat_h'];
                    $lat_min = $_POST['lat_min'];
                    $lon_h = $_POST['lon_h'];
                    $lon_min = $_POST['lon_min'];
                    $GeoKretyApiSecid = addslashes($_POST['GeoKretyApiSecid']);

                    //set user messages
                    tpl_set_var('username', $username);
                    tpl_set_var('notify_radius', $radius);
                    tpl_set_var('username_message', '');
                    tpl_set_var('lat_message', '');
                    tpl_set_var('lon_message', '');
                    tpl_set_var('notify_message', '');

                    //validate data
                    $username_not_ok = mb_ereg_match(regex_username, $username) ? false : true;
                    if ($username_not_ok == false) { // username should not be formatted like an email-address
                        $username_not_ok = is_valid_email_address($username) ? true : false;
                    }

                    /*GeoKretyApi validate secid*/
                    if ((strlen($GeoKretyApiSecid) != 128)) {
                        tpl_set_var('secid_message', tr('GKApi11'));
                        $secid_not_ok = true;
                    } else {
                        $secid_not_ok = false;
                        tpl_set_var('secid_message', '');
                    }
                    if ($GeoKretyApiSecid == '') {
                        $secid_not_ok = false;
                        tpl_set_var('secid_message', '');
                    }

                    //check coordinates
                    if ($lat_h!='' || $lat_min!='') {
                        if (!mb_ereg_match('^[0-9]{1,2}$', $lat_h)) {
                            tpl_set_var('lat_message', $error_coords_not_ok);
                            $lat_h_not_ok = true;
                        } else {
                            if (($lat_h >= 0) && ($lat_h < 90)) {
                                $lat_h_not_ok = false;
                            } else  {
                                tpl_set_var('lat_message', $error_coords_not_ok);
                                $lat_h_not_ok = true;
                            }
                        }

                        if (is_numeric($lat_min))  {
                            if (($lat_min >= 0) && ($lat_min < 60)) {
                                $lat_min_not_ok = false;
                            } else {
                                tpl_set_var('lat_message', $error_coords_not_ok);
                                $lat_min_not_ok = true;
                            }
                        } else {
                            tpl_set_var('lat_message', $error_coords_not_ok);
                            $lat_min_not_ok = true;
                        }

                        $latitude = $lat_h + $lat_min / 60;
                        if ($latNS == 'S') {
                            $latitude = -$latitude;
                        }
                    } else {
                        $latitude = NULL;
                        $lat_h_not_ok = false;
                        $lat_min_not_ok = false;
                    }

                    if ($lon_h!='' || $lon_min!='') {
                        if (!mb_ereg_match('^[0-9]{1,3}$', $lon_h)) {
                            tpl_set_var('lon_message', $error_coords_not_ok);
                            $lon_h_not_ok = true;
                        } else  {
                            if (($lon_h >= 0) && ($lon_h < 180)) {
                                $lon_h_not_ok = false;
                            } else {
                                tpl_set_var('lon_message', $error_coords_not_ok);
                                $lon_h_not_ok = true;
                            }
                        }

                        if (is_numeric($lon_min)) {
                            if (($lon_min >= 0) && ($lon_min < 60)) {
                                $lon_min_not_ok = false;
                            } else  {
                                tpl_set_var('lon_message', $error_coords_not_ok);
                                $lon_min_not_ok = true;
                            }
                        } else {
                            tpl_set_var('lon_message', $error_coords_not_ok);
                            $lon_min_not_ok = true;
                        }
                        $longitude = $lon_h + $lon_min / 60;
                        if ($lonEW == 'W') {
                            $longitude = -$longitude;
                        }
                    } else {
                        $longitude = null;
                        $lon_h_not_ok = false;
                        $lon_min_not_ok = false;
                    }

                    $lon_not_ok = $lon_min_not_ok || $lon_h_not_ok;
                    $lat_not_ok = $lat_min_not_ok || $lat_h_not_ok;

                    //check if username is in the database
                    $username_exists = false;
                    $username_not_ok = mb_ereg_match(regex_username, $username) ? false : true;
                    if ($username_not_ok == false) {
                        // username should not be formatted like an email-address
                        // exception: $username == $email
                        $username_not_ok = is_valid_email_address($username) ? true : false;
                    }
                    if ($username_not_ok) {
                        tpl_set_var('username_message', $error_username_not_ok);
                    } else {
                        if ($username != $usr['username']) {
                            $sql = "SELECT `username` FROM `user` WHERE `username`=:1";
                            $db->multiVariableQuery($sql, $username);
                            if ($db->rowCount() > 0) {
                                $username_exists = true;
                                tpl_set_var('username_message', $error_username_exists);
                            }
                        }
                    }

                    if ($radius != '') {
                        $radius = $radius+0;
                        $radius_not_ok = (($radius >= 0) && ($radius <= 150)) ? false : true;
                        if ($radius_not_ok) {
                            tpl_set_var('notify_message', $error_radius_not_ok);
                        }
                    } else {
                        $radius_not_ok = false;
                    }

                    //submit
                    if (isset($_POST['submit'])) {
                        //try to save
                        if (!($username_not_ok ||
                            $username_exists ||
                            $lon_not_ok ||
                            $lat_not_ok ||
                            $radius_not_ok ||
                            $secid_not_ok)) {

                            /* GeoKretyApi - insert or update in database user secid from Geokrety */
                            if (strlen($GeoKretyApiSecid) == 128) {
                                $db->multiVariableQuery("insert into `GeoKretyAPI` (`userID`, `secid`) values (:1, :2) on duplicate key update `secid`=:2", $usr['userid'], $GeoKretyApiSecid);
                                $db->reset();
                                tpl_set_var('GeoKretyApiIntegration', tr('yes'));
                            } elseif ($GeoKretyApiSecid == '') {
                                $db->multiVariableQuery("DELETE FROM `GeoKretyAPI` WHERE `userID` = :1",$usr['userid']);
                                $db->reset();
                                tpl_set_var('GeoKretyApiIntegration', tr('no'));
                            }
                            $sql = "UPDATE `user` SET `username`=:1, `last_modified`=NOW(), `latitude`=:2, `longitude`=:3, `pmr_flag`=:4, `country`=:5, `permanent_login_flag`=:6, `power_trail_email`=:8 , `notify_radius`=:9, `ozi_filips`=:10, `guru`=:11 WHERE `user_id`=:7";
                            $db->multiVariableQuery($sql, $username, $latitude, $longitude, 0, $country, $using_permantent_login, (int) $usr['userid'], $geoPathsEmail, $radius, $ozi_path,$guide);
                            $db->reset();
                            $tplname = 'myprofile';
                            tpl_set_var('country', htmlspecialchars(db_CountryFromShort($country), ENT_COMPAT, 'UTF-8'));
                            tpl_set_var('coords', htmlspecialchars(help_latToDegreeStr($latitude), ENT_COMPAT, 'UTF-8') . '<br />' . htmlspecialchars(help_lonToDegreeStr($longitude), ENT_COMPAT, 'UTF-8'));
                            if($radius + 0 > 0) {
                                tpl_set_var('notify', mb_ereg_replace('{radius}', $radius + 0, $notify_radius_message));
                            } else  {
                                tpl_set_var('notify', $no_notify_message);
                            }
                        }
                    }
                } else { // display form
                    if ($record['guru']==1 || $guide == 1){
                        tpl_set_var('guides_start', '');
                        tpl_set_var('guides_end', '');
                    } else {
                        tpl_set_var('guides_start', '<!--');
                        tpl_set_var('guides_end', '-->');
                    }
                    $geoPathsEmail = $record['power_trail_email'];
                    $show_all_countries = 0;
                    $country = $record['country'];
                    $guide= $record['guru'];
                    $longitude = $record['longitude'];
                    $latitude = $record['latitude'];
                    $using_permantent_login = $record['permanent_login_flag'];
                    $ozi_path = strip_tags($record['ozi_filips']);
                    tpl_set_var('ozi_path', $ozi_path);

                    if ($using_permantent_login == 1) {
                        tpl_set_var('permanent_login_sel', ' checked="checked"');
                    } else {
                        tpl_set_var('permanent_login_sel', '');
                    }
                    if ($guide == 1) {
                        tpl_set_var('guide_sel', ' checked="checked"');
                    } else {
                        tpl_set_var('guide_sel', '');
                    }
                    if ($geoPathsEmail == 1) {
                        tpl_set_var('geoPathsEmailCheckboxChecked', ' checked="checked"');
                    } else {
                        tpl_set_var('geoPathsEmailCheckboxChecked', '');
                    }

                    if ($longitude < 0)  {
                        $lonEW = 'W';
                        $longitude = -$longitude;
                    } else  {
                        $lonEW = 'E';
                    }
                    if ($latitude < 0)  {
                        $latNS = 'S';
                        $latitude = -$latitude;
                    } else {
                        $latNS = 'N';
                    }

                    if ($latNS == 'N') {
                        tpl_set_var('latNsel', ' selected="selected"');
                        tpl_set_var('latSsel', '');
                    } else {
                        tpl_set_var('latSsel', ' selected="selected"');
                        tpl_set_var('latNsel', '');
                    }
                    if ($lonEW == 'E') {
                        tpl_set_var('lonEsel', ' selected="selected"');
                        tpl_set_var('lonWsel', '');
                    } else {
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
                if ($country == 'XX') {
                    $stmp = '<option value="XX" selected="selected">' . $no_answer . '</option>';
                } else {
                    $stmp = '<option value="XX">' . $no_answer . '</option>';
                }
                if (isset($_POST['submit_all_countries'])) {
                    $show_all_countries = 1;
                }
                if(checkField('countries','list_default_'.$lang)){ $lang_db = $lang;
                } else{
                    $lang_db = "en";
                }
                //Country in defaults ?
                if (($show_all_countries == 0) && ($country != 'XX')) {
                    $db->multiVariableQuery("SELECT `list_default_" . sql_escape($lang_db) . "` FROM `countries` WHERE `short`=:1", $country);
                    $record2 = $db->dbResultFetch();
                    if ($record2['list_default_' . $lang_db] == 0) {
                        $show_all_countries = 1;
                    } else {
                        $show_all_countries = 0;
                    }
                    $db->reset();
                }

                if ($show_all_countries == 1) {
                    $rs2 = sql("SELECT `&1`, `list_default_" . sql_escape($lang_db) . "`, `short`, `sort_" . sql_escape($lang_db) . "` FROM `countries` ORDER BY `sort_" . sql_escape($lang_db) . '` ASC', $lang_db);
                } else {
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

                if ($show_all_countries == 0) {
                    tpl_set_var('allcountriesbutton', '<input type="submit" class="formbuttons" name="submit_all_countries" value="' . $allcountries . '" />');
                } else {
                    tpl_set_var('allcountriesbutton', '');
                }
            }
        }

        //build useroptions
        $user_options = '';
        if ($using_permantent_login == 1) {
            $user_options .= $using_permantent_login_message . '<br />';
        }
        if ($geoPathsEmail == 1) {
            $user_options .= '<div style="display: '.displayGeoPatchSection('div').'">'.tr('pt235') . '</div><br />';
        }
        if ($user_options == '') {
            $user_options = '&nbsp;';
        }
        tpl_set_var('user_options', $user_options);
        $ozi_path = strip_tags($record['ozi_filips']);
        if( isset($_POST['ozi_path'])) {
            tpl_set_var('ozi_path', strip_tags($_POST['ozi_path']));
        } else{
            tpl_set_var('ozi_path', $ozi_path);
        }
    }
}

//make the template and send it out
tpl_BuildTemplate();

function displayGeoPatchSection($type)
{
    global $powerTrailModuleSwitchOn;
    if($powerTrailModuleSwitchOn === true){
        switch ($type) {
            case 'div':
                    return 'block';
            case 'table':
                    return 'table-row';
            default:
                    return 'inline';
        }
    }
    return 'none';
}
