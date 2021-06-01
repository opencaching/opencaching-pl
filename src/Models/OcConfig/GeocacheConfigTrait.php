<?php

namespace src\Models\OcConfig;

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
     * The number of founds which user needs to log to create its own new geocache
     */
    public static function getMinUserFoundsForNewCache(): int
    {
        return self::getKeyFromGeoCacheConfig('minUserFoundsForNewCache');
    }

    /**
     * The minimum number of active geocaches owned by user to skip OCTEAM
     * verification of every new geocache
     */
    public static function getMinCachesToSkipNewCacheVerification(): int
    {
        return self::getKeyFromGeoCacheConfig('minCachesToSkipNewCacheVerification');
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
