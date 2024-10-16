<?php

use src\Models\ApplicationContainer;
use src\Models\GeoCache\WaypointCommons;
use src\Utils\Database\XDb;

require_once(__DIR__ . '/lib/common.inc.php');

$no_tpl_build = false;

//user logged in?
$loggedUser = ApplicationContainer::GetAuthorizedUser();
if (!$loggedUser) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}

//New Waypoint
if (isset($_REQUEST['cacheid'])) {
    $cache_id = $_REQUEST['cacheid'];
}
if (isset($_POST['cacheid'])) {
    $cache_id = $_POST['cacheid'];
}
tpl_set_var("cacheid", $cache_id);

$cache_rs = XDb::xSql("SELECT `user_id`, `name`, `type`,  `longitude`, `latitude`,  `status`, `logpw`
                         FROM `caches` WHERE `cache_id`= ? ", $cache_id);

if ($cache_record = XDb::xFetchArray($cache_rs)) {

    tpl_set_var("cache_name", htmlspecialchars($cache_record['name']));
    tpl_set_var("cachetype", htmlspecialchars($cache_record['type']));

    $wp_rs = XDb::xSql("SELECT `stage`,`type` FROM `waypoints`
                            WHERE `cache_id`= ? AND (type<>4 OR type<>5) ORDER BY `stage` DESC", $cache_id);

    if ($wp_record = XDb::xFetchArray($wp_rs)) {
        if ($cache_record['type'] == '2' || $cache_record['type'] == '4' || $cache_record['type'] == '5' || $cache_record['type'] == '6' || $cache_record['type'] == '8' || $cache_record['type'] == '9') {
            $next_stage = 0;
            $wp_stage = 0;
            tpl_set_var("stage", "0");
            tpl_set_var("nextstage", "0");
            tpl_set_var("start_stage", '<!--');
            tpl_set_var("end_stage", '-->');
        } else {
            $next_stage = ($wp_record['stage'] + 1);
            tpl_set_var("nextstage", $next_stage);
            tpl_set_var("stage", $next_stage);
            tpl_set_var("start_stage", '');
            tpl_set_var("end_stage", '');
        }
    } else {
        if ($cache_record['type'] == '2' || $cache_record['type'] == '4' || $cache_record['type'] == '5' || $cache_record['type'] == '6' || $cache_record['type'] == '8' || $cache_record['type'] == '9') {
            $wp_stage = 0;
            tpl_set_var("stage", "0");
            tpl_set_var("nextstage", "0");
            tpl_set_var("start_stage", '<!--');
            tpl_set_var("end_stage", '-->');
        } else {
            tpl_set_var("start_stage", '');
            tpl_set_var("end_stage", '');
        }

        tpl_set_var("stage", "1");
        tpl_set_var("nextstage", "1");
    }

    if ($cache_record['user_id'] == $loggedUser->getUserId() || $loggedUser->hasOcTeamRole()) {

        $tplname = 'newwp';


        // former /src/Views/newcache.inc.php' contents
        $submit = tr('new_cache2');
        $default_region = '0';
        $show_all = tr('show_all');
        $default_NS = 'N';
        $default_EW = 'E';
        $date_time_format_message = tr('newcacheDateFormat');

        $error_general = '<div class="warning">' . tr('error_new_cache') . '</div>';
        $error_coords_not_ok = '<br><img src="images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;<span class="errormsg">' . tr('bad_coordinates') . '</span>';
        $time_not_ok_message = '<br><img src="images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;<span class="errormsg">' . tr('time_incorrect') . '</span>';
        $way_length_not_ok_message = '<br><img src="images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;<span class="errormsg">' . tr('distance_incorrect') . '</span>';
        $date_not_ok_message = '<br><img src="images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;<span class="errormsg">' . tr('date_incorrect') . '</span>';
        $name_not_ok_message = '<br><img src="images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;<span class="errormsg">' . tr('no_cache_name') . '</span>';
        $descwp_not_ok_message = '<br><img src="images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;<span class="errormsg">' . tr('descwp_incorrect') . '</span>';
        $type_not_ok_message = '<br><img src="images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;&nbsp;<span class="errormsg">' . tr('type_incorrect') . '</span>';
        $typewp_not_ok_message = '<br><img src="images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;&nbsp;<span class="errormsg">' . tr('typewp_incorrect') . '</span>';
        $size_not_ok_message = '<br><img src="images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;&nbsp;<span class="errormsg">' . tr('size_incorrect') . '</span>';
        $diff_not_ok_message = '<br><img src="images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;&nbsp;<span class="errormsg">' . tr('diff_incorrect') . '</span>';
        $sizemismatch_message = '<br><img src="images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;&nbsp;<span class="errormsg">' . tr('virtual_cache_size') . '</span>';
        $regionNotOkMessage = '<br><img src="images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;&nbsp;<span class="errormsg">' . tr('region_not_ok') . '</span>';
        $sel_message = tr('choose');
        $cache_attrib_js = "new Array({id}, {selected}, '{img_undef}', '{img_large}')";
        $cache_attrib_pic = '<img id="attr{attrib_id}" src="{attrib_pic}" alt="{attrib_text}" title="{attrib_text}" onmousedown="toggleAttr({attrib_id})"> ';
        // former /src/Views/newcache.inc.php' contents ends

        //set template replacements
        tpl_set_var('lon_message', '');
        tpl_set_var('lat_message', '');
        tpl_set_var('general_message', '');
        tpl_set_var('desc_message', '');
        tpl_set_var('type_message', '');
        tpl_set_var('stage_message', '');

        //build typeoptions
        $sel_type = isset($_POST['type']) ? $_POST['type'] : -1;

        $types = '<option disabled selected="selected">' . tr('choose_waypoint_type') . '</options>';
//                  if ($cache_record['type'] == '2' || $cache_record['type'] == '6' || $cache_record['type'] == '8' || $cache_record['type'] == '9')
        // check if final waypoint alreday exist for this cache
        $wp_check_final_exist = XDb::xMultiVariableQueryValue(
            "SELECT COUNT(*) FROM `waypoints`
                     WHERE `cache_id`= :1 AND type = 3", false, $cache_id);

        if ($wp_check_final_exist == 1)
            $pomin = 1;
        else
            $pomin = 0;

        foreach (WaypointCommons::getTypesArray($cache_record['type']) as $type) {
            if ($type == $sel_type) {
                if (($type == WaypointCommons::TYPE_FINAL) && ($pomin == 1)) {

                } // if final waypoint alreday exist for this cache do not allow create new waypoint type "final location"
                else
                    $types .= '<option value="' . $type . '" selected="selected">' .
                        htmlspecialchars(tr(WaypointCommons::typeTranslationKey($type)), ENT_COMPAT, 'UTF-8') . '</option>';
            } else {
                if (($type == WaypointCommons::TYPE_FINAL) && ($pomin == 1)) {

                } //// if final waypoint already exist for this cache do not allow create new waypoint type "final location"
                else
                    $types .= '<option value="' . $type . '">' .
                        htmlspecialchars(tr(WaypointCommons::typeTranslationKey($type)), ENT_COMPAT, 'UTF-8') . '</option>';
            }
        }

        tpl_set_var('typeoptions', $types);

        //coords
        $lonEW = isset($_POST['lonEW']) ? $_POST['lonEW'] : $default_EW;
        if ($lonEW == 'E') {
            tpl_set_var('lonEsel', ' selected="selected"');
            tpl_set_var('lonWsel', '');
        } else {
            tpl_set_var('lonEsel', '');
            tpl_set_var('lonWsel', ' selected="selected"');
        }
        $lon_h = isset($_POST['lon_h']) ? $_POST['lon_h'] : '';
        tpl_set_var('lon_h', htmlspecialchars($lon_h, ENT_COMPAT, 'UTF-8'));

        $lon_min = isset($_POST['lon_min']) ? $_POST['lon_min'] : '';
        tpl_set_var('lon_min', htmlspecialchars($lon_min, ENT_COMPAT, 'UTF-8'));

        $latNS = isset($_POST['latNS']) ? $_POST['latNS'] : $default_NS;
        if ($latNS == 'N') {
            tpl_set_var('latNsel', ' selected="selected"');
            tpl_set_var('latSsel', '');
        } else {
            tpl_set_var('latNsel', '');
            tpl_set_var('latSsel', ' selected="selected"');
        }
        $lat_h = isset($_POST['lat_h']) ? $_POST['lat_h'] : '';
        tpl_set_var('lat_h', htmlspecialchars($lat_h, ENT_COMPAT, 'UTF-8'));

        $lat_min = isset($_POST['lat_min']) ? $_POST['lat_min'] : '';
        tpl_set_var('lat_min', htmlspecialchars($lat_min, ENT_COMPAT, 'UTF-8'));

        // =============== openchecker =======================================================
        // is variable $_POST['openchecker'] exist then $OpenChecker_present should be set up as 1
        // otherwise $OpenChecker_present should be set up as 0
        if ($config['module']['openchecker']['enabled']) {
            tpl_set_var('openchecker_start', '');
            tpl_set_var('openchecker_end', '');
        } else {
            tpl_set_var('openchecker_start', '<!--');
            tpl_set_var('openchecker_end', '-->');
        }
        if (isset($_POST['openchecker'])) {
            $OpenChecker_present = 1;
            tpl_set_var('openchecker_checked', 'checked=""');
        } else {
            $OpenChecker_present = 0;
        }
        // hides or shows openchecker checkbox depend on type of waypoint
        if ($sel_type == 3 && $config['module']['openchecker']['enabled'])
            tpl_set_var('openchecker_display', 'block');
        else
            tpl_set_var('openchecker_display', 'none');
        //================ openchecker end ===================================================
        //stage
        $wp_stage = isset($_POST['stage']) ? $_POST['stage'] : '0';

        //status
        $status1 = "";
        $status2 = "";
        $status3 = "";
        $wp_status = isset($_POST['status']) ? $_POST['status'] : '1';
        if ($wp_status == 1) {
            $status1 = "checked";
        }
        if ($wp_status == 2) {
            $status2 = "checked";
        }
        if ($wp_status == 3) {
            $status3 = "checked";
        }
        tpl_set_var("checked1", $status1);
        tpl_set_var("checked2", $status2);
        tpl_set_var("checked3", $status3);
        //desc
        $wp_desc = isset($_POST['desc']) ? $_POST['desc'] : '';
//              $wp_desc = nl2br($wp_desc);
        tpl_set_var('desc', htmlspecialchars($wp_desc, ENT_COMPAT, 'UTF-8'));

        if (isset($_POST['back'])) {
            tpl_redirect('editcache.php?cacheid=' . urlencode($cache_id));
            XDb::xFreeResults($cache_rs);
            XDb::xFreeResults($wp_rs);
            exit;
        }

        if (isset($_POST['submitform'])) {
            //check the entered data
            if ($sel_type == '4' || $sel_type == '5' || $sel_type == '6')
                $wp_stage = 0;
            //check coordinates
            if ($lat_h != '' || $lat_min != '') {
                if (!mb_ereg_match('^[0-9]{1,2}$', $lat_h)) {
                    tpl_set_var('lat_message', $error_coords_not_ok);
                    $lat_h_not_ok = true;
                } else {
                    if (($lat_h >= 0) && ($lat_h < 90)) {
                        $lat_h_not_ok = false;
                    } else {
                        tpl_set_var('lat_message', $error_coords_not_ok);
                        $lat_h_not_ok = true;
                    }
                }

                if (is_numeric($lat_min)) {
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
                if ($latNS == 'S')
                    $latitude = -$latitude;

                if ($latitude == 0) {
                    tpl_set_var('lon_message', $error_coords_not_ok);
                    $lat_min_not_ok = true;
                }
            } else {
                $latitude = NULL;
                $lat_h_not_ok = false;
                $lat_min_not_ok = false;
            }

            if ($lon_h != '' || $lon_min != '') {
                if (!mb_ereg_match('^[0-9]{1,3}$', $lon_h)) {
                    tpl_set_var('lon_message', $error_coords_not_ok);
                    $lon_h_not_ok = true;
                } else {
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
                    } else {
                        tpl_set_var('lon_message', $error_coords_not_ok);
                        $lon_min_not_ok = true;
                    }
                } else {
                    tpl_set_var('lon_message', $error_coords_not_ok);
                    $lon_min_not_ok = true;
                }

                $longitude = $lon_h + $lon_min / 60;
                if ($lonEW == 'W')
                    $longitude = -$longitude;

                if ($longitude == 0) {
                    tpl_set_var('lon_message', $error_coords_not_ok);
                    $lon_min_not_ok = true;
                }
            } else {
                $longitude = NULL;
                $lon_h_not_ok = false;
                $lon_min_not_ok = false;
            }

            $lon_not_ok = $lon_min_not_ok || $lon_h_not_ok;
            $lat_not_ok = $lat_min_not_ok || $lat_h_not_ok;

            // stage only numeric
//                  if (is_numeric($wp_stage))
//                      {
//                              $stage_not_ok = false;
//                          }
//                          else
//                          {
//                              tpl_set_var('stage_message', $stage_not_ok);
//                              $stage_not_ok = true;
//                          }
            //desc
            if ($wp_desc == '') {
                tpl_set_var('desc_message', $descwp_not_ok_message);
                $descwp_not_ok = true;
            } else {
                $descwp_not_ok = false;
            }
            //wp-type
            $type_not_ok = false;
            if ($sel_type == -1) {
                tpl_set_var('type_message', $typewp_not_ok_message);
                $type_not_ok = true;
            }

            //no errors?
            if (!($descwp_not_ok || $lon_not_ok || $lat_not_ok || $type_not_ok)) {
                //add record

                XDb::xSql("INSERT INTO `waypoints` (
                                    `cache_id`,`longitude`,`latitude`,`type` ,
                                    `status` ,`stage` ,`desc` ,`opensprawdzacz`)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    $cache_id, $longitude, $latitude, $sel_type,
                    $wp_status, $wp_stage, $wp_desc, $OpenChecker_present);


                XDb::xSql("UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`= ? ", $cache_id);

                // ==== openchecker ===============================================
                // add/update active status to/in opensprawdzacz table

                if (($OpenChecker_present == 1) && ($sel_type == 3)) {

                    $proba = XDb::xMultiVariableQueryValue(
                        "SELECT COUNT(*) FROM `opensprawdzacz` WHERE `cache_id` = :1 ", 0, $cache_id);

                    if ($proba == 0) {
                        XDb::xSql("INSERT INTO `opensprawdzacz`(`cache_id`, `proby`, `sukcesy`)
                                                     VALUES ('$cache_id', 0, 0)");
                    }

                }
                // ==== openchecker end ===========================================

                tpl_redirect('editcache.php?cacheid=' . urlencode($cache_id));
            } else {
                tpl_set_var('general_message', $error_general);
            }

            // end submit
        }
        XDb::xFreeResults($cache_rs);
        XDb::xFreeResults($wp_rs);
    } else {
        $no_tpl_build = true;
    }
}

if ($no_tpl_build == false) {
    //make the template and send it out
    tpl_BuildTemplate();
}
