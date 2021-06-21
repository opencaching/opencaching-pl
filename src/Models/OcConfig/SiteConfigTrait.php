<?php

namespace src\Models\OcConfig;

/**
 * Loads configuration from site.*.php.
 *
 * @mixin OcConfig
 */
trait SiteConfigTrait
{
    protected $siteConfig = null;

    public static function getSitePageTitle(): string
    {
        return self::getKeyFromSiteConfig('pageTitle');
    }

    public static function getSiteName(): string
    {
        return self::getKeyFromSiteConfig('siteName');
    }

    public static function getSiteShortName(): string
    {
        return self::getKeyFromSiteConfig('shortName');
    }

    /**
     * @see https://wiki.opencaching.eu/index.php?title=Node_IDs
     */
    public static function getSiteNodeId(): int
    {
        return self::getKeyFromSiteConfig('ocNodeId');
    }

    public static function getSiteMainDomain(): string
    {
        return self::getKeyFromSiteConfig('mainDomain');
    }

    /**
     * Returns the list of primaryCountries.
     *
     * @return string[]
     */
    public static function getSitePrimaryCountriesList(): array
    {
        $primaryCountries = self::getKeyFromSiteConfig('primaryCountries');

        return empty($primaryCountries)
            ? self::initPrimaryCountries()
            : $primaryCountries;
    }

    /**
     * List of default countries to be presented on countries list (for example
     * in search). List will be presented in the same order as below.
     *
     * @return string[]
     */
    public static function getSiteDefaultCountriesList(): array
    {
        return self::getKeyFromSiteConfig('defaultCountriesList');
    }

    /**
     * Returns TRUE is cacheAccessLog is enabled for site
     */
    public static function isSiteCacheAccessLogEnabled(): bool
    {
        return self::getKeyFromSiteConfig('cacheAccessLogEnabled');
    }

    /**
     * Enable debug mode (USE ONLY IN DEV/TEST ENV!)
     */
    public static function debugModeEnabled(): bool
    {
        return self::getKeyFromSiteConfig('debugModeEnabled');
    }

    /**
     * Returns Google Analytics site tag if used by the node
     */
    public static function getSiteGoogleAnalyticsTag(): string
    {
        return self::getKeyFromSiteConfig('googleAnalyticsTag');
    }


    /**
     * Returns icon's path relative to the public directory.
     */
    public static function getSiteMainViewIcon(string $iconName): string
    {
        $icons = self::getKeyFromSiteConfig('mainViewIcons');

        return $icons[$iconName] ?? "Unknown-icon-{$iconName}";
    }

    protected function getSiteConfig(): array
    {
        if (! $this->siteConfig) {
            $this->siteConfig = self::getConfig('site', 'site');
        }

        return $this->siteConfig;
    }

    /**
     * @return mixed
     */
    private static function getKeyFromSiteConfig(string $key)
    {
        $siteConfig = self::instance()->getSiteConfig();

        return $siteConfig[$key];
    }

    private static function initPrimaryCountries(): array
    {
        $country = strtoupper(substr(self::getSiteName(), -2));

        return self::instance()->siteConfig['primaryCountries'] = [$country];
    }
}
