<?php
namespace src\Models\OcConfig;

/**
 * This trait group access to email settings stored in /config/primaAprilis.* conf. files
 * BEWARE OF FUNCTIONS NAME COLLISION BETWEEN CONFIG TRAITS!
 */
trait PrimaAprilisTrait {

    protected $primaAprilisConfig = null;

    private static function isPAEnabled()
    {
        return !self::getPAVar('disableAllPrimaAprilisChanges');
    }

    /**
     * Returns TRUE if dane shoudl be activated
     * @return string
     */
    public static function isPADanceEnabled()
    {
        return self::isPAEnabled() && self::getPAVar('danceEnabled');
    }

    /**
     * Returns TRUE if dane shoudl be activated
     * @return string
     */
    public static function isPAUserStatsRandEnabled()
    {
        return self::isPAEnabled() && self::getPAVar('randUserStats');
    }

    /**
     * Returns TRUE if dane shoudl be activated
     * @return string
     */
    public static function isPAFakeUserNameEnabled()
    {
        return self::isPAEnabled() && self::getPAVar('fakeUserNameInProfile');
    }

    /**
     * Returns site properties
     *
     * @return array site properties
     */
    protected function getPAConfig()
    {
        if (!$this->primaAprilisConfig) {
            $this->primaAprilisConfig = self::getConfig("primaAprilis", "config");
        }
        return $this->primaAprilisConfig;
    }

    /**
     * Get Var from site.* files
     *
     * @param string $varName
     * @throws \Exception
     * @return string|array
     */
    private static function getPAVar($varName)
    {
        $config = self::instance()->getPAConfig();
        if (!is_array($config)) {
            throw new \Exception("Invalid $varName setting: see /config/primaAprilis.*");
        }
        return $config[$varName];
    }
}
