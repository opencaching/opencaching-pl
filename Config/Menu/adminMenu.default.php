<?php
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
 *
 *  '<translation-key-used-as-link-text>', '<url>',
 *
 */

$menu = [ // DON'T CHANGE $menu var name!

    'mnu_reports'       => '/admin_reports.php', /* counters added in MainLayoutCtrl */
    'mnu_pendings'      => '/viewpendings.php', /* counters added in MainLayoutCtrl */
    'mnu_octeamStats'   => '/articles.php?page=cog',
    'mnu_notFoundCaches' => '/admin_cachenotfound.php',
    'mnu_searchUser'    => '/admin_searchuser.php',
    'mnu_ocTeamNews'    => '/admin_news.php',
    'mnu_geoPathAdmin'  => '/powerTrailCOG.php',
];

