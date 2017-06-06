<?php

/**
 * class autoloader
 */
require_once __DIR__ . '/ClassPathDictionary.php';

use Utils\Database\XDb;
use Utils\Database\OcDb;
use Utils\View\View;
use Utils\Uri\Uri;



if ((!isset($GLOBALS['no-session'])) || ($GLOBALS['no-session'] == false))
    session_start();

if ((!isset($GLOBALS['no-ob'])) || ($GLOBALS['no-ob'] == false))
    ob_start();

if ((!isset($GLOBALS['oc_waypoint'])) && isset($GLOBALS['ocWP']))
    $GLOBALS['oc_waypoint'] = $GLOBALS['ocWP'];



global $menu;

if (!isset($rootpath)){
    if(isset($GLOBALS['rootpath'])){
        $rootpath =  $GLOBALS['rootpath'];
    }else{
        $rootpath = "./";
    }
}

require_once($rootpath . 'lib/settings.inc.php');

// TODO: kojoty: it should be removed after config refactoring
// now if common.inc.php is not loaded in global context settings are not accessible
$GLOBALS['config'] = $config;
$GLOBALS['lang'] = $lang;
$GLOBALS['style'] = $style;

require_once($rootpath . 'lib/calculation.inc.php'); //TODO: remove it from global context...
require_once($rootpath . 'lib/common_tpl_funcs.php');
require_once($rootpath . 'lib/cookie.class.php');


//todo: former inside lib/consts.inc.php
//- should be moved outside of global context...
define('NOTIFY_NEW_CACHES', 1);


// TODO: this should be moved to config...
$datetimeformat = '%Y-%m-%d %H:%M:%S';
$dateformat = '%Y-%m-%d';

// yepp, we will use UTF-8
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
mb_language('uni');


//detecting errors
//TODO: this is never set and should be removed but it needs to touch hungreds of files...
$error = false;

//site in service?
if ($site_in_service == false) {
    header('Content-type: text/html; charset=utf-8');
    $page_content = file_get_contents($rootpath . 'html/outofservice.tpl.php');
    die($page_content);
}

//by default, use start template
if (!isset($tplname))
    $tplname = 'start';

// create global view variable (used in templates)
// TODO: it should be moved to context..
$view = new View();

//set up the style path
if (!isset($stylepath)){
    $stylepath = $rootpath . 'tpl/' . $style;
}

//set up the defaults for the main template
require_once($stylepath . '/varset.inc.php');

/*
 * Global $emailheaders from clicompatbase -
 * TODO: should be removed from here in future...
 */
$emailheaders = "Content-Type: text/plain; charset=utf-8\r\n";
$emailheaders .= "Content-Transfer-Encoding: 8bit\r\n";
$emailheaders .= 'From: "' . $emailaddr . '" <' . $emailaddr . '>';




$db = OcDb::instance();

// include the authentication functions
require($rootpath . 'lib/auth.inc.php');

//user authenification from cookie
auth_user();
if ($GLOBALS['usr'] == false) {
    //no user logged in
    $view->setVar('_isUserLogged', false);
    $view->setVar('_target',Uri::getCurrentUri());

} else { // user logged in

    // check for user_id in session
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = $usr['userid'];
    }

    if($GLOBALS['config']['checkRulesConfirmation']){
        // check for rules confirmation
        $rules_confirmed = $db->multiVariableQueryValue(
            "SELECT `rules_confirmed` FROM `user` WHERE `user_id` = :1", 0, $usr['userid']);

        if ($rules_confirmed == 0) {
            if (!isset($_SESSION['called_from_confirm']))
                header("Location: confirm.php");
            else
                unset($_SESSION['called_from_confirm']);
        }
    }

    if (!(isset($_SESSION['logout_cookie']))) {
        $_SESSION['logout_cookie'] = mt_rand(1000, 9999) . mt_rand(1000, 9999);
    }

    $view->setVar('_isUserLogged', true);
    $view->setVar('_username', $usr['username']);
    $view->setVar('_logoutCookie', $_SESSION['logout_cookie']);


    $usr['admin'] = $db->multiVariableQueryValue(
        'SELECT admin FROM user WHERE user_id=:1', 0, $usr['userid']);

}

tpl_set_var('site_name', $site_name);
tpl_set_var('contact_mail', $contact_mail);

// BSz: to make ease use of wikilinks
foreach($wikiLinks as $key => $value){
    tpl_set_var('wiki_link_'.$key, $value);
}



//load translations
require_once($rootpath . 'lib/loadlanguage.php');




/* help_ for usefull functions
 *
 */

/**
 * -- This script is moved here from clicompatbase - should be removed from here in the future --
 *
 * Create a "universal unique" replication "identifier"
 */
function create_uuid()
{
    $uuid = mb_strtoupper(md5(uniqid(rand(), true)));

    //split into XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX (type VARCHAR 36, case insensitiv)
    $uuid = mb_substr($uuid, 0, 8) . '-' . mb_substr($uuid, -24);
    $uuid = mb_substr($uuid, 0, 13) . '-' . mb_substr($uuid, -20);
    $uuid = mb_substr($uuid, 0, 18) . '-' . mb_substr($uuid, -16);
    $uuid = mb_substr($uuid, 0, 23) . '-' . mb_substr($uuid, -12);

    return $uuid;
}


// decimal longitude to string E/W hhh°mm.mmm
function help_lonToDegreeStr($lon, $type = 1)
{
    if ($lon < 0) {
        $retval = 'W ';
        $lon = -$lon;
    } else {
        $retval = 'E ';
    }


    if ($type == 1) {
        $retval = $retval . sprintf("%02d", floor($lon)) . '° ';
        $lon = $lon - floor($lon);
        $retval = $retval . sprintf("%06.3f", round($lon * 60, 3)) . '\'';
    } else if ($type == 0) {
        $retval .= sprintf("%.5f", $lon) . '° ';
    } else if ($type == 2) {
        $retval = $retval . sprintf("%02d", floor($lon)) . '° ';
        $lon = $lon - floor($lon);
        $lon *= 60;
        $retval = $retval . sprintf("%02d", floor($lon)) . '\' ';

        $lonmin = $lon - floor($lon);
        $retval = $retval . sprintf("%02.02f", $lonmin * 60) . '\'\'';
    }

    return $retval;
}

// decimal latitude to string N/S hh°mm.mmm
function help_latToDegreeStr($lat, $type = 1)
{
    if ($lat < 0) {
        $retval = 'S ';
        $lat = -$lat;
    } else {
        $retval = 'N ';
    }

    if ($type == 1) {
        $retval = $retval . sprintf("%02d", floor($lat)) . '° ';
        $lat = $lat - floor($lat);
        $retval = $retval . sprintf("%06.3f", round($lat * 60, 3)) . '\'';
    } else if ($type == 0) {
        $retval .= sprintf("%.5f", $lat) . '° ';
    } else if ($type == 2) {
        $retval = $retval . sprintf("%02d", floor($lat)) . '° ';
        $lat = $lat - floor($lat);
        $lat *= 60;
        $retval = $retval . sprintf("%02d", floor($lat)) . '\' ';

        $latmin = $lat - floor($lat);
        $retval = $retval . sprintf("%02.02f", $latmin * 60) . '\'\'';
    }

    return $retval;
}

/**
 * This function checks if given table contains column of given name
 * @param unknown $tableName
 * @param unknown $columnName
 * @return 1 on success 0 in failure
 */
function checkField($tableName, $columnName)
{
    $tableName = XDb::xEscape($tableName);
    $stmt = XDb::xSql("SHOW COLUMNS FROM $tableName" );
    while( $column = XDb::xFetchArray($stmt)){
        if( $column['Field'] == $columnName ){
            return 1;
        }
    }
    return 0;
}

function fixPlMonth($string)
{
    $string = str_ireplace('styczeń', 'stycznia', $string);
    $string = str_ireplace('luty', 'lutego', $string);
    $string = str_ireplace('marzec', 'marca', $string);
    $string = str_ireplace('kwiecień', 'kwietnia', $string);
    $string = str_ireplace('maj', 'maja', $string);
    $string = str_ireplace('czerwiec', 'czerwca', $string);
    $string = str_ireplace('lipiec', 'lipca', $string);
    $string = str_ireplace('sierpień', 'sierpnia', $string);
    $string = str_ireplace('wrzesień', 'września', $string);
    $string = str_ireplace('październik', 'października', $string);
    $string = str_ireplace('listopad', 'listopada', $string);
    $string = str_ireplace('grudzień', 'grudnia', $string);
    return $string;
}

/**
 * class witch common methods
 */
class common
{

    /**
     * add slashes to each element of $array.
     * @param array $array
     */
    public static function sanitize(&$array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                self::sanitize($value);
            } else {
                $array[$key] = addslashes(htmlspecialchars($value));
            }
        }
    }

    public static function buildCacheSizeSelector($sel_type, $sel_size)
    {
        $cache = cache::instance();
        $cacheSizes = $cache->getCacheSizes();

        $sizes = '<option value="-1" disabled selected="selected">' . tr('select_one') . '</option>';
        foreach ($cacheSizes as $size) {
            if ($sel_type == cache::TYPE_EVENT || $sel_type == cache::TYPE_VIRTUAL || $sel_type == cache::TYPE_WEBCAM) {
                if ($size['id'] == cache::SIZE_NOCONTAINER) {
                    $sizes .= '<option value="' . $size['id'] . '" selected="selected">' . tr($size['translation']) . '</option>';
                } else {
                    $sizes .= '<option value="' . $size['id'] . '">' . tr($size['translation']) . '</option>';
                }
            } elseif ($size['id'] != cache::SIZE_NOCONTAINER) {
                if ($size['id'] == $sel_size) {
                    $sizes .= '<option value="' . $size['id'] . '" selected="selected">' . tr($size['translation']) . '</option>';
                } else {
                    $sizes .= '<option value="' . $size['id'] . '">' . tr($size['translation']) . '</option>';
                }
            }
        }
        return $sizes;
    }

    /**
     * @param type $db
     */
    public static function getUserActiveCacheCountByType($db, $userId)
    {
        $query = 'SELECT type, count(*) as cacheCount FROM `caches` WHERE `user_id` = :1 AND STATUS !=3 GROUP by type';
        $s = $db->multiVariableQuery($query, $userId);
        $userCacheCountByType = $db->dbResultFetchAll($s);
        $cacheLimitByTypePerUser = array();
        foreach ($userCacheCountByType as $cacheCount) {
            $cacheLimitByTypePerUser[$cacheCount['type']] = $cacheCount['cacheCount'];
        }
        return $cacheLimitByTypePerUser;
    }

}
