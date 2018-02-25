<?php

require_once __DIR__ . '/ClassPathDictionary.php'; // class autoloader

use Utils\View\View;
use Utils\Uri\Uri;
use Utils\I18n\I18n;
use Utils\I18n\Languages;
use lib\Objects\User\UserAuthorization;
use lib\Objects\OcConfig\OcConfig;
use Controllers\ErrorController;

// register error handlers
ErrorController::registerErrorHandlers();

session_start();

//TODO: kojoty: do we need no-ob check ???
//if ((!isset($GLOBALS['no-ob'])) || ($GLOBALS['no-ob'] == false))
ob_start();

//TODO: kojoty: do we need it ???
//if ((!isset($GLOBALS['oc_waypoint'])) && isset($GLOBALS['ocWP']))
//    $GLOBALS['oc_waypoint'] = $GLOBALS['ocWP'];


if (!isset($rootpath)){
    if(isset($GLOBALS['rootpath'])){
        $rootpath =  $GLOBALS['rootpath'];
    }else{
        $rootpath = "./";
    }
}

require_once($rootpath . 'lib/settingsGlue.inc.php');

// TODO: kojoty: it should be removed after config refactoring
// now if common.inc.php is not loaded in global context settings are not accessible
$GLOBALS['config'] = $config;
$GLOBALS['lang'] = $lang;
$GLOBALS['style'] = $style;
$GLOBALS['site_name'] = $site_name;
$GLOBALS['contact_mail'] = $contact_mail;
$GLOBALS['pagetitle'] = $pagetitle;

require_once($rootpath . 'lib/language.inc.php');     // main translation funcs
require_once($rootpath . 'lib/common_tpl_funcs.php'); // template engine
require_once($rootpath . 'lib/cookie.class.php');     // class used to deal with cookies

// yepp, we will use UTF-8
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
mb_language('uni');


if (php_sapi_name() != "cli") { // this is not neccesarry for command-line scripts...

    //detecting errors
    //TODO: this is never set and should be removed but it needs to touch hungreds of files...
    $error = false;

    //site in service?
    if ($site_in_service == false) {
        header('Content-type: text/html; charset=utf-8');
        $page_content = file_get_contents($rootpath . 'html/outofservice.tpl.php');
        die($page_content);
    }

    UserAuthorization::verify();

    initTemplateSystem();
    loadTranslation();

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
    tpl_set_var('site_name', $GLOBALS['site_name']);
    tpl_set_var('contact_mail', $GLOBALS['contact_mail']);


    // set wikiLinks used in translations
    foreach(OcConfig::getWikiLinks() as $key => $value){
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
            $view = tpl_getView();

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


//TODO: Remove all functions below from here!

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



