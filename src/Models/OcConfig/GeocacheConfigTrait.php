<?php

namespace src\Models\OcConfig;

use Exception;

/**
 * This trait group access to email settings stored in /config/geocache.* conf. files
 * BEWARE OF FUNCTIONS NAME COLLISION BETWEEN CONFIG TRAITS!
 */
trait GeocacheConfigTrait
{
    protected $geocacheConfig = null;

    /**
     * Returns array of types enabled on this OC node
     *
     * @return array
     */
    public static function getEnabledCacheSizesArray()
    {
        return self::getGeocacheConfigVar('enabledSizes');
    }

    /**
     * Returns array of types which can't be created on this OC node (former forbiddenTypes)
     *
     * @return array
     */
    public static function getNoNewCacheOfTypesArray()
    {
        return self::getGeocacheConfigVar('noNewCachesOfTypes');
    }

    /**
     * Returns site properties
     *
     * @return array site properties
     */
    protected function getGeocacheConfig()
    {
        if (! $this->geocacheConfig) {
            $this->geocacheConfig = self::getConfig('geocache', 'geocache');
        }

        return $this->geocacheConfig;
    }

    /**
     * Get Var from geocache.* files
     *
     * @param string $varName
     * @return string|array
     * @throws Exception
     */
    private static function getGeocacheConfigVar($varName)
    {
        $config = self::instance()->getGeocacheConfig();
        if (! is_array($config)) {
            throw new Exception("Invalid {$varName} setting: see /config/geocache.*");
        }
        return $config[$varName];
    }
}
