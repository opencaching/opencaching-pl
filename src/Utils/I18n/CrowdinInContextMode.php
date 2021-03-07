<?php

namespace src\Utils\I18n;

use src\Utils\Uri\OcCookie;
use src\Models\OcConfig\OcConfig;
use src\Utils\Uri\Uri;
use src\Utils\Uri\SimpleRouter;

class CrowdinInContextMode
{
    const VAR_NAME = 'crowdinInContextMode';

    const PREVIOUS_LANG_VAR = 'crowdinInContextBackToLang';

    /**
     * This method is called at the beginning of every request
     * to enable or disable CrowdinInContext if necessary.
     */
    public static function checkRequest(string $languageToUse): void
    {
        if (! isset($_REQUEST[self::VAR_NAME])) {
            return;
        }

        self::enabled() ? self::disable() : self::enable($languageToUse);
    }

    public static function enabled(): bool
    {
        return (bool) OcCookie::getOrDefault(self::VAR_NAME, false);
    }

    public static function enable(string $previousLang): void
    {
        OcCookie::set(self::VAR_NAME, true);
        OcCookie::set(self::PREVIOUS_LANG_VAR, $previousLang, true);

        SimpleRouter::redirect(
            Uri::removeParam(self::VAR_NAME)
        );

        exit;
    }

    public static function disable(): void
    {
        $prevLang = self::getPreviousLanguage();
        OcCookie::delete(self::PREVIOUS_LANG_VAR, true);
        OcCookie::delete(self::VAR_NAME, true);

        $uri = Uri::removeParam(self::VAR_NAME);
        $uri = Uri::setOrReplaceParamValue(I18n::URI_LANG_VAR, $prevLang, $uri);

        SimpleRouter::redirect($uri);

        exit;
    }

    public static function getPreviousLanguage(): string
    {
        $prevLang = OcCookie::get(self::PREVIOUS_LANG_VAR);

        return $prevLang ?? I18n::getDefaultLang();
    }

    public static function isSupportedInConfig(): bool
    {
        return OcConfig::isI18nCrowdinInContextSupported();
    }

    public static function getPseudoLang(): string
    {
        return OcConfig::getI18nCrowdinInContextPseudoLang();
    }
}
