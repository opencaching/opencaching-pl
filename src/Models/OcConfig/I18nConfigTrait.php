<?php

namespace src\Models\OcConfig;

use Exception;

/**
 * This trait group access to email settings stored in /config/email.* conf. files
 * BEWARE OF FUNCTIONS NAME COLLISION BETWEEN CONFIG TRAITS!
 */
trait I18nConfigTrait
{
    protected $i18nConfig = null;

    public static function getI18nDefaultLang()
    {
        return self::getI18nVar('defaultLang');
    }

    public static function getI18nSupportedLangs()
    {
        return self::getI18nVar('supportedLanguages');
    }

    public static function isI18nCrowdinInContextSupported()
    {
        return self::getI18nVar('crowdinInContextSupported');
    }

    public static function getI18nCrowdinInContextPseudoLang()
    {
        return self::getI18nVar('crowdinInContextPseudoLang');
    }

    /**
     * Read config from files
     *
     * @return array
     */
    private function getI18nConfig()
    {
        if ($this->i18nConfig == null) {
            $this->i18nConfig = self::getConfig('i18n');
        }

        return $this->i18nConfig;
    }

    /**
     * Get Var from email.* files
     *
     * @param string $varName
     * @return string
     * @throws Exception
     */
    private static function getI18nVar($varName)
    {
        $i18nConfig = self::instance()->getI18nConfig();

        if (! is_array($i18nConfig)) {
            throw new Exception("Invalid {$varName} setting: see /config/i18n.*");
        }

        return $i18nConfig[$varName];
    }
}
