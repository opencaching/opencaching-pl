<?php

use lib\Objects\GeoCache\GeoCache;
use lib\Objects\OcConfig\OcConfig;
use lib\Objects\GeoCache\Waypoint;
use Utils\Database\XDb;
use Utils\Database\OcDb;
use Utils\Email\EmailSender;
use Utils\Gis\Gis;



//prepare the templates and include all neccessary
if (!isset($rootpath)){
    global $rootpath;
}
require_once('./lib/common.inc.php');
require_once('lib/cache_icon.inc.php');
global $caches_list, $usr, $hide_coords, $cache_menu, $octeam_email, $site_name, $absolute_server_URI, $octeamEmailsSignature;
global $dynbasepath, $powerTrailModuleSwitchOn, $googlemap_key, $titled_cache_period_prefix;

global $config;

$applicationContainer = \lib\Objects\ApplicationContainer::Instance();

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
    if (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y'){
        $tplname = 'viewcache_print';
    }else{
        $tplname = 'viewcache';
    }

    // require_once($rootpath . 'lib/caches.inc.php');
    require_once($stylepath . '/lib/icons.inc.php');
    require($stylepath . '/viewcache.inc.php');
    require($stylepath . '/viewlogs.inc.php');
    require($stylepath . '/smilies.inc.php');

    $dbc = OcDb::instance();
    $cache_id = 0;
    if (isset($_REQUEST['cacheid'])) {
        $cache_id = (int) $_REQUEST['cacheid'];
    } else if (isset($_REQUEST['uuid'])) {
        $uuid = $_REQUEST['uuid'];
        $thatquery = "SELECT `cache_id` FROM `caches` WHERE uuid=:v1 LIMIT 1";
        $params['v1']['value'] = (string) $uuid;
        $params['v1']['data_type'] = 'string';
        $s = $dbc->paramQuery($thatquery, $params);
        if ($r = $dbc->dbResultFetchOneRowOnly($s)) {
            $cache_id = $r['cache_id'];
        }
    } else if (isset($_REQUEST['wp'])) {
        $wp = $_REQUEST['wp'];

        $query = 'SELECT `cache_id` FROM `caches` WHERE wp_';
        if (mb_strtoupper(mb_substr($wp, 0, 2)) == 'GC')
            $query .= 'gc';
        else if (mb_strtoupper(mb_substr($wp, 0, 2)) == 'NC')
            $query .= 'nc';
        else if (mb_strtoupper(mb_substr($wp, 0, 2)) == 'QC')
            $query .= 'qc';
        else
            $query .= 'oc';

        $query .= '=:1 LIMIT 1';
        $s = $dbc->multiVariableQuery($query, $wp);
        if ( $r = $dbc->dbResultFetchOneRowOnly($s) ) {
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
        tpl_set_var('uType', $usr['admin']);
    }
    $no_crypt = 0;
    if (isset($_REQUEST['nocrypt'])) {
        $no_crypt = $_REQUEST['nocrypt'];
    }

    if ($cache_id != 0) {
        //get cache record

        $geocache = new GeoCache(array('cacheId'=>$cache_id));

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
                            (NOW(), :1, :2, \'B\', \'view_cache\', :3, :4, :5)',
                        $cache_id, $user_id, $_SERVER['REMOTE_ADDR'],
                        ( isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '' ),
                        ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '' )
                );
                $access_log[$cache_id] = true;
                $_SESSION['CACHE_ACCESS_LOG_VC_' . $user_id] = $access_log;
            }
        }

        if ($geocache->getOwner()->getUserId() == $usr['userid']) {
            $show_edit = true;
            $show_ignore = false;
            $show_watch = false;
        } else {
            if ($usr['admin']) {
                $show_edit = true;
            } else {
                $show_edit = false;
            }
            $show_ignore = true;
            $show_watch = true;
        }

        $orig_coord_info_lon = ''; //use to determine whether icon shall be displayed
        $coords_correct = true;
        $mod_coords_modified = false;
        $cache_type = $geocache->getCacheType();
        $mod_coord_delete_mode = isset($_POST['resetCoords']);
        $cache_mod_lat = 0;
        $cache_mod_lon = 0;
        if ($usr != false && ($cache_type == GeoCache::TYPE_QUIZ || $cache_type == GeoCache::TYPE_OTHERTYPE || $cache_type == GeoCache::TYPE_MULTICACHE)) {

            $orig_cache_lon = $geocache->getCoordinates()->getLongitude();
            $orig_cache_lat = $geocache->getCoordinates()->getLatitude();
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
            $s = $dbc->paramQuery($thatquery, $params);
            unset($params); //clear to avoid overlaping on next paramQuery (if any))
            $cache_mod_coords = $dbc->dbResultFetchOneRowOnly($s);

            if ($cache_mod_coords != 0) {
                if ($mod_coord_delete_mode == false) {
                    $orig_coord_info_lon = htmlspecialchars(help_lonToDegreeStr($orig_cache_lon), ENT_COMPAT, 'UTF-8');
                    $orig_coord_info_lat = htmlspecialchars(help_latToDegreeStr($orig_cache_lat), ENT_COMPAT, 'UTF-8');
                    $geocache->getCoordinates()->setLongitude($cache_mod_coords['mod_lon']);
                    $geocache->getCoordinates()->setLatitude($cache_mod_coords['mod_lat']);
                }
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
                    unset($params);

                    $orig_coord_info_lon = htmlspecialchars(help_lonToDegreeStr($orig_cache_lon), ENT_COMPAT, 'UTF-8');
                    $orig_coord_info_lat = htmlspecialchars(help_latToDegreeStr($orig_cache_lat), ENT_COMPAT, 'UTF-8');
                    $geocache->getCoordinates()->setLongitude($cache_mod_lon);
                    $geocache->getCoordinates()->setLatitude($cache_mod_lat);
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
        $rs = $dbc->paramQuery($thatquery, $params);
        unset($params); //clear to avoid overlaping on next paramQuery (if any))
        if ($dbc->rowCount($rs) == 0) {
            $cache_id = 0;
        } else {
            $lm = $dbc->dbResultFetch($rs);
            $lastModified = new DateTime($lm['last_modified']);
            tpl_set_var('last_modified', $lastModified->format($applicationContainer->getOcConfig()->getDateFormat()));
        }
        unset($ls);
    }
    if (isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'y') {
        // add cache to print (do not duplicate items)
        if ( !isset($_SESSION['print_list']) || count($_SESSION['print_list']) == 0){
            $_SESSION['print_list'] = array();
        }

        if (onTheList($_SESSION['print_list'], $cache_id) == -1)
            array_push($_SESSION['print_list'], $cache_id);
    }
    if (isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'n') {
        // remove cache from print list
        while (onTheList($_SESSION['print_list'], $cache_id) != -1)
            unset($_SESSION['print_list'][onTheList($_SESSION['print_list'], $cache_id)]);
        $_SESSION['print_list'] = array_values($_SESSION['print_list']);
    }



    if ( /* check if coords should be displayed */
        $cache_id != 0 &&
        (
            (
                $geocache->getStatus() != GeoCache::STATUS_WAITAPPROVERS &&
                $geocache->getStatus() != GeoCache::STATUS_NOTYETAVAILABLE &&
                $geocache->getStatus() != GeoCache::STATUS_BLOCKED
            ) ||
            $usr['userid'] == $geocache->getOwner()->getUserId() ||
            $usr['admin'] ||
            ( $geocache->getStatus() == GeoCache::STATUS_WAITAPPROVERS &&
              !is_null($applicationContainer->getLoggedUser()) &&
              $applicationContainer->getLoggedUser()->getIsGuide() )
            )
        ) {

        //ok, cache is here, let's process
        $owner_id = $geocache->getOwner()->getUserId();
        tpl_set_var('owner_id', $owner_id);
        // check XY home if OK redirect to myn
        if (!is_null($applicationContainer->getLoggedUser())) {

            $ulat = $applicationContainer->getLoggedUser()->getHomeCoordinates()->getLatitude();
            $ulon = $applicationContainer->getLoggedUser()->getHomeCoordinates()->getLongitude();

            if (($ulon != NULL && $ulat != NULL) || ($ulon != 0 && $ulat != 0)) {

                $distancecache = sprintf("%.2f", Gis::distance($ulat, $ulon, $geocache->getCoordinates()->getLatitude(), $geocache->getCoordinates()->getLongitude()));
                tpl_set_var('distance_cache', '<img src="tpl/stdstyle/images/free_icons/car.png" class="icon16" alt="distance" title="" align="middle" />&nbsp;' . tr('distance_to_cache') . ': <b>' . $distancecache . ' km</b><br />');
            } else {
                tpl_set_var('distance_cache', '');
            }
        } else {
            tpl_set_var('distance_cache', '');
        }

        // check if there is geokret in this cache
        $thatquery = "SELECT gk_item.id, name, distancetravelled as distance FROM gk_item INNER JOIN gk_item_waypoint ON (gk_item.id = gk_item_waypoint.id) WHERE gk_item_waypoint.wp = :v1 AND stateid<>1 AND stateid<>4 AND stateid <>5 AND typeid<>2 AND missing=0";
        $params['v1']['value'] = (string) $geocache->getGeocacheWaypointId();
        $params['v1']['data_type'] = 'string';
        $s = $dbc->paramQuery($thatquery, $params);
        unset($params); //clear to avoid overlaping on next paramQuery (if any))
        $geokrety_all_count = $dbc->rowCount($s);
        if ($geokrety_all_count == 0) {
            // no geokrets in this cache
            tpl_set_var('geokrety_begin', '<!--');
            tpl_set_var('geokrety_end', '-->');
            tpl_set_var('geokrety_content', '');
        } else {
            // geokret is present in this cache
            $geokrety_content = '';
            $geokrety_all = $dbc->dbResultFetchAll($s);

            for ($i = 0; $i < $geokrety_all_count; $i++) {
                $geokret = $geokrety_all[$i];
                $geokrety_content .= "<img src=\"/images/geokret.gif\" alt=\"\"/>&nbsp;<a href='http://geokrety.org/konkret.php?id=" . $geokret['id'] . "'>" . $geokret['name'] . "</a> - " . tr('total_distance') . ": " . $geokret['distance'] . " km<br/>";
            }
            tpl_set_var('geokrety_begin', '');
            tpl_set_var('geokrety_end', '');
            tpl_set_var('geokrety_content', $geokrety_content);
        }

        if ($geocache->getRatingVotes() < 3) {
            // DO NOT show cache's score
            $score = "";
            $scorecolor = "";
            $font_size = "";
            tpl_set_var('score', tr('not_available'));
            tpl_set_var('scorecolor', "#000000");
        } else {
            // show cache's score
            $score = $geocache->getScore();
            $scorenum = score2ratingnum($score);
            $font_size = "2";
            if ($scorenum == 0){
                $scorecolor = "#DD0000";
            } elseif ($scorenum == 1) {
                $scorecolor = "#F06464";
            } elseif ($scorenum == 2) {
                $scorecolor = "#DD7700";
            } elseif ($scorenum == 3){
                $scorecolor = "#77CC00";
            } elseif ($scorenum == 4){
                $scorecolor = "#00DD00";
            }
            tpl_set_var('score', score2rating($score));
            tpl_set_var('scorecolor', $scorecolor);
        }

        // begin visit-counter
        // delete cache_visits older 1 day 60*60*24 = 86400
        $query = "DELETE FROM `cache_visits` WHERE `cache_id`=:1 AND `user_id_ip` != '0' AND NOW()-`last_visited` > 86400";
        $dbc->multiVariableQuery($query, $cache_id);

        // first insert record for visit counter if not in db
        $chkuserid = isset($usr['userid']) ? $usr['userid'] : $_SERVER["REMOTE_ADDR"];

        // note the visit of this user
        $query2 = "INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`, `last_visited`) VALUES (:1, :2, 1, NOW()) ON DUPLICATE KEY UPDATE `count`=`count`+1";
        $dbc->multiVariableQuery($query2,$cache_id, $chkuserid);

        if ($chkuserid != $owner_id) {
            // increment the counter for this cache
            $query3 = "INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`, `last_visited`) VALUES (:1, '0', 1, NOW()) ON DUPLICATE KEY UPDATE `count`=`count`+1, `last_visited`=NOW()";
            $dbc->multiVariableQuery($query3, $cache_id);
        }
        // end visit-counter
        // hide coordinates when user is not logged in
        if ($usr == true || !$hide_coords) {
            $coords = mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_latToDegreeStr($geocache->getCoordinates()->getLatitude()), ENT_COMPAT, 'UTF-8')) . '&nbsp;' . mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_lonToDegreeStr($geocache->getCoordinates()->getLongitude()), ENT_COMPAT, 'UTF-8'));
            $coords2 = mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_latToDegreeStr($geocache->getCoordinates()->getLatitude(), 0), ENT_COMPAT, 'UTF-8')) . '&nbsp;' . mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_lonToDegreeStr($geocache->getCoordinates()->getLongitude(), 0), ENT_COMPAT, 'UTF-8'));
            $coords3 = mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_latToDegreeStr($geocache->getCoordinates()->getLatitude(), 2), ENT_COMPAT, 'UTF-8')) . '&nbsp;' . mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_lonToDegreeStr($geocache->getCoordinates()->getLongitude(), 2), ENT_COMPAT, 'UTF-8'));
            $coords_other = "<a href=\"#\" onclick=\"javascript:window.open('coordinates.php?lat=" . $geocache->getCoordinates()->getLatitude() . "&amp;lon=" . $geocache->getCoordinates()->getLongitude() . "&amp;popup=y&amp;wp=" . htmlspecialchars($geocache->getWaypointId(), ENT_COMPAT, 'UTF-8') . "','Koordinatenumrechnung','width=240,height=334,resizable=yes,scrollbars=1')\">" . tr('coords_other') . "</a>";
        } else {
            $coords = tr('hidden_coords');
            $coords_other = "";
        }


        if ($geocache->getCacheType() == GeoCache::TYPE_EVENT) {
            $cache_stats = '';
        } else {
            if (($geocache->getFounds() + $geocache->getNotFounds() + $geocache->getNotesCount()) != 0) {
                $cache_stats = "<a class =\"links2\" href=\"javascript:void(0)\" onmouseover=\"Tip('" . tr('show_statictics_cache') . "', BALLOON, true, ABOVE, false, OFFSETX, -17, PADDING, 8, WIDTH, -240)\" onmouseout=\"UnTip()\" onclick=\"javascript:window.open('cache_stats.php?cacheid=" . $geocache->getCacheId() . "&amp;popup=y','Cache_Statistics','width=500,height=750,resizable=yes,scrollbars=1')\"><img src=\"tpl/stdstyle/images/blue/stat1.png\" alt=\"Statystyka skrzynki\" title=\"Statystyka skrzynki\" /></a>";
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
        tpl_set_var('typeLetter', typeToLetter($geocache->getCacheType()));

        // cache locations
        tpl_set_var('kraj', "");
        tpl_set_var('woj', "");
        tpl_set_var('dziubek1', "");
        tpl_set_var('miasto', "");
        tpl_set_var('dziubek2', "");

        $geocacheLocation = $geocache->getCacheLocation();
        if (substr(@tr($geocacheLocation['code1']), -5) == '-todo'){
            $countryTranslation = $geocacheLocation['adm1'];
        } else {
            $countryTranslation = tr($geocacheLocation['code1']);
        }
        // if (substr(@tr($cache_record['code3']), -5) == '-todo') $regionTranslation = $cache_record['adm3']; else $regionTranslation = tr($cache_record['code3']);
        $regionTranslation = $geocacheLocation['adm3'];

        if ($geocacheLocation != "") {
            tpl_set_var('kraj', $countryTranslation);
        } else {
            tpl_set_var('kraj', tr($geocacheLocation['country_short']));
        }
        if ($geocacheLocation['code3'] != "") {
            $woj = $geocacheLocation['adm3'];
            tpl_set_var('woj', $regionTranslation);
        } else {
            $woj = $geocacheLocation['adm2'];
            tpl_set_var('woj', $woj);
        }
        if ($woj == "") {
            tpl_set_var('woj', $geocacheLocation['adm4']);
        }
        if ($woj != "" || $geocacheLocation['adm3'] != "")
            tpl_set_var('dziubek1', ">");

        // NPA - nature protection areas
        $npac = "0";
        $npa_content = '';
        if(count($geocache->getNatureRegions()) > 0){
            $npa_content .="<table width=\"90%\" border=\"0\" style=\"border-collapse: collapse; font-weight: bold;font-size: 14px; line-height: 1.6em\"><tr>
            <td align=\"center\" valign=\"middle\"><b>" . tr('npa_info') . " <font color=\"green\"></font></b>:<br /></td><td align=\"center\" valign=\"middle\">&nbsp;</td></tr>";
            $npac = "1";
            foreach ($geocache->getNatureRegions() as $key => $npa) {
                $npa_content .= "<tr><td align=\"center\" valign=\"middle\"><font color=\"blue\"><a target=\"_blank\" href=\"http://" . $npa['npalink'] . "\">" . $npa['npaname'] . "</a></font><br />";
                $npa_content .="</td><td align=\"center\" valign=\"middle\"><img src=\"tpl/stdstyle/images/pnk/" . $npa['npalogo'] . "\"></td></tr>";
            }
            $npa_content .="</table>";
        }

        // Natura 200

        if (count($geocache->getNatura2000Sites()) > 0) {
            $npa_content .="<table width=\"90%\" border=\"0\" style=\"border-collapse: collapse; font-weight: bold;font-size: 14px; line-height: 1.6em\"><tr>
            <td width=90% align=\"center\" valign=\"middle\"><b>" . tr('npa_info') . " <font color=\"green\">NATURA 2000</font></b>:<br />";
            $npac = "1";
            foreach ($geocache->getNatura2000Sites() as $npa) {
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

        $icons = $geocache->dictionary->getCacheTypeIcons();

        //cache data
        list($iconname) = getCacheIcon($usr['userid'], $geocache->getCacheId(), $geocache->getStatus(), $geocache->getOwner()->getUserId(), $icons[$geocache->getCacheType()]['icon']);
        list($lat_dir, $lat_h, $lat_min) = help_latToArray($geocache->getCoordinates()->getLatitude());
        list($lon_dir, $lon_h, $lon_min) = help_lonToArray($geocache->getCoordinates()->getLongitude());

        $tpl_subtitle = htmlspecialchars($geocache->getCacheName(), ENT_COMPAT, 'UTF-8') . ' - ';
        $map_msg = mb_ereg_replace("{target}", urlencode("viewcache.php?cacheid=" . $cache_id), tr('map_msg'));

        tpl_set_var('googlemap_key', $googlemap_key);
        tpl_set_var('map_msg', $map_msg);
        tpl_set_var('typeLetter', typeToLetter($geocache->getCacheType()));
        tpl_set_var('cacheid_urlencode', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'));
        tpl_set_var('cachename', htmlspecialchars($geocache->getCacheName(), ENT_COMPAT, 'UTF-8'));

        if ( $geocache->isTitled() ){
            $ntitled_cache = $titled_cache_period_prefix.'_titled_cache';
            tpl_set_var('icon_titled', '<img src="tpl/stdstyle/images/free_icons/award_star_gold_1.png" class="icon16" alt="'.tr($ntitled_cache).'" title="'.tr($ntitled_cache).'"/>');
        } else {
            tpl_set_var('icon_titled', '');
        }

        // cache type Mobile add calculate distance
        if ($geocache->getCacheType() == GeoCache::TYPE_MOVING) {
            tpl_set_var('moved_icon', $moved_icon);
            tpl_set_var('dystans', $geocache->getDistance());
            tpl_set_var('moved', $geocache->getMoveCount());
            tpl_set_var('hidemobile_start', '');
            tpl_set_var('hidemobile_end', '');
        } else {
            tpl_set_var('hidemobile_start', '<!--');
            tpl_set_var('hidemobile_end', '-->');
        }


        tpl_set_var('coords', $coords);
        if ($usr || !$hide_coords) {
            if ($geocache->getCoordinates()->getLongitude() < 0) {
                $longNC = $geocache->getCoordinates()->getLongitude() * (-1);
                tpl_set_var('longitudeNC', $longNC);
            } else {
                tpl_set_var('longitudeNC', $geocache->getCoordinates()->getLongitude());
            }

            tpl_set_var('longitude', $geocache->getCoordinates()->getLongitude());
            tpl_set_var('latitude', $geocache->getCoordinates()->getLatitude());
            tpl_set_var('lon_h', $lon_h);
            tpl_set_var('lon_min', $lon_min);
            tpl_set_var('lonEW', $lon_dir);
            tpl_set_var('lat_h', $lat_h);
            tpl_set_var('lat_min', $lat_min);
            tpl_set_var('latNS', $lat_dir);
        }
        tpl_set_var('cacheid', $cache_id);
        $geocacheType = $geocache->dictionary->getCacheTypes();
        tpl_set_var('cachetype', htmlspecialchars(tr($geocacheType[$geocache->getCacheType()]['translation']), ENT_COMPAT, 'UTF-8'));
        $iconname = str_replace("mystery", "quiz", $iconname);
        tpl_set_var('icon_cache', htmlspecialchars("$stylepath/images/cache/$iconname", ENT_COMPAT, 'UTF-8'));
        tpl_set_var('cachesize', htmlspecialchars(tr($geocache->getSizeDesc()), ENT_COMPAT, 'UTF-8'));
        tpl_set_var('oc_waypoint', htmlspecialchars($geocache->getWaypointId(), ENT_COMPAT, 'UTF-8'));
        if ($geocache->getRecommendations() == 1){
            tpl_set_var('rating_stat', mb_ereg_replace('{ratings}', $geocache->getRecommendations(), $rating_stat_show_singular));
        } elseif ($geocache->getRecommendations() > 1) {
            tpl_set_var('rating_stat', mb_ereg_replace('{ratings}', $geocache->getRecommendations(), $rating_stat_show_plural));
        } else {
            tpl_set_var('rating_stat', '');
        }
        // cache_rating list of users
        // no geokrets in this cache
        tpl_set_var('list_of_rating_begin', '');
        tpl_set_var('list_of_rating_end', '');
        tpl_set_var('body_scripts', '');
        tpl_set_var('altitude', $geocache->getAltitude()->getAltitude());
        tpl_set_var('body_scripts', '<script type="text/javascript" src="lib/js/wz_tooltip.js"></script><script type="text/javascript" src="lib/js/tip_balloon.js"></script><script type="text/javascript" src="lib/js/tip_centerwindow.js"></script>');
        if ($geocache->getUsersRecomeded() === false) {
            tpl_set_var('list_of_rating_begin', '');
            tpl_set_var('list_of_rating_end', '');
        } else { // ToolTips Ballon
            $lists = ''; $i=0;
            foreach ($geocache->getUsersRecomeded() as $record) {
                $i++;
                $lists .= $record['username'];
                if (count($geocache->getUsersRecomeded())  == 1) {
                    $lists .= ' ';
                } else {
                    $lists .= ', ';
                }
            }
            $content_list = "<a class =\"links2\" href=\"javascript:void(0)\" onmouseover=\"Tip('<b>" . tr('recommended_by') . ": </b><br /><br />";
            $content_list .= $lists;
            $content_list .= "<br /><br/>', BALLOON, true, ABOVE, false, OFFSETY, 20, OFFSETX, -17, PADDING, 8, WIDTH, -240)\" onmouseout=\"UnTip()\">";

            tpl_set_var('list_of_rating_begin', $content_list);
            tpl_set_var('list_of_rating_end', '</a>');
        }

        if ((($geocache->getWayLenght() == null) && ($geocache->getSearchTime() == null)) ||
                (($geocache->getWayLenght() == 0) && ($geocache->getSearchTime() == 0))) {
            tpl_set_var('hidetime_start', '<!-- ');
            tpl_set_var('hidetime_end', ' -->');

            tpl_set_var('search_time', tr('not_available'));
            tpl_set_var('way_length', tr('not_available'));
        } else {
            tpl_set_var('hidetime_start', '');
            tpl_set_var('hidetime_end', '');

            if (($geocache->getSearchTime() == null) || ($geocache->getSearchTime() == 0)) {
                tpl_set_var('search_time', tr('not_available'));
            } else {
                $time_hours = floor($geocache->getSearchTime());
                $time_min = ($geocache->getSearchTime() - $time_hours) * 60;
                $time_min = sprintf('%02d', round($time_min, 1));
                tpl_set_var('search_time', $time_hours . ':' . $time_min . ' h');
            }

            if (($geocache->getWayLenght() == null) || ($geocache->getWayLenght() == 0)){
                tpl_set_var('way_length', tr('not_available'));
            } else {
                tpl_set_var('way_length', sprintf('%01.2f km', $geocache->getWayLenght()));
            }
        }

        tpl_set_var('country', htmlspecialchars($geocacheLocation['adm1']), ENT_COMPAT, 'UTF-8');
//        tpl_set_var('cache_log_pw', (($cache_record['logpw'] == NULL) || ($cache_record['logpw'] == '')) ? '' : $cache_log_pw);
        tpl_set_var('nocrypt', $no_crypt);
        $hidden_date = $geocache->getDatePlaced()->format($applicationContainer->getOcConfig()->getDateFormat());
        tpl_set_var('hidden_date', $hidden_date);

        $listed_on = array();
        if ($usr !== false && $usr['userFounds'] >= $config['otherSites_minfinds']) {
            $geocacheOtherWaypoints = $geocache->getOtherWaypointIds();
            if ($geocacheOtherWaypoints['ge'] != '' && $config['otherSites_gpsgames_org'] == 1){
                $listed_on[] = '<a href="http://geocaching.gpsgames.org/cgi-bin/ge.pl?wp=' . $geocacheOtherWaypoints['ge'] . '" target="_blank">GPSgames.org (' . $geocacheOtherWaypoints['ge'] . ')</a>';
            }
            if ($geocacheOtherWaypoints['tc'] != '' && $config['otherSites_terracaching_com'] == 1){
                $listed_on[] = '<a href="http://play.terracaching.com/Cache/' . $geocacheOtherWaypoints['tc'] . '" target="_blank">Terracaching.com (' . $geocacheOtherWaypoints['tc'] . ')</a>';
            }
            if ($geocacheOtherWaypoints['nc'] != '' && $config['otherSites_navicache_com'] == 1) {
                $wpnc = hexdec(mb_substr($geocacheOtherWaypoints['nc'], 1));
                $listed_on[] = '<a href="http://www.navicache.com/cgi-bin/db/displaycache2.pl?CacheID=' . $wpnc . '" target="_blank">Navicache.com (' . $wpnc . ')</a>';
            }
            if ($geocacheOtherWaypoints['gc'] != '' && $config['otherSites_geocaching_com'] == 1){
                $listed_on[] = '<a href="http://coord.info/' . $geocacheOtherWaypoints['gc'] . '" target="_blank">Geocaching.com (' . $geocacheOtherWaypoints['gc'] . ')</a> ';
            }
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
        $st = $geocache->dictionary->getCacheStatuses();
        if ($geocache->getStatus() != 1) {
            tpl_set_var('status', $error_prefix . htmlspecialchars(tr($st[$geocache->getStatus()]['translation']), ENT_COMPAT, 'UTF-8') . $error_suffix);
        } else {
            tpl_set_var('status', '<span style="color:green;font-weight:bold;">' . htmlspecialchars(tr($st[$geocache->getStatus()]['translation']), ENT_COMPAT, 'UTF-8') . '</span>');
        }

        tpl_set_var('date_created', $geocache->getDateCreated()->format($applicationContainer->getOcConfig()->getDateFormat()));

        tpl_set_var('difficulty_icon_diff', icon_difficulty("diff", $geocache->getDifficulty()));
        tpl_set_var('difficulty_text_diff', htmlspecialchars(sprintf($difficulty_text_diff, $geocache->getDifficulty() / 2), ENT_COMPAT, 'UTF-8'));
        tpl_set_var('difficulty_icon_terr', icon_difficulty("terr", $geocache->getTerrain()));
        tpl_set_var('difficulty_text_terr', htmlspecialchars(sprintf($difficulty_text_terr, $geocache->getTerrain() / 2), ENT_COMPAT, 'UTF-8'));

        tpl_set_var('founds', htmlspecialchars($geocache->getFounds(), ENT_COMPAT, 'UTF-8'));
        tpl_set_var('notfounds', htmlspecialchars($geocache->getNotFounds(), ENT_COMPAT, 'UTF-8'));
        tpl_set_var('notes', htmlspecialchars($geocache->getNotesCount(), ENT_COMPAT, 'UTF-8'));
        tpl_set_var('total_number_of_logs', htmlspecialchars($geocache->getFounds() + $geocache->getNotFounds() + $geocache->getNotesCount(), ENT_COMPAT, 'UTF-8'));

        // Personal cache notes
        //user logged in?
        if ($usr == true) {
            $s = $dbc->multiVariableQuery("SELECT `cache_notes`.`note_id` `note_id`,`cache_notes`.`date` `date`, `cache_notes`.`desc` `desc`, `cache_notes`.`desc_html` `desc_html` FROM `cache_notes` WHERE `cache_notes` .`user_id`=:1 AND `cache_notes`.`cache_id`=:2", $usr['userid'], $cache_id);
            $cacheNotesRowCount = $dbc->rowCount($s);

            if ($cacheNotesRowCount > 0) {
                $notes_record = $dbc->dbResultFetchOneRowOnly($s);
            }

            tpl_set_var('note_content', "");
            tpl_set_var('CacheNoteE', '-->');
            tpl_set_var('CacheNoteS', '<!--');
            tpl_set_var('EditCacheNoteE', '');
            tpl_set_var('EditCacheNoteS', '');

            //edit user note...
            if (isset($_POST['edit'])) {
                tpl_set_var('CacheNoteE', '-->');
                tpl_set_var('CacheNoteS', '<!--');
                tpl_set_var('EditCacheNoteE', '');
                tpl_set_var('EditCacheNoteS', '');

                if ($cacheNotesRowCount > 0) {
                    $note = $notes_record['desc'];
                    tpl_set_var('noteid', $notes_record['note_id']);
                } else {
                    $note = "";
                }
                tpl_set_var('note_content', $note);
            }

            //remove the user note from the cache
            if (isset($_POST['remove'])) {

                if ($cacheNotesRowCount > 0) {
                    $note_id = $notes_record['note_id'];
                    //remove
                    XDb::xSql(
                        "DELETE FROM `cache_notes` WHERE `note_id`= ? and user_id= ? ", $note_id, $usr['userid']);
                    //display cache-page
                    tpl_redirect('viewcache.php?cacheid=' . urlencode($cache_id));
                    exit;
                }
            }

            //save current value of the user note
            if (isset($_POST['save'])) {

                $cnote = $_POST['note_content'];
                $cn = strlen($cnote);

                if ($cacheNotesRowCount != 0) {
                    $note_id = $notes_record['note_id'];
                    $dbc->multiVariableQuery("UPDATE `cache_notes` SET `date`=NOW(),`desc`=:1, `desc_html`=:2 WHERE `note_id`=:3", $cnote, '0', $note_id);
                }

                if ($cacheNotesRowCount == 0 && $cn != 0) {
                    $dbc->multiVariableQuery("INSERT INTO `cache_notes` ( `note_id`, `cache_id`, `user_id`, `date`, `desc_html`, `desc`) VALUES ('', :1, :2, NOW(), :3, :4)", $cache_id, $usr['userid'], '0', $cnote);
                }

                //display cache-page
                tpl_redirect('viewcache.php?cacheid=' . urlencode($cache_id) . '#cache_note2');
                exit;
            }


            if ($cacheNotesRowCount != 0 && (!isset($_POST['edit']) || !isset($_REQUEST['edit']))) {
                tpl_set_var('CacheNoteE', '');
                tpl_set_var('CacheNoteS', '');
                tpl_set_var('EditCacheNoteE', '-->');
                tpl_set_var('EditCacheNoteS', '<!--');


                $note_desc = $notes_record['desc'];

                if ($notes_record['desc_html'] == '0'){
                    $note_desc = htmlspecialchars($note_desc, ENT_COMPAT, 'UTF-8');
                } else {
                    require_once($rootpath . 'lib/class.inputfilter.php');
                    $myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
                    $note_desc = $myFilter->process($note_desc);
                }
                tpl_set_var('notes_content', nl2br($note_desc));
            }
        } else {
            tpl_set_var('note_content', "");
            tpl_set_var('CacheNoteE', '-->');
            tpl_set_var('CacheNoteS', '<!--');
            tpl_set_var('EditCacheNoteE', '-->');
            tpl_set_var('EditCacheNoteS', '<!--');
        }
        // end personal cache note
        tpl_set_var('watcher', $geocache->getWatchingUsersCount());
        tpl_set_var('ignorer_count', $geocache->getIgnoringUsersCount());
        tpl_set_var('votes_count', $geocache->getratingVotes());
        tpl_set_var('note_icon', $note_icon);
        tpl_set_var('notes_icon', $notes_icon);
        tpl_set_var('vote_icon', $vote_icon);
        tpl_set_var('gk_icon', $gk_icon);
        tpl_set_var('watch_icon', $watch_icon);
        tpl_set_var('visit_icon', $visit_icon);
        tpl_set_var('score_icon', $score_icon);
        tpl_set_var('save_icon', $save_icon);
        tpl_set_var('search_icon', $search_icon);
        if ($geocache->getCacheType() == GeoCache::TYPE_EVENT) {
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
            $del_count = $dbc->multiVariableQueryValue('SELECT count(*) number FROM `cache_logs` WHERE `deleted`=1 and `cache_id`=:1', 0, $geocache->getCacheId());
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
        $s = $dbc->multiVariableQuery(
            "SELECT `count` FROM `cache_visits`
            WHERE `cache_id`=:1 AND `user_id_ip`='0'",
            $cache_id);

        if ($dbc->rowCount($s) == 0){
            tpl_set_var('visits', '0');
        } else {
            $watcher_record = $dbc->dbResultFetchOneRowOnly($s);
            tpl_set_var('visits', $watcher_record['count']);
        }
        $HideDeleted = true;
        if(isset($_SESSION['showdel']) && $_SESSION['showdel'] == 'y'){
           $HideDeleted = false;
        }

        //now include also those deleted due to displaying this type of record for all unless hide_deletions is on
        if (($usr['admin'] == 1) || ($HideDeleted == false)) {
            $query_hide_del = "";  //include deleted
        } else {
            $query_hide_del = "`deleted`=0 AND"; //exclude deleted
        }

        $number_logs = $dbc->multiVariableQueryValue(
            "SELECT count(*) number FROM `cache_logs` WHERE " . $query_hide_del . " `cache_id`=:1 "
            , 0, $geocache->getCacheId());

        tpl_set_var('logEnteriesCount', $number_logs);

        if ($number_logs > $logs_to_display) {
            tpl_set_var('viewlogs_last', mb_ereg_replace('{cacheid_urlencode}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $viewlogs_last));
            tpl_set_var('viewlogs', mb_ereg_replace('{cacheid_urlencode}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $viewlogs));

            $viewlogs_from = $dbc->multiVariableQueryValue(
                "SELECT id FROM cache_logs
                WHERE " . $query_hide_del . " cache_id=:1
                ORDER BY date DESC, id
                LIMIT ".XDb::xEscape($logs_to_display),
                -1, $cache_id );

            tpl_set_var('viewlogs_from', $viewlogs_from);
        } else {
            tpl_set_var('viewlogs_last', '');
            tpl_set_var('viewlogs', '');
            tpl_set_var('viewlogs_from', '');
        }

        tpl_set_var('cache_watcher', '');
        if ($geocache->getWatchingUsersCount() > 0) {
            tpl_set_var('cache_watcher', mb_ereg_replace('{watcher}', htmlspecialchars($geocache->getWatchingUsersCount(), ENT_COMPAT, 'UTF-8'), isset($cache_watchers) ? $cache_watchers : 10 ));
        }

        tpl_set_var('owner_name', htmlspecialchars($geocache->getOwner()->getUserName(), ENT_COMPAT, 'UTF-8'));
        tpl_set_var('userid_urlencode', htmlspecialchars(urlencode($geocache->getOwner()->getUserId()), ENT_COMPAT, 'UTF-8'));

        if ($geocache->getFounder() == null ||
            $geocache->getFounder()->getUserId() == $geocache->getOwner()->getUserId()) {

            tpl_set_var('creator_name_start', '<!--');
            tpl_set_var('creator_name_end', '-->');
        } else {
            tpl_set_var('creator_name_start', '');
            tpl_set_var('creator_name_end', '');
            tpl_set_var('creator_userid', $geocache->getFounder()->getUserId());
            tpl_set_var('creator_name', htmlspecialchars($geocache->getFounder()->getUserName(), ENT_COMPAT, 'UTF-8'));
        }

        //get description languages
        $desclangs = mb_split(',', $geocache->getDescLanguagesList());


        // use cache desc in lang of interface by default
        $desclang = mb_strtoupper($lang);

        // check if there is a desc in current lang
        if (array_search($desclang, $desclangs) === false) {
            $desclang = $desclangs[0];
            $enable_google_translation = true; //no desc in current lang - enable translation
        }

        // check if user requests other lang of cache desc...
        if ( isset($_REQUEST['desclang']) && array_search($_REQUEST['desclang'], $desclangs) ) {
            $desclang = $_REQUEST['desclang'];
            $enable_google_translation = false; //user wants this lang - disable translations
        }

        if ( ! OcConfig::instance()->isGoogleTranslationEnabled() ){
            $enable_google_translation = false;
            //TODO: Translation is not available - needs implementation...
        }

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

        // ===== openchecker ========================================================
        if ( $config['module']['openchecker']['enabled'] ) {
            $s = $dbc->multiVariableQuery(
                "SELECT `waypoints`.`wp_id`, `opensprawdzacz`.`proby`,
                        `opensprawdzacz`.`sukcesy`
                FROM   `waypoints`,  `opensprawdzacz`
                WHERE  `waypoints`.`cache_id` = :1
                    AND    `waypoints`.`type` = 3
                    AND    `waypoints`.`opensprawdzacz` = 1
                    AND    `waypoints`.`cache_id` = `opensprawdzacz`.cache_id",
                $geocache->getCacheId()
            );
            if ($dbc->rowCount($s) != 0 && $config['module']['openchecker']['enabled']) {
                $openchecker_data = $dbc->dbResultFetchOneRowOnly($s);
                tpl_set_var('attempts_counter', $openchecker_data['proby']);
                tpl_set_var('hits_counter', $openchecker_data['sukcesy']);
                tpl_set_var('openchecker_end', '');
                tpl_set_var('openchecker_start', '');
            } else {
                tpl_set_var('openchecker_end', '-->');
                tpl_set_var('openchecker_start', '<!--');
            }
        } else {
            tpl_set_var('openchecker_end', '-->');
            tpl_set_var('openchecker_start', '<!--');
        }
        // ===== openchecker end ====================================================
        // show additional waypoints
        $cache_type = $geocache->getCacheType();
        $waypoints_visible = 0;
        $s = $dbc->multiVariableQuery(
            "SELECT `wp_id`, `type`, `longitude`, `latitude`,  `desc`, `status`, `stage`,
                    `waypoint_type`.en wp_type, waypoint_type.icon wp_icon
            FROM `waypoints` INNER JOIN waypoint_type ON (waypoints.type = waypoint_type.id)
            WHERE `cache_id`=:1 ORDER BY `stage`,`wp_id`", $geocache->getCacheId());

        $wptCount = $dbc->rowCount($s);
        if ($wptCount != 0 && $geocache->getCacheType() != GeoCache::TYPE_MOVING) { // check status all waypoints
            foreach ($dbc->dbResultFetchAll($s) as $wp_check) {
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

            /*@var $waypoint lib\Objects\GeoCache\Waypoint */
            foreach ($geocache->getWaypoints() as $waypoint) {
                if ($waypoint->getStatus() != Waypoint::STATUS_HIDDEN) {
                    $wpTypeTranslation = tr('wayPointType'.$waypoint->getType());
                    $tmpline1 = $wpline;    // string in viewcache.inc.php

                    if ($waypoint->getStatus() == Waypoint::STATUS_VISIBLE) {
                        $coords_lat_lon = "<a class=\"links4\" href=\"#\" onclick=\"javascript:window.open('http://www.opencaching.pl/coordinates.php?lat=" . $waypoint->getCoordinates()->getLatitude() . "&amp;lon=" . $waypoint->getCoordinates()->getLongitude() . "&amp;popup=y&amp;wp=" . htmlspecialchars($geocache->getWaypointId(), ENT_COMPAT, 'UTF-8') . "','Koordinatenumrechnung','width=240,height=334,resizable=yes,scrollbars=1'); return event.returnValue=false\">" . mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_latToDegreeStr($waypoint->getCoordinates()->getLatitude()), ENT_COMPAT, 'UTF-8') . "<br/>" . htmlspecialchars(help_lonToDegreeStr($waypoint->getCoordinates()->getLongitude()), ENT_COMPAT, 'UTF-8')) . "</a>";
                    }
                    if ($waypoint->getStatus() == Waypoint::STATUS_VISIBLE_HIDDEN_COORDS) {
                        $coords_lat_lon = "N ?? ??????<br />E ?? ??????";
                    }
                    $tmpline1 = mb_ereg_replace('{wp_icon}', htmlspecialchars($waypoint->getIconName(), ENT_COMPAT, 'UTF-8'), $tmpline1);
                    $tmpline1 = mb_ereg_replace('{type}', htmlspecialchars($wpTypeTranslation, ENT_COMPAT, 'UTF-8'), $tmpline1);
                    $tmpline1 = mb_ereg_replace('{lat_lon}', $coords_lat_lon, $tmpline1);
                    $tmpline1 = mb_ereg_replace('{desc}', "&nbsp;" . nl2br($waypoint->getDescription()) . "&nbsp;", $tmpline1);
                    $tmpline1 = mb_ereg_replace('{wpid}', $waypoint->getId(), $tmpline1);

                    if ($cache_type == 1 || $cache_type == 3 || $cache_type == 7) {
                        $tmpline1 = mb_ereg_replace('{stagehide_end}', '', $tmpline1);
                        $tmpline1 = mb_ereg_replace('{stagehide_start}', '', $tmpline1);
                        if ($waypoint->getStage() == 0) {
                            $tmpline1 = mb_ereg_replace('{number}', "", $tmpline1);
                        } else {
                            $tmpline1 = mb_ereg_replace('{number}', $waypoint->getStage(), $tmpline1);
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
        if ($geocache->getMp3count() > 0) {
            if (isset($_REQUEST['mp3_files']) && $_REQUEST['mp3_files'] == 'no'){
                tpl_set_var('mp3_files', "");
            }else{
                tpl_set_var('mp3_files', viewcache_getmp3table($cache_id, $geocache->getMp3count()));
            }
            tpl_set_var('hidemp3_start', '');
            tpl_set_var('hidemp3_end', '');
        }
        else {
            tpl_set_var('mp3_files', '<br />');
            tpl_set_var('hidemp3_start', '<!--');
            tpl_set_var('hidemp3_end', '-->');
        }


        // show pictures
        if ($geocache->getPicturesCount() == 0 || (isset($_REQUEST['print']) && $_REQUEST['pictures'] == 'no')) {
            tpl_set_var('pictures', '<br />');
            tpl_set_var('hidepictures_start', '<!--');
            tpl_set_var('hidepictures_end', '-->');
        } else {
            if (isset($_REQUEST['spoiler_only']) && $_REQUEST['spoiler_only'] == 1){
                $spoiler_only = true;
            } else {
                $spoiler_only = false;
            }
            if (isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'big'){
                tpl_set_var('pictures', viewcache_getfullsizedpicturestable($cache_id, true, $spoiler_only, $cache_record['picturescount'], $disable_spoiler_view));
            } elseif (isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'small'){
                tpl_set_var('pictures', viewcache_getpicturestable($cache_id, true, true, $spoiler_only, true, $cache_record['picturescount'], $disable_spoiler_view));
            } elseif (isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'no'){
                tpl_set_var('pictures', "");
            } else {
                tpl_set_var('pictures', viewcache_getpicturestable($cache_id, true, true, false, false, $geocache->getPicturesCount(), $disable_spoiler_view));
            }
            tpl_set_var('hidepictures_start', '');
            tpl_set_var('hidepictures_end', '');
        }


        // add OC Team comment
        if ($usr['admin'] && isset($_POST['rr_comment']) && $_POST['rr_comment'] != "" && $_SESSION['submitted'] != true) {
            $sender_name = $usr['username'];
            $comment = nl2br($_POST['rr_comment']);
            $date = date("d-m-Y H:i:s");
            $octeam_comment = '<b><span class="content-title-noshade txt-blue08">' . tr('date') . ': ' . $date . ', ' . tr('add_by') . ' ' . $sender_name . '</span></b><br/>' . $comment;

            XDb::xSql(
                "UPDATE cache_desc
                SET rr_comment = CONCAT('" . XDb::xEscape($octeam_comment) . "<br/><br/>', rr_comment),
                    last_modified = NOW()
                WHERE cache_id= ? ", $cache_id);

            $_SESSION['submitted'] = true;

            EmailSender::sendNotifyOfOcTeamCommentToCache(__DIR__ . '/tpl/stdstyle/email/octeam_comment.email.html',
                $geocache, $usr['userid'], $usr['username'], nl2br($_POST['rr_comment']));
        }

        // remove OC Team comment
        if ($usr['admin'] && isset($_GET['removerrcomment']) && isset($_GET['cacheid'])) {
            XDb::xSql(
                "UPDATE cache_desc SET rr_comment='' WHERE cache_id= ? ",$cache_id);
        }

        // show description
        $query =
            "SELECT `short_desc`, `desc`, `desc_html`, `hint`, `rr_comment` FROM `cache_desc`
            WHERE `cache_id`=:1 AND `language`=:2 LIMIT 1";
        $s = $dbc->multiVariableQuery($query, $cache_id, $desclang);
        $desc_record = $dbc->dbResultFetchOneRowOnly($s);

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
                tpl_set_var('remove_rr_comment', '[<a href="viewcache.php?cacheid=' . $cache_id . '&amp;removerrcomment=1" onclick="return confirm(\'' . tr("confirm_remove_rr_comment") . '\');">' . tr('remove_rr_comment') . '</a>]');
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
            tpl_set_var('hintEncrypted', $hint);
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
        $rspiclogs = $dbc->multiVariableQueryValue("SELECT COUNT(*) FROM `pictures`,`cache_logs` WHERE `pictures`.`object_id`=`cache_logs`.`id` AND `pictures`.`object_type`=1 AND `cache_logs`.`cache_id`= :1", 0, $cache_id);

        if ($rspiclogs != 0) {
            tpl_set_var('gallery', $gallery_icon . '&nbsp;' . $rspiclogs . 'x&nbsp;' . mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $gallery_link));
        } else {
            tpl_set_var('gallery', '');
        }

        $includeDeletedLogs = false;
        if ($usr && !$HideDeleted && $usr['admin'] != 1) {
            $includeDeletedLogs = true;
        }
        if($usr['admin'] == 1){
            $includeDeletedLogs = true;
        }
        tpl_set_var('includeDeletedLogs', $includeDeletedLogs ? 1 : 0);


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
        $is_watched = "";
        $watch_label = "";
        $is_ignored = "";
        $ignore_label = "";
        $ignore_icon = "";

        //sql request only if we want show 'watch' button for user
        if($show_watch) {
            //is this cache watched by this user?
            $s = $dbc->multiVariableQuery(
                "SELECT * FROM `cache_watches` WHERE `cache_id`=:1 AND `user_id`=:2 LIMIT 1",
                $cache_id, $usr['userid']);

            if ($dbc->rowCount($s) == 0) {
                $watch_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_watch);
                $is_watched = 'watchcache.php?cacheid=' . $cache_id . '&amp;target=viewcache.php%3Fcacheid=' . $cache_id;
                $watch_label = tr('watch');
            } else {
                $watch_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_watch_not);
                $is_watched = 'removewatch.php?cacheid=' . $cache_id . '&amp;target=viewcache.php%3Fcacheid=' . $cache_id;
                $watch_label = tr('watch_not');
            }
        }

        //sql request only if we want show 'ignore' button for user
        if($show_ignore) {
            //is this cache ignored by this user?
            $s = $dbc->multiVariableQuery("SELECT `cache_id` FROM `cache_ignore` WHERE `cache_id`=:1 AND `user_id`=:2 LIMIT 1",
                $cache_id, $usr['userid']);

            if ($dbc->rowCount($s) == 0) {
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
        }

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
                        'visible' => $show_watch,
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
                        'visible' => $show_ignore,
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
        $has_password = $geocache->hasPassword();

        // cache-attributes
        $s = $dbc->multiVariableQuery(
            "SELECT `cache_attrib`.`text_long`, `cache_attrib`.`icon_large`
            FROM  `cache_attrib`, `caches_attributes`
            WHERE `cache_attrib`.`id`=`caches_attributes`.`attrib_id`
                AND `cache_attrib`.`language`=:1
                AND `caches_attributes`.`cache_id`=:2
            ORDER BY `cache_attrib`.`category`, `cache_attrib`.`id`", strtoupper($lang), $geocache->getCacheId());

        $num_of_attributes = $dbc->rowCount($s);
        if ($num_of_attributes > 0 || $has_password) {
            $cache_attributes = '';
            foreach ($dbc->dbResultFetchAll($s) as $record) {
                $cache_attributes .= '<img src="' . htmlspecialchars($record['icon_large'], ENT_COMPAT, 'UTF-8') . '" border="0" title="' . htmlspecialchars($record['text_long'], ENT_COMPAT, 'UTF-8') . '" alt="' . htmlspecialchars($record['text_long'], ENT_COMPAT, 'UTF-8') . '" />&nbsp;';
            }

            if ($has_password){
                tpl_set_var('password_req', '<img src="' . $config['search-attr-icons']['password'][0] .'" title="' . tr('LogPassword') .'" alt="Potrzebne hasło"/>');
            } else {
                tpl_set_var('password_req', '');
            }
            tpl_set_var('cache_attributes', $cache_attributes);
            tpl_set_var('cache_attributes_start', '');
            tpl_set_var('cache_attributes_end', '');
        } else {
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

tpl_set_var('viewcache_js', "tpl/stdstyle/js/viewcache." . filemtime($rootpath . 'tpl/stdstyle/js/viewcache.js') . ".js");

tpl_BuildTemplate();

if (isset($_REQUEST["posY"])) {
    echo "<script type='text/javascript'>";
    echo "window.scroll(0," . $_REQUEST["posY"] . ");";
    echo "</script>";
}
