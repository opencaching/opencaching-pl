<?php

namespace src\Models\OcConfig;

use Exception;

/**
 * This trait group access to local OKAPI config settings - settings here are used in okapi_settings.php
 * BEWARE OF FUNCTIONS NAME COLLISION BETWEEN CONFIG TRAITS!
 */
trait OkapiConfigTrait
{
    protected $okapiConfig = null;

    /**
     * Returns blacklist of okpai cron jobs
     *
     * @return array|null
     */
    public static function getOkapiCronJobBlacklist()
    {
        return self::getOkapiConfigVar('cronJobsBlackList');
    }

    /**
     * Returns okpai config properties
     *
     * @return array okapiConfig properties
     */
    protected function getOkapiConfig()
    {
        if (! $this->okapiConfig) {
            $this->okapiConfig = self::getConfig('okapiConfig', 'config');
        }

        return $this->okapiConfig;
    }

    /**
     * Get Var from okapiConfig.* files
     *
     * @param string $varName
     * @return string|array
     * @throws Exception
     */
    private static function getOkapiConfigVar($varName)
    {
        $okapiConfig = self::instance()->getOkapiConfig();

        if (! is_array($okapiConfig)) {
            throw new Exception("Invalid {$varName} setting: see /config/okapiConfig.*");
        }

        return $okapiConfig[$varName];
    }
}
