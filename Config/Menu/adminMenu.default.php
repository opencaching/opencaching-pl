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

// DON'T CHANGE $menu var name!
$menu = [
    'reports' => '/admin_reports.php', /* counters added in MainLayoutCtrl */
    'pendings' => '/viewpendings.php', /* counters added in MainLayoutCtrl */
    'stat_octeam' => '/articles.php?page=cog',
    'cache_notfound' => '/admin_cachenotfound.php',
    'search_user' => '/admin_searchuser.php',
    'news_menu_OCTeam' => '/admin_news.php',
    'pt208' => '/powerTrailCOG.php',
];

