<?php

namespace src\Models\OcConfig;

use src\Models\ApplicationContainer;

/**
 * Loads configuration from geocache.*.php.
 *
 * @mixin OcConfig
 */
trait GeocacheConfigTrait
{
    protected $geocacheConfig = null;

    /**
     * Get geocache types enabled on this OC node.
     */
    public static function getEnabledCacheSizesArray(): array
    {
        return self::getKeyFromGeoCacheConfig('enabledSizes');
    }

    /**
     * Get geocache types which can't be created on this OC node.
     */
    public static function getNoNewCacheOfTypesArray(): array
    {
        return self::getKeyFromGeoCacheConfig('noNewCachesOfTypes');
    }

    /**
     * Get titled geocache mechanism period
     */
    public static function getTitledCachePeriod(): string
    {
        return self::getKeyFromGeoCacheConfig('titledCachePeriod');
    }

    /**
     * Get minimum number of founds necessary for titled geocache
     */
    public static function getTitledCacheMinFounds(): int
    {
        return self::getKeyFromGeoCacheConfig('titledCacheMinFounds');
    }

    /**
     * Hide coordinates for non-logged users
     */
    public static function coordsHiddenForNonLogged(): bool
    {
        return self::getKeyFromGeoCacheConfig('coordsHiddenForNonLogged');
    }

    /**
     * Return TRUE if reactivation rules are enabbled in config
     * @return bool
     */
    public static function isReactivationRulesEnabled(): bool
    {
        return self::getKeyFromGeoCacheConfig('reactivationRulesEnabled');
    }

    /**
     * Return list of predefined options (translation keys) for reactivation rules
     * @return array
     */
    public static function getReactivationRulesPredefinedOpts(): array
    {
        return self::getKeyFromGeoCacheConfig('reactivationRulesPredefinedOpts');
    }

    /**
     * Return list of attributes supported by the node.
     * The order is significant - the same order is used in all views.
     */
    public static function getSupportedAttributes(): array
    {
        return self::getKeyFromGeoCacheConfig('supportedAttributes');
    }

    protected function getGeoCacheConfig(): array
    {
        if (! $this->geocacheConfig) {
            $this->geocacheConfig = self::getConfig('geocache', 'geocache');
        }

        return $this->geocacheConfig;
    }

    /**
     * @return mixed
     */
    private static function getKeyFromGeoCacheConfig(string $key)
    {
        $geoCacheConfig = self::instance()->getGeoCacheConfig();

        return $geoCacheConfig[$key];
    }
}
