<?php

namespace src\Models\OcConfig;

use Exception;
use src\Models\Coordinates\Coordinates;

/**
 * Loads configuration from map.*.php.
 *
 * @mixin OcConfig
 */
trait MapConfigTrait
{
    protected $mapConfig = null;

    /**
     * Returns key value from map keys config or null if there is no such key.
     *
     * @return string|null
     */
    public static function getMapKey($keyName)
    {
        $key = self::getKeyFromMapConfig('keys')[$keyName] ?? null;

        return empty($key) ? null : $key;
    }

    /**
     * Returns JS configuration of map layers.
     *
     * @return string (JSON)
     */
    public static function getMapJsConfig()
    {
        $keyInjector = self::getKeyFromMapConfig('keyInjectionCallback');

        if (! is_null($keyInjector)) {
            // only if keyInjector exists
            if (! is_callable($keyInjector)) {
                throw new Exception('Wrong keyInjectionCallback config value!');
            }

            if (! $keyInjector(self::instance()->mapConfig)) {
                throw new Exception('MapConfig key injector init failed!');
            }
        }

        return self::getKeyFromMapConfig('jsConfig');
    }

    public static function getMapDefaultCenter(): Coordinates
    {
        return Coordinates::FromCoordsFactory(
            self::getKeyFromMapConfig('mapDefaultCenterLat'),
            self::getKeyFromMapConfig('mapDefaultCenterLon')
        );
    }

    public static function getStartPageMapDimensions()
    {
        return self::getKeyFromMapConfig('startPageMapDimensions');
    }

    public static function getStartPageMapZoom()
    {
        return self::getKeyFromMapConfig('startPageMapZoom');
    }

    public static function getMapExternalUrls()
    {
        $maps = self::getKeyFromMapConfig('external') ?? [];

        $result = [];
        foreach ($maps as $key => $conf) {
            if (! is_array($conf)) {
                continue;
            }

            if (isset($conf['url']) && ($conf['enabled'] ?? true)) {
                $result[$key] = $conf['url'];
            }
        }

        return $result;
    }

    protected function getMapConfig(): array
    {
        if (! $this->mapConfig) {
            $this->mapConfig = self::getConfig('map', 'map');
        }

        return $this->mapConfig;
    }

    /**
     * @return mixed
     */
    private static function getKeyFromMapConfig(string $key)
    {
        $mapConfig = self::instance()->getMapConfig();

        return $mapConfig[$key];
    }
}
