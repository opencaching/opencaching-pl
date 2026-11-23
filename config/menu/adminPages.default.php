<?php

/**
 * Links presented on the sidebar for authorized admins (COG) only.
 *
 * This is a default configuration, used when node-specific configuration
 * file does not exist. You may customize those values by copying this file
 * and changing 'default' in its filename to your node country code.
 *
 * Every record of $menu array should be in form:
 *  '<translation-key-used-as-link-text>' => '<url>',
 *
 * If link needs to be opened in a new window, wrap it in an array:
 *  '<translation-key-used-as-link-text>' => ['<url>'],
 *
 * Do NOT change $menu variable name!
 */

use src\Controllers\Admin\CacheSetAdminController;
use src\Utils\Uri\SimpleRouter;

/** @var array $links OcConfig::$links is accessible in within this scope */

$menu = [];

$menu['mnu_reports'] = '/admin_reports.php';
$menu['mnu_pendings'] = SimpleRouter::getLink('Admin.GeoCacheApprovalAdmin');
$menu['mnu_octeamStats'] = '/articles.php?page=cog';
$menu['mnu_notFoundCaches'] = '/admin_cachenotfound.php';
$menu['mnu_searchUser'] = SimpleRouter::getLink('Admin.UserAdmin', 'search');
$menu['mnu_ocTeamNews'] = SimpleRouter::getLink('News.NewsAdmin');
$menu['mnu_geoPathAdmin'] = '/powerTrailCOG.php';
$menu['menu_octeam_power_trail'] = SimpleRouter::getLink('Admin.PowerTrailAdmin');
$menu['mnu_abandonCacheSets'] = SimpleRouter::getLink(
    CacheSetAdminController::class,
    'cacheSetsToArchive'
);
$menu['mnu_badgeCategories'] = SimpleRouter::getLink('Admin.BadgeCategoriesAdmin');
$menu['mnu_badgesAdmin'] = SimpleRouter::getLink('Admin.BadgesAdmin');
