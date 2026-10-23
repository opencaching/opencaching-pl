<?php


/**
 * Links presented on the sidebar for system administrators (sysadmin) only.
 *
 * This is a default configuration, used when node-specific configuration
 * file does not exist.
 *
 * Every record of $menu array should be in form:
 *  '<translation-key-used-as-link-text>' => '<url>',
 *
 * Do NOT change $menu variable name!
 */

/** @var array $links OcConfig::$links is accessible in within this scope */

use src\Utils\Uri\SimpleRouter;

$menu['menu_sys_admin_sys_panel'] = SimpleRouter::getLink('sys');
$menu['menu_sys_admin_role_panel'] = SimpleRouter::getLink('SysAdmin.RoleManager');
