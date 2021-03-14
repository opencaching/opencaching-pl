<?php

require_once __DIR__ . '/ClassPathDictionary.php'; // class autoloader

use src\Utils\View\View;
use src\Models\User\UserAuthorization;
use src\Models\OcConfig\OcConfig;
use src\Utils\I18n\I18n;
use src\Models\ApplicationContainer;

ApplicationContainer::ocBaseInit();

// load legacy settings
// TODO: kojoty: it should be removed after config refactoring
// now if common.inc.php is not loaded in global context settings are not accessible
require_once(__DIR__.'/settingsGlue.inc.php');
$GLOBALS['config'] = $config;

// legacy View functions
require_once(__DIR__.'/common_tpl_funcs.php'); // template engine


if (php_sapi_name() != "cli") { // this is not neccesarry for command-line scripts...

    UserAuthorization::verify();

    initTemplateSystem();
    I18n::init();
}

function initTemplateSystem(){

    // create global view variable (used in templates)
    // TODO: it should be moved to context..
    if (!isset($GLOBALS['view'])) {
        $GLOBALS['view'] = new View();
    }

    //by default, use start template
    if (!isset($GLOBALS['tplname'])){
        $GLOBALS['tplname'] = 'start';
    }


    // load vars from settings...
    tpl_set_var('site_name', OcConfig::getSiteName());
    tpl_set_var('contact_mail', OcConfig::getEmailAddrOcTeam(true));


    // set wikiLinks used in translations
    foreach(OcConfig::getWikiLinks() as $key => $value){
        tpl_set_var('wiki_link_'.$key, $value);
    }

    tpl_set_var('title', htmlspecialchars(OcConfig::getSitePageTitle(), ENT_COMPAT, 'UTF-8'));
    tpl_set_var('bodyMod', '');
    tpl_set_var('cachemap_header', ''); //used only by myroutes...


    $GLOBALS['tpl_subtitle'] = '';
}
