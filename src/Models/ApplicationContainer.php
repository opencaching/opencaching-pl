<?php

namespace src\Models;

use src\Models\OcConfig\OcConfig;
use src\Models\User\User;
use src\Models\User\UserAuthorization;
use src\Utils\Debug\ErrorHandler;
use src\Utils\I18n\I18n;
use src\Utils\View\View;

/**
 * Core class used to run generic initialize the OC code and store the user authentication info
 */
final class ApplicationContainer
{
    private ?User $loggedUser = null;

    private bool $ocInitDone = false;

    public static function Instance(): ApplicationContainer
    {
        static $inst = null;

        if ($inst === null) {
            $inst = new ApplicationContainer();
        }

        return $inst;
    }

    public static function ocBaseInit(): void
    {
        $instance = self::Instance();

        if ($instance->ocInitDone) {
            // to be sure to call ocInit only once
            return;
        }

        // Install error handlers
        ErrorHandler::install();

        session_start();
        ob_start();

        // reset server encoding - to be sure we use UTF-8
        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');
        mb_language('uni');

        self::loadLegacyConfig();

        if (php_sapi_name() != 'cli') { // this is not necessary for command-line scripts...
            UserAuthorization::verify();
            I18n::init();

            // legacy View functions - this should be cleanup later
            require_once __DIR__ . '/../../lib/common_tpl_funcs.php'; // template engine
            self::initLegacyTemplateSystem();
        }
    }

    private static function initLegacyTemplateSystem()
    {
        // create global view variable (used in templates)
        // TODO: it should be moved to context..
        if (! isset($GLOBALS['view'])) {
            $GLOBALS['view'] = new View();
        }

        // by default, use start template
        if (! isset($GLOBALS['tplname'])) {
            $GLOBALS['tplname'] = 'start';
        }

        // load vars from settings...
        tpl_set_var('site_name', OcConfig::getSiteName());
        tpl_set_var('contact_mail', OcConfig::getEmailAddrOcTeam(true));

        // set wikiLinks used in translations
        foreach (OcConfig::getWikiLinks() as $key => $value) {
            tpl_set_var('wiki_link_' . $key, $value);
        }

        tpl_set_var('title', htmlspecialchars(OcConfig::getSitePageTitle(), ENT_COMPAT));
        tpl_set_var('bodyMod', '');
    }

    /**
     * Load legacy config files from /lib/settings*
     * There is several number of vars which must be loaded to the global scope
     * All these vars should be refactored to new config
     */
    private static function loadLegacyConfig()
    {
        require_once __DIR__ . '/../../lib/settingsGlue.inc.php';

        $GLOBALS['config'] = $config;
        $GLOBALS['absolute_server_URI'] = $absolute_server_URI;
        $GLOBALS['mp3dir'] = $mp3dir;
        $GLOBALS['mp3url'] = $mp3url;
        $GLOBALS['maxmp3size'] = $maxmp3size;
        $GLOBALS['mp3extensions'] = $mp3extensions;
        $GLOBALS['contactData'] = $contactData;
        $GLOBALS['dateFormat'] = $dateFormat;
        $GLOBALS['datetimeFormat'] = $datetimeFormat;
    }

    /**
     * Return authorized user object or null if user is not authorized
     */
    public static function GetAuthorizedUser(): ?User
    {
        return self::Instance()->loggedUser;
    }

    public static function SetAuthorizedUser(User $loggedUser = null): void
    {
        self::Instance()->loggedUser = $loggedUser;
    }
}
