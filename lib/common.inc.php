<?php

require_once __DIR__ . '/ClassPathDictionary.php'; // class autoloader

use Utils\Debug\ErrorHandler;
use Utils\View\View;
use lib\Objects\User\UserAuthorization;
use lib\Objects\OcConfig\OcConfig;
use Utils\I18n\I18n;

ErrorHandler::install();

session_start();

ob_start();

require_once(__DIR__.'/settingsGlue.inc.php');

// TODO: kojoty: it should be removed after config refactoring
// now if common.inc.php is not loaded in global context settings are not accessible
$GLOBALS['config'] = $config;
$GLOBALS['lang'] = $lang;
$GLOBALS['site_name'] = $site_name;
$GLOBALS['contact_mail'] = $contact_mail;

require_once(__DIR__.'/language.inc.php');     // main translation funcs
require_once(__DIR__.'/common_tpl_funcs.php'); // template engine

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
        $page_content = file_get_contents(__DIR__.'/../html/outofservice.tpl.php');
        die($page_content);
    }

    UserAuthorization::verify();

    initTemplateSystem();
    I18n::init();
}

function initTemplateSystem(){

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

    tpl_set_var('title', htmlspecialchars(OcConfig::instance()->getPageTitle(), ENT_COMPAT, 'UTF-8'));
    tpl_set_var('lang', $GLOBALS['lang']);
    tpl_set_var('bodyMod', '');
    tpl_set_var('cachemap_header', '');
    tpl_set_var('htmlheaders', '');


    $GLOBALS['tpl_subtitle'] = '';
}
