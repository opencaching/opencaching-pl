<?php

namespace src\Models;

use src\Models\OcConfig\OcConfig;
use src\Models\User\User;
use src\Models\User\UserAuthorization;
use src\Utils\Debug\ErrorHandler;
use src\Utils\I18n\I18n;
use src\Utils\View\View;

final class ApplicationContainer
{
    /** @var User */
    private $loggedUser = null;

    private $ocInitDone = false;

    /**
     * @return ApplicationContainer
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new ApplicationContainer();
        }
        return $inst;
    }

    public static function ocBaseInit()
    {
        $instance = self::Instance();
        if($instance->ocInitDone) {
            // be sure to do ocInit only once
            return;
        }

        // Install error handlers
        ErrorHandler::install();

        session_start();
        ob_start();

        // reset server encondig - to be sure we use UTF-8
        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');
        mb_language('uni');

        self::loadLegacyConfig();

        if (php_sapi_name() != "cli") { // this is not neccesarry for command-line scripts...

            UserAuthorization::verify();

            // legacy View functions
            require_once(__DIR__.'/../../lib/common_tpl_funcs.php'); // template engine

            self::initLegacyTemplateSystem();
            I18n::init();
        }
    }

    private static function initLegacyTemplateSystem()
    {
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
    }

    /**
     * Load legacy config files from /lib/settings*
     * There is several number of vars which must be loaded to the global scope
     * All these vars shoudl be refactored to new config
     */
    private static function loadLegacyConfig()
    {
        require_once(__DIR__.'/../../lib/settingsGlue.inc.php');

        $GLOBALS['config'] = $config;
        //$GLOBALS['oc_waypoint'] = $oc_waypoint;
        $GLOBALS['hide_coords'] = $hide_coords;
        $GLOBALS['debug_page'] = $debug_page;
        $GLOBALS['absolute_server_URI'] = $absolute_server_URI;
        $GLOBALS['mp3dir'] = $mp3dir;
        $GLOBALS['mp3url'] = $mp3url;
        $GLOBALS['maxmp3size'] = $maxmp3size;
        $GLOBALS['mp3extensions'] = $mp3extensions;
        $GLOBALS['googlemap_key'] = $googlemap_key;

        $GLOBALS['powerTrailModuleSwitchOn'] = $powerTrailModuleSwitchOn;
        $GLOBALS['powerTrailMinimumCacheCount'] = $powerTrailMinimumCacheCount;
        $GLOBALS['powerTrailUserMinimumCacheFoundToSetNewPowerTrail'] = $powerTrailUserMinimumCacheFoundToSetNewPowerTrail;
        $GLOBALS['enable_cache_access_logs'] = $enable_cache_access_logs;
        $GLOBALS['short_sitename'] = $short_sitename;
        $GLOBALS['contactData'] = $contactData;
        $GLOBALS['dateFormat'] = $dateFormat;
        $GLOBALS['datetimeFormat'] = $datetimeFormat;
        $GLOBALS['titled_cache_nr_found'] = $titled_cache_nr_found;
        $GLOBALS['titled_cache_period_prefix'] = $titled_cache_period_prefix;
    }

    /**
     * Return authorized user object or null if user is not authorized
     *
     * @return \src\Models\User\User
     */
    public static function GetAuthorizedUser(): ?User
    {
        return self::Instance()->loggedUser;
    }

    public static function SetAuthorizedUser(User $loggedUser=null)
    {
        self::Instance()->loggedUser = $loggedUser;
    }
}
