<?php

namespace src\Models\OcConfig;

/**
 * Loads configuration from primaAprilis.*.php.
 *
 * @mixin OcConfig
 */
trait UserConfigTrait
{
    protected $userConfig = null;

    public static function getUserDefaultStatPicText(): string
    {
        return self::getKeyFromUserConfig('defaultStatpicText');
    }

    public static function getUserRmAccountPrefix(): string
    {
        return self::getKeyFromUserConfig('removedUserUsernamePrefix');
    }

    public static function getUserRmAccountDesc(): string
    {
        return self::getKeyFromUserConfig('removedUserDescription');
    }

    protected function getUserConfig(): array
    {
        if (! $this->userConfig) {
            $this->userConfig = self::getConfig('user');
        }
        return $this->userConfig;
    }

    /**
     * @return mixed
     */
    private static function getKeyFromUserConfig(string $key)
    {
        $userCfg = self::instance()->getUserConfig();
        return $userCfg[$key];
    }
}
