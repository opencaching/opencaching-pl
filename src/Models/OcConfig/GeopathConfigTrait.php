<?php

namespace src\Models\OcConfig;

/**
 * Loads configuration from geopath.*.php.
 *
 * @mixin OcConfig
 */
trait GeopathConfigTrait
{
    protected $geopathConfig = null;

    /**
     * Returns TRUE if geopathc are supported on site
     */
    public static function areGeopathsSupported(): bool
    {
        return self::getKeyFromGeopathConfig('geopathsSupported');
    }

    /**
     * Returns min. caches to create geopath
     */
    public static function geopathMinCacheCount(): int
    {
        return self::getKeyFromGeopathConfig('minCachesCount');
    }

    /**
     * Returns min. founds to be geopath owner
     */
    public static function geopathOwnerMinFounds(): int
    {
        return self::getKeyFromGeopathConfig('geopathOwnerMinFounds');
    }

    protected function getGeopathConfig(): array
    {
        if (! $this->geopathConfig) {
            $this->geopathConfig = self::getConfig('geopath', 'geopathCfg');
        }

        return $this->geopathConfig;
    }

    /**
     * @return mixed
     */
    private static function getKeyFromGeopathConfig(string $key)
    {
        $geopathConfig = self::instance()->getGeopathConfig();

        return $geopathConfig[$key];
    }
}
