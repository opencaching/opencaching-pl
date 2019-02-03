<?php
namespace lib\Objects\OcConfig;

/**
 * This trait group access to email settings stored in /config/email.* conf. files
 */
trait SiteConfigTrait {

    protected $siteConfig = null;

    /**
     * Retruns the list of primaryCountries
     *
     * @return array
     */
    public static function getSitePrimaryCountriesList()
    {
        $primaryCountries = self::getVar('primaryCountries');
        if (!is_array($primaryCountries) || empty($primaryCountries)) {
            // init primaryCountries for improper|empty config
            $primaryCountries = self::initPrimaryCountries();
        }
        return $primaryCountries;
    }

    /**
     * Returns site properties
     *
     * @return array site properties
     */
    protected function getSiteConfig()
    {
        if (!$this->siteConfig) {
            $this->siteConfig = self::getConfig("site", "site");
        }
        return $this->siteConfig;
    }

    /**
     * Get Var from site.* files
     *
     * @param string $varName
     * @throws \Exception
     * @return string|array
     */
    private static function getVar($varName)
    {
        $siteConfig = self::instance()->getSiteConfig();
        if (!is_array($siteConfig)) {
            throw new \Exception("Invalid $varName setting: see /config/site.*");
        }
        return $siteConfig[$varName];
    }

    private static function initPrimaryCountries()
    {
        $instance = self::instance();
        $instance->siteConfig['primaryCountries'] = [strtoupper(substr(self::getSiteName(), -2))];
        return $instance->siteConfig['primaryCountries'];
    }

}
