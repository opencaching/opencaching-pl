<?php
namespace Utils\I18n;

use Utils\Uri\OcCookie;
use lib\Objects\OcConfig\OcConfig;
use Utils\Uri\Uri;
use Utils\Uri\SimpleRouter;

class CrowdinInContextMode
{
    const VAR_NAME = 'crowdinInContextMode';
    const PREVIOUS_LANG_VAR = 'crowdinInContextBackToLang';

    /**
     * This function is call at the begining of every script
     * to handle CrowdinInContext if necessary
     */
    public static function checkRequest($langToUse)
    {
        if (isset($_REQUEST[self::VAR_NAME])){
            if (CrowdinInContextMode::enabled()) {
                // CrowdinInContext mode is enabled now => this is request to disable it
                CrowdinInContextMode::disable();
            } else {
                // CrowdinInContext mode is disabled now => this is request to enable it
                CrowdinInContextMode::enable($langToUse);
            }
        }
    }

    public static function enabled()
    {
        return OcCookie::getOrDefault(self::VAR_NAME, false);
    }

    public static function enable($previousLang)
    {
        OcCookie::set(self::VAR_NAME, true);
        OcCookie::set(self::PREVIOUS_LANG_VAR, $previousLang, true);

        $cleanUri = Uri::removeParam(self::VAR_NAME);
        SimpleRouter::redirect($cleanUri);
        exit;
    }

    public static function disable()
    {
        $prevLang = self::getPreviousLanguage();
        OcCookie::delete(self::PREVIOUS_LANG_VAR, true);
        OcCookie::delete(self::VAR_NAME, true);

        $uri = Uri::removeParam(self::VAR_NAME);
        $uri = Uri::setOrReplaceParamValue(I18n::URI_LANG_VAR, $prevLang, $uri);

        SimpleRouter::redirect($uri);
        exit;
    }

    public static function getPreviousLanguage()
    {
        $prevLang = OcCookie::getOrDefault(self::PREVIOUS_LANG_VAR, null);
        if (is_null($prevLang)) {
            // previous language is not found in cookie
            return I18n::getDefaultLang();
        }
        return $prevLang;
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