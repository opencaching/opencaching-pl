<?php
/**
 * This is simple configuration of links presented in sidebar of the page
 * for authorized users only.
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
    /* 'translation key' => 'url' */
    'new_cache' => '/newcache.php',
    'my_caches' => '/mycaches.php',
    'my_statistics' => '/viewprofile.php',
    'my_account' => '/myprofile.php',

    'myroutes' => '/myroutes.php',
    'mycache_note' => '/mycache_notes.php',
    'watched_caches' => 'mywatches.php',
    'ignored_caches' => 'myignores.php',
    'my_recommendations' => 'mytop5.php',
    'collected_queries' => 'query.php',
    'okapi_apps' => 'okapi/apps/?langpref=' . $GLOBALS['lang'],

    'adoption_cache' => 'chowner.php',
    'search_user' => 'searchuser.php',

    'mnu_newCaches'    => '/newcaches.php',
    'mnu_newLogs'      => '/newlogs.php',
    'mnu_incommingEvents' => '/newevents.php',
    'recommended_caches' => '/cacheratings.php',

    'mnu_oldCacheMap'   => '/cachemap2.php', //PL only!
    'Flopp_map'     => 'https://www.flopp-caching.de',
    'Field_Notes' => '/log_cache_multi_send.php',
    'openchecker_name' => 'openchecker.php', //ENABLE?!
    'mnu_qrCode' => '/qrcode.php',
];
