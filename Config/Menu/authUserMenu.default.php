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
    'news'          => '/news.php',
    'new_caches'    => '/newcaches.php',
    'new_logs'      => '/newlogs.php',
    'incomming_events' => '/newevents.php',
    'cache_map'     => '/cachemap3.php',
    'search_cache'  => '/search.php',
    'recommended_caches' => '/cacheratings.php',
    'statistics'    => '/articles.php?page=stat',
    'rules'         => $links['wiki']['rules'],
    'gp_mainTitile' => '/powerTrail.php',
];
