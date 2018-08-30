<?php
use Utils\Uri\SimpleRouter;
use Controllers\Admin\CacheSetAdminController;

/**
 * This is simple configuration of links presented in sidebar of the page
 * for authorized admins (COG) only.
 *
 * This is a DEFAULT configuration for ALL nodes.
 *
 * If you want to customize footer for your node
 * create config for your node by copied this file and changing its name.
 *
 * Every record of $menu table should be table record in form:
 *  '<translation-key-used-as-link-text>' => '<url>',
 *
 * or if link needs to be open in a new window (use php array)
 *  '<translation-key-used-as-link-text>' => ['<url>'],
 *
 */

$menu = [ // DON'T CHANGE $menu var name!

    'mnu_reports'           => '/admin_reports.php', /* counters added in MainLayoutCtrl */
    'mnu_pendings'          => '/viewpendings.php', /* counters added in MainLayoutCtrl */
    'mnu_octeamStats'       => '/articles.php?page=cog',
    'mnu_notFoundCaches'    => '/admin_cachenotfound.php',
    'mnu_searchUser'        => '/admin_searchuser.php',
    'mnu_ocTeamNews'        => SimpleRouter::getLink('News.NewsAdmin'),
    'mnu_geoPathAdmin'      => '/powerTrailCOG.php',
    'mnu_abandonCacheSets'  => SimpleRouter::getLink(
                                CacheSetAdminController::class, 'cacheSetsToArchive'),
];