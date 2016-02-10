<?php

use lib\Objects\GeoCache\GeoCache;

//prepare the templates and include all neccessary
if (!isset($rootpath))
    global $rootpath;
require_once('./lib/common.inc.php');
require_once('lib/cache_icon.inc.php');
global $caches_list, $usr, $hide_coords, $cache_menu, $octeam_email, $site_name, $absolute_server_URI, $octeamEmailsSignature;
global $dynbasepath, $powerTrailModuleSwitchOn, $googlemap_key, $titled_cache_period_prefix;

function onTheList($theArray, $item)
{
    for ($i = 0; $i < count($theArray); $i++) {
        if ($theArray[$i] == $item)
            return $i;
    }
    return -1;
}

//Preprocessing
if (!isset($error))
    global $error;
if ($error == false) {

    //set here the template to process
    if (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y')
        $tplname = 'viewcache_print';
    else
        $tplname = 'viewcache';

    require_once($rootpath . 'lib/caches.inc.php');
    require_once($stylepath . '/lib/icons.inc.php');
    require($stylepath . '/viewcache.inc.php');
    require($stylepath . '/viewlogs.inc.php');
    require($stylepath . '/smilies.inc.php');

    /* @var $dbc \dataBase */
    $dbc = \lib\Database\DataBaseSingleton::Instance();
    $cache_id = 0;
    if (isset($_REQUEST['cacheid'])) {
        $cache_id = (int) $_REQUEST['cacheid'];
    } else if (isset($_REQUEST['uuid'])) {
        $uuid = $_REQUEST['uuid'];
        $thatquery = "SELECT `cache_id` FROM `caches` WHERE uuid=:v1 LIMIT 1";
        $params['v1']['value'] = (string) $uuid;
        $params['v1']['data_type'] = 'string';
        $dbc->paramQuery($thatquery, $params);
        if ($r = $dbc->dbResultFetch()) {
            $cache_id = $r['cache_id'];
        }
    } else if (isset($_REQUEST['wp'])) {
        $wp = $_REQUEST['wp'];

        $sql = 'SELECT `cache_id` FROM `caches` WHERE wp_';
        if (mb_strtoupper(mb_substr($wp, 0, 2)) == 'GC')
            $sql .= 'gc';
        else if (mb_strtoupper(mb_substr($wp, 0, 2)) == 'NC')
            $sql .= 'nc';
        else if (mb_strtoupper(mb_substr($wp, 0, 2)) == 'QC')
            $sql .= 'qc';
        else
            $sql .= 'oc';

        $sql .= '=\'' . sql_escape($wp) . '\' LIMIT 1';
        $dbc->simpleQuery($sql);
        if ($r = $dbc->dbResultFetch()) {
            $cache_id = $r['cache_id'];
        }
    }

    if ($usr == false && $hide_coords) {
        $disable_spoiler_view = true; //hide any kind of spoiler if usr not logged in
    } else {
        $disable_spoiler_view = false;
    }
    if ($usr == false) {
        tpl_set_var('hidesearchdownloadsection_start', '<!--');
        tpl_set_var('hidesearchdownloadsection_end', '-->');
    } else {
        tpl_set_var('hidesearchdownloadsection_start', '');
        tpl_set_var('hidesearchdownloadsection_end', '');
    }
    $no_crypt = 0;
    if (isset($_REQUEST['nocrypt'])) {
        $no_crypt = $_REQUEST['nocrypt'];
    }

    if ($cache_id != 0) {
        //get cache record
        if (checkField('countries', 'list_default_' . $lang))
            $lang_db = $lang;
        else
            $lang_db = "en";
        $thatquery = "SELECT `caches`.`cache_id` `cache_id`,
                              `caches`.`user_id` `user_id`,
                              `caches`.`status` `status`,
                              `caches`.`latitude` `latitude`,
                              `caches`.`longitude` `longitude`,
                              `caches`.`name` `name`,
                              `caches`.`type` `type`,
                              `caches`.`size` `size`,
                              `caches`.`search_time` `search_time`,
                              `caches`.`way_length` `way_length`,
                              `caches`.`country` `country`,
                              `caches`.`logpw` `logpw`,
                              `caches`.`date_hidden` `date_hidden`,
                              `caches`.`wp_oc` `wp_oc`,
                              `caches`.`wp_gc` `wp_gc`,
                              `caches`.`wp_ge` `wp_ge`,
                              `caches`.`wp_tc` `wp_tc`,
                              `caches`.`wp_nc` `wp_nc`,
                              `caches`.`wp_qc` `wp_qc`,
                              `caches`.`date_created` `date_created`,
                              `caches`.`difficulty` `difficulty`,
                              `caches`.`terrain` `terrain`,
                              `caches`.`founds` `founds`,
                              `caches`.`notfounds` `notfounds`,
                              `caches`.`notes` `notes`,
                              `caches`.`watcher` `watcher`,
                              `caches`.`votes` `votes`,
                              `caches`.`score` `score`,
                              `caches`.`picturescount` `picturescount`,
                              `caches`.`mp3count` `mp3count`,
                              `caches`.`desc_languages` `desc_languages`,
                              `caches`.`topratings` `topratings`,
                              `caches`.`ignorer_count` `ignorer_count`,
                              `caches`.`votes` `votes_count`,
                              `cache_type`.`icon_large` `icon_large`,
                              `user`.`username` `username`,
                              `countries`.`short` AS `country_short`,
                            IFNULL(`cache_location`.`code1`, '') AS `code1`,
                            IFNULL(`cache_location`.`adm1`, '') AS `adm1`,
                            IFNULL(`cache_location`.`adm2`, '') AS `adm2`,
                            IFNULL(`cache_location`.`adm3`, '') AS `adm3`,
                            IFNULL(`cache_location`.`code3`, '') AS `code3`,
                            IFNULL(`cache_location`.`adm4`, '') AS `adm4`,
                            caches.org_user_id,
                            org_user.username as org_username,
                            cache_titled.date_alg date_alg
                          FROM (`caches`
                                    JOIN user ON `caches`.`user_id` = `user`.`user_id`
                                    JOIN cache_type ON `cache_type`.`id`=`caches`.`type`
                                    LEFT JOIN `cache_location` ON `caches`.`cache_id` = `cache_location`.`cache_id`
                                    LEFT OUTER JOIN user org_user ON org_user.user_id = caches.org_user_id
                                    LEFT JOIN `cache_titled` ON `caches`.`cache_id` = `cache_titled`.`cache_id`
                                )
                                INNER JOIN countries ON (caches.country = countries.short)
                          WHERE `caches`.`cache_id`= :v1";
        // $params['v1']['value'] = (string) $lang_db;; //TODO: be check if to replace with translation throuhgh languages
        // $params['v1']['data_type'] = 'string';
        $params['v1']['value'] = (integer) $cache_id;
        $params['v1']['data_type'] = 'integer';

        $dbc->paramQuery($thatquery, $params);
        unset($params); //clear to avoid overlaping on next paramQuery (if any))
        if ($dbc->rowCount() == 0) {
            $cache_id = 0;
        } else {
            $cache_record = $dbc->dbResultFetch();
            $geocache = new GeoCache(array('cacheId'=>$cache_id));
        }

        // detailed cache access logging
        if (@$enable_cache_access_logs && $cache_id > 0) {
            $user_id = $usr !== false ? $usr['userid'] : null;
            $access_log = @$_SESSION['CACHE_ACCESS_LOG_VC_' . $user_id];
            if ($access_log === null) {
                $_SESSION['CACHE_ACCESS_LOG_VC_' . $user_id] = array();
                $access_log = $_SESSION['CACHE_ACCESS_LOG_VC_' . $user_id];
            }
            if (@$access_log[$cache_id] !== true) {
                $dbc->multiVariableQuery(
                        'INSERT INTO CACHE_ACCESS_LOGS
                            (event_date, cache_id, user_id, source, event, ip_addr, user_agent, forwarded_for)
                         VALUES
                            (NOW(), :1, :2, \'B\', \'view_cache\', :3, :4, :5)', $cache_id, $user_id, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_X_FORWARDED_FOR']
                );
                $access_log[$cache_id] = true;
                $_SESSION['CACHE_ACCESS_LOG_VC_' . $user_id] = $access_log;
            }
        }


        //mysql_free_result($rs);
        if ($cache_record['user_id'] == $usr['userid'] || $usr['admin']) {
            $show_edit = true;
        } else
            $show_edit = false;
        //mysql_query("SET NAMES 'utf8'");
// coordnates modificator -START
// getting modified cords
        $orig_coord_info_lon = ''; //use to determine whether icon shall be displayed
        $coords_correct = true;
        $mod_coords_modified = false;
        $cache_type = $cache_record['type'];
        $mod_coord_delete_mode = isset($_POST['resetCoords']);
        $cache_mod_lat = 0;
        $cache_mod_lon = 0;
        if ($usr != false && ($cache_type == GeoCache::TYPE_QUIZ || $cache_type == GeoCache::TYPE_OTHERTYPE || $cache_type == GeoCache::TYPE_MULTICACHE)) {

            $orig_cache_lon = $cache_record['longitude'];
            $orig_cache_lat = $cache_record['latitude'];
            $cache_modifiable = true;
            $thatquery = "SELECT `cache_mod_cords`.`id` AS `mod_cords_id`,
                    `cache_mod_cords`.`longitude` AS `mod_lon`, `cache_mod_cords`.`latitude` AS `mod_lat`
                    FROM `cache_mod_cords` INNER JOIN `caches` ON (`caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND
                            `cache_mod_cords`.`user_id` = :v1)
                            WHERE `caches`.`cache_id`=:v2";
            $params['v1']['value'] = (integer) $usr['userid'];
            $params['v1']['data_type'] = 'integer';
            $params['v2']['value'] = (integer) $cache_id;
            $params['v2']['data_type'] = 'integer';
            $dbc->paramQuery($thatquery, $params);
            unset($params); //clear to avoid overlaping on next paramQuery (if any))
            $cache_mod_coords = $dbc->dbResultFetch();
            if ($cache_mod_coords != 0) {
                if ($mod_coord_delete_mode == false) {
                    $orig_coord_info_lon = htmlspecialchars(help_lonToDegreeStr($orig_cache_lon), ENT_COMPAT, 'UTF-8');
                    $orig_coord_info_lat = htmlspecialchars(help_latToDegreeStr($orig_cache_lat), ENT_COMPAT, 'UTF-8');
                    $cache_record['longitude'] = $cache_mod_coords['mod_lon'];
                    $cache_record['latitude'] = $cache_mod_coords['mod_lat'];
                };
            }
            // insert/edit modified coordinates
            if (isset($_POST['modCoords'])) {
                $coords_lat_h = $_POST['coordmod_lat_degree'];
                $coords_lon_h = $_POST['coordmod_lon_degree'];
                $coords_lat_min = $_POST['coordmod_lat'];
                $coords_lon_min = $_POST['coordmod_lon'];

                // validation. Copy&Paste from editcache.php. It should be available in common code place.
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

                if ($lat_not_ok || $lon_not_ok) {
                    $coords_correct = false;
                    tpl_set_var('coords_message', $error_coords_not_ok);
                } else {
                    // set inputs values
                    $mod_coords_modified = true;
                    tpl_set_var('coordmod_lat_h', $coords_lat_h);
                    tpl_set_var('coordmod_lat', $coords_lat_min);
                    tpl_set_var('coordmod_lon_h', $coords_lon_h);
                    tpl_set_var('coordmod_lon', $coords_lon_min);

                    $cache_mod_lat = $coords_lat_h + round($coords_lat_min, 3) / 60;
                    if ($_POST['coordmod_latNS'] == 'S')
                        $cache_mod_lat = -$cache_mod_lat;

                    $cache_mod_lon = $coords_lon_h + round($coords_lon_min, 3) / 60;
                    if ($_POST['coordmod_lonEW'] == 'W')
                        $cache_mod_lon = -$cache_mod_lon;

                    if ($cache_mod_coords['mod_cords_id'] != null) {  // user modified caches cords earlier
                        $thatquery = "UPDATE `cache_mod_cords` SET `date` = NOW(), `longitude` = :v1, `latitude` = :v2
                                WHERE `id` = :v3";
                        $params['v1']['value'] = (float) $cache_mod_lon;
                        $params['v1']['data_type'] = 'string';
                        $params['v2']['value'] = (float) $cache_mod_lat;
                        $params['v2']['data_type'] = 'string';
                        $params['v3']['value'] = (integer) $cache_mod_coords['mod_cords_id'];
                        $params['v3']['data_type'] = 'integer';

                    } else { // first edit
                        $thatquery = "INSERT INTO `cache_mod_cords` (`cache_id`, `user_id`, `date`, `longitude`, `latitude`) values(:v1, :v2, now(), :v3, :v4)";
                        $params['v1']['value'] = (integer) $cache_id;
                        $params['v1']['data_type'] = 'integer';
                        $params['v2']['value'] = (integer) $usr['userid'];
                        $params['v2']['data_type'] = 'integer';
                        $params['v3']['value'] = (float) $cache_mod_lon;
                        $params['v3']['data_type'] = 'string';
                        $params['v4']['value'] = (float) $cache_mod_lat;
                        $params['v4']['data_type'] = 'string';
                    }
                    $dbc->paramQuery($thatquery, $params);
                    $dbc->reset();
                    unset($params);

                    $orig_coord_info_lon = htmlspecialchars(help_lonToDegreeStr($orig_cache_lon), ENT_COMPAT, 'UTF-8');
                    $orig_coord_info_lat = htmlspecialchars(help_latToDegreeStr($orig_cache_lat), ENT_COMPAT, 'UTF-8');
                    $cache_record['longitude'] = $cache_mod_lon;
                    $cache_record['latitude'] = $cache_mod_lat;
                }
            }

            // delete modified coordinates
            if ($mod_coord_delete_mode) {
                $thatquery = "DELETE FROM `cache_mod_cords` WHERE `id` = :v1";
                $params['v1']['value'] = (integer) $cache_mod_coords['mod_cords_id'];
                $params['v1']['data_type'] = 'integer';
                $dbc->paramQuery($thatquery, $params);
                unset($params);
            }
        } else {
            $cache_mod_coords = false;
        }
        if ($coords_correct) {
            tpl_set_var('coords_message', "");
        }

        if ($orig_coord_info_lon !== '' && (!$mod_coord_delete_mode)) {
            $orig_coord_info_full = tr('orig_coord_modified_info') . '&#10;' . $orig_coord_info_lat . '&#10;' . $orig_coord_info_lon;
            $orig_coord_info_icon = '<a href="#coords_mod"><img src="tpl/stdstyle/images/blue/signature1-orange.png" class="icon32" alt="' . $orig_coord_info_full . '" title="' . $orig_coord_info_full . '" /></a>';
            tpl_set_var('mod_cord_info', $orig_coord_info_icon);
            if ($cache_mod_lat >= 0) {
                tpl_set_var('N_selected', 'selected="selected"');
                tpl_set_var('S_selected', '');
            } else {
                tpl_set_var('S_selected', 'selected="selected"');
                tpl_set_var('N_selected', '');
            }
            if ($cache_mod_lon >= 0) {
                tpl_set_var('E_selected', 'selected="selected"');
                tpl_set_var('W_selected', '');
            } else {
                tpl_set_var('W_selected', 'selected="selected"');
                tpl_set_var('E_selected', '');
            }
            tpl_set_var('mod_suffix', '[F]');
        } else {
            tpl_set_var('mod_cord_info', '');
            tpl_set_var('N_selected', 'selected="selected"'); //set default coords to N and E
            tpl_set_var('S_selected', '');
            tpl_set_var('E_selected', 'selected="selected"');
            tpl_set_var('W_selected', '');
            tpl_set_var('mod_suffix', '');
        }

        if ($usr == false || !isset($cache_modifiable)) {
            tpl_set_var('coordsmod_start', '<!--');
            tpl_set_var('coordsmod_end', '-->');
            tpl_set_var('mod_suffix', '');
        } else {
            tpl_set_var('coordsmod_start', '');
            tpl_set_var('coordsmod_end', '');
        }
        if (!$mod_coords_modified) {
            if (($cache_mod_coords['mod_cords_id'] != null) && ($mod_coord_delete_mode == false)) {
                $mod_lat_arr = help_latToArray($cache_mod_coords['mod_lat']);
                tpl_set_var('coordmod_lat_h', $mod_lat_arr[1]);
                tpl_set_var('coordmod_lat', $mod_lat_arr[2]);

                $mod_lon_arr = help_latToArray($cache_mod_coords['mod_lon']);
                tpl_set_var('coordmod_lon_h', $mod_lon_arr[1]);
                tpl_set_var('coordmod_lon', $mod_lon_arr[2]);
            } else {
                tpl_set_var('coordmod_lat_h', '-');
                tpl_set_var('coordmod_lat', '00.000');
                tpl_set_var('coordmod_lon_h', '-');
                tpl_set_var('coordmod_lon', '00.000');
            }
        }

        // coordnates modificator -END
        //get last last_modified
        $thatquery = "SELECT MAX(`last_modified`) `last_modified` FROM
                         (SELECT `last_modified` FROM `caches` WHERE `cache_id` = :v1
                            UNION
                            SELECT `last_modified` FROM `cache_desc` WHERE `cache_id` =:v1) `tmp_result`";
        $params['v1']['value'] = (integer) $cache_id;
        $params['v1']['data_type'] = 'integer';
        $dbc->paramQuery($thatquery, $params);
        unset($params); //clear to avoid overlaping on next paramQuery (if any))
        if ($dbc->rowCount() == 0) {
            $cache_id = 0;
        } else {
            $lm = $dbc->dbResultFetch();
            $last_modified = strtotime($lm['last_modified']);
            tpl_set_var('last_modified', fixPlMonth(htmlspecialchars(strftime("%d %B %Y", $last_modified), ENT_COMPAT, 'UTF-8')));
        }
        unset($ls);
    }
    if (isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'y') {
        // add cache to print (do not duplicate items)
        if (count($_SESSION['print_list']) == 0)
            $_SESSION['print_list'] = array();
        if (onTheList($_SESSION['print_list'], $cache_id) == -1)
            array_push($_SESSION['print_list'], $cache_id);
    }
    if (isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'n') {
        // remove cache from print list
        while (onTheList($_SESSION['print_list'], $cache_id) != -1)
            unset($_SESSION['print_list'][onTheList($_SESSION['print_list'], $cache_id)]);
        $_SESSION['print_list'] = array_values($_SESSION['print_list']);
    }
    $guru = sqlvalue("SELECT `guru` FROM `user` WHERE `user_id` = '" . sql_escape($usr['userid']) . "' LIMIT 1", 0);

    if ($cache_id != 0 && (($cache_record['status'] != 4 && $cache_record['status'] != 5 && ($cache_record['status'] != 6 /* || $cache_record['type'] == 6 */)) || $usr['userid'] == $cache_record['user_id'] || $usr['admin'] || ( $cache_record['status'] == 4 && $guru ))) {
        //ok, cache is here, let's process
        $owner_id = $cache_record['user_id'];

        // check XY home if OK redirect to myn
        if ($usr == true) {
            $ulat = sqlValue("SELECT `latitude` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);
            $ulon = sqlValue("SELECT `longitude` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);

            if (($ulon != NULL && $ulat != NULL) || ($ulon != 0 && $ulat != 0)) {

                $distancecache = sprintf("%.2f", calcDistance($ulat, $ulon, $cache_record['latitude'], $cache_record['longitude']));
                tpl_set_var('distance_cache', '<img src="tpl/stdstyle/images/free_icons/car.png" class="icon16" alt="distance" title="" align="middle" />&nbsp;' . tr('distance_to_cache') . ': <b>' . $distancecache . ' km</b><br />');
            } else {
                tpl_set_var('distance_cache', '');
            }
        } else {
            tpl_set_var('distance_cache', '');
        }
        // get cache waypoint
        $cache_wp = '';
        if ($cache_record['wp_oc'] != '')
            $cache_wp = $cache_record['wp_oc'];
        else if ($cache_record['wp_gc'] != '')
            $cache_wp = $cache_record['wp_gc'];
        else if ($cache_record['wp_nc'] != '')
            $cache_wp = $cache_record['wp_nc'];
        else if ($cache_record['wp_tc'] != '')
            $cache_wp = $cache_record['wp_tc'];
        else if ($cache_record['wp_ge'] != '')
            $cache_wp = $cache_record['wp_ge'];

        // check if there is geokret in this cache
        $thatquery = "SELECT gk_item.id, name, distancetravelled as distance FROM gk_item INNER JOIN gk_item_waypoint ON (gk_item.id = gk_item_waypoint.id) WHERE gk_item_waypoint.wp = :v1 AND stateid<>1 AND stateid<>4 AND stateid <>5 AND typeid<>2 AND missing=0";
        $params['v1']['value'] = (string) $cache_wp;
        $params['v1']['data_type'] = 'string';
        $dbc->paramQuery($thatquery, $params);
        unset($params); //clear to avoid overlaping on next paramQuery (if any))
        $geokrety_all_count = $dbc->rowCount();
        if ($geokrety_all_count == 0) {
            // no geokrets in this cache
            tpl_set_var('geokrety_begin', '<!--');
            tpl_set_var('geokrety_end', '-->');
            tpl_set_var('geokrety_content', '');
        } else {
            // geokret is present in this cache
            $geokrety_content = '';
            $geokrety_all = $dbc->dbResultFetchAll();

            for ($i = 0; $i < $geokrety_all_count; $i++) {
                $geokret = $geokrety_all[$i];
                $geokrety_content .= "<img src=\"/images/geokret.gif\" alt=\"\"/>&nbsp;<a href='http://geokrety.org/konkret.php?id=" . $geokret['id'] . "'>" . $geokret['name'] . "</a> - " . tr('total_distance') . ": " . $geokret['distance'] . " km<br/>";
            }
            tpl_set_var('geokrety_begin', '');
            tpl_set_var('geokrety_end', '');
            tpl_set_var('geokrety_content', $geokrety_content);
        }

        /**
         * GeoKretyApi. Display window with logging report of Geokrets.
         * (only when page was redirected from log.php and Geokrety was logged)
         *
         * @author Andrzej Łza Woźniak 2013
         */
        if (isset($_SESSION['GeoKretyApi'])) {
            $GeoKretyLogResult = unserialize($_SESSION['GeoKretyApi']);

            if (count($GeoKretyLogResult) > 0) {
                unset($_SESSION['GeoKretyApi']);
                $geoKretErrorInfoDisplay = false;
                $GeokretyWindowContent = '';
                foreach ($GeoKretyLogResult as $geokret) {
                    $GeokretyWindowContent .= $geokret['geokretName'];
                    foreach ($geokret['errors'] as $errorGK) {
                        if ($errorGK['error'] == '') {
                            $GeokretyWindowContent .= ' - ' . tr('GKApi20');
                        } else {
                            $GeokretyWindowContent .= '  - ' . tr('GKApi21') . ': ' . tr('GKApi22');
                            $geoKretErrorInfoDisplay = true;
                        }
                    }
                    $GeokretyWindowContent .= '<br />';
                }
                if ($geoKretErrorInfoDisplay)
                    $GeokretyWindowContent .= '<br/><br/>' . tr('GKApi30') . '<br/>';

                tpl_set_var('jQueryPopUpWindowscripts', '
                        <link rel="stylesheet" href="tpl/stdstyle/js/jquery_1.9.2_ocTheme/themes/cupertino/jquery.ui.all.css">
                        <script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/jquery-1.8.3.js"></script>
                        <script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.ui.core.js"></script>
                        <script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.ui.widget.js"></script>
                        <script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.ui.mouse.js"></script>
                        <script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.ui.button.js"></script>
                        <script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.ui.draggable.js"></script>
                        <script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.ui.position.js"></script>
                        <script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.ui.resizable.js"></script>
                        <script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.ui.dialog.js"></script>
                        <script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.ui.effect.js"></script>
                        <script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.ui.effect-blind.js"></script>
                        <script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.ui.effect-explode.js"></script>
                        <link rel="stylesheet" href="/tpl/stdstyle/js/jquery_1.9.2_ocTheme/jquery.css">
                        <script src="/tpl/stdstyle/js/jquery_1.9.2_ocTheme/viewcache_GeoKretyAPI_message.js"></script>
                        ');
                tpl_set_var('GeoKretyApi_window_display', 'inline');
                tpl_set_var('GeoKretyApi_windowContent', $GeokretyWindowContent);
            } else {
                tpl_set_var('jQueryPopUpWindowscripts', ' ');
                tpl_set_var('GeoKretyApi_window_display', 'none');
                unset($_SESSION['GeoKretyApi']);
            }
        } else {
            tpl_set_var('jQueryPopUpWindowscripts', ' ');
            tpl_set_var('GeoKretyApi_window_display', 'none');
            unset($_SESSION['GeoKretyApi']);
        }
        # end of GeoKretyApi

        if ($cache_record['votes'] < 3) {
            // DO NOT show cache's score
            $score = "";
            $scorecolor = "";
            $font_size = "";
            tpl_set_var('score', "N/A");
            tpl_set_var('scorecolor', "#000000");
        } else {
            // show cache's score
            $score = $cache_record['score'];
            $scorenum = score2ratingnum($cache_record['score']);
            $font_size = "2";
            if ($scorenum == 0)
                $scorecolor = "#DD0000";
            else
            if ($scorenum == 1)
                $scorecolor = "#F06464";
            else
            if ($scorenum == 2)
                $scorecolor = "#DD7700";
            else
            if ($scorenum == 3)
                $scorecolor = "#77CC00";
            else
            if ($scorenum == 4)
                $scorecolor = "#00DD00";
            tpl_set_var('score', score2rating($score));
            tpl_set_var('scorecolor', $scorecolor);
        }

        // begin visit-counter
        // delete cache_visits older 1 day 60*60*24 = 86400
        sql("DELETE FROM `cache_visits` WHERE `cache_id`=&1 AND `user_id_ip` != '0' AND NOW()-`last_visited` > 86400", $cache_id);

        // first insert record for visit counter if not in db
        $chkuserid = isset($usr['userid']) ? $usr['userid'] : $_SERVER["REMOTE_ADDR"];

        // note the visit of this user
        sql("INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`, `last_visited`) VALUES (&1, '&2', 1, NOW())
                    ON DUPLICATE KEY UPDATE `count`=`count`+1", $cache_id, $chkuserid);

        if ($chkuserid != $owner_id) {
            // if the previous statement does an INSERT, it was the first visit for this user
            if (mysql_affected_rows($dblink) == 1) {
                // increment the counter for this cache
                sql("INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`, `last_visited`) VALUES (&1, '0', 1, NOW())
                            ON DUPLICATE KEY UPDATE `count`=`count`+1, `last_visited`=NOW()", $cache_id);
            }
        }
        // end visit-counter
        // hide coordinates when user is not logged in
        if ($usr == true || !$hide_coords) {
            $coords = mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_latToDegreeStr($cache_record['latitude']), ENT_COMPAT, 'UTF-8')) . '&nbsp;' . mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_lonToDegreeStr($cache_record['longitude']), ENT_COMPAT, 'UTF-8'));
            $coords2 = mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_latToDegreeStr($cache_record['latitude'], 0), ENT_COMPAT, 'UTF-8')) . '&nbsp;' . mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_lonToDegreeStr($cache_record['longitude'], 0), ENT_COMPAT, 'UTF-8'));
            $coords3 = mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_latToDegreeStr($cache_record['latitude'], 2), ENT_COMPAT, 'UTF-8')) . '&nbsp;' . mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_lonToDegreeStr($cache_record['longitude'], 2), ENT_COMPAT, 'UTF-8'));
            $coords_other = "<a href=\"#\" onclick=\"javascript:window.open('http://www.opencaching.pl/coordinates.php?lat=" . $cache_record['latitude'] . "&amp;lon=" . $cache_record['longitude'] . "&amp;popup=y&amp;wp=" . htmlspecialchars($cache_record['wp_oc'], ENT_COMPAT, 'UTF-8') . "','Koordinatenumrechnung','width=240,height=334,resizable=yes,scrollbars=1')\">" . tr('coords_other') . "</a>";
        } else {
            $coords = tr('hidden_coords');
            $coords_other = "";
        }


        if ($cache_record['type'] == GeoCache::TYPE_EVENT) {
            $cache_stats = '';
        } else {
            if (($cache_record['founds'] + $cache_record['notfounds'] + $cache_record['notes']) != 0) {
                $cache_stats = "<a class =\"links2\" href=\"javascript:void(0)\" onmouseover=\"Tip('" . tr('show_statictics_cache') . "', BALLOON, true, ABOVE, false, OFFSETX, -17, PADDING, 8, WIDTH, -240)\" onmouseout=\"UnTip()\" onclick=\"javascript:window.open('cache_stats.php?cacheid=" . $cache_record['cache_id'] . "&amp;popup=y','Cache_Statistics','width=500,height=750,resizable=yes,scrollbars=1')\"><img src=\"tpl/stdstyle/images/blue/stat1.png\" alt=\"Statystyka skrzynki\" title=\"Statystyka skrzynki\" /></a>";
            } else {
                $cache_stats = "<a class =\"links2\" href=\"javascript:void(0)\" onmouseover=\"Tip('" . tr('not_stat_cache') . "', BALLOON, true, ABOVE, false, OFFSETX, -17, PADDING, 8, WIDTH, -240)\" onmouseout=\"UnTip()\"><img src=\"tpl/stdstyle/images/blue/stat1.png\" alt=\"\" title=\"\" /></a>";
            }
        }
        if (!isset($map_msg))
            $map_msg = '';
        if (!isset($map_msg))
            $map_msg = '';
        if (!isset($coords2))
            $coords2 = '';
        if (!isset($coords3))
            $coords3 = '';
        tpl_set_var('cache_stats', $cache_stats);
        tpl_set_var('googlemap_key', $googlemap_key);
        tpl_set_var('map_msg', $map_msg);
        tpl_set_var('coords2', $coords2);
        tpl_set_var('coords3', $coords3);
        tpl_set_var('coords_other', $coords_other);
        tpl_set_var('typeLetter', typeToLetter($cache_record['type']));

        // cache locations
        tpl_set_var('kraj', "");
        tpl_set_var('woj', "");
        tpl_set_var('dziubek1', "");
        tpl_set_var('miasto', "");
        tpl_set_var('dziubek2', "");

        if (substr(@tr($cache_record['code1']), -5) == '-todo')
            $countryTranslation = $cache_record['adm1'];
        else
            $countryTranslation = tr($cache_record['code1']);
        // if (substr(@tr($cache_record['code3']), -5) == '-todo') $regionTranslation = $cache_record['adm3']; else $regionTranslation = tr($cache_record['code3']);
        $regionTranslation = $cache_record['adm3'];

        if ($cache_record['code1'] != "") {
            tpl_set_var('kraj', $countryTranslation);
        } else {
            tpl_set_var('kraj', tr($cache_record['country_short']));
        }
        if ($cache_record['code3'] != "") {
            $woj = $cache_record['adm3'];
            tpl_set_var('woj', $regionTranslation);
        } else {
            $woj = $cache_record['adm2'];
            tpl_set_var('woj', $woj);
        }
        if ($woj == "") {
            tpl_set_var('woj', $cache_record['adm4']);
        }
        if ($woj != "" || $cache_record['adm3'] != "")
            tpl_set_var('dziubek1', ">");

        // NPA - nature protection areas
        $npac = "0";
        $npa_content = '';

        // Parki Narodowe , Krajobrazowe
        $rsArea = sql("SELECT `parkipl`.`id` AS `npaId`, `parkipl`.`name` AS `npaname`,`parkipl`.`link` AS `npalink`,`parkipl`.`logo` AS `npalogo`
                 FROM `cache_npa_areas`
           INNER JOIN `parkipl` ON `cache_npa_areas`.`parki_id`=`parkipl`.`id`
                WHERE `cache_npa_areas`.`cache_id`='&1' AND `cache_npa_areas`.`parki_id`!='0'", $cache_record['cache_id']);

        if (mysql_num_rows($rsArea) != 0) {
            $npa_content .="<table width=\"90%\" border=\"0\" style=\"border-collapse: collapse; font-weight: bold;font-size: 14px; line-height: 1.6em\"><tr>
            <td align=\"center\" valign=\"middle\"><b>" . tr('npa_info') . " <font color=\"green\"></font></b>:<br /></td><td align=\"center\" valign=\"middle\">&nbsp;</td></tr>";
            $npac = "1";
            while ($npa = mysql_fetch_array($rsArea)) {
                $npa_content .= "<tr><td align=\"center\" valign=\"middle\"><font color=\"blue\"><a target=\"_blank\" href=\"http://" . $npa['npalink'] . "\">" . $npa['npaname'] . "</a></font><br />";
                $npa_content .="</td><td align=\"center\" valign=\"middle\"><img src=\"tpl/stdstyle/images/pnk/" . $npa['npalogo'] . "\"></td></tr>";
            }
            $npa_content .="</table>";
        }

        // Natura 200
        $rsArea = sql("SELECT `npa_areas`.`id` AS `npaId`, `npa_areas`.`linkid` AS `linkid`,`npa_areas`.`sitename` AS `npaSitename`, `npa_areas`.`sitecode` AS `npaSitecode`, `npa_areas`.`sitetype` AS `npaSitetype`
                 FROM `cache_npa_areas`
           INNER JOIN `npa_areas` ON `cache_npa_areas`.`npa_id`=`npa_areas`.`id`
                WHERE `cache_npa_areas`.`cache_id`='&1' AND `cache_npa_areas`.`npa_id`!='0'", $cache_record['cache_id']);

        if (mysql_num_rows($rsArea) != 0) {
            $npa_content .="<table width=\"90%\" border=\"0\" style=\"border-collapse: collapse; font-weight: bold;font-size: 14px; line-height: 1.6em\"><tr>
            <td width=90% align=\"center\" valign=\"middle\"><b>" . tr('npa_info') . " <font color=\"green\">NATURA 2000</font></b>:<br />";
            $npac = "1";

            while ($npa = mysql_fetch_array($rsArea)) {
                $npa_item = $config['nature2000link'];
                $npa_item = mb_ereg_replace('{linkid}', $npa['linkid'], $npa_item);
                $npa_item = mb_ereg_replace('{sitename}', $npa['npaSitename'], $npa_item);
                $npa_item = mb_ereg_replace('{sitecode}', $npa['npaSitecode'], $npa_item);
                $npa_content .= $npa_item . '<br />';
            }
            $npa_content .="</td><td align=\"center\" valign=\"middle\"><img src=\"tpl/stdstyle/images/misc/natura2000.png\"></td>
                </tr></table>";
        }

        if ($npac == "0") {

            tpl_set_var('hidenpa_start', '<!--');
            tpl_set_var('hidenpa_end', '-->');
            tpl_set_var('npa_content', '');
        } else {

            tpl_set_var('hidenpa_start', '');
            tpl_set_var('hidenpa_end', '');
            tpl_set_var('npa_content', $npa_content);
        }




        //cache data
        list($iconname) = getCacheIcon($usr['userid'], $cache_record['cache_id'], $cache_record['status'], $cache_record['user_id'], $cache_record['icon_large']);

        list($lat_dir, $lat_h, $lat_min) = help_latToArray($cache_record['latitude']);
        list($lon_dir, $lon_h, $lon_min) = help_lonToArray($cache_record['longitude']);

        $tpl_subtitle = htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8') . ' - ';
        $map_msg = mb_ereg_replace("{target}", urlencode("viewcache.php?cacheid=" . $cache_id), tr('map_msg'));

        tpl_set_var('googlemap_key', $googlemap_key);
        tpl_set_var('map_msg', $map_msg);
        tpl_set_var('typeLetter', typeToLetter($cache_record['type']));

        tpl_set_var('cacheid_urlencode', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'));
        tpl_set_var('cachename', htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8'));

        if ( $cache_record['date_alg'] == '' )
            tpl_set_var('icon_titled', '');
        else
        {
            $ntitled_cache = $titled_cache_period_prefix.'_titled_cache';
            tpl_set_var('icon_titled', '<img src="tpl/stdstyle/images/free_icons/award_star_gold_1.png" class="icon16" alt="'.tr($ntitled_cache).'" title="'.tr($ntitled_cache).'"/>');
        }

        // cache type Mobile add calculate distance
        // todo: poszerzyć tabelkę 'caches' (lub stworzyć nową z relacją)
        //       pole dystans, żeby nie trzeba było za każdym razem zliczać
        //       dystansu.
        if ($cache_record['type'] == GeoCache::TYPE_MOVING) {
            tpl_set_var('moved_icon', $moved_icon);
            /* if (!isset($_REQUEST['cacheid'])) $OpencacheID = $cache_id */
            $moved = sqlValue("SELECT COUNT(*) FROM `cache_logs` WHERE type=4 AND cache_logs.deleted='0' AND cache_id='" . $cache_id /* sql_escape($_REQUEST['cacheid']) */ . "'", 0);

            // calculate mobile cache distance
            $dst = mysql_fetch_assoc(mysql_query("SELECT sum(km) AS dystans FROM cache_moved WHERE cache_id=" . $cache_id /* sql_escape($_REQUEST['cacheid']) */));
            $dystans = round($dst['dystans'], 2);



            tpl_set_var('dystans', $dystans);
            tpl_set_var('moved', $moved);
            tpl_set_var('hidemobile_start', '');
            tpl_set_var('hidemobile_end', '');
        } else {
            tpl_set_var('hidemobile_start', '<!--');
            tpl_set_var('hidemobile_end', '-->');
        }


        tpl_set_var('coords', $coords);
        if ($usr || !$hide_coords) {
            if ($cache_record['longitude'] < 0) {
                $longNC = $cache_record['longitude'] * (-1);
                tpl_set_var('longitudeNC', $longNC);
            } else {
                tpl_set_var('longitudeNC', $cache_record['longitude']);
            }

            tpl_set_var('longitude', $cache_record['longitude']);
            tpl_set_var('latitude', $cache_record['latitude']);
            tpl_set_var('lon_h', $lon_h);
            tpl_set_var('lon_min', $lon_min);
            tpl_set_var('lonEW', $lon_dir);
            tpl_set_var('lat_h', $lat_h);
            tpl_set_var('lat_min', $lat_min);
            tpl_set_var('latNS', $lat_dir);
        }
        tpl_set_var('cacheid', $cache_id);
        tpl_set_var('cachetype', htmlspecialchars(cache_type_from_id($cache_record['type'], $lang), ENT_COMPAT, 'UTF-8'));
        $iconname = str_replace("mystery", "quiz", $iconname);
        tpl_set_var('icon_cache', htmlspecialchars("$stylepath/images/$iconname", ENT_COMPAT, 'UTF-8'));
        tpl_set_var('cachesize', htmlspecialchars(cache_size_from_id($cache_record['size'], $lang), ENT_COMPAT, 'UTF-8'));
        tpl_set_var('oc_waypoint', htmlspecialchars($cache_record['wp_oc'], ENT_COMPAT, 'UTF-8'));
        if ($cache_record['topratings'] == 1)
            tpl_set_var('rating_stat', mb_ereg_replace('{ratings}', $cache_record['topratings'], $rating_stat_show_singular));
        else if ($cache_record['topratings'] > 1)
            tpl_set_var('rating_stat', mb_ereg_replace('{ratings}', $cache_record['topratings'], $rating_stat_show_plural));
        else
            tpl_set_var('rating_stat', '');
        // cache_rating list of users
        // no geokrets in this cache
        tpl_set_var('list_of_rating_begin', '');
        tpl_set_var('list_of_rating_end', '');
        tpl_set_var('body_scripts', '');
        tpl_set_var('altitude', $geocache->getAltitude()->getAltitude());
        $rscr = sql("SELECT user.username username FROM `cache_rating` INNER JOIN user ON (cache_rating.user_id = user.user_id) WHERE cache_id=&1 ORDER BY username", $cache_id);
        if ($rscr == false) {
            tpl_set_var('list_of_rating_begin', '');
            tpl_set_var('list_of_rating_end', '');
        } else {
// ToolTips Ballon
            tpl_set_var('body_scripts', '<script type="text/javascript" src="lib/js/wz_tooltip.js"></script><script type="text/javascript" src="lib/js/tip_balloon.js"></script><script type="text/javascript" src="lib/js/tip_centerwindow.js"></script>');
            $lists = '';
            $numr = (mysql_num_rows($rscr) - 1);
            for ($i = 0; $i < mysql_num_rows($rscr); $i++) {
                $record = sql_fetch_array($rscr);
                $lists .= $record['username'];
                if (mysql_num_rows($rscr) == 1) {
                    $lists .= ' ';
                } else {
                    if ($i == $numr) {
                        $lists .= ' ';
                    } else {
                        $lists .= ', ';
                    }
                }
            }
            $content_list = "<a class =\"links2\" href=\"javascript:void(0)\" onmouseover=\"Tip('<b>" . tr('recommended_by') . ": </b><br /><br />";
            $content_list .= $lists;
            $content_list .= "<br /><br/>', BALLOON, true, ABOVE, false, OFFSETY, 20, OFFSETX, -17, PADDING, 8, WIDTH, -240)\" onmouseout=\"UnTip()\">";

            tpl_set_var('list_of_rating_begin', $content_list);
            tpl_set_var('list_of_rating_end', '</a>');
        }

        if ((($cache_record['way_length'] == null) && ($cache_record['search_time'] == null)) ||
                (($cache_record['way_length'] == 0) && ($cache_record['search_time'] == 0))) {
            tpl_set_var('hidetime_start', '<!-- ');
            tpl_set_var('hidetime_end', ' -->');

            tpl_set_var('search_time', 'b.d.');
            tpl_set_var('way_length', 'b.d.');
        } else {
            tpl_set_var('hidetime_start', '');
            tpl_set_var('hidetime_end', '');

            if (($cache_record['search_time'] == null) || ($cache_record['search_time'] == 0))
                tpl_set_var('search_time', 'b.d.');
            else {
                $time_hours = floor($cache_record['search_time']);
                $time_min = ($cache_record['search_time'] - $time_hours) * 60;
                $time_min = sprintf('%02d', round($time_min, 1));
                tpl_set_var('search_time', $time_hours . ':' . $time_min . ' h');
            }

            if (($cache_record['way_length'] == null) || ($cache_record['way_length'] == 0))
                tpl_set_var('way_length', 'b.d.');
            else
                tpl_set_var('way_length', sprintf('%01.2f km', $cache_record['way_length']));
        }

        tpl_set_var('country', htmlspecialchars(db_CountryFromShort($cache_record['country']), ENT_COMPAT, 'UTF-8'));
        tpl_set_var('cache_log_pw', (($cache_record['logpw'] == NULL) || ($cache_record['logpw'] == '')) ? '' : $cache_log_pw);
        tpl_set_var('nocrypt', $no_crypt);
        $hidden_date = strtotime($cache_record['date_hidden']);
        tpl_set_var('hidden_date', fixPlMonth(htmlspecialchars(strftime("%d %B %Y", $hidden_date), ENT_COMPAT, 'UTF-8')));

        $listed_on = array();
        if ($usr !== false && $usr['userFounds'] >= $config['otherSites_minfinds']) {
            if ($cache_record['wp_ge'] != '' && $config['otherSites_gpsgames_org'] == 1)
                $listed_on[] = '<a href="http://geocaching.gpsgames.org/cgi-bin/ge.pl?wp=' . $cache_record['wp_ge'] . '" target="_blank">GPSgames.org (' . $cache_record['wp_ge'] . ')</a>';

            if ($cache_record['wp_tc'] != '' && $config['otherSites_terracaching_com'] == 1)
                $listed_on[] = '<a href="http://play.terracaching.com/Cache/' . $cache_record['wp_tc'] . '" target="_blank">Terracaching.com (' . $cache_record['wp_tc'] . ')</a>';

            if ($cache_record['wp_qc'] != '' && $config['otherSites_qualitycaching_com'] == 1)
                $listed_on[] = '<a href="http://www.qualitycaching.com/QCView.aspx?cid=' . $cache_record['wp_qc'] . '" target="_blank">Qualitycaching.com. (' . $cache_record['wp_qc'] . ')</a>';

            if ($cache_record['wp_nc'] != '' && $config['otherSites_navicache_com'] == 1) {
                $wpnc = hexdec(mb_substr($cache_record['wp_nc'], 1));
                $listed_on[] = '<a href="http://www.navicache.com/cgi-bin/db/displaycache2.pl?CacheID=' . $wpnc . '" target="_blank">Navicache.com (' . $wpnc . ')</a>';
            }
            if ($cache_record['wp_gc'] != '' && $config['otherSites_geocaching_com'] == 1)
                $listed_on[] = '<a href="http://www.geocaching.com/seek/cache_details.aspx?wp=' . $cache_record['wp_gc'] . '" target="_blank">Geocaching.com (' . $cache_record['wp_gc'] . ')</a>';
        }
        tpl_set_var('listed_on', sizeof($listed_on) == 0 ? $listed_only_oc : implode(", ", $listed_on));
        if (sizeof($listed_on) == 0) {
            tpl_set_var('hidelistingsites_start', '<!--');
            tpl_set_var('hidelistingsites_end', '-->');
        } else {
            tpl_set_var('hidelistingsites_start', '');
            tpl_set_var('hidelistingsites_end', '');
        }

        //cache available
        if ($cache_record['status'] != 1) {
            tpl_set_var('status', $error_prefix . htmlspecialchars(cache_status_from_id($cache_record['status'], $lang), ENT_COMPAT, 'UTF-8') . $error_suffix);
        } else {
            tpl_set_var('status', '<span style="color:green;font-weight:bold;">' . htmlspecialchars(cache_status_from_id($cache_record['status'], $lang), ENT_COMPAT, 'UTF-8') . '</span>');
        }

        $date_created = strtotime($cache_record['date_created']);
        tpl_set_var('date_created', fixPlMonth(htmlspecialchars(strftime("%d %B %Y", $date_created), ENT_COMPAT, 'UTF-8')));

        tpl_set_var('difficulty_icon_diff', icon_difficulty("diff", $cache_record['difficulty']));
        tpl_set_var('difficulty_text_diff', htmlspecialchars(sprintf($difficulty_text_diff, $cache_record['difficulty'] / 2), ENT_COMPAT, 'UTF-8'));
        tpl_set_var('difficulty_icon_terr', icon_difficulty("terr", $cache_record['terrain']));
        tpl_set_var('difficulty_text_terr', htmlspecialchars(sprintf($difficulty_text_terr, $cache_record['terrain'] / 2), ENT_COMPAT, 'UTF-8'));

        tpl_set_var('founds', htmlspecialchars($cache_record['founds'], ENT_COMPAT, 'UTF-8'));
        tpl_set_var('notfounds', htmlspecialchars($cache_record['notfounds'], ENT_COMPAT, 'UTF-8'));
        tpl_set_var('notes', htmlspecialchars($cache_record['notes'], ENT_COMPAT, 'UTF-8'));
        tpl_set_var('total_number_of_logs', htmlspecialchars($cache_record['notes'] + $cache_record['notfounds'] + $cache_record['founds'], ENT_COMPAT, 'UTF-8'));

        // Personal cache notes
        //user logged in?
        if ($usr == true) {

            $notes_rs = sql("SELECT `cache_notes`.`note_id` `note_id`,`cache_notes`.`date` `date`, `cache_notes`.`desc` `desc`, `cache_notes`.`desc_html` `desc_html` FROM `cache_notes` WHERE `cache_notes` .`user_id`=&1 AND `cache_notes`.`cache_id`=&2", $usr['userid'], $cache_id);

            tpl_set_var('note_content', "");
            tpl_set_var('CacheNoteE', '-->');
            tpl_set_var('CacheNoteS', '<!--');
            tpl_set_var('EditCacheNoteE', '');
            tpl_set_var('EditCacheNoteS', '');



            if (isset($_POST['edit'])) {
                tpl_set_var('CacheNoteE', '-->');
                tpl_set_var('CacheNoteS', '<!--');
                tpl_set_var('EditCacheNoteE', '');
                tpl_set_var('EditCacheNoteS', '');

                if (mysql_num_rows($notes_rs) != 0) {
                    $notes_record = sql_fetch_array($notes_rs);
                    $note = $notes_record['desc'];
                    tpl_set_var('noteid', $notes_record['note_id']);
                } else {
                    $note = "";
                }
                tpl_set_var('note_content', $note);
            }

            if (isset($_POST['remove'])) {

                $n_record = sql_fetch_array($notes_rs);
                $note_id = $n_record['note_id'];
                //remove
                sql("DELETE FROM `cache_notes` WHERE `note_id`='&1' and user_id='&2'", $note_id, $usr['userid']);
                //display cache-page
                tpl_redirect('viewcache.php?cacheid=' . urlencode($cache_id));
                exit;
            }

            if (isset($_POST['save'])) {

                $cnote = $_POST['note_content'];
                $cn = strlen($cnote);

                if (mysql_num_rows($notes_rs) != 0) {
                    $n_record = sql_fetch_array($notes_rs);
                    $note_id = $n_record['note_id'];
                    sql("UPDATE `cache_notes` SET `date`=NOW(),`desc`='&1', `desc_html`='&2' WHERE `note_id`='&3'", $cnote, '0', $note_id);
                }

                if (mysql_num_rows($notes_rs) == 0 && $cn != 0) {
                    sql("INSERT INTO `cache_notes` (
                                `note_id`,
                                `cache_id`,
                                 `user_id`,
                                 `date`,
                                `desc_html`,
                                `desc`
                                ) VALUES (
                            '', '&1', '&2',NOW(),'&3', '&4')", $cache_id, $usr['userid'], '0', $cnote);
                }

                //display cache-page
                tpl_redirect('viewcache.php?cacheid=' . urlencode($cache_id) . '#cache_note2');
                exit;
            }



            if (mysql_num_rows($notes_rs) != 0 && (!isset($_POST['edit']) || !isset($_REQUEST['edit']))) {
                tpl_set_var('CacheNoteE', '');
                tpl_set_var('CacheNoteS', '');
                tpl_set_var('EditCacheNoteE', '-->');
                tpl_set_var('EditCacheNoteS', '<!--');

                $notes_record = sql_fetch_array($notes_rs);
                $note_desc = $notes_record['desc'];

                if ($notes_record['desc_html'] == '0')
                    $note_desc = htmlspecialchars($note_desc, ENT_COMPAT, 'UTF-8');
                else {
                    require_once($rootpath . 'lib/class.inputfilter.php');
                    $myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
                    $note_desc = $myFilter->process($note_desc);
                }

                $note_desc = nl2br($note_desc);

                tpl_set_var('notes_content', $note_desc);
            }


            mysql_free_result($notes_rs);
        } else {
            tpl_set_var('note_content', "");
            tpl_set_var('CacheNoteE', '-->');
            tpl_set_var('CacheNoteS', '<!--');
            tpl_set_var('EditCacheNoteE', '-->');
            tpl_set_var('EditCacheNoteS', '<!--');
        }
        // end personal cache note
        tpl_set_var('watcher', $cache_record['watcher'] + 0);
        tpl_set_var('ignorer_count', $cache_record['ignorer_count'] + 0);
        tpl_set_var('votes_count', $cache_record['votes_count'] + 0);
        tpl_set_var('note_icon', $note_icon);
        tpl_set_var('notes_icon', $notes_icon);
        tpl_set_var('vote_icon', $vote_icon);
        tpl_set_var('gk_icon', $gk_icon);
        tpl_set_var('watch_icon', $watch_icon);
        tpl_set_var('visit_icon', $visit_icon);
        tpl_set_var('score_icon', $score_icon);
        tpl_set_var('save_icon', $save_icon);
        tpl_set_var('search_icon', $search_icon);
        if ($cache_record['type'] == GeoCache::TYPE_EVENT) {
            tpl_set_var('found_icon', $exist_icon);
            tpl_set_var('notfound_icon', $wattend_icon);
            $event_attendance_list = mb_ereg_replace('{id}', urlencode($cache_id), $event_attendance_list);
            tpl_set_var('event_attendance_list', $event_attendance_list);
            tpl_set_var('found_text', $event_attended_text);
            tpl_set_var('notfound_text', $event_will_attend_text);
        } else {
            tpl_set_var('found_icon', $found_icon);
            tpl_set_var('notfound_icon', $notfound_icon);
            tpl_set_var('event_attendance_list', '');
            tpl_set_var('found_text', $cache_found_text);
            tpl_set_var('notfound_text', $cache_notfound_text);
        }

        if (($usr['admin'] == 1)) {
            $showhidedel_link = ""; //no need to hide/show deletion for COG (they always see deletions)
        } else {
            $del_count = sqlValue("SELECT count(*) number FROM `cache_logs` WHERE `deleted`=1 and `cache_id`='" . sql_escape($cache_record['cache_id']) . "'", 0);
            if ($del_count == 0) {
                $showhidedel_link = ""; //don't show link if no deletion '
            } else {
                if (isset($_SESSION['showdel']) && $_SESSION['showdel'] == 'y') {
                    $showhidedel_link = $hide_del_link;
                } else {
                    $showhidedel_link = $show_del_link;
                }
                $showhidedel_link = str_replace('{thispage}', 'viewcache.php', $showhidedel_link); //$show_del_link is defined in viecache.inc.php - for both viewlogs and viewcashes .php
            }
        }

        tpl_set_var('showhidedel_link', mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $showhidedel_link));
        tpl_set_var('new_log_entry_link', mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $new_log_entry_link));

        // number of visits
        $rs = sql("SELECT `count` FROM `cache_visits` WHERE `cache_id`='&1' AND `user_id_ip`='0'", $cache_id);
        if (mysql_num_rows($rs) == 0)
            tpl_set_var('visits', '0');
        else {
            $watcher_record = sql_fetch_array($rs);
            tpl_set_var('visits', $watcher_record['count']);
        }
        isset($_SESSION['showdel']) && $_SESSION['showdel'] == 'y' ? $HideDeleted = false : $HideDeleted = true;
        //now include also those deleted due to displaying this type of record for all unless hide_deletions is on
        if (($usr['admin'] == 1) || ($HideDeleted == false)) {
            $sql_hide_del = "";  //include deleted
        } else {
            $sql_hide_del = "`deleted`=0 AND"; //exclude deleted
        }

        $number_logs_sql = "SELECT count(*) number FROM `cache_logs` WHERE " . $sql_hide_del . " `cache_id`='" . sql_escape($cache_record['cache_id']) . "'";
        $number_logs = sqlValue($number_logs_sql, 0);
        if ($number_logs > $logs_to_display) {
            tpl_set_var('viewlogs_last', mb_ereg_replace('{cacheid_urlencode}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $viewlogs_last));
            tpl_set_var('viewlogs', mb_ereg_replace('{cacheid_urlencode}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $viewlogs));
            tpl_set_var('viewlogs_start', "");
            tpl_set_var('viewlogs_end', "");
            $viewlogs_from_sql = "SELECT id FROM cache_logs WHERE " . $sql_hide_del . " cache_id=:1 ORDER BY date DESC, id LIMIT " . sql_escape($logs_to_display) . ",1 "; // sorry, bound variables does not work for LIMIT
            $dbc->reset();
            $viewlogs_from = $dbc->multiVariableQueryValue($viewlogs_from_sql, -1, $cache_id);
            tpl_set_var('viewlogs_from', $viewlogs_from);
        } else {
            tpl_set_var('viewlogs_last', '');
            tpl_set_var('viewlogs', '');
            tpl_set_var('viewlogs_start', "<!--");
            tpl_set_var('viewlogs_end', "-->");
            tpl_set_var('viewlogs_from', '');
        }

        tpl_set_var('cache_watcher', '');
        if ($cache_record['watcher'] > 0) {
            tpl_set_var('cache_watcher', mb_ereg_replace('{watcher}', htmlspecialchars($cache_record['watcher'], ENT_COMPAT, 'UTF-8'), isset($cache_watchers) ? $cache_watchers : 10 ));
        }

        tpl_set_var('owner_name', htmlspecialchars($cache_record['username'], ENT_COMPAT, 'UTF-8'));
        tpl_set_var('userid_urlencode', htmlspecialchars(urlencode($cache_record['user_id']), ENT_COMPAT, 'UTF-8'));

        if ($cache_record['org_user_id'] == null || $cache_record['org_user_id'] == $cache_record['user_id']) {
            tpl_set_var('creator_name_start', '<!--');
            tpl_set_var('creator_name_end', '-->');
        } else {
            tpl_set_var('creator_name_start', '');
            tpl_set_var('creator_name_end', '');
            tpl_set_var('creator_userid', $cache_record['org_user_id']);
            tpl_set_var('creator_name', htmlspecialchars($cache_record['org_username'], ENT_COMPAT, 'UTF-8'));
        }

        //get description languages
        $desclangs = mb_split(',', $cache_record['desc_languages']);

        $desclang = mb_strtoupper($lang);
        //is a description language wished?
        if (isset($_REQUEST['desclang'])) {
            $desclang = $_REQUEST['desclang'];
        }

        $enable_google_translation = false;

        //is no description available in the wished language?
        if (array_search($desclang, $desclangs) === false) {
            $desclang = $desclangs[0];
        }

        if (strtolower($desclang) != $lang && $lang != 'pl')
            $enable_google_translation = true;
        else
            $enable_google_translation = false;

        //build langs list
        $langlist = '';
        foreach ($desclangs AS $desclanguage) {
            if ($langlist != '')
                $langlist .= ', ';

            $langlist .= '<a href="viewcache.php?cacheid=' . urlencode($cache_id) . '&amp;desclang=' . urlencode($desclanguage) . $linkargs . '">';
            if ($desclanguage == $desclang) {
                $langlist .= '<i>' . htmlspecialchars($desclanguage, ENT_COMPAT, 'UTF-8') . '</i>';
            } else {
                $langlist .= htmlspecialchars($desclanguage, ENT_COMPAT, 'UTF-8');
            }
            $langlist .= '</a>';
        }

        tpl_set_var('desc_langs', $langlist);

        // ===== opensprawdzacz ========================================================

        $os_exist = sql("SELECT `waypoints`.`wp_id` ,
                                    `opensprawdzacz`.`proby`,
                                    `opensprawdzacz`.`sukcesy`
                             FROM   `waypoints`,  `opensprawdzacz`
                             WHERE  `waypoints`.`cache_id` = '&1'
                             AND    `waypoints`.`type` = 3
                             AND    `waypoints`.`opensprawdzacz` = 1
                             AND    `waypoints`.`cache_id` = `opensprawdzacz`.cache_id
                             ", $cache_id
        );
        if (mysql_num_rows($os_exist) != 0) {
            $dane_opensprawdzacza = mysql_fetch_array($os_exist);
            tpl_set_var('proby', $dane_opensprawdzacza['proby']);
            tpl_set_var('sukcesy', $dane_opensprawdzacza['sukcesy']);
            tpl_set_var('opensprawdzacz', 'opensprawdzacz');
            tpl_set_var('opensprawdzacz_end', '');
            tpl_set_var('opensprawdzacz_start', '');
        } else {
            tpl_set_var('opensprawdzacz', 'brak danych do opensprawdzacza');
            tpl_set_var('opensprawdzacz_end', '-->');
            tpl_set_var('opensprawdzacz_start', '<!--');
        }
        mysql_free_result($os_exist);
        // ===== opensprawdzacz end ====================================================
        // show additional waypoints
        if (checkField('waypoint_type', $lang))
            $lang_db = $lang;
        else
            $lang_db = "en";

        $cache_type = $cache_record['type'];
        $waypoints_visible = 0;
        $wp_rsc = sql("SELECT `wp_id`, `type`, `longitude`, `latitude`,  `desc`, `status`, `stage`, `waypoint_type`.`&1` wp_type, waypoint_type.icon wp_icon FROM `waypoints` INNER JOIN waypoint_type ON (waypoints.type = waypoint_type.id) WHERE `cache_id`='&2' ORDER BY `stage`,`wp_id`", $lang_db, $cache_id);
        if (mysql_num_rows($wp_rsc) != 0 && $cache_record['type'] != GeoCache::TYPE_MOVING) { // check status all waypoints
            for ($i = 0; $i < mysql_num_rows($wp_rsc); $i++) {
                $wp_check = sql_fetch_array($wp_rsc);
                if ($wp_check['status'] == 1 || $wp_check['status'] == 2) {
                    $waypoints_visible = 1;
                }
            }
            if ($waypoints_visible != 0) {
                $waypoints = '<table id="gradient" cellpadding="5" width="97%" border="1" style="border-collapse: collapse; font-size: 12px; line-height: 1.6em">';
                $waypoints .= '<tr>';
                if ($cache_type == 1 || $cache_type == 3 || $cache_type == 7)
                    $waypoints .= '<th align="center" valign="middle" width="30"><b>' . tr('stage_wp') . '</b></th>';

                $waypoints .='<th align="center" valign="middle" width="40">&nbsp;<b>' . tr('symbol_wp') . '</b>&nbsp;</th>
                        <th align="center" valign="middle" width="40">&nbsp;<b>' . tr('type_wp') . '</b>&nbsp;</th>
                        <th width="90" align="center" valign="middle">&nbsp;<b>' . tr('coordinates_wp') . '</b>&nbsp;</th>
                        <th align="center" valign="middle"><b>' . tr('describe_wp') . '</b></th></tr>';
            }

            $wp_rs = sql("SELECT `wp_id`, `type`, `longitude`, `latitude`,  `desc`, `status`, `stage`, `waypoint_type`.`&1` wp_type, waypoint_type.icon wp_icon FROM `waypoints` INNER JOIN waypoint_type ON (waypoints.type = waypoint_type.id) WHERE `cache_id`='&2' ORDER BY `stage`,`wp_id`", $lang_db, $cache_id);
            for ($i = 0; $i < mysql_num_rows($wp_rs); $i++) {
                $wp_record = sql_fetch_array($wp_rs);
                if ($wp_record['status'] != 3) {
                    $tmpline1 = $wpline;    // string in viewcache.inc.php

                    if ($wp_record['status'] == 1) {
                        $coords_lat_lon = "<a class=\"links4\" href=\"#\" onclick=\"javascript:window.open('http://www.opencaching.pl/coordinates.php?lat=" . $wp_record['latitude'] . "&amp;lon=" . $wp_record['longitude'] . "&amp;popup=y&amp;wp=" . htmlspecialchars($cache_record['wp_oc'], ENT_COMPAT, 'UTF-8') . "','Koordinatenumrechnung','width=240,height=334,resizable=yes,scrollbars=1'); return event.returnValue=false\">" . mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_latToDegreeStr($wp_record['latitude']), ENT_COMPAT, 'UTF-8') . "<br/>" . htmlspecialchars(help_lonToDegreeStr($wp_record['longitude']), ENT_COMPAT, 'UTF-8')) . "</a>";
                    }
                    if ($wp_record['status'] == 2) {
                        $coords_lat_lon = "N ?? ??????<br />E ?? ??????";
                    }
                    $tmpline1 = mb_ereg_replace('{wp_icon}', htmlspecialchars($wp_record['wp_icon'], ENT_COMPAT, 'UTF-8'), $tmpline1);
                    $tmpline1 = mb_ereg_replace('{type}', htmlspecialchars($wp_record['wp_type'], ENT_COMPAT, 'UTF-8'), $tmpline1);
                    $tmpline1 = mb_ereg_replace('{lat_lon}', $coords_lat_lon, $tmpline1);
                    $tmpline1 = mb_ereg_replace('{desc}', "&nbsp;" . nl2br($wp_record['desc']) . "&nbsp;", $tmpline1);
                    $tmpline1 = mb_ereg_replace('{wpid}', $wp_record['wp_id'], $tmpline1);

                    if ($cache_type == 1 || $cache_type == 3 || $cache_type == 7) {
                        $tmpline1 = mb_ereg_replace('{stagehide_end}', '', $tmpline1);
                        $tmpline1 = mb_ereg_replace('{stagehide_start}', '', $tmpline1);
                        if ($wp_record['stage'] == 0) {
                            $tmpline1 = mb_ereg_replace('{number}', "", $tmpline1);
                        } else {
                            $tmpline1 = mb_ereg_replace('{number}', $wp_record['stage'], $tmpline1);
                        }
                    } else {
                        $tmpline1 = mb_ereg_replace('{stagehide_end}', '-->', $tmpline1);
                        $tmpline1 = mb_ereg_replace('{stagehide_start}', '<!--', $tmpline1);
                    }

                    $waypoints .= $tmpline1;
                }
            }
            if ($waypoints_visible != 0) {
                $waypoints .= '</table>';
                tpl_set_var('waypoints_content', $waypoints);
                tpl_set_var('waypoints_start', '');
                tpl_set_var('waypoints_end', '');
            } else {
                tpl_set_var('waypoints_content', '<br />');
                tpl_set_var('waypoints_start', '<!--');
                tpl_set_var('waypoints_end', '-->');
            }
        } else {
            tpl_set_var('waypoints_content', '<br />');
            tpl_set_var('waypoints_start', '<!--');
            tpl_set_var('waypoints_end', '-->');
        }


        // show mp3 files for PodCache
        if ($cache_record['mp3count'] > 0) {

            if (isset($_REQUEST['mp3_files']) && $_REQUEST['mp3_files'] == 'no')
                tpl_set_var('mp3_files', "");
            else
                tpl_set_var('mp3_files', viewcache_getmp3table($cache_id, $cache_record['mp3count']));

            tpl_set_var('hidemp3_start', '');
            tpl_set_var('hidemp3_end', '');
        }
        else {
            tpl_set_var('mp3_files', '<br />');
            tpl_set_var('hidemp3_start', '<!--');
            tpl_set_var('hidemp3_end', '-->');
        }


        // show pictures
        if ($cache_record['picturescount'] == 0 || (isset($_REQUEST['print']) && $_REQUEST['pictures'] == 'no')) {
            tpl_set_var('pictures', '<br />');
            tpl_set_var('hidepictures_start', '<!--');
            tpl_set_var('hidepictures_end', '-->');
        } else {
            if (isset($_REQUEST['spoiler_only']) && $_REQUEST['spoiler_only'] == 1)
                $spoiler_only = true;
            else
                $spoiler_only = false;
            if (isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'big')
                tpl_set_var('pictures', viewcache_getfullsizedpicturestable($cache_id, true, $spoiler_only, $cache_record['picturescount'], $disable_spoiler_view));
            else if (isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'small')
                tpl_set_var('pictures', viewcache_getpicturestable($cache_id, true, true, $spoiler_only, true, $cache_record['picturescount'], $disable_spoiler_view));
            else if (isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'no')
                tpl_set_var('pictures', "");
            else
                tpl_set_var('pictures', viewcache_getpicturestable($cache_id, true, true, false, false, $cache_record['picturescount'], $disable_spoiler_view));

            tpl_set_var('hidepictures_start', '');
            tpl_set_var('hidepictures_end', '');
        }


        // add OC Team comment
        if ($usr['admin'] && isset($_POST['rr_comment']) && $_POST['rr_comment'] != "" && $_SESSION['submitted'] != true) {
            $sender_name = $usr['username'];
            $comment = nl2br($_POST['rr_comment']);
            $date = date("d-m-Y H:i:s");
            $octeam_comment = '<b><span class="content-title-noshade txt-blue08">' . tr('date') . ': ' . $date . ', ' . tr('add_by') . ' ' . $sender_name . '</span></b><br/>' . $comment;
            $sql = "UPDATE cache_desc
                    SET rr_comment=CONCAT('" . sql_escape($octeam_comment) . "<br/><br/>', rr_comment),
                            last_modified = NOW()
                    WHERE cache_id='" . sql_escape(intval($cache_id)) . "'";
            @mysql_query($sql);
            $_SESSION['submitted'] = true;

            // send notify to owner cache and copy to OC Team
            $query1 = sql("SELECT `email` FROM `user` WHERE `user_id`='&1'", $cache_record['user_id']);
            $owner_email = sql_fetch_array($query1);
            $sender_email = $usr['email'];
            $email_content = read_file($stylepath . '/email/octeam_comment.email');
            $email_content = mb_ereg_replace('{server}', $absolute_server_URI, $email_content);
            $email_content = mb_ereg_replace('{cachename}', $cache_record['name'], $email_content);
            $email_content = mb_ereg_replace('{cacheid}', $cache_record['cache_id'], $email_content);
            $email_content = mb_ereg_replace('{octeam_comment}', $_POST['rr_comment'], $email_content);
            $email_content = mb_ereg_replace('{sender}', $sender_name, $email_content);
            $email_content = mb_ereg_replace('{ocTeamComment_01}', tr('ocTeamComment_01'), $email_content);
            $email_content = mb_ereg_replace('{ocTeamComment_02}', tr('ocTeamComment_02'), $email_content);
            $email_content = mb_ereg_replace('{ocTeamComment_03}', tr('ocTeamComment_03'), $email_content);
            $email_content = mb_ereg_replace('{ocTeamComment_04}', tr('ocTeamComment_04'), $email_content);
            $email_content = mb_ereg_replace('{ocTeamComment_05}', tr('ocTeamComment_05'), $email_content);
            $email_content = mb_ereg_replace('{octeamEmailsSignature}', $octeamEmailsSignature, $email_content);
            $email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
            $email_headers .= "From: $site_name <" . $octeam_email . ">\r\n";
            $email_headers .= "Reply-To: " . $octeam_email . "\r\n";
            //send email to owner
            $subject = tr('octeam_comment_subject');
            mb_send_mail($owner_email['email'], $subject . ": " . $cache_record['name'], $email_content, $email_headers);
            //send copy email to OC Team
            $subject_copy = tr('octeam_comment_subject_copy');
            mb_send_mail($sender_email, $subject . " " . $cache_record['name'], $subject_copy . " " . $sender_name . ":\n\n" . $email_content, $email_headers);
        }

        // remove OC Team comment
        if ($usr['admin'] && isset($_GET['removerrcomment']) && isset($_GET['cacheid'])) {
            $sql = "UPDATE cache_desc SET rr_comment='' WHERE cache_id='" . sql_escape(intval($cache_id)) . "'";
            @mysql_query($sql);
        }

        // show description
        $query = "SELECT `short_desc`, `desc`, `desc_html`, `hint`, `rr_comment` FROM `cache_desc` WHERE `cache_id`=:1 AND `language`=:2";
        $dbc->multiVariableQuery($query, $cache_id, $desclang);
        $desc_record = $dbc->dbResultFetch();
        $dbc->reset();

        $desc_html = $desc_record['desc_html'];

        $short_desc = $desc_record['short_desc'];

        // plain text, needs escaping
        $short_desc = htmlspecialchars($short_desc, ENT_COMPAT, 'UTF-8');
        //replace { and } to prevent replacing
        $short_desc = mb_ereg_replace('{', '&#0123;', $short_desc);
        $short_desc = mb_ereg_replace('}', '&#0125;', $short_desc);
        tpl_set_var('short_desc', $short_desc);

        $desc = $desc_record['desc'];
        if ($desc_html != 2) {
            // unsafe HTML, needs purifying
            $desc = htmlspecialchars_decode($desc);
            if (isset($_GET['use_purifier']) && $_GET['use_purifier'] == 0) {
                // skip using HTML Purifier - to let show original content
            } else {
                $desc = userInputFilter::purifyHtmlString($desc);
            }
        } else {
            // safe HTML - pass as is
        }
        //replace { and } to prevent replacing
        $desc = mb_ereg_replace('{', '&#0123;', $desc);
        $desc = mb_ereg_replace('}', '&#0125;', $desc);

        // TODO: UTF-8 compatible str_replace (with arrays)
        $desc = str_replace($smileytext, $smileyimage, $desc);

        $res = '';

        tpl_set_var('desc', $desc, true);

        if ($usr['admin']) {
            tpl_set_var('add_rr_comment', '[<a href="add_octeam_comment.php?cacheid=' . $cache_id . '">' . tr('add_rr_comment') . '</a>]');
            if ($desc_record['rr_comment'] == "")
                tpl_set_var('remove_rr_comment', '');
            else
                tpl_set_var('remove_rr_comment', '[<a href="viewcache.php?cacheid=' . $cache_id . '&amp;removerrcomment=1" onclick="return confirm(\'Czy usunąć wszystkie adnotacje?\');">' . tr('remove_rr_comment') . '</a>]');
        }
        else {
            tpl_set_var('add_rr_comment', '');
            tpl_set_var('remove_rr_comment', '');
        }

        if ($desc_record['rr_comment'] != "") {
            tpl_set_var('start_rr_comment', '', true);
            tpl_set_var('end_rr_comment', '', true);
            tpl_set_var('rr_comment', $desc_record['rr_comment'], true);
        } else {
            tpl_set_var('rr_comment_label', '', true);
            tpl_set_var('rr_comment', '', true);
            tpl_set_var('start_rr_comment', '<!--', true);
            tpl_set_var('end_rr_comment', '-->', true);
            $_POST['rr_comment'] = "";
        }
        // show hints
        //
            $hint = $desc_record['hint'];

        if ($hint == '') {  // no hint - blank all items
            tpl_set_var('cryptedhints', '');
            tpl_set_var('hints', '');
            tpl_set_var("decrypt_link_start", '');
            tpl_set_var("decrypt_link_end", '');
            tpl_set_var("decrypt_table_start", '');
            tpl_set_var("decrypt_table_end", '');
            tpl_set_var("decrypt_icon", '');
            tpl_set_var('$decrypt_table', '');

            tpl_set_var('hidehint_start', '<!--');
            tpl_set_var('hidehint_end', '-->');
        } elseif ($usr == false && $hide_coords) { // hind avaiable but user not logged on
            tpl_set_var('hints', '<span class="notice" style="width:500px;height:44px"  >' . tr('vc_hint_for_logged_only') . '</span> ');
            tpl_set_var('cryptedhints', '');
            tpl_set_var("decrypt_link_start", '');
            tpl_set_var("decrypt_link_end", '');
            tpl_set_var("decrypt_table_start", '');
            tpl_set_var("decrypt_table_end", '');
            tpl_set_var('hidehint_start', '');
            tpl_set_var('hidehint_end', '');
            tpl_set_var('decrypt_link', '');
            tpl_set_var('decrypt_icon', '');
            tpl_set_var('decrypt_table', '');
        } else { //hind avaiable, user logged - proceed with hint
            tpl_set_var('hidehint_start', '');
            tpl_set_var('hidehint_end', '');
            tpl_set_var('decrypt_icon', $decrypt_icon);
            tpl_set_var('decrypt_table', $decrypt_table);

            if ($no_crypt == 0) {
                $link = mb_ereg_replace('{cacheid_urlencode}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $decrypt_link);
                $link = mb_ereg_replace('{desclang}', htmlspecialchars(urlencode($desclang), ENT_COMPAT, 'UTF-8'), $link);

                tpl_set_var('decrypt_link', $link);
                tpl_set_var("decrypt_link_start", '');
                tpl_set_var("decrypt_link_end", '');
                tpl_set_var("decrypt_table_start", '');
                tpl_set_var("decrypt_table_end", '');

                //crypt the hint ROT13, but keep HTML-Tags and Entities
                $hint = str_rot13_html($hint);

                //TODO: mark all that isn't ROT13 coded
            } else {
                $cryptedhints = mb_ereg_replace('{decrypt_link}', '', $cryptedhints);
                tpl_set_var("decrypt_link_start", "<!--");
                tpl_set_var("decrypt_link_end", "-->");
                tpl_set_var("decrypt_table_start", "<!--");
                tpl_set_var("decrypt_table_end", "-->");
            }

            //replace { and } to prevent replacing
            $hint = mb_ereg_replace('{', '&#0123;', $hint);
            $hint = mb_ereg_replace('}', '&#0125;', $hint);

            tpl_set_var('hints', $hint);
        }

        //check number of pictures in logs
        $rspiclogs = sqlValue("SELECT COUNT(*) FROM `pictures`,`cache_logs` WHERE `pictures`.`object_id`=`cache_logs`.`id` AND `pictures`.`object_type`=1 AND `cache_logs`.`cache_id`= " . addslashes($cache_id), 0);

        if ($rspiclogs != 0) {
            tpl_set_var('gallery', $gallery_icon . '&nbsp;' . $rspiclogs . 'x&nbsp;' . mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $gallery_link));
        } else {
            tpl_set_var('gallery', '');
            ;
        }

        $show_deleted_logs = "`cache_logs`.`deleted` `deleted`,";
        $show_deleted_logs2 = "";
        if ($HideDeleted && !$usr['admin']) {
            $show_deleted_logs = "";
            $show_deleted_logs2 = " AND `cache_logs`.`deleted` = 0 ";
        }

        $thatquery = "SELECT `cache_logs`.`user_id` `userid`,
                                                " . $show_deleted_logs . "
                              `cache_logs`.`id` `logid`,
                              `cache_logs`.`date` `date`,
                              `cache_logs`.`type` `type`,
                              `cache_logs`.`text` `text`,
                              `cache_logs`.`text_html` `text_html`,
                              `cache_logs`.`picturescount` `picturescount`,
                              `cache_logs`.`mp3count` `mp3count`,
                              `cache_logs`.`last_modified` AS `last_modified`,
                              `cache_logs`.`last_deleted` AS `last_deleted`,
                              `cache_logs`.`edit_count` AS `edit_count`,
                              `cache_logs`.`date_created` AS `date_created`,
                              `user`.`username` `username`,
                              `user`.`admin` `admin`,
                              `user`.`hidden_count` AS    `ukryte`,
                              `user`.`founds_count` AS    `znalezione`,
                              `user`.`notfounds_count` AS `nieznalezione`,
                              `u2`.`username` AS `del_by_username`,
                              `u2`.`admin` AS `del_by_admin`,
                              `u3`.`username` AS `edit_by_username`,
                              `u3`.`admin` AS `edit_by_admin`,
                              `log_types`.`icon_small` `icon_small`,

                              IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`
                         FROM `cache_logs` INNER JOIN `log_types` ON `cache_logs`.`type`=`log_types`.`id`

                                           INNER JOIN `user` ON `cache_logs`.`user_id`=`user`.`user_id`
                                           LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
                                           LEFT JOIN `user` `u2` ON `cache_logs`.`del_by_user_id`=`u2`.`user_id`
                                           LEFT JOIN `user` `u3` ON `cache_logs`.`edit_by_user_id`=`u3`.`user_id`
                        WHERE `cache_logs`.`cache_id`=:v1
                                    " . $show_deleted_logs2 . "
                     ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`id` DESC
                        LIMIT :v2";
        $params['v1']['value'] = (integer) $cache_id;
        $params['v1']['data_type'] = 'integer';
        $params['v2']['value'] = (integer) $logs_to_display + 0;
        $params['v2']['data_type'] = 'integer';

        $dbc->paramQuery($thatquery, $params);
        unset($params); //clear to avoid overlaping on next paramQuery (if any))

        $logs = '';
        $thisdateformat = "%d %B %Y";
        $thisdatetimeformat = "%d %B %Y %H:%M";
        $edit_count_date_from = date_create('2005-01-01 00:00');
        $logs_count = $dbc->rowCount();
        $all_rec = $dbc->dbResultFetchAll();

        for ($i = 0; $i < $logs_count; $i++) {
            $record = $all_rec[$i];
            $record['text_listing'] = ucfirst(tr('logType' . $record['type'])); //add new attrib 'text_listing based on translation (instead of query as before)'
            $show_deleted = "";
            $processed_text = "";
            if (isset($record['deleted']) && $record['deleted']) {
                if ($usr['admin']) {
                    $show_deleted = "show_deleted";
                    $processed_text = $record['text'];
                } else {
                    // for 'Needs maintenance', 'Ready to search' and 'Temporarly unavailable' log types
                    if ($record['type'] == 5 || $record['type'] == 10 || $record['type'] == 11) {
                        // hide if user is not logged in
                        if (!isset($usr)) {
                            continue;
                        }
                        // hide if user is neither a geocache owner nor log author
                        if ($owner_id != $usr['userid'] && $record['userid'] != $usr['userid']) {
                            continue;
                        }
                    }

                    $record['icon_small'] = "log/16x16-trash.png"; //replace record icon with trash icon
                    $comm_replace = tr('vl_Record_of_type') . " [" . $record['text_listing'] . "] " . tr('vl_deleted');
                    $record['text_listing'] = tr('vl_Record_deleted'); ////replace type of record
                    if (isset($record['del_by_username']) && $record['del_by_username']) {
                        if ($record['del_by_admin'] == 1) { //if deleted by Admin
                            if (($record['del_by_username'] == $record['username']) && ($record['type'] != 12)) { // show username in case maker and deleter are same and comment is not Commnent by COG
                                $delByCOG = false;
                            } else {
                                $comm_replace.=" " . tr('vl_by_COG');
                                $delByCOG = true;
                            }
                        }
                        if ($delByCOG == false) {
                            $comm_replace.=" " . tr('vl_by_user') . " " . $record['del_by_username'];
                        }
                    }
                    if (isset($record['last_deleted'])) {
                        $comm_replace.=" " . tr('vl_on_date') . " " . fixPlMonth(htmlspecialchars(strftime($thisdateformat, strtotime($record['last_deleted'])), ENT_COMPAT, 'UTF-8'));
                    }
                    $comm_replace.=".";
                    $processed_text = $comm_replace;
                }
            } else {
                $processed_text = $record['text'];
            }

            // add edit footer if record has been modified
            $record_date_create = date_create($record['date_created']);

            if ($record['edit_count'] > 0) {
                //check if editted at all
                $edit_footer = "<div><small>" . tr('vl_Recently_modified_on') . " " . fixPlMonth(htmlspecialchars(strftime($thisdatetimeformat, strtotime($record['last_modified'])), ENT_COMPAT, 'UTF-8'));
                if (!$usr['admin'] && isset($record['edit_by_admin'])) {
                    if ($record['edit_by_username'] == $record['username']) {
                        $byCOG = false;
                    } else {
                        $edit_footer.=" " . tr('vl_by_COG');
                        $byCOG = true;
                    }
                }
                if (isset($byCOG) && $byCOG == false) {
                    $edit_footer.=" " . tr('vl_by_user') . " " . $record['edit_by_username'];
                }
                if ($record_date_create > $edit_count_date_from) { //check if record created after implementation date (to avoid false readings for record changed before) - actually nor in use
                    $edit_footer.=" - " . tr('vl_totally_modified') . " " . $record['edit_count'] . " ";
                    if ($record['edit_count'] > 1) {
                        $edit_footer.=tr('vl_count_plural');
                    } else {
                        $edit_footer.=tr('vl_count_singular');
                    }
                }

                $edit_footer.=".</small></div>";
            } else {
                $edit_footer = "";
            }

            $tmplog = read_file($stylepath . '/viewcache_log.tpl.php');
            $tmplog_username = htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8');
            $tmplog_date = fixPlMonth(htmlspecialchars(strftime("%d %B %Y", strtotime($record['date'])), ENT_COMPAT, 'UTF-8'));
            $dateTimeTmpArray = explode(' ', $record['date']);
            $tmplog = mb_ereg_replace('{time}', substr($dateTimeTmpArray[1], 0, -3), $tmplog);
            // replace smilies in log-text with images and add hyperlinks
            // display user activity (by Łza 2012)
            if ((date('m') == 4) and ( date('d') == 1)) {
                $tmplog_username_aktywnosc = ' (<img src="tpl/stdstyle/images/blue/thunder_ico.png" alt="user activity" width="13" height="13" border="0" title="' . tr('viewlog_aktywnosc') . '"/>' . rand(1, 9) . ') ';
            } else {
                $tmplog_username_aktywnosc = ' (<img src="tpl/stdstyle/images/blue/thunder_ico.png" alt="user activity" width="13" height="13" border="0" title="' . tr('viewlog_aktywnosc') . ' [' . $record['znalezione'] . '+' . $record['nieznalezione'] . '+' . $record['ukryte'] . ']"/>' . ($record['ukryte'] + $record['znalezione'] + $record['nieznalezione']) . ') ';
            }

            // ukrywanie autora komentarza COG przed zwykłym userem
            if ($record['type'] == 12 && !$usr['admin']) {
                $record['userid'] = '0';
                $tmplog_username_aktywnosc = '';
                $tmplog_username = tr('cog_user_name');
            }

            $tmplog = mb_ereg_replace('{username_aktywnosc}', $tmplog_username_aktywnosc, $tmplog);

            // mobile caches
            if (($cache_record['type'] == GeoCache::TYPE_MOVING) && ($record['type'] == 4)) {
                $dane_mobilniaka = sql_fetch_array(sql("SELECT `user_id`, `longitude`, `latitude`, `km` FROM `cache_moved` WHERE `log_id` = '&1'", $record['logid']));
                if ($dane_mobilniaka['latitude'] != 0) {
                    $tmplog_kordy_mobilnej = mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_latToDegreeStr($dane_mobilniaka['latitude']), ENT_COMPAT, 'UTF-8')) . '&nbsp;' . mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_lonToDegreeStr($dane_mobilniaka['longitude']), ENT_COMPAT, 'UTF-8'));
                    $tmplog = mb_ereg_replace('{kordy_mobilniaka}', $dane_mobilniaka['km'] . ' km [<img src="tpl/stdstyle/images/blue/szczalka_mobile.png" title="' . tr('viewlog_kordy') . '" />' . $tmplog_kordy_mobilnej . ']', $tmplog);
                } else {
                    $tmplog = mb_ereg_replace('{kordy_mobilniaka}', ' ', $tmplog);
                }
            } else {
                $tmplog = mb_ereg_replace('{kordy_mobilniaka}', ' ', $tmplog);
            }
            if ($record['text_html'] == 0) {
                $processed_text = htmlspecialchars($processed_text, ENT_COMPAT, 'UTF-8');
                $processed_text = help_addHyperlinkToURL($processed_text);
            } else {
                $processed_text = userInputFilter::purifyHtmlStringAndDecodeHtmlSpecialChars($processed_text);
            }
            $processed_text = str_replace($smileytext, $smileyimage, $processed_text);

            $tmplog_text = $processed_text . $edit_footer;
            $tmplog_text = str_replace($smileytext, $smileyimage, $tmplog_text);
            // pictures
            if (($record['picturescount'] > 0) && (($record['deleted'] == false) || ($usr['admin']))) { // show pictures if (any added) and ((not deleted) or (user is admin))
                $logpicturelines = '';
                $thatquery = "SELECT `url`, `title`, `user_id`, `uuid`, `spoiler` FROM `pictures` WHERE `object_id`= :v1 AND `object_type`=1";
                $params['v1']['value'] = (integer) $record['logid'];
                $params['v1']['data_type'] = 'integer';
                $dbc->paramQuery($thatquery, $params);
                unset($params);  //clear to avoid overlaping on next paramQuery (if any))
                $rspictures_count = $dbc->rowCount();
                $rspictures_all = $dbc->dbResultFetchAll();

                for ($j = 0; $j < $rspictures_count; $j++) {
                    $pic_record = $rspictures_all[$j];
                    if (!isset($showspoiler))
                        $showspoiler = '';
                    $thisline = $logpictureline;
                    if ($disable_spoiler_view && intval($pic_record['spoiler']) == 1) {  // if hide spoiler (due to user not logged in) option is on prevent viewing pic link and show alert
                        $thisline = mb_ereg_replace('{log_picture_onclick}', "alert('" . $spoiler_disable_msg . "'); return false;", $thisline);
                        $thisline = mb_ereg_replace('{link}', 'index.php', $thisline);
                        $thisline = mb_ereg_replace('{longdesc}', 'index.php', $thisline);
                    } else {
                        $thisline = mb_ereg_replace('{log_picture_onclick}', "enlarge(this)", $thisline);
                        $thisline = mb_ereg_replace('{link}', $pic_record['url'], $thisline);
                        $thisline = mb_ereg_replace('{longdesc}', str_replace("images/uploads", "upload", $pic_record['url']), $thisline);
                    }

                    $thisline = mb_ereg_replace('{imgsrc}', 'thumbs2.php?' . $showspoiler . 'uuid=' . urlencode($pic_record['uuid']), $thisline);
                    $thisline = mb_ereg_replace('{title}', htmlspecialchars($pic_record['title'], ENT_COMPAT, 'UTF-8'), $thisline);
                    if ($pic_record['user_id'] == $usr['userid'] || (isset($user['admin']) && $user['admin']))
                        $thisline = mb_ereg_replace('{functions}', mb_ereg_replace('{uuid}', $pic_record['uuid'], $remove_picture), $thisline);
                    else
                        $thisline = mb_ereg_replace('{functions}', '', $thisline);

                    $logpicturelines .= $thisline;
                }

                $logpicturelines = mb_ereg_replace('{lines}', $logpicturelines, $logpictures);
                $tmplog = mb_ereg_replace('{logpictures}', $logpicturelines, $tmplog);
            } else {
                $tmplog = mb_ereg_replace('{logpictures}', '', $tmplog);
            }

            if (!isset($record['deleted'])) {
                $record['deleted'] = 0;
            }
            if ($record['deleted'] != 1 && ((!isset($_REQUEST['print']) || $_REQUEST['print'] != 'y') && (($usr['userid'] == $record['userid']) || ($usr['userid'] == $cache_record['user_id']) || $usr['admin']))) {
                $tmpFunctions = $functions_start;

                if ($usr['userid'] == $record['userid'] || $usr['admin']) {
                    $tmpFunctions .= $edit_log . $functions_middle;
                }
                if ($record['type'] != 12 && ($usr['userid'] == $cache_record['user_id'] || $usr['admin'] == false)) {
                    $tmpFunctions .= $remove_log . $functions_middle;
                }
                elseif ($usr['admin']) {
                    $tmpFunctions .= $remove_log . $functions_middle;
                }

                if ($record['deleted'] != 1 && $usr['userid'] == $record['userid']){
                    $tmpFunctions = $tmpFunctions . $functions_middle . $upload_picture;
                }
                $tmpFunctions .= $functions_end;
                $tmpFunctions = mb_ereg_replace('{logid}', $record['logid'], $tmpFunctions);
                $tmplog = mb_ereg_replace('{logfunctions}', $tmpFunctions, $tmplog);
            } else {
                if ($usr['admin']) {
                    $tmpFunctions = $functions_start . $edit_log . $functions_middle . $revertLog . $functions_middle . $functions_end;
                    $tmpFunctions = mb_ereg_replace('{logid}', $record['logid'], $tmpFunctions);
                    $tmplog = mb_ereg_replace('{logfunctions}', $tmpFunctions, $tmplog);
                } else {
                    $tmplog = mb_ereg_replace('{logfunctions}', '', $tmplog);
                }
            }
            $tmplog = mb_ereg_replace('{show_deleted}', $show_deleted, $tmplog);
            $tmplog = mb_ereg_replace('{username}', $tmplog_username, $tmplog);
            $tmplog = mb_ereg_replace('{userid}', $record['userid'], $tmplog);
            $tmplog = mb_ereg_replace('{date}', $tmplog_date, $tmplog);
            $tmplog = mb_ereg_replace('{type}', $record['text_listing'], $tmplog);
            $tmplog = mb_ereg_replace('{logtext}', $tmplog_text, $tmplog);
            $tmplog = mb_ereg_replace('{logimage}', '<a href="viewlogs.php?logid=' . $record['logid'] . '">' . icon_log_type($record['icon_small'], $record['logid']) . '</a>', $tmplog);
            $tmplog = mb_ereg_replace('{log_id}', $record['logid'], $tmplog);

            if ($record['recommended'] == 1 && $record['type'] == 1){
                $tmplog = mb_ereg_replace('{ratingimage}', $rating_picture, $tmplog);
            } else {
                $tmplog = mb_ereg_replace('{ratingimage}', '', $tmplog);
            }
            $logs .= "$tmplog\n";
        }

        //replace { and } to prevent replacing
        $logs = mb_ereg_replace('{', '&#0123;', $logs);
        $logs = mb_ereg_replace('}', '&#0125;', $logs);

        tpl_set_var('logs', $logs, true);

        if (isset($_REQUEST['logbook']) && $_REQUEST['logbook'] == 'no') {
            tpl_set_var('hidelogbook_start', '<!--');
            tpl_set_var('hidelogbook_end', '-->');
        } else {
            tpl_set_var('hidelogbook_start', '');
            tpl_set_var('hidelogbook_end', '');
        }

        // action functions
        $edit_action = "";
        $log_action = "";
        $watch_action = "";
        $ignore_action = "";
        $print_action = "";


        //is this cache watched by this user?
        $rs = sql("SELECT * FROM `cache_watches` WHERE `cache_id`='&1' AND `user_id`='&2'", $cache_id, $usr['userid']);
        if (mysql_num_rows($rs) == 0) {
            $watch_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_watch);
            $is_watched = 'watchcache.php?cacheid=' . $cache_id . '&amp;target=viewcache.php%3Fcacheid=' . $cache_id;
            $watch_label = tr('watch');
        } else {
            $watch_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_watch_not);
            $is_watched = 'removewatch.php?cacheid=' . $cache_id . '&amp;target=viewcache.php%3Fcacheid=' . $cache_id;
            $watch_label = tr('watch_not');
        }
        //is this cache ignored by this user?
        $rs = sql("SELECT `cache_id` FROM `cache_ignore` WHERE `cache_id`='&1' AND `user_id`='&2'", $cache_id, $usr['userid']);
        if (mysql_num_rows($rs) == 0) {
            $ignore_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_ignore);
            $is_ignored = "addignore.php?cacheid=" . $cache_id . "&amp;target=viewcache.php%3Fcacheid%3D" . $cache_id;
            $ignore_label = tr('ignore');
            $ignore_icon = 'images/actions/ignore';
        } else {
            $ignore_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_ignore_not);
            $is_ignored = "removeignore.php?cacheid=" . $cache_id . "&amp;target=viewcache.php%3Fcacheid%3D" . $cache_id;
            $ignore_label = tr('ignore_not');
            $ignore_icon = 'images/actions/ignore';
        }
        mysql_free_result($rs);


        if ($usr !== false) {
            //user logged in => he can log
            $log_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_log);


            $printt = tr('print');
            $addToPrintList = tr('add_to_list');
            $removeFromPrintList = tr('remove_from_list');

            if (isset($_SESSION['print_list'])) {
                $sesPrintList = $_SESSION['print_list'];
            } else {
                $sesPrintList = array();
            }

            if (onTheList($sesPrintList, $cache_id) == -1) {
                $print_list = "viewcache.php?cacheid=$cache_id&amp;print_list=y";
                $print_list_label = $addToPrintList;
                $print_list_icon = 'images/actions/list-add';
            } else {
                $print_list = "viewcache.php?cacheid=$cache_id&amp;print_list=n";
                $print_list_label = $removeFromPrintList;
                $print_list_icon = 'images/actions/list-remove';
            }


            $cache_menu = array(
                'title' => tr('cache_menu'),
                'menustring' => tr('cache_menu'),
                'siteid' => 'cachelisting',
                'navicolor' => '#E8DDE4',
                'visible' => false,
                'filename' => 'viewcache.php',
                'submenu' => array(
                    array(
                        'title' => tr('new_log_entry'),
                        'menustring' => tr('new_log_entry'),
                        'visible' => true,
                        'filename' => 'log.php?cacheid=' . $cache_id,
                        'newwindow' => false,
                        'siteid' => 'new_log',
                        'icon' => 'images/actions/new-entry'
                    ),
                    array(
                        'title' => $watch_label,
                        'menustring' => $watch_label,
                        'visible' => true,
                        'filename' => $is_watched,
                        'newwindow' => false,
                        'siteid' => 'observe_cache',
                        'icon' => 'images/actions/watch'
                    ),
                    array(
                        'title' => tr('report_problem'),
                        'menustring' => tr('report_problem'),
                        'visible' => true,
                        'filename' => 'reportcache.php?cacheid=' . $cache_id,
                        'newwindow' => false,
                        'siteid' => 'report_cache',
                        'icon' => 'images/actions/report-problem'
                    ),
                    array(
                        'title' => tr('print'),
                        'menustring' => tr('print'),
                        'visible' => true,
                        'filename' => 'printcache.php?cacheid=' . $cache_id,
                        'newwindow' => false,
                        'siteid' => 'print_cache',
                        'icon' => 'images/actions/print'
                    ),
                    array(
                        'title' => $print_list_label,
                        'menustring' => $print_list_label,
                        'visible' => true,
                        'filename' => $print_list,
                        'newwindow' => false,
                        'siteid' => 'print_list_cache',
                        'icon' => $print_list_icon
                    ),
                    array(
                        'title' => $ignore_label,
                        'menustring' => $ignore_label,
                        'visible' => true,
                        'filename' => $is_ignored,
                        'newwindow' => false,
                        'siteid' => 'ignored_cache',
                        'icon' => $ignore_icon
                    ),
                    array(
                        'title' => tr('edit'),
                        'menustring' => tr('edit'),
                        'visible' => $show_edit,
                        'filename' => 'editcache.php?cacheid=' . $cache_id,
                        'newwindow' => false,
                        'siteid' => 'edit_cache',
                        'icon' => 'images/actions/edit'
                    )
                )
            );
            $report_action = "<li><a href=\"reportcache.php?cacheid=$cache_id\">" . tr('report_problem') . "</a></li>";
        } else {
            $cache_menu = array(
                'title' => tr('cache_menu'),
                'menustring' => tr('cache_menu'),
                'siteid' => 'cachelisting',
                'navicolor' => '#E8DDE4',
                'visible' => false,
                'filename' => 'viewcache.php',
                'submenu' => array(),
            );
        }

        tpl_set_var('log', $log_action);
        tpl_set_var('watch', $watch_action);
        tpl_set_var('report', isset($report_action) ? $report_action : '');
        tpl_set_var('ignore', $ignore_action);
        tpl_set_var('edit', $edit_action);
        tpl_set_var('print', $print_action);
        tpl_set_var('print_list', isset($print_list) ? $print_list : '');


        // check if password is required
        $has_password = isPasswordRequired($cache_id);

        // cache-attributes
        $rs = sql("SELECT `cache_attrib`.`text_long`,
                              `cache_attrib`.`icon_large`
                        FROM  `cache_attrib`, `caches_attributes`
                        WHERE `cache_attrib`.`id`=`caches_attributes`.`attrib_id`
                          AND `cache_attrib`.`language`='&1'
                          AND `caches_attributes`.`cache_id`='&2'
                     ORDER BY `cache_attrib`.`category`, `cache_attrib`.`id`", strtoupper($lang), $cache_id);
        $num_of_attributes = mysql_num_rows($rs);
        if ($num_of_attributes > 0 || $has_password) {
            $cache_attributes = '';
            if ($num_of_attributes > 0) {
                while ($record = sql_fetch_array($rs)) {
                    $cache_attributes .= '<img src="' . htmlspecialchars($record['icon_large'], ENT_COMPAT, 'UTF-8') . '" border="0" title="' . htmlspecialchars($record['text_long'], ENT_COMPAT, 'UTF-8') . '" alt="' . htmlspecialchars($record['text_long'], ENT_COMPAT, 'UTF-8') . '" />&nbsp;';
                }
            }

            if ($has_password)
                tpl_set_var('password_req', '<img src="' . $config['search-attr-icons']['password'][0] .'" title="' . tr('LogPassword') .'" alt="Potrzebne hasło"/>');
            else
                tpl_set_var('password_req', '');
            tpl_set_var('cache_attributes', $cache_attributes);
            tpl_set_var('cache_attributes_start', '');
            tpl_set_var('cache_attributes_end', '');
        }
        else {
            tpl_set_var('cache_attributes_start', '<!--');
            tpl_set_var('cache_attributes_end', '-->');
            tpl_set_var('cache_attributes', '');
            tpl_set_var('password_req', '');
        }
    } else {
        //display search page
        $tplname = 'viewcache_error';
    }
}

//make the template and send it out

$decrypt_script = '
<script type="text/javascript">
<!--
    var last="";var rot13map;function decryptinit(){var a=new Array();var s="abcdefghijklmnopqrstuvwxyz";for(i=0;i<s.length;i++)a[s.charAt(i)]=s.charAt((i+13)%26);for(i=0;i<s.length;i++)a[s.charAt(i).toUpperCase()]=s.charAt((i+13)%26).toUpperCase();return a}
function decrypt(elem){if(elem.nodeType != 3) return; var a = elem.data;if(!rot13map)rot13map=decryptinit();s="";for(i=0;i<a.length;i++){var b=a.charAt(i);s+=(b>=\'A\'&&b<=\'Z\'||b>=\'a\'&&b<=\'z\'?rot13map[b]:b)}elem.data = s}
-->
</script>';

$viewcache_header = '
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="rot13.js"></script>
    <script type="text/javascript">

    google.load("language", "1");



    function translateDesc()
        {
            var maxlen = 1100;
            var i=0;

            // tekst do przetlumaczenia
            var text = document.getElementById("description").innerHTML;

            // tablica wyrazow
            var splitted = text.split(" ");

            // liczba wyrazow
            var totallen = splitted.length;

            var toTranslate="";
            var container = document.getElementById("description");
            container.innerHTML = "";

            ' . (($enable_google_translation) ? "google.language.getBranding('branding');" : "") . '
            while( i < totallen )
            {
                var loo = splitted[i].length;
                while(( toTranslate.length + loo) < maxlen )
                {
                    toTranslate += " " + splitted[i];
                    i++;
                    if( i >= totallen )
                        break;
                }

                google.language.translate(toTranslate, "pl", "' . $lang . '", function(result)
                {
                //  var container = document.getElementById("description");

                    // poprawki
                    var toHTML = (result.translation).replace(/[eE]nglish/g, "Polish");
                    toHTML = toHTML.replace(/[iI]nbox/g, "Geocache");
                    toHTML = toHTML.replace(/[iI]nboxes/g, "Geocaches");
                    toHTML = toHTML.replace(/[mM]ailbox/g, "Geocache");
                    toHTML = toHTML.replace(/[mM]ailboxes/g, "Geocaches");
                    toHTML = toHTML.replace(/[dD]eutsch/g, "Polnisch");
                    toHTML = toHTML.replace(/[sS]houlder/g, "shovel");

                    container.innerHTML += toHTML;
                });
                toTranslate = "";
            }
    }

        function translateHint()
        {
            var maxlen = 1100;
            var i=0;

            // tekst do przetlumaczenia
            var container = document.getElementById("decrypt-hints");
            if( container == null )
                return "";
            ';


if (isset($_REQUEST['nocrypt']))
    $viewcache_header .= 'var text = container.innerHTML;';
else
    $viewcache_header .= 'var text = rot13(container.innerHTML);';
$viewcache_header .= '

            // tablica wyrazow
            var splitted = text.split(" ");

            // liczba wyrazow
            var totallen = splitted.length;

            var toTranslate="";
            container.innerHTML = "";
            while( i < totallen )
            {
                var loo = splitted[i].length;
                while(( toTranslate.length + loo) < maxlen )
                {
                    toTranslate += " " + splitted[i];
                    i++;
                    if( i >= totallen )
                        break;
                }

                google.language.translate(toTranslate, "pl", "' . $lang . '", function(result)
                {
                    //var container = document.getElementById("description");

                    // poprawki
                    var toHTML = (result.translation).replace(/[eE]nglish/g, "Polish");
                    toHTML = toHTML.replace(/[iI]nbox/g, "Geocache");
                    toHTML = toHTML.replace(/[iI]nboxes/g, "Geocaches");
                    toHTML = toHTML.replace(/[mM]ailbox/g, "Geocache");
                    toHTML = toHTML.replace(/[mM]ailboxes/g, "Geocaches");
                    toHTML = toHTML.replace(/[dD]eutsch/g, "Polnisch");
                    toHTML = toHTML.replace(/[sS]houlder/g, "shovel");
                    ';
if (isset($_REQUEST['nocrypt']))
    $viewcache_header .= 'container.innerHTML += toHTML;';
else
    $viewcache_header .= 'container.innerHTML += rot13(toHTML);';

$viewcache_header .= '
                });
                toTranslate = "";
            }
    }

            google.setOnLoadCallback(translateDesc);
            google.setOnLoadCallback(translateHint);
    </script>
';

//opencaching.pl

if (!$enable_google_translation) {
    tpl_set_var('branding', "");
    tpl_set_var('viewcache_header', $decrypt_script);
} else {
    tpl_set_var('branding', "<span class='txt-green07'>Automatic translation thanks to:</span>");
    tpl_set_var('viewcache_header', $viewcache_header . $decrypt_script);
}

tpl_set_var('bodyMod', '');

// pass to tmplate if user is logged (hide other geocaching portals links)
if ($usr == false || $usr['userFounds'] < 99)
    $userLogged = 'none';
else
    $userLogged = 'block';
tpl_set_var('userLogged', $userLogged);

// power trails
if ($powerTrailModuleSwitchOn && $cache_id != null) {
    $ptArr = powerTrailBase::checkForPowerTrailByCache($cache_id);
    if (count($ptArr) > 0) {
        $ptHtml = '<table width="99%">';
        foreach ($ptArr as $pt) {
            if ($pt['image'] == '')
                $ptImg = 'tpl/stdstyle/images/blue/powerTrailGenericLogo.png';
            else
                $ptImg = $pt['image'];
            $ptHtml .= '<tr><td width="51"><img border="0" width="50" src="' . $ptImg . '" /></td><td align="center"><span style="font-size: 13px;"><a href="powerTrail.php?ptAction=showSerie&ptrail=' . $pt['id'] . '">' . $pt['name'] . '</a> </td></tr>';
        }
        $ptHtml .= '</table>';
        $ptDisplay = 'block';
    } else {
        $ptHtml = '';
        $ptDisplay = 'none';
    }
} else {
    $ptHtml = '';
    $ptDisplay = 'none';
}

tpl_set_var('ptName', $ptHtml);
tpl_set_var('ptSectionDisplay', $ptDisplay);



tpl_BuildTemplate();

echo "<script type='text/javascript' src='lib/js/other.js'></script>";

if (isset($_REQUEST["posY"])) {
    echo "<script type='text/javascript'>";
    echo "window.scroll(0," . $_REQUEST["posY"] . ");";
    echo "</script>";
}
