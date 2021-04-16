<?php

namespace src\Models\OcConfig;

use Exception;

/**
 * All configuration files should be stored in /config.
 * Legacy configuration will be loaded from /lib/settings.inc.php.
 *
 * For all config files there is 3-level hierarchy:
 *   - <config-name>.default.php   - Defaults for all nodes, stored in git.
 *
 *   - <config-name>.<node-id>.php - Defaults for a node, stored in git.
 *                                   Node id is a country code, e.g. 'pl', 'ro'...
 *
 *   - <config-name>.local.php     - Custom local configuration.
 *                                   Those files are ignored by git.
 *
 * Node identifier should be set in site.local.php.
 */
abstract class ConfigReader
{
    const CONFIG_DIR = __DIR__ . '/../../../config/';

    const LEGACY_LOCAL_CONFIG = __DIR__ . '/../../../lib/settingsGlue.inc.php';
    const LOCAL_CONFIG = self::CONFIG_DIR . 'site.local.php';

    const MENU_DIR = self::CONFIG_DIR . 'menu/';
    const MENU_FOOTER_PREFIX = 'footerMenu';
    const MENU_ADMIN_PREFIX = 'adminPages';
    const MENU_AUTH_USER = 'authUserMainMenu';
    const MENU_CUSTOM_USER = 'customUserMenu';
    const MENU_NON_AUTH_USER = 'noUserMainMenu';
    const MENU_HORIZONTAL_BAR = 'horizontalBarMenu';
    const MENU_ADDITIONAL_PAGES = 'additionalPages';

    /**
     * Node identifier read form site.local.php.
     *   e.g. 'pl', 'ro', 'nl', 'uk'...
     */
    private $ocNode = null;

    protected $links = null;

    /**
     * Get the given menu. Local version will be used if it present, otherwise
     * node-specific and finally the default version will be used as a fallback.
     */
    public static function getMenu(string $menuPrefix): array
    {
        // Make $links accessible in menu configuration files
        $links = self::getLinks();

        $ocNode = self::getOcNode();
        $localMenuFile = self::MENU_DIR . "{$menuPrefix}.local.php";
        $nodeMenuFile = self::MENU_DIR . "{$menuPrefix}.{$ocNode}.php";

        if (is_file($localMenuFile)) {
            include $localMenuFile;

            return $menu;
        }

        if (is_file($nodeMenuFile)) {
            include $nodeMenuFile;

            return $menu;
        }

        include self::MENU_DIR . "{$menuPrefix}.default.php";

        return $menu;
    }

    public static function getLinks()
    {
        $ctrl = static::instance();

        if (! $ctrl->links) {
            $ctrl->links = self::getConfig('links', 'links');
        }

        return $ctrl->links;
    }

    /**
     * Get config merged from default, node-specific and local config files.
     *
     * @param string $configFile Prefix of the config file, e.g. site, okapiConfig
     * @param string $variable Variable in config file containing config
     * @return mixed
     */
    protected static function getConfig(string $configFile, string $variable = 'config')
    {
        $ocNode = self::getOcNode();

        $nodeConfigFile = self::CONFIG_DIR . "{$configFile}.{$ocNode}.php";
        $localConfigFile = self::CONFIG_DIR . "{$configFile}.local.php";

        include self::CONFIG_DIR . "{$configFile}.default.php";;

        if (is_file($nodeConfigFile)) {
            include $nodeConfigFile;
        }

        if (is_file($localConfigFile)) {
            include $localConfigFile;
        }

        return $$variable;
    }

    /**
     * Get the node identifier. First legacy /lib/settings.inc.php is checked.
     * If it doesn't exist, /config/site.local.php is used.
     */
    public static function getOcNode(): string
    {
        if (static::instance()->ocNode) {
            return static::instance()->ocNode;
        }

        $ocNode = self::getNodeIdFromLegacyConfig() ?? self::getNodeIdFromConfig();

        if ($ocNode) {
            return static::instance()->ocNode = $ocNode;
        }

        throw new Exception('Neither legacy nor a non-legacy config file exists.');
    }

    private static function getNodeIdFromConfig(): ?string
    {
        if (! is_file(self::LOCAL_CONFIG)) {
            return null;
        }

        include self::LOCAL_CONFIG;

        return $config['ocNode'] ?? 'pl';
    }

    private static function getNodeIdFromLegacyConfig(): ?string
    {
        if (! is_file(self::LEGACY_LOCAL_CONFIG)) {
            return null;
        }

        include self::LEGACY_LOCAL_CONFIG;

        return $config['ocNode'] ?? 'pl';
    }
}
