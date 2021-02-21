<?php

namespace src\Models\OcConfig;

/**
 * Loads configuration from okapiConfig.*.php.
 *
 * @mixin OcConfig
 */
trait OkapiConfigTrait
{
    protected $okapiConfig = null;

    /**
     * Get blacklist of okapi cron jobs.
     *
     * @return array|null
     */
    public static function getOkapiCronJobBlacklist()
    {
        return self::getKeyFromOkapiConfig('cronJobsBlackList');
    }

    protected function getOkapiConfig(): array
    {
        if (! $this->okapiConfig) {
            $this->okapiConfig = self::getConfig('okapiConfig');
        }

        return $this->okapiConfig;
    }

    /**
     * @return mixed
     */
    private static function getKeyFromOkapiConfig(string $key)
    {
        $okapiConfig = self::instance()->getOkapiConfig();

        return $okapiConfig[$key];
    }
}
