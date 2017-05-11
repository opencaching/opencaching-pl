<?php

/**
 * class autoloader
 */
require_once __DIR__ . '/ClassPathDictionary.php';

use Utils\Database\XDb;
use Utils\Database\OcDb;
use Utils\View\View;



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

require_once($rootpath . 'lib/language.inc.php');
require_once($rootpath . 'lib/settings.inc.php');
require_once($rootpath . 'lib/calculation.inc.php');
require_once($rootpath . 'lib/consts.inc.php');
require_once($rootpath . 'lib/common_tpl_funcs.php');
require_once($rootpath . 'lib/cookie.class.php');



$lang_array = available_languages(); //array("pl", "en", "sv", "de", "cs", "fr", "es");
$datetimeformat = '%d %B %Y o godz. %H:%M:%S ';
$dateformat = '%d %B %Y';
$simpledateformat = '%d.%m.%Y';

// yepp, we will use UTF-8
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
mb_language('uni');



$STATUS = array("READY" => 1,
    "TEMP_UNAVAILABLE" => 2,
    "ARCHIVED" => 3,
    "HIDDEN_FOR_APPROVAL" => 4,
    "NOT_YET_AVAILABLE" => 5,
    "BLOCKED" => 6
);

$CACHESIZE = array("MICRO" => 2,
    "SMALL" => 3,
    "NORMAL" => 4,
    "LARGE" => 5,
    "VERY_LARGE" => 6,
    "NO_CONTAINER" => 7
);


//detecting errors
$error = false;

// set default CSS
tpl_set_var('css', 'main.css');

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


//restore cookievars[]
load_cookie_settings();

require_once($rootpath . 'lib/loadlanguage.php');


//check if $lang is supported by site
if(!in_array($lang, $lang_array)){
    die('Critical Error: The specified language does not exist!');
}


if(isset($_REQUEST['style'])){
    $style = $_REQUEST['style'];
}

//does the style exist?
if (!file_exists($rootpath . 'tpl/' . $style . '/')) {
    die('Critical Error: The specified style does not exist!');
}

//set up the style path
if (!isset($stylepath))
    $stylepath = $rootpath . 'tpl/' . $style;

//set up the language path
if (!isset($langpath))
    $langpath = $stylepath . '';

//load language specific strings
require_once($langpath . '/expressions.inc.php');

//set up the defaults for the main template
require_once($stylepath . '/varset.inc.php');

// thumbs-dir/url
if (!isset($thumbdir))
    $thumbdir = $picdir . '/thumbs';
if (!isset($thumburl))
    $thumburl = $picurl . '/thumbs';


/**
 * Global $emailheaders from clicompatbase - should be removed from here in future...
 */
// prepare EMail-From
$emailheaders = "Content-Type: text/plain; charset=utf-8\r\n";
$emailheaders .= "Content-Transfer-Encoding: 8bit\r\n";
$emailheaders .= 'From: "' . $emailaddr . '" <' . $emailaddr . '>';


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

$db = OcDb::instance();

/*
 * TODO: new Global error handling should be implemented...
 *
if ($dblink === false) {
    //error while connecting to the database
    $error = true;

    //set up error report
    tpl_set_var('error_msg', htmlspecialchars(mysql_error(), ENT_COMPAT, 'UTF-8'));
    tpl_set_var('tplname', $tplname);
    $tplname = 'error';
} else {
*/
    // include the authentication functions
    require($rootpath . 'lib/auth.inc.php');

    //user authenification from cookie
    auth_user();
    if ($GLOBALS['usr'] == false) {
        //no user logged in
        if (isset($_POST['target'])) {
            $target = $_POST['target'];
        } elseif (isset($_REQUEST['target'])) {
            $target = $_REQUEST['target'];
        } elseif (isset($_GET['target'])) {
            $target = $_GET['target'];
        } else {
            $target = '{target}';
        }
        $sLoggedOut = mb_ereg_replace('{target}', $target, $sLoggedOut);
        tpl_set_var('loginbox', $sLoggedOut);
    } else {

        // check for user_id in session
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = $usr['userid'];
        }
        //user logged in
        // check for rules confirmation
        if ((strtotime("2008-11-01 00:00:00") <= strtotime(date("Y-m-d h:i:s")))) {

            $rules_confirmed = $db->multiVariableQueryValue(
                "SELECT `rules_confirmed` FROM `user` WHERE `user_id` = :1",
                0, $usr['userid']);

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

        $sTmpString = mb_ereg_replace('{username}', $usr['username'], $sLoggedIn);
        $sTmpString = mb_ereg_replace('{logout_cookie}', $_SESSION['logout_cookie'], $sTmpString);
        tpl_set_var('loginbox', $sTmpString);
        unset($sTmpString);
    }
//} //TODO

// zeitmessung
require_once($rootpath . 'lib/bench.inc.php');
$bScriptExecution = new Cbench;
$bScriptExecution->start();


tpl_set_var('site_name', $site_name);
tpl_set_var('contact_mail', $contact_mail);

// BSz: to make ease use of wikilinks
foreach($wikiLinks as $key => $value){
    tpl_set_var('wiki_link_'.$key, $value);
}


// get the language from a given shortage
// on success return the name, otherwise false
function db_LanguageFromShort($langcode)
{
    global $lang;

    $lang = XDb::xEscape($lang);

    //select the right record
    $rs = XDb::xSql(
        "SELECT `short`, `$lang` FROM `languages` WHERE `short`= ? ", $langcode);

    if ( $record = XDb::xFetchArray($rs) ) {

        //return the language
        return $record[$lang];
    } else {

        //language not found
        return false;
    }
}

//get the stored settings and authentification data from the cookie
function load_cookie_settings()
{
    global $cookie, $lang, $style;

    //speach
    if ($cookie->is_set('lang')) {
        $lang = $cookie->get('lang');
    }

    //style
    if ($cookie->is_set('style')) {
        $style = $cookie->get('style');
    }
}

//store the cookie vars
function write_cookie_settings()
{
    global $cookie, $lang, $style;

    //language
    $cookie->set('lang', $lang);

    //style
    $cookie->set('style', $style);

    //send cookie
    $cookie->header();
}

//returns the cookie value, otherwise false
function get_cookie_setting($name)
{
    global $cookie;

    if ($cookie->is_set($name)) {
        return $cookie->get($name);
    } else {
        return false;
    }
}

//sets the cookie value
function set_cookie_setting($name, $value)
{
    global $cookie;
    $cookie->set($name, $value);
}






function writeLanguageFlags($languages)
{
    global $lang;
    $language_flags = "";
    foreach ($languages as $s_lang) {
        $_SERVER['QUERY_STRING'] = str_replace("&lang=" . $s_lang, "", $_SERVER['QUERY_STRING']);
        $_SERVER['QUERY_STRING'] = str_replace("lang=" . $s_lang, "", $_SERVER['QUERY_STRING']);
    }
    foreach ($languages as $s_lang) {
        if ($s_lang != $lang) {
            $language_flags .= '<li><a rel="nofollow" style="text-decoration:none;" href="' . ($_SERVER['PHP_SELF']);

            if (strlen($_SERVER['QUERY_STRING']) > 0)
                $language_flags .= '?' . htmlspecialchars($_SERVER['QUERY_STRING']) . '&amp;lang=' . $s_lang . '"><img class="img-navflag" src="images/' . $s_lang . '.jpg" alt="' . $s_lang . ' version" title="">&nbsp;';
            else
                $language_flags .= '?lang=' . $s_lang . '"><img class="img-navflag" src="images/' . $s_lang . '.png" alt="' . $s_lang . ' version" title="">&nbsp;';

            $language_flags .= '</a></li>';
        }
    }
    return $language_flags;
}




function http_write_no_cache()
{
    // HTTP/1.1
    header("Cache-Control: no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    // HTTP/1.0
    header("Pragma: no-cache");
    // Date in the past
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    // always modified
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
}





/* help_ for usefull functions
 *
 */

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

// decimal longitude to array(direction, h, min)
function help_lonToArray($lon)
{
    if ($lon < 0) {
        $dir = 'W';
        $lon = -$lon;
    } else {
        $dir = 'E';
    }

    $h = sprintf("%02d", floor($lon));
    $lon = $lon - floor($lon);
    $min = sprintf("%06.3f", round($lon * 60, 3));

    return array($dir, $h, $min);
}

// decimal longitude to array(direction, h_int, min_int, sec_int, min_float)
function help_lonToArray2($lon)
{
    list($dir, $lon_h_int, $lon_min_float) = help_lonToArray($lon);

    $lon_min_int = sprintf("%02d", floor($lon_min_float));

    $lon_min_frac = $lon_min_float - $lon_min_int;
    $lon_sec_float = sprintf("%02.2f", $lon_min_frac * 60);

    return array($dir, $lon_h_int, $lon_min_int, $lon_sec_float, $lon_min_float);
}

// decimal latitude to array(direction, h, min)
function help_latToArray($lat)
{
    if ($lat < 0) {
        $dir = 'S';
        $lat = -$lat;
    } else {
        $dir = 'N';
    }

    $h = sprintf("%02d", floor($lat));
    $lat = $lat - floor($lat);
    $min = sprintf("%06.3f", round($lat * 60, 3));

    return array($dir, $h, $min);
}

// decimal latitude to array(direction, h_int, min_int, sec_int, min_float)
function help_latToArray2($lat)
{
    list($dir, $lat_h_int, $lat_min_float) = help_latToArray($lat);

    $lat_min_int = sprintf("%02d", floor($lat_min_float));

    $lat_min_frac = $lat_min_float - $lat_min_int;
    $lat_sec_float = sprintf("%02.2f", $lat_min_frac * 60);

    return array($dir, $lat_h_int, $lat_min_int, $lat_sec_float, $lat_min_float);
}

// create qth locator
function help_latlongToQTH($lat, $lon)
{

    $lon += 180;
    $l[0] = floor($lon / 20);
    $lon -= 20 * $l[0];
    $l[2] = floor($lon / 2);
    $lon -= 2 * $l[2];
    $l[4] = floor($lon * 60 / 5);

    $lat += 90;
    $l[1] = floor($lat / 10);
    $lat -= 10 * $l[1];
    $l[3] = floor($lat);
    $lat -= $l[3];
    $l[5] = floor($lat * 120 / 5);

    return sprintf("%c%c%c%c%c%c", $l[0] + 65, $l[1] + 65, $l[2] + 48, $l[3] + 48, $l[4] + 65, $l[5] + 65);
}

//perform str_rot13 without renaming HTML-Tags
function str_rot13_html($str)
{
    $delimiter[0][0] = '&'; // start-char
    $delimiter[0][1] = ';'; // end-char
    $delimiter[1][0] = '<';
    $delimiter[1][1] = '>';
    $delimiter[2][0] = '[';
    $delimiter[2][1] = ']';

    $retval = '';

    while (mb_strlen($retval) < mb_strlen($str)) {
        $nNextStart = false;
        $sNextEndChar = '';
        foreach ($delimiter AS $del) {
            $nThisStart = mb_strpos($str, $del[0], mb_strlen($retval));

            if ($nThisStart !== false)
                if (($nNextStart > $nThisStart) || ($nNextStart === false)) {
                    $nNextStart = $nThisStart;
                    $sNextEndChar = $del[1];
                }
        }

        if ($nNextStart === false) {
            $retval .= str_rot13(mb_substr($str, mb_strlen($retval), mb_strlen($str) - mb_strlen($retval)));
        } else {
            // crypted part
            $retval .= str_rot13(mb_substr($str, mb_strlen($retval), $nNextStart - mb_strlen($retval)));

            // uncrypted part
            $nNextEnd = mb_strpos($str, $sNextEndChar, $nNextStart);

            if ($nNextEnd === false)
                $retval .= mb_substr($str, $nNextStart, mb_strlen($str) - mb_strlen($retval));
            else
                $retval .= mb_substr($str, $nNextStart, $nNextEnd - $nNextStart + 1);
        }
    }

    return $retval;
}

function help_addHyperlinkToURL($text)
{
    $texti = mb_strtolower($text);
    $retval = '';
    $curpos = 0;
    $starthttp = mb_strpos($texti, 'http://', $curpos);
    $endhttp = false;
    while (($starthttp !== false) || ($endhttp >= mb_strlen($text))) {
        $endhttp1 = mb_strpos($text, ' ', $starthttp);
        if ($endhttp1 === false)
            $endhttp1 = mb_strlen($text);
        $endhttp2 = mb_strpos($text, "\n", $starthttp);
        if ($endhttp2 === false)
            $endhttp2 = mb_strlen($text);
        $endhttp3 = mb_strpos($text, "\r", $starthttp);
        if ($endhttp3 === false)
            $endhttp3 = mb_strlen($text);
        $endhttp4 = mb_strpos($text, '<', $starthttp);
        if ($endhttp4 === false)
            $endhttp4 = mb_strlen($text);
        $endhttp5 = mb_strpos($text, '] ', $starthttp);
        if ($endhttp5 === false)
            $endhttp5 = mb_strlen($text);
        $endhttp6 = mb_strpos($text, ')', $starthttp);
        if ($endhttp6 === false)
            $endhttp6 = mb_strlen($text);
        $endhttp7 = mb_strpos($text, '. ', $starthttp);
        if ($endhttp7 === false)
            $endhttp7 = mb_strlen($text);

        $endhttp = min($endhttp1, $endhttp2, $endhttp3, $endhttp4, $endhttp5, $endhttp6, $endhttp7);

        $retval .= mb_substr($text, $curpos, $starthttp - $curpos);
        $url = mb_substr($text, $starthttp, $endhttp - $starthttp);
        $retval .= '<a href="' . $url . '" alt="" target="_blank">' . $url . '</a>';

        $curpos = $endhttp;
        if ($curpos >= mb_strlen($text))
            break;
        $starthttp = mb_strpos(mb_strtolower($text), 'http://', $curpos);
    }

    $retval .= mb_substr($text, $curpos);

    return $retval;
}



if (isset($usr['userid'])){
    $usr['admin'] = $db->multiVariableQueryValue('SELECT admin FROM user WHERE user_id=:1', 0, $usr['userid']);
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


function typeToLetter($type)
{
    switch ($type) {
        case "1":
        default:
            return "u";
        case "2":
            return "t";
        case "3":
            return "m";
        case "4":
            return "v";
        case "5":
            return "w";
        case "6":
            return "e";
        case "7":
            return "q";
        case "8":
            return "m";
    }
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
 * TODO: it seems that this function is used only by loogbook...
 */
function encrypt($text, $key)
{
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv));
}

//TODO: not used anywhere?
function decrypt($text, $key)
{
    if (!$text)
        return "";
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($text), MCRYPT_MODE_ECB, $iv), "\0");
}

/**
 * TODO: it seems that this function is used only by loogbook...
 */
function validate_msg($cookietext)
{
    if (!ereg("[0-9]+ This is a secret message", $cookietext))
        return false;

    $num = 0;
    sscanf($cookietext, "%d", $num);
    return $num;
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

/**
 * -- This function is moved from clicompatbase --
 * @param unknown $str
 */
function mb_trim($str)
{
    $bLoop = true;
    while ($bLoop == true) {
        $sPos = mb_substr($str, 0, 1);

        if ($sPos == ' ' || $sPos == "\r" || $sPos == "\n" || $sPos == "\t" || $sPos == "\x0B" || $sPos == "\0")
            $str = mb_substr($str, 1, mb_strlen($str) - 1);
            else
                $bLoop = false;
    }

    $bLoop = true;
    while ($bLoop == true) {
        $sPos = mb_substr($str, -1, 1);

        if ($sPos == ' ' || $sPos == "\r" || $sPos == "\n" || $sPos == "\t" || $sPos == "\x0B" || $sPos == "\0")
            $str = mb_substr($str, 0, mb_strlen($str) - 1);
            else
                $bLoop = false;
    }

    return $str;
}
