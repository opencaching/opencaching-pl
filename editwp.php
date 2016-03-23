<?php

use Utils\Database\XDb;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        //Edit Waypoint
        if (isset($_REQUEST['wpid'])) {
            $wp_id = $_REQUEST['wpid'];
        }
        $remove = 0;
        if (isset($_REQUEST['delete'])) {
            $wp_id = $_REQUEST['wpid'];
            $remove = 1;
        }
        if (isset($_POST['delete'])) {
            $wp_id = $_POST['wpid'];
            $remove = 1;
        }

        $wp_rs = XDb::xSql("SELECT `wp_id`, `cache_id`, `type`, `longitude`, `latitude`,  `desc`, `status`, `stage`,
                              `opensprawdzacz`, `waypoint_type`.`pl` `wp_type`, `waypoint_type`.`icon` `wp_icon`
                      FROM `waypoints` INNER JOIN waypoint_type ON (waypoints.type = waypoint_type.id) WHERE `wp_id`= ? ", $wp_id);

        if ( $wp_record = XDb::xFetchArray($wp_rs) ) {
            $cache_id = $wp_record['cache_id'];
        }else{
            //TODO: does it needs error handling?
            trigger_error("Can't find waypoint with wp_id=".$wp_id, E_USER_ERROR);
            exit;
        }

        $cache_rs = XDb::xSql("SELECT `user_id`, `name`, `type`,  `longitude`, `latitude`,  `status`, `logpw`
                               FROM `caches` WHERE `cache_id`= ? LIMIT 1", $cache_id);

        if ($cache_record = XDb::xFetchArray($cache_rs)) {

            if ($cache_record['type'] == '2' || $cache_record['type'] == '4' ||
                $cache_record['type'] == '5' || $cache_record['type'] == '6' ||
                $cache_record['type'] == '8' || $cache_record['type'] == '9') {
                tpl_set_var("start_stage", '<!--');
                tpl_set_var("end_stage", '-->');
            } else {
                tpl_set_var("start_stage", '');
                tpl_set_var("end_stage", '');
            }


            if ($cache_record['user_id'] == $usr['userid'] || $usr['admin']) {

                if ($remove == 1) {
                    //remove
                    XDb::xSql("DELETE FROM `waypoints` WHERE `wp_id`= ? ", $wp_id);
                    XDb::xSql("UPDATE `caches` SET  `last_modified`=NOW() WHERE `cache_id`= ? ", $cache_id);
                    tpl_redirect('editcache.php?cacheid=' . urlencode($cache_id));
                    exit;
                }


                $tplname = 'editwp';
                require_once($rootpath . 'lib/caches.inc.php');
                require($stylepath . '/newcache.inc.php');

                $wp_type = isset($_POST['type']) ? $_POST['type'] : $wp_record['type'];
                //build typeoptions
                $types = '';
                foreach (get_wp_types_from_database($cache_record['type']) as $type) {

                    if ($type['id'] == $wp_type) {
                        $types .= '<option value="' . $type['id'] . '" selected="selected">' . htmlspecialchars($type[$lang], ENT_COMPAT, 'UTF-8') . '</option>';
                    } else {
                        $types .= '<option value="' . $type['id'] . '">' . htmlspecialchars($type[$lang], ENT_COMPAT, 'UTF-8') . '</option>';
                    }
                }
                tpl_set_var('typeoptions', $types);

                // ===== opensprawdzacz =====================================================
                // hide or show opensprawdzacz section if type of waypoint is "final location"
                if ($wp_type == 3)
                    tpl_set_var('opensprawdzacz_display', 'block');
                else
                    tpl_set_var('opensprawdzacz_display', 'none');
                // checks checkbox if in database is checked.
                if ($wp_record['opensprawdzacz'] == 1) {
                    tpl_set_var('opensprawdzacz_checked', 'checked=""');
                } else {
                    tpl_set_var('opensprawdzacz_checked', '');
                }
                if (isset($_POST['oprawdzacz']))
                    $opensprawdzacz_taknie = 1;
                else
                    $opensprawdzacz_taknie = 0;
                // ==== opensprawdzacz end ====================================================

                if (isset($_POST['latNS'])) {
                    //get coords from post-form
                    $coords_latNS = $_POST['latNS'];
                    $coords_lonEW = $_POST['lonEW'];
                    $coords_lat_h = $_POST['lat_h'];
                    $coords_lon_h = $_POST['lon_h'];
                    $coords_lat_min = $_POST['lat_min'];
                    $coords_lon_min = $_POST['lon_min'];
                } else {
                    //get coords from DB
                    $coords_lon = $wp_record['longitude'];
                    $coords_lat = $wp_record['latitude'];

                    if ($coords_lon < 0) {
                        $coords_lonEW = 'W';
                        $coords_lon = -$coords_lon;
                    } else {
                        $coords_lonEW = 'E';
                    }

                    if ($coords_lat < 0) {
                        $coords_latNS = 'S';
                        $coords_lat = -$coords_lat;
                    } else {
                        $coords_latNS = 'N';
                    }

                    $coords_lat_h = floor($coords_lat);
                    $coords_lon_h = floor($coords_lon);

                    $coords_lat_min = sprintf("%02.3f", round(($coords_lat - $coords_lat_h) * 60, 3));
                    $coords_lon_min = sprintf("%02.3f", round(($coords_lon - $coords_lon_h) * 60, 3));
                }

                //here we validate the data
                //coords
                $lon_not_ok = false;

                if (!mb_ereg_match('^[0-9]{1,3}$', $coords_lon_h)) {
                    $lon_not_ok = true;
                } else {
                    $lon_not_ok = (($coords_lon_h >= 0) && ($coords_lon_h < 180)) ? false : true;
                }

                if (is_numeric($coords_lon_min)) {
                    // important: use here |=
                    $lon_not_ok |= (($coords_lon_min >= 0) && ($coords_lon_min < 60)) ? false : true;
                } else {
                    $lon_not_ok = true;
                }

                //same with lat
                $lat_not_ok = false;

                if (!mb_ereg_match('^[0-9]{1,3}$', $coords_lat_h)) {
                    $lat_not_ok = true;
                } else {
                    $lat_not_ok = (($coords_lat_h >= 0) && ($coords_lat_h < 180)) ? false : true;
                }

                if (is_numeric($coords_lat_min)) {
                    // important: use here |=
                    $lat_not_ok |= (($coords_lat_min >= 0) && ($coords_lat_min < 60)) ? false : true;
                } else {
                    $lat_not_ok = true;
                }
                $wp_stage = isset($_POST['stage']) ? $_POST['stage'] : $wp_record['stage'];
                $status1 = "";
                $status2 = "";
                $status3 = "";
                $wp_status = isset($_POST['status']) ? $_POST['status'] : $wp_record['status'];
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

                $wp_desc = isset($_POST['desc']) ? $_POST['desc'] : $wp_record['desc'];
//                  $wp_desc = nl2br($wp_desc);
                $descwp_not_ok = false;
                if (isset($_POST['desc'])) {
                    if ($_POST['desc'] == "")
                        $descwp_not_ok = true;
                }

                if (isset($_POST['back'])) {
                    tpl_redirect('editcache.php?cacheid=' . urlencode($cache_id));
                    XDb::xFreeResults($cache_rs);
                    XDb::xFreeResults($wp_rs);
                    exit;
                }
                //try to save to DB?
                if (isset($_POST['submit'])) {
                    //all validations ok?
                    //check the entered data
                    if ($wp_type == '4' || $wp_type == '5')
                        $wp_stage = '0';
                    if (!($lat_not_ok || $lon_not_ok || $descwp_not_ok)) {
                        $wp_lat = $coords_lat_h + $coords_lat_min / 60;
                        if ($coords_latNS == 'S')
                            $wp_lat = -$wp_lat;

                        $wp_lon = $coords_lon_h + $coords_lon_min / 60;
                        if ($coords_lonEW == 'W')
                            $wp_lon = -$wp_lon;

//                          $wp_desc=nl2br($wp_desc);
                        //save to DB
                        XDb::xSql("UPDATE `waypoints` SET `longitude`=?, `latitude`=?, `type`=?,`status`=?,
                                                    `stage`= ?,`desc`= ?, `opensprawdzacz`= ? WHERE `wp_id`= ?",
                            $wp_lon, $wp_lat, $wp_type, $wp_status, $wp_stage, $wp_desc, $opensprawdzacz_taknie, $wp_id);

                        XDb::xSql("UPDATE `caches` SET  `last_modified`=NOW() WHERE `cache_id`= ? ", $cache_id);

                        // ==== opensprawdzacz ===============================================
                        // add/update active status to/in opensprawdzacz table

                        if (($opensprawdzacz_taknie == 1) && ($wp_type == 3)) {
                            $proba = XDb::xSimpleQueryValue("SELECT count(*) FROM `opensprawdzacz` WHERE `cache_id` = '$cache_id'",'');
                            if ($proba == 0) {
                                XDb::xSql("INSERT INTO `opensprawdzacz`(
                                                                    `id`,
                                                                    `cache_id`,
                                                                    `proby`,
                                                                    `sukcesy`)
                                                      VALUES ('', '$cache_id',   0,       0)");
                            }

                        }
                        // ==== opensprawdzacz end ===========================================
                        //display cache-page
                        tpl_redirect('editcache.php?cacheid=' . urlencode($cache_id));
                        exit;
                    }
                }
                tpl_set_var('selLatN', ($coords_latNS == 'N') ? ' selected="selected"' : '');
                tpl_set_var('selLatS', ($coords_latNS == 'S') ? ' selected="selected"' : '');
                tpl_set_var('selLonE', ($coords_lonEW == 'E') ? ' selected="selected"' : '');
                tpl_set_var('selLonW', ($coords_lonEW == 'W') ? ' selected="selected"' : '');
                tpl_set_var('lat_h', htmlspecialchars($coords_lat_h, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('lat_min', htmlspecialchars($coords_lat_min, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('lon_h', htmlspecialchars($coords_lon_h, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('lon_min', htmlspecialchars($coords_lon_min, ENT_COMPAT, 'UTF-8'));

                tpl_set_var('desc_message', ($descwp_not_ok == true) ? $descwp_not_ok_message : '');
                tpl_set_var('lon_message', ($lon_not_ok == true) ? $error_coords_not_ok : '');
                tpl_set_var('lat_message', ($lat_not_ok == true) ? $error_coords_not_ok : '');

                if ($lon_not_ok || $lat_not_ok || $descwp_not_ok)
                    tpl_set_var('general_message', $error_general);
                else
                    tpl_set_var('general_message', "");

                tpl_set_var("desc", htmlspecialchars($wp_desc));
                tpl_set_var("type", htmlspecialchars($wp_type));
                tpl_set_var("stage", htmlspecialchars($wp_stage));
                tpl_set_var("nextstage", htmlspecialchars($wp_stage));
                tpl_set_var("status", htmlspecialchars($wp_status));
                tpl_set_var("wpid", htmlspecialchars($wp_record['wp_id']));
                tpl_set_var("cacheid", htmlspecialchars($wp_record['cache_id']));
                tpl_set_var("cache_name", htmlspecialchars($cache_record['name']));
            }
            XDb::xFreeResults($cache_rs);
            XDb::xFreeResults($wp_rs);
        }
    }
}
tpl_BuildTemplate();

