<?php
namespace Utils\I18n;

use Utils\Uri\OcCookie;
use lib\Objects\OcConfig\OcConfig;
use Utils\Uri\Uri;
use Utils\Uri\SimpleRouter;

class CrowdinInContextMode
{
    const VAR_NAME = 'crowdinInContextMode';

    /**
     * This function is call at the begining of every script
     * to handle CrowdinInContext if necessary
     */
    public static function initHandler()
    {
        if (!isset($_REQUEST[self::VAR_NAME])) {
            // nothing new to do
            return;
        }

        // crowdinInContext mode toggle detected
        if (self::enabled()) {
            self::disable();
        } else {
            self::enable();
        }
    }

    public static function enabled()
    {
        return OcCookie::getOrDefault(self::VAR_NAME, false);
    }

    public static function enable()
    {
        OcCookie::set(self::VAR_NAME, true, true);

        $cleanUri = Uri::removeParam(self::VAR_NAME);
        SimpleRouter::redirect($cleanUri);
    }

    public static function disable()
    {
        OcCookie::delete(self::VAR_NAME, true);

        $cleanUri = Uri::removeParam(self::VAR_NAME);
        SimpleRouter::redirect($cleanUri);
    }

    public static function isSupportedInConfig()
    {
        return OcConfig::instance()->getI18Config()['crowdinInContextSupported'];
    }

    public static function getPseudoLang()
    {
        return OcConfig::instance()->getI18Config()['crowdinInContextPseudoLang'];
    }

}