<?php

namespace src\Models\OcConfig;

/**
 * Loads configuration from i18n.*.php.
 *
 * @mixin OcConfig
 */
trait I18nConfigTrait
{
    protected $i18nConfig = null;

    public static function getI18nDefaultLang()
    {
        return self::getKeyFromI18nConfig('defaultLang');
    }

    public static function getI18nSupportedLangs()
    {
        return self::getKeyFromI18nConfig('supportedLanguages');
    }

    public static function isI18nCrowdinInContextSupported()
    {
        return self::getKeyFromI18nConfig('crowdinInContextSupported');
    }

    public static function getI18nCrowdinInContextPseudoLang()
    {
        return self::getKeyFromI18nConfig('crowdinInContextPseudoLang');
    }

    private function getI18nConfig(): array
    {
        if (! $this->i18nConfig) {
            $this->i18nConfig = self::getConfig('i18n');
        }

        return $this->i18nConfig;
    }

    /**
     * @return mixed
     */
    private static function getKeyFromI18nConfig(string $key)
    {
        $i18nConfig = self::instance()->getI18nConfig();

        return $i18nConfig[$key];
    }
}
