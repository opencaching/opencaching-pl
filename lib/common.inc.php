<?php

/**
 * class autoloader
 */
require_once __DIR__ . '/ClassPathDictionary.php';

use Utils\Database\XDb;
use Utils\Database\OcDb;
use Utils\View\View;
use Utils\Uri\Uri;
use Utils\I18n\I18n;
use Utils\I18n\Languages;
use lib\Objects\ApplicationContainer;
use lib\Objects\User\User;

session_start();

//kojoty: do we need no-ob check ???
//if ((!isset($GLOBALS['no-ob'])) || ($GLOBALS['no-ob'] == false))
ob_start();

//kojoty: do we need it ???
//if ((!isset($GLOBALS['oc_waypoint'])) && isset($GLOBALS['ocWP']))
//    $GLOBALS['oc_waypoint'] = $GLOBALS['ocWP'];


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

require_once($rootpath . 'lib/common_tpl_funcs.php'); // template engine
require_once($rootpath . 'lib/cookie.class.php');     // class used to deal with cookies
require_once($rootpath . 'lib/language.inc.php');     // main translation funcs
require_once($rootpath . 'lib/login.class.php');        // authentication funcs

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

initTemplateSystem();

processAuthentication();

loadTranslation();



function processAuthentication(){

    $db = OcDb::instance();
    $view = tpl_getView();

    //user authenification from cookie
    global $usr, $login;

    $login->verify();
    if ($login->userid != 0) {   //user already logged in

        $user = User::fromUserIdFactory($login->userid);

        $applicationContainer = ApplicationContainer::Instance();
        $applicationContainer->setLoggedUser($user);

        // set obsolate global $usr[] array
        $usr['username'] = $user->getUserName();
        $usr['hiddenCacheCount'] = $user->getHiddenGeocachesCount();
        $usr['logNotesCount'] = $user->getLogNotesCount();
        $usr['userFounds'] = $user->getFoundGeocachesCount();
        $usr['notFoundsCount'] = $user->getNotFoundGeocachesCount();
        $usr['userid'] = $user->getUserId();
        $usr['email'] = $user->getEmail();
        $usr['country'] = $user->getCountry();
        $usr['latitude'] = $user->getHomeCoordinates()->getLatitude();
        $usr['longitude'] = $user->getHomeCoordinates()->getLongitude();
        $usr['admin'] = $user->isAdmin();


        // check for user_id in session
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = $user->getUserId();
        }

        if($GLOBALS['config']['checkRulesConfirmation']){

            if (! $user->areRulesConfirmed() ) {
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
        $view->setVar('_username', $user->getUserName());
        $view->setVar('_logoutCookie', $_SESSION['logout_cookie']);


    } else {
        $usr = false;

        $view->setVar('_isUserLogged', false);
        $view->setVar('_target',Uri::getCurrentUri());

    }
}

function initTemplateSystem(){

    global $rootpath, $style;

    // set up the style path
    // TODO: in fact we have only one style: stdstyle
    // so we can drop it in future
    if (!isset($GLOBALS['stylepath'])){
        $GLOBALS['stylepath'] = $rootpath . 'tpl/' . $style;
    }

    // create global view variable (used in templates)
    // TODO: it should be moved to context..
    $GLOBALS['view'] = new View();

    //by default, use start template
    if (!isset($GLOBALS['tplname'])){
        $GLOBALS['tplname'] = 'start';
    }


    // load vars from settings...
    //global $site_name, $contact_mail, $wikiLinks;

    tpl_set_var('site_name', $GLOBALS['site_name']);
    tpl_set_var('contact_mail', $GLOBALS['contact_mail']);

    foreach($GLOBALS['wikiLinks'] as $key => $value){
        tpl_set_var('wiki_link_'.$key, $value);
    }

    tpl_set_var('title', htmlspecialchars($GLOBALS['pagetitle'], ENT_COMPAT, 'UTF-8'));
    tpl_set_var('lang', $GLOBALS['lang']);
    tpl_set_var('style', $GLOBALS['style']);
    tpl_set_var('bodyMod', '');
    tpl_set_var('cachemap_header', '');
    tpl_set_var('htmlheaders', '');


    $GLOBALS['tpl_subtitle'] = '';
}

function loadTranslation(){

        global $lang, $cookie;
        if ($cookie->is_set('lang')) {
            $lang = $cookie->get('lang');
        }

        //language changed?
        if(isset($_REQUEST['lang'])){
            $lang = $_REQUEST['lang'];
        }

        //check if $lang is supported by site
        if(!I18n::isTranslationSupported($lang)){

            // requested language is not supported - display error...

            tpl_set_tplname('error/langNotSupported');
            header("HTTP/1.0 404 Not Found");

            $view->loadJQuery();
            $view->setVar("localCss",
                Uri::getLinkWithModificationTime('/tpl/stdstyle/error/error.css'));
            $view->setVar('requestedLang', $lang);
            $lang = 'en'; //English must be always supported

            $view->setVar('allLanguageFlags', I18n::getLanguagesFlagsData());
            load_language_file($lang);

            tpl_BuildTemplate();
            exit;
        }

        // load language settings
        load_language_file($lang);
        Languages::setLocale($lang);

}



/*
 * TODO: Remove all functions below from here!
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



