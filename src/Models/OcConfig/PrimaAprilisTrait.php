<?php

namespace src\Models\OcConfig;

/**
 * Loads configuration from primaAprilis.*.php.
 *
 * @mixin OcConfig
 */
trait PrimaAprilisTrait
{
    protected $primaAprilisConfig = null;

    private static function isPAEnabled(): bool
    {
        return ! self::getKeyFromPAConfig('disableAllPrimaAprilisChanges');
    }

    public static function isPADanceEnabled(): bool
    {
        return self::isPAEnabled() && self::getKeyFromPAConfig('danceEnabled');
    }

    public static function isPAUserStatsRandEnabled(): bool
    {
        return self::isPAEnabled() && self::getKeyFromPAConfig('randUserStats');
    }

    public static function isPAFakeUserNameEnabled(): bool
    {
        return self::isPAEnabled() && self::getKeyFromPAConfig('fakeUserNameInProfile');
    }

    protected function getPAConfig(): array
    {
        if (! $this->primaAprilisConfig) {
            $this->primaAprilisConfig = self::getConfig('primaAprilis');
        }

        return $this->primaAprilisConfig;
    }

    /**
     * @return mixed
     */
    private static function getKeyFromPAConfig(string $key)
    {
        $config = self::instance()->getPAConfig();

        return $config[$key];
    }
}
