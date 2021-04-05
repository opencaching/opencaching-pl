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

    protected function getGeopathConfig(): array
    {
        if (! $this->geopathConfig) {
            $this->geopathConfig = self::getConfig('geopath', '$geopathCfg');
        }

        return $this->geopathConfig;
    }

    /**
     * @return mixed
     */
    private static function getKeyFromGeoCacheConfig(string $key)
    {
        $geopathConfig = self::instance()->getGeopathConfig();

        return $geopathConfig[$key];
    }
}
