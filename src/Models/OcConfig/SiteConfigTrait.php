<?php
namespace src\Models\OcConfig;

/**
 * This trait group access to email settings stored in /config/email.* conf. files
 * BEWARE OF FUNCTIONS NAME COLLISION BETWEEN CONFIG TRAITS!
 */
trait SiteConfigTrait {

    protected $siteConfig = null;

    /**
     * Returns pageTitle from config
     * @return string
     */
    public static function getSitePageTitle()
    {
        return self::getSiteVar('pageTitle');
    }

    /**
     * Returns siteName from config
     * @return string
     */
    public static function getSiteName()
    {
        return self::getSiteVar('siteName');
    }

    /**
     * Returns nodeId from config
     * Possible values: @see https://wiki.opencaching.eu/index.php?title=Node_IDs
     *
     * @return string
     */
    public static function getSiteNodeId()
    {
        return self::getSiteVar('ocNodeId');
    }

    /**
     * Returns nodeId from config
     * Possible values: @see https://wiki.opencaching.eu/index.php?title=Node_IDs
     *
     * @return string
     */
    public static function getSiteMainDomain()
    {
        return self::getSiteVar('mainDomain');
    }

    /**
     * Retruns the list of primaryCountries
     *
     * @return array
     */
    public static function getSitePrimaryCountriesList()
    {
        $primaryCountries = self::getSiteVar('primaryCountries');
        if (!is_array($primaryCountries) || empty($primaryCountries)) {
            // init primaryCountries for improper|empty config
            $primaryCountries = self::initPrimaryCountries();
        }
        return $primaryCountries;
    }

    /**
     * Retruns the list of dafaultCountries - used to isplay countries list for example in search.
     *
     * @return array
     */
    public static function getSiteDefaultCountriesList()
    {
        return self::getSiteVar('defaultCountriesList');
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
    private static function getSiteVar($varName)
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
