<?php

use Utils\Database\OcDb;
use Utils\Database\XDb;
use lib\Objects\GeoCache\GeoCache;

//prepare the templates and include all neccessary
global $site_name, $absolute_server_URI;
if (!isset($rootpath)) {
    $rootpath = './';
}
require_once('./lib/common.inc.php');

$ocWP = $GLOBALS['oc_waypoint'];
$no_tpl_build = false;

//Preprocessing
if ($error == false) {
    if ($usr == false) { //user logged in?
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        $db = OcDb::instance();

        $user = new \lib\Objects\User\User(array('userId'=>$usr['userid']));
        $user->loadExtendedSettings();

        $default_country = getDefaultCountry($usr, $lang);
        if (isset($_REQUEST['newcache_info'])) {
            $newcache_info = $_REQUEST['newcache_info'];
        } else {
            $newcache_info = 1;
        }

        if ($newcache_info == 1) {
            // display info about register new cache
            $tplname = 'newcache_info';
        } else {
            //set here the template to process
            $tplname = 'newcache';
        }
        require_once($rootpath . '/lib/caches.inc.php');
        require_once($stylepath . '/newcache.inc.php');

        $rs = XDb::xSql(
            "SELECT `hide_flag` as hide_flag, `verify_all` as verify_all FROM `user`
            WHERE `user_id` = ? ", $user->getUserId());

        $record = XDb::xFetchArray($rs);
        $hide_flag = $record['hide_flag'];
        $verify_all = $record['verify_all'];

        if ($hide_flag == 10) {
            // user is banned for creating new caches for some reason
            $tplname = 'newcache_forbidden';
            require_once($rootpath . '/lib/caches.inc.php');
        }

        // display info for begginner about number of find caches to possible register first cache
        $num_find_caches = XDb::xMultiVariableQueryValue(
            "SELECT COUNT(`cache_logs`.`cache_id`) as num_fcaches FROM cache_logs, caches
            WHERE cache_logs.cache_id=caches.cache_id AND (caches.type='1'
                OR caches.type='2' OR caches.type='3' OR caches.type='7'
                OR caches.type='8') AND cache_logs.type='1'
                AND cache_logs.deleted='0' AND `cache_logs`.`user_id` = :1 ", 0,$usr['userid'] );
        tpl_set_var('number_finds_caches', $num_find_caches);

        if ($num_find_caches < $NEED_FIND_LIMIT && !$user->isIngnoreGeocacheLimitWhileCreatingNewGeocache()) {
            $tplname = 'newcache_beginner';
            require_once($rootpath . '/lib/caches.inc.php');
        }

        $errors = false; // set if there was any errors

        $rsnc = XDb::xSql(
            "SELECT COUNT(`caches`.`cache_id`) as num_caches FROM `caches`
            WHERE `user_id` = ? AND status = 1", $usr['userid']);
        $record = XDb::xFetchArray($rsnc);
        $num_caches = $record['num_caches'];

        $cacheLimitByTypePerUser = common::getUserActiveCacheCountByType($db, $usr['userid']);

        if ($num_caches < $NEED_APPROVE_LIMIT) {
            // user needs approvement for first 3 caches to be published
            $needs_approvement = true;
            tpl_set_var('hide_publish_start', '<!--');
            tpl_set_var('hide_publish_end', '-->');
            tpl_set_var('approvement_note', '<div class="notice"><font color="red"><b>' . tr('first_cache_approvement') . '</b></font></div>');
        } else if ($verify_all == 1) {
            $needs_approvement = true;
            tpl_set_var('hide_publish_start', '<!--');
            tpl_set_var('hide_publish_end', '-->');
            tpl_set_var('approvement_note', '<div class="notice"><font color="red"><b>' . tr('all_cache_approvement') . '</b></font></div>');
        } else {
            $needs_approvement = false;
            tpl_set_var('hide_publish_start', '');
            tpl_set_var('hide_publish_end', '');
            tpl_set_var('approvement_note', '');
        }

        //set template replacements
        tpl_set_var('reset', $reset);
        tpl_set_var('submit', $submit);
        tpl_set_var('general_message', '');
        tpl_set_var('hidden_since_message', $date_time_format_message);
        tpl_set_var('activate_on_message', $date_time_format_message);
        tpl_set_var('lon_message', '');
        tpl_set_var('lat_message', '');
        tpl_set_var('tos_message', '');
        tpl_set_var('name_message', '');
        tpl_set_var('desc_message', '');
        tpl_set_var('effort_message', '');
        tpl_set_var('size_message', '');
        tpl_set_var('type_message', '');
        tpl_set_var('diff_message', '');
        tpl_set_var('region_message', '');
        // configuration variables needed in translation strings
        tpl_set_var('limits_promixity',$config['oc']['limits']['proximity']);
        tpl_set_var('short_sitename',$short_sitename);

        if (!isset($cache_type)) {
            $cache_type = -1;
        }
        $sel_type = isset($_POST['type']) ? $_POST['type'] : -1;
        if (!isset($_POST['size'])) {
            if ($sel_type == 6)
                $sel_size = 7;
            else if ($sel_type == 4 || $sel_type == 5) {
                $sel_type = 1;
                $sel_size = 1;
            } else {
                $sel_size = -1;
            }
        } else {
            $sel_size = isset($_POST['size']) ? $_POST['size'] : -1;
            if ($cache_type == 4 || $cache_type == 5 || $cache_type == 6) {
                $sel_size = 7;
            }
        }
        $sel_lang = isset($_POST['desc_lang']) ? $_POST['desc_lang'] : $default_lang;
        $sel_country = isset($_POST['country']) ? $_POST['country'] : $default_country;
        $sel_region = isset($_POST['region']) ? $_POST['region'] : $default_region;
        $show_all_countries = isset($_POST['show_all_countries']) ? $_POST['show_all_countries'] : 0;
        $show_all_langs = isset($_POST['show_all_langs']) ? $_POST['show_all_langs'] : 0;
        $altitude = isset($_POST['altitude']) ? $_POST['altitude'] : NULL;

        //coords
        $lonEW = isset($_POST['lonEW']) ? $_POST['lonEW'] : $default_EW;
        if ($lonEW == 'E') {
            tpl_set_var('lonWsel', '');
            tpl_set_var('lonEsel', ' selected="selected"');
        } else {
            tpl_set_var('lonE_sel', '');
            tpl_set_var('lonWsel', ' selected="selected"');
        }
        $lon_h = isset($_POST['lon_h']) ? $_POST['lon_h'] : '0';
        tpl_set_var('lon_h', htmlspecialchars($lon_h, ENT_COMPAT, 'UTF-8'));

        $lon_min = isset($_POST['lon_min']) ? $_POST['lon_min'] : '00.000';
        tpl_set_var('lon_min', htmlspecialchars($lon_min, ENT_COMPAT, 'UTF-8'));

        $latNS = isset($_POST['latNS']) ? $_POST['latNS'] : $default_NS;
        if ($latNS == 'N') {
            tpl_set_var('latNsel', ' selected="selected"');
            tpl_set_var('latSsel', '');
        } else {
            tpl_set_var('latNsel', '');
            tpl_set_var('latSsel', ' selected="selected"');
        }
        $lat_h = isset($_POST['lat_h']) ? $_POST['lat_h'] : '0';
        tpl_set_var('lat_h', htmlspecialchars($lat_h, ENT_COMPAT, 'UTF-8'));

        $lat_min = isset($_POST['lat_min']) ? $_POST['lat_min'] : '00.000';
        tpl_set_var('lat_min', htmlspecialchars($lat_min, ENT_COMPAT, 'UTF-8'));

        //name
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        tpl_set_var('name', htmlspecialchars($name, ENT_COMPAT, 'UTF-8'));

        //shortdesc
        $short_desc = isset($_POST['short_desc']) ? $_POST['short_desc'] : '';
        tpl_set_var('short_desc', htmlspecialchars($short_desc, ENT_COMPAT, 'UTF-8'));

        //desc
        $desc = isset($_POST['desc']) ? $_POST['desc'] : '';
        tpl_set_var('desc', htmlspecialchars($desc, ENT_COMPAT, 'UTF-8'));

        if (isset($_POST['descMode'])) {
            $descMode = (int) $_POST['descMode'];
        } else {
            $descMode = 1;
        }

        // for old versions of OCProp
        if (isset($_POST['submit']) && !isset($_POST['version2'])) {
            $descMode = (isset($_POST['desc_html']) && ($_POST['desc_html'] == 1)) ? 2 : 1;
            $_POST['submitform'] = $_POST['submit'];

            $short_desc = iconv("utf-8", "UTF-8", $short_desc);
            $desc = iconv("utf-8", "UTF-8", $desc);
            $name = iconv("utf-8", "UTF-8", $name);
        }

        tpl_set_var('descMode', 1);

        //effort
        $search_time = isset($_POST['search_time']) ? $_POST['search_time'] : '0';
        $way_length = isset($_POST['way_length']) ? $_POST['way_length'] : '0';

        $search_time = mb_ereg_replace(',', '.', $search_time);
        $way_length = mb_ereg_replace(',', '.', $way_length);

        if (mb_strpos($search_time, ':') == mb_strlen($search_time) - 3) {
            $st_hours = mb_substr($search_time, 0, mb_strpos($search_time, ':'));
            $st_minutes = mb_substr($search_time, mb_strlen($st_hours) + 1);

            if (is_numeric($st_hours) && is_numeric($st_minutes)) {
                if (($st_minutes >= 0) && ($st_minutes < 60)) {
                    $search_time = $st_hours + $st_minutes / 60;
                }
            }
        }

        $st_hours = floor($search_time);
        $st_minutes = sprintf('%02d', ($search_time - $st_hours) * 60);

        tpl_set_var('search_time', $st_hours . ':' . $st_minutes);
        tpl_set_var('way_length', $way_length);


        //hints
        $hints = isset($_POST['hints']) ? $_POST['hints'] : '';
        tpl_set_var('hints', htmlspecialchars($hints, ENT_COMPAT, 'UTF-8'));

        // for old versions of OCProp
        if (isset($_POST['submit']) && !isset($_POST['version2'])) {
            $hints = iconv("utf-8", "UTF-8", $hints);
        }

        //hidden_since
        $hidden_day = isset($_POST['hidden_day']) ? $_POST['hidden_day'] : date('d');
        $hidden_month = isset($_POST['hidden_month']) ? $_POST['hidden_month'] : date('m');
        $hidden_year = isset($_POST['hidden_year']) ? $_POST['hidden_year'] : date('Y');
        tpl_set_var('hidden_day', htmlspecialchars($hidden_day, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('hidden_month', htmlspecialchars($hidden_month, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('hidden_year', htmlspecialchars($hidden_year, ENT_COMPAT, 'UTF-8'));

        //activation date
        $activate_day = isset($_POST['activate_day']) ? $_POST['activate_day'] : date('d');
        $activate_month = isset($_POST['activate_month']) ? $_POST['activate_month'] : date('m');
        $activate_year = isset($_POST['activate_year']) ? $_POST['activate_year'] : date('Y');
        tpl_set_var('activate_day', htmlspecialchars($activate_day, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('activate_month', htmlspecialchars($activate_month, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('activate_year', htmlspecialchars($activate_year, ENT_COMPAT, 'UTF-8'));

        if (isset($_POST['publish'])) {
            $publish = $_POST['publish'];
            if ($publish == 'now') {
                tpl_set_var('publish_now_checked', 'checked="checked"');
            } else {
                tpl_set_var('publish_now_checked', '');
            }

            if ($publish == 'later') {
                tpl_set_var('publish_later_checked', 'checked="checked"');
            } else {
                tpl_set_var('publish_later_checked', '');
            }

            if ($publish == 'notnow') {
                tpl_set_var('publish_notnow_checked', 'checked="checked"');
            } else {
                tpl_set_var('publish_notnow_checked', '');
            }
        } else {
            // Standard
            tpl_set_var('publish_now_checked', '');
            tpl_set_var('publish_later_checked', '');
            tpl_set_var('publish_notnow_checked', 'checked="checked"');
        }

        // fill activate hours
        $activate_hour = isset($_POST['activate_hour']) ? $_POST['activate_hour'] + 0 : date('H') + 0;
        $activation_hours = '';
        for ($i = 0; $i <= 23; $i++) {
            if ($activate_hour == $i) {
                $activation_hours .= '<option value="' . $i . '" selected="selected">' . $i . '</option>';
            } else {
                $activation_hours .= '<option value="' . $i . '">' . $i . '</option>';
            }
            $activation_hours .= "\n";
        }
        tpl_set_var('activation_hours', $activation_hours);

        //log-password (no password for traditional caches)
        $log_pw = (isset($_POST['log_pw']) && $sel_type != 2) ? mb_substr($_POST['log_pw'], 0, 20) : '';
        tpl_set_var('log_pw', htmlspecialchars($log_pw, ENT_COMPAT, 'UTF-8'));

        // gc- and nc-waypoints
        $wp_gc = isset($_POST['wp_gc']) ? $_POST['wp_gc'] : '';
        tpl_set_var('wp_gc', htmlspecialchars($wp_gc, ENT_COMPAT, 'UTF-8'));

        $wp_ge = isset($_POST['wp_ge']) ? $_POST['wp_ge'] : '';
        tpl_set_var('wp_ge', htmlspecialchars($wp_ge, ENT_COMPAT, 'UTF-8'));

        $wp_tc = isset($_POST['wp_tc']) ? $_POST['wp_tc'] : '';
        tpl_set_var('wp_tc', htmlspecialchars($wp_tc, ENT_COMPAT, 'UTF-8'));

        $wp_nc = isset($_POST['wp_nc']) ? $_POST['wp_nc'] : '';
        tpl_set_var('wp_nc', htmlspecialchars($wp_nc, ENT_COMPAT, 'UTF-8'));

        //difficulty
        $difficulty = isset($_POST['difficulty']) ? $_POST['difficulty'] : 1;
        $difficulty_options = '<option value="1">' . $sel_message . '</option>';
        for ($i = 2; $i <= 10; $i++) {
            if ($difficulty == $i) {
                $difficulty_options .= '<option value="' . $i . '" selected="selected">' . $i / 2 . '</option>';
            } else {
                $difficulty_options .= '<option value="' . $i . '">' . $i / 2 . '</option>';
            }
            $difficulty_options .= "\n";
        }
        tpl_set_var('difficulty_options', $difficulty_options);

        //terrain
        $terrain = isset($_POST['terrain']) ? $_POST['terrain'] : 1;
        $terrain_options = '<option value="1">' . $sel_message . '</option>';
        for ($i = 2; $i <= 10; $i++) {
            if ($terrain == $i) {
                $terrain_options .= '<option value="' . $i . '" selected="selected">' . $i / 2 . '</option>';
            } else {
                $terrain_options .= '<option value="' . $i . '">' . $i / 2 . '</option>';
            }
            $terrain_options .= "\n";
        }
        tpl_set_var('terrain_options', $terrain_options);
        tpl_set_var('sizeoptions', common::buildCacheSizeSelector($sel_type, $sel_size));
        //typeoptions

        $cache = cache::instance();
        $cacheTypes = $cache->getCacheTypes();
        $types = '<option value="-1">' . tr('select_one') . '</option>';
        foreach ($cacheTypes as $typeId => $type) {
            /* block creating forbidden cache types */
            if (in_array($typeId, $config['forbidenCacheTypes'])) {
                continue;
            }
            /* apply cache limit by type per user */
            if (isset($config['cacheLimitByTypePerUser'][$typeId]) && isset($cacheLimitByTypePerUser[$typeId]) && $cacheLimitByTypePerUser[$typeId] >= $config['cacheLimitByTypePerUser'][$typeId]) {
                continue;
            }
            if ($typeId == $sel_type) {
                $types .= '<option value="' . $typeId . '" selected="selected">' . tr($type['translation']) . '</option>';
            } else {
                $types .= '<option value="' . $typeId . '">' . tr($type['translation']) . '</option>';
            }
        }
        tpl_set_var('typeoptions', $types);

        if (isset($_POST['show_all_countries_submit'])) {
            $show_all_countries = 1;
        } elseif (isset($_POST['show_all_langs_submit'])) {
            $show_all_langs = 1;
        }


        //langoptions selector
        buildDescriptionLanguageSelector($show_all_langs, $lang, $config['defaultLanguageList'], $db, $show_all, $show_all_langs);



        //countryoptions
        $countriesoptions = '';
        if ($show_all_countries == 1) {
            tpl_set_var('show_all_countries', '1');
            tpl_set_var('show_all_countries_submit', '');
            $db->simpleQuery("SELECT `short` FROM `countries` ORDER BY `short` ASC");
            $dbResult = $db->dbResultFetchAll();
            $defaultCountryList = array();
            foreach ($dbResult as $value) {
                $defaultCountryList[] = $value['short'];
            }
        } else {
            tpl_set_var('show_all_countries', '0');
            tpl_set_var('show_all_countries_submit', '<input type="submit" name="show_all_countries_submit" value="' . $show_all . '"/>');
        }

        foreach ($defaultCountryList as $record) {
            if ($record == $sel_country) {
                $countriesoptions .= '<option value="' . htmlspecialchars($record, ENT_COMPAT, 'UTF-8') . '" selected="selected">' . tr($record) . '</option>';
            } else {
                $countriesoptions .= '<option value="' . htmlspecialchars($record, ENT_COMPAT, 'UTF-8') . '">' . tr($record) . '</option>';
            }
            $countriesoptions .= "\n";
        }

        tpl_set_var('countryoptions', $countriesoptions);

        // cache-attributes
        $cache_attribs = isset($_POST['cache_attribs']) ? mb_split(';', $_POST['cache_attribs']) : array();

        // cache-attributes
        $cache_attrib_list = '';
        $cache_attrib_array = '';
        $cache_attribs_string = '';

        $rs = XDb::xSql(
            "SELECT `id`, `text_long`, `icon_undef`, `icon_large` FROM `cache_attrib`
            WHERE `language`= ? ORDER BY `category`, `id`", $default_lang);

        while ($record = XDb::xFetchArray($rs)) {
            $line = $cache_attrib_pic;
            $line = mb_ereg_replace('{attrib_id}', $record['id'], $line);
            $line = mb_ereg_replace('{attrib_text}', $record['text_long'], $line);
            if (in_array($record['id'], $cache_attribs)) {
                $line = mb_ereg_replace('{attrib_pic}', $record['icon_large'], $line);
            } else {
                $line = mb_ereg_replace('{attrib_pic}', $record['icon_undef'], $line);
            }
            $cache_attrib_list .= $line;
            $line = $cache_attrib_js;
            $line = mb_ereg_replace('{id}', $record['id'], $line);
            if (in_array($record['id'], $cache_attribs)) {
                $line = mb_ereg_replace('{selected}', 1, $line);
            } else {
                $line = mb_ereg_replace('{selected}', 0, $line);
            }
            $line = mb_ereg_replace('{img_undef}', $record['icon_undef'], $line);
            $line = mb_ereg_replace('{img_large}', $record['icon_large'], $line);
            if ($cache_attrib_array != '') {
                $cache_attrib_array .= ',';
            }
            $cache_attrib_array .= $line;

            if (in_array($record['id'], $cache_attribs)) {
                if ($cache_attribs_string != '') {
                    $cache_attribs_string .= ';';
                }
                $cache_attribs_string .= $record['id'];
            }
        }

        tpl_set_var('cache_attrib_list', $cache_attrib_list);
        tpl_set_var('jsattributes_array', $cache_attrib_array);
        tpl_set_var('cache_attribs', $cache_attribs_string);

        if (isset($_POST['submitform'])) {
            //check the entered data
            /* Prevent binary data in cache descriptions, e.g. <img src='data:...'> tags. */
            if (strlen($desc) > 300000) {
                tpl_set_var('desc_message', tr('error3KCharsExcedeed'));
                $error = true;
            }

            //check coordinates
            if ($lat_h != '' || $lat_min != '') {
                if (!mb_ereg_match('^[0-9]{1,2}$', $lat_h)) {
                    tpl_set_var('lat_message', $error_coords_not_ok);
                    $error = true;
                    $lat_h_not_ok = true;
                } else {
                    if (($lat_h >= 0) && ($lat_h < 90)) {
                        $lat_h_not_ok = false;
                    } else {
                        tpl_set_var('lat_message', $error_coords_not_ok);
                        $error = true;
                        $lat_h_not_ok = true;
                    }
                }

                if (is_numeric($lat_min)) {
                    if (($lat_min >= 0) && ($lat_min < 60)) {
                        $lat_min_not_ok = false;
                    } else {
                        tpl_set_var('lat_message', $error_coords_not_ok);
                        $error = true;
                        $lat_min_not_ok = true;
                    }
                } else {
                    tpl_set_var('lat_message', $error_coords_not_ok);
                    $error = true;
                    $lat_min_not_ok = true;
                }

                $latitude = $lat_h + round($lat_min, 3) / 60;
                if ($latNS == 'S') {
                    $latitude = -$latitude;
                }

                if ($latitude == 0) {
                    tpl_set_var('lon_message', $error_coords_not_ok);
                    $error = true;
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
                    $error = true;
                    $lon_h_not_ok = true;
                } else {
                    if (($lon_h >= 0) && ($lon_h < 180)) {
                        $lon_h_not_ok = false;
                    } else {
                        tpl_set_var('lon_message', $error_coords_not_ok);
                        $error = true;
                        $lon_h_not_ok = true;
                    }
                }

                if (is_numeric($lon_min)) {
                    if (($lon_min >= 0) && ($lon_min < 60)) {
                        $lon_min_not_ok = false;
                    } else {
                        tpl_set_var('lon_message', $error_coords_not_ok);
                        $error = true;
                        $lon_min_not_ok = true;
                    }
                } else {
                    tpl_set_var('lon_message', $error_coords_not_ok);
                    $error = true;
                    $lon_min_not_ok = true;
                }

                $longitude = $lon_h + round($lon_min, 3) / 60;
                if ($lonEW == 'W') {
                    $longitude = -$longitude;
                }
                if ($longitude == 0) {
                    tpl_set_var('lon_message', $error_coords_not_ok);
                    $error = true;
                    $lon_min_not_ok = true;
                }
            } else {
                $longitude = NULL;
                $lon_h_not_ok = false;
                $lon_min_not_ok = false;
            }

            $lon_not_ok = $lon_min_not_ok || $lon_h_not_ok;
            $lat_not_ok = $lat_min_not_ok || $lat_h_not_ok;

            //check effort
            $time_not_ok = true;
            if (is_numeric($search_time) || ($search_time == '')) {
                $time_not_ok = false;
            }
            if ($time_not_ok) {
                tpl_set_var('effort_message', $time_not_ok_message);
                $error = true;
            }
            $way_length_not_ok = true;
            if (is_numeric($way_length) || ($search_time == '')) {
                $way_length_not_ok = false;
            }
            if ($way_length_not_ok) {
                tpl_set_var('effort_message', $way_length_not_ok_message);
                $error = true;
            }

            //check hidden_since
            $hidden_date_not_ok = true;
            if (is_numeric($hidden_day) && is_numeric($hidden_month) && is_numeric($hidden_year)) {
                $hidden_date_not_ok = (checkdate($hidden_month, $hidden_day, $hidden_year) == false);
            }
            if ($hidden_date_not_ok) {
                tpl_set_var('hidden_since_message', $date_not_ok_message);
                $error = true;
            }

            if ($needs_approvement) {
                $activation_date_not_ok = false;
            } else {
                //check date_activate if approvement is not required
                $activation_date_not_ok = true;

                if (is_numeric($activate_day) && is_numeric($activate_month) && is_numeric($activate_year) && is_numeric($activate_hour)) {
                    $activation_date_not_ok = ((checkdate($activate_month, $activate_day, $activate_year) == false) || $activate_hour < 0 || $activate_hour > 23);
                }
                if ($activation_date_not_ok == false) {
                    if (!($publish == 'now' || $publish == 'later' || $publish == 'notnow')) {
                        $activation_date_not_ok = true;
                    }
                }
                if ($activation_date_not_ok) {
                    tpl_set_var('activate_on_message', $date_not_ok_message);
                    $error = true;
                }
            }

            //name
            if ($name == '') {
                tpl_set_var('name_message', $name_not_ok_message);
                $error = true;
                $name_not_ok = true;
            } else {
                $name_not_ok = false;
            }

            // validate region
            // Andrzej "Łza" 2013-06-02
            if ($sel_region == '0') {
                tpl_set_var('region_message', $regionNotOkMessage);
                $error = true;
                $region_not_ok = true;
            } else {
                $region_not_ok = false;
                tpl_set_var('region_message', '');
            }
            tpl_set_var('sel_region', $sel_region);

            //html-desc?
            $desc_html_not_ok = false;

            //cache-size
            $size_not_ok = false;
            if ($sel_size == -1) {
                tpl_set_var('size_message', $size_not_ok_message);
                $error = true;
                $size_not_ok = true;
            }

            //cache-type
            $type_not_ok = false;
            //block register virtual and webcam
            if ($sel_type == -1 || $sel_type == 4 || $sel_type == 5) {
                tpl_set_var('type_message', $type_not_ok_message);
                $error = true;
                $type_not_ok = true;
            }
            if ($sel_size != 7 && ($sel_type == 4 || $sel_type == 5 || $sel_type == 6)) {
                if (!$size_not_ok) {
                    tpl_set_var('size_message', $sizemismatch_message);
                }
                $error = true;
                $size_not_ok = true;
            }
            //difficulty / terrain
            $diff_not_ok = false;
            if ($difficulty < 2 || $difficulty > 10 || $terrain < 2 || $terrain > 10) {
                tpl_set_var('diff_message', $diff_not_ok_message);
                $error = true;
                $diff_not_ok = true;
            }

            //no errors?
            if (!($name_not_ok || $hidden_date_not_ok || $activation_date_not_ok || $lon_not_ok || $lat_not_ok || $desc_html_not_ok || $time_not_ok || $way_length_not_ok || $size_not_ok || $type_not_ok || $diff_not_ok || $region_not_ok)) {
                //sel_status
                $now = getdate();
                $today = mktime(0, 0, 0, $now['mon'], $now['mday'], $now['year']);
                $hidden_date = mktime(0, 0, 0, $hidden_month, $hidden_day, $hidden_year);

                if ($needs_approvement) {
                    $sel_status = 4;
                    $activation_date = 'NULL';
                } else {
                    if (($hidden_date > $today) && ($sel_type != 6)) {
                        $sel_status = 2; //currently not available
                    } else {
                        $sel_status = 1; //available
                    }

                    if ($publish == 'now') {
                        $activation_date = null;
                        $activation_column = ' ';
                    } elseif ($publish == 'later') {
                        $sel_status = 5;
                        $activation_date =
                            date('Y-m-d H:i:s', mktime($activate_hour, 0, 0, $activate_month, $activate_day, $activate_year));
                    } elseif ($publish == 'notnow') {
                        $sel_status = 5;
                        $activation_date = null;
                    } else {
                        // should never happen
                        $activation_date = null;
                    }
                }
                $cache_uuid = create_uuid();

                //add record to caches table
                XDb::xSql(
                    "INSERT INTO `caches` SET
                        `cache_id` = '', `user_id` = ?, `name` = ?, `longitude` = ?, `latitude` = ?, `last_modified` = NOW(),
                        `date_created` = NOW(), `type` = ?, `status` = ?, `country` = ?, `date_hidden` = ?, `date_activate` = ?,
                        `founds` = 0, `notfounds` = 0, `watcher` = 0, `notes` = 0, `last_found` = NULL, `size` = ?, `difficulty` = ?,
                        `terrain` = ?, `uuid` = ?, `logpw` = ?, `search_time` = ?, `way_length` = ?, `wp_gc` = ?,
                        `wp_nc` = ?, `wp_ge` = ?, `wp_tc` = ?, `node` = ? ",
                    $usr['userid'], $name, $longitude, $latitude, $sel_type, $sel_status, $sel_country,
                    date('Y-m-d', $hidden_date), $activation_date, $sel_size, $difficulty, $terrain, $cache_uuid, $log_pw,
                    $search_time, $way_length, $wp_gc, $wp_nc, $wp_ge, $wp_tc, $oc_nodeid);

                $cache_id = XDb::xLastInsertId();

                // insert cache_location
                $code1 = $sel_country;
                $eLang = XDb::xEscape($lang);
                $adm1 = XDb::xMultiVariableQueryValue(
                    "SELECT `countries`.$eLang FROM `countries`
                                    WHERE `countries`.`short`= :1 ", 0, $code1);
                // check if selected country has no districts, then use $default_region
                if ($sel_region == -1) {
                    $sel_region = $default_region;
                }
                if ($sel_region != "0") {
                    $code3 = $sel_region;
                    $adm3 = XDb::xMultiVariableQueryValue(
                        "SELECT `name` FROM `nuts_codes`
                        WHERE `code`= :1 ", 0, $sel_region);
                } else {
                    $code3 = null;
                    $adm3 = null;
                }
                XDb::xSql(
                    "INSERT INTO `cache_location` (cache_id,adm1,adm3,code1,code3)
                    VALUES ( ?, ?, ?, ?, ?)",
                    $cache_id, $adm1, $adm3, $code1, $code3);

                // update cache last modified, it is for work of cache_locations update information
                XDb::xSql(
                    "UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`= ? ", $cache_id);

                // waypoint erstellen
                setCacheWaypoint($cache_id, $oc_waypoint);

                $desc_uuid = create_uuid();
                //add record to cache_desc table
                $desc = userInputFilter::purifyHtmlString($desc);

                $db->multiVariableQuery(
                    "INSERT INTO `cache_desc` (
                        `cache_id`, `language`, `desc`, `hint`,
                        `short_desc`, `last_modified`, `uuid`, `node` )
                    VALUES (:1, :2, :3, :4, :5, NOW(), :6, :7)",
                    $cache_id, $sel_lang, $desc,
                    nl2br(htmlspecialchars($hints, ENT_COMPAT, 'UTF-8')),
                    $short_desc, $desc_uuid, $oc_nodeid);

                GeoCache::setCacheDefaultDescLang($cache_id);

                // insert cache-attributes
                for ($i = 0; $i < count($cache_attribs); $i++) {
                    if (($cache_attribs[$i] + 0) > 0) {
                        XDb::xSql(
                            "INSERT INTO `caches_attributes` (`cache_id`, `attrib_id`)
                            VALUES ( ?, ?)", $cache_id, $cache_attribs[$i]);
                    }
                }

                // only if no approval is needed and cache is published NOW or activate_date is in the past
                if (!$needs_approvement && ($publish == 'now' || ($publish == 'later' && mktime($activate_hour, 0, 0, $activate_month, $activate_day, $activate_year) <= $today))) {
                    //do event handling
                    include_once($rootpath . '/lib/eventhandler.inc.php');

                    event_notify_new_cache($cache_id + 0);
                    event_new_cache($usr['userid'] + 0);
                }

                if ($needs_approvement) { // notify RR that new cache has to be verified
                    $email_content = file_get_contents($stylepath . '/email/rr_activate_cache.email');
                    $email_content = mb_ereg_replace('{server}', $absolute_server_URI, $email_content);
                    $email_content = mb_ereg_replace('{rrActivateCache_01}', tr('rrActivateCache_01'), $email_content);
                    $email_content = mb_ereg_replace('{rrActivateCache_02}', tr('rrActivateCache_02'), $email_content);
                    $email_content = mb_ereg_replace('{rrActivateCache_03}', tr('rrActivateCache_03'), $email_content);
                    $email_content = mb_ereg_replace('{rrActivateCache_04}', tr('rrActivateCache_04'), $email_content);
                    $email_content = mb_ereg_replace('{rrActivateCache_05}', tr('rrActivateCache_05'), $email_content);
                    $email_content = mb_ereg_replace('{rrActivateCache_06}', tr('rrActivateCache_06'), $email_content);
                    $email_content = mb_ereg_replace('{username}', $usr['username'], $email_content);
                    $email_content = mb_ereg_replace('{cachename}', $name, $email_content);
                    $email_content = mb_ereg_replace('{cacheid}', $cache_id, $email_content);
                    $email_content = mb_ereg_replace('{octeamEmailsSignature}', $octeamEmailsSignature, $email_content);

                    $email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
                    $email_headers .= "From: $site_name <$octeam_email>\r\n";
                    $email_headers .= "Reply-To: $octeam_email\r\n";
                    $octeam_email = $octeam_email;

                    //send email to octeam
                    mb_send_mail($octeam_email, tr('rrActivateCache_07') . ": " . $name, $email_content, $email_headers);
                    XDb::xSql(
                        "UPDATE sysconfig SET value = value + 1
                        WHERE name = 'hidden_for_approval'");
                }

                /* add cache altitude altitude */
                $geoCache = new \lib\Objects\GeoCache\GeoCache(array('cacheId' => $cache_id));
                $geoCache->getAltitude()->pickAndStoreAltitude($altitude);

                // redirection
                tpl_redirect('mycaches.php?status=' . urlencode($sel_status));
            } else {
                tpl_set_var('general_message', $error_general);
            }
        }
    }
}
tpl_set_var('is_disabled_size', '');
tpl_set_var('language4js', $lang);
if ($no_tpl_build == false) {
    //make the template and send it out
    tpl_BuildTemplate();
}

function getDefaultCountry($usr, $lang)
{
    if ($usr['country'] != '') {
        return $usr['country'];
    } else {
        return strtoupper($lang);
    }
}

function buildDescriptionLanguageSelector($show_all_langs, $lang, $defaultLangugaeList, $db, $show_all, $show_all_langs)
{
    tpl_set_var('show_all_langs', '0');
    tpl_set_var('show_all_langs_submit', '<input type="submit" name="show_all_langs_submit" value="' . $show_all . '"/>');
    if ($show_all_langs == 1) {
        tpl_set_var('show_all_langs', '1');
        tpl_set_var('show_all_langs_submit', '');
        $db->simpleQuery('SELECT short FROM languages');
        $dbResult = $db->dbResultFetchAll();
        $defaultLangugaeList = array();
        foreach ($dbResult as $langTmp) {
            $defaultLangugaeList[] = $langTmp['short'];
        }
    }
    $langsoptions = '';
    foreach ($defaultLangugaeList as $defLang) {
        if (strtoupper($lang) === strtoupper($defLang)) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
        $langsoptions .= '<option value="' . htmlspecialchars($defLang, ENT_COMPAT, 'UTF-8') . '" ' . $selected . ' >' . htmlspecialchars(tr('language_' . strtoupper($defLang)), ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
    }
    tpl_set_var('langoptions', $langsoptions);
}


function generateNextWaypoint($currentWP, $ocWP)
{
    $wpCharSequence = "0123456789ABCDEFGHJKLMNPQRSTUWXYZ";

    $wpCode = mb_substr($currentWP, 2, 4);
    if (strcasecmp($wpCode, "8000") < 0) {
        // Old rule - use hexadecimal wp codes
        $nNext = dechex(hexdec($wpCode) + 1);
        while (mb_strlen($nNext) < 4)
            $nNext = '0' . $nNext;
            $wpCode = mb_strtoupper($nNext);
    } else {
        // New rule - use digits and (almost) full latin alphabet
        // as defined in $wpCharSequence
        for ($i = 3; $i >= 0; $i--) {
            $pos = strpos($wpCharSequence, $wpCode[$i]);
            if ($pos < strlen($wpCharSequence) - 1) {
                $wpCode[$i] = $wpCharSequence[$pos + 1];
                break;
            } else {
                $wpCode[$i] = $wpCharSequence[0];
            }
        }
    }
    return $ocWP . $wpCode;
}

// set a unique waypoint to this cache
function setCacheWaypoint($cacheid, $ocWP)
{

    $r['maxwp'] = XDb::xSimpleQueryValue(
        'SELECT MAX(`wp_oc`) `maxwp` FROM `caches`',null);

    if ($r['maxwp'] == null)
        $sWP = $ocWP . "0001";
    else
        $sWP = generateNextWaypoint($r['maxwp'], $ocWP);

    XDb::xSql(
        "UPDATE `caches` SET `wp_oc`= ?
        WHERE `cache_id`= ? AND ISNULL(`wp_oc`)",
        $sWP, $cacheid);
}

