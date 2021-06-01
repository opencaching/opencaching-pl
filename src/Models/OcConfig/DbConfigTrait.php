<?php

namespace src\Models\OcConfig;

/**
 * Loads configuration from db.*.php.
 *
 * @mixin OcConfig
 */
trait DbConfigTrait
{
    protected $dbConfig = null;

    /**
     * Temporary func - it will be removed when all nodes migrate to new config
     * Default TRUE means that legacy settings.inc.php DB config needs to be used
     * when db.local.php is ready set this var to false there
     */
    private static function useLegacyConfig(): bool
    {
        return self::getKeyFromDbConfig('_TMP_useLegacyConfig');
    }

    /**
     * Get DB host
     */
    public static function getDbHost(): string
    {
        if (self::useLegacyConfig()) {
            return OcConfig::instance()->_getDbHost();
        }
        return self::getKeyFromDbConfig('dbhost');
    }

    /**
     * Get DB name
     */
    public static function getDbName(): string
    {
        if (self::useLegacyConfig()) {
            return OcConfig::instance()->_getDbName();
        }
        return self::getKeyFromDbConfig('dbname');
    }

    /**
     * Get DB username
     */
    public static function getDbUser(): string
    {
        if (self::useLegacyConfig()) {
            return OcConfig::instance()->_getDbUser();
        }
        return self::getKeyFromDbConfig('dbuser');
    }

    /**
     * Get DB pass
     */
    public static function getDbPass(): string
    {
        if (self::useLegacyConfig()) {
            return OcConfig::instance()->_getDbPass();
        }
        return self::getKeyFromDbConfig('dbpass');
    }

    private function getDbConfig(): array
    {
        if (! $this->dbConfig) {
            $this->dbConfig = self::getConfig('db', 'db');
        }
        return $this->dbConfig;
    }

    /**
     * @return mixed
     */
    private static function getKeyFromDbConfig(string $key)
    {
        $dbCfg = self::instance()->getDbConfig();
        return $dbCfg[$key];
    }
}
