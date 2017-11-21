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
$menu = [ // DON'T CHANGE $menu var name!
    /* 'translation key' => 'url' */
    'mnu_news'          => '/news.php',
    'mnu_cacheMap'      => '/cachemap3.php',

    'mnu_myNeighborhood'=> '/myneighborhood.php',
    'mnu_searchCache'   => '/search.php',
    'mnu_geoPaths'      => '/powerTrail.php',

    'mnu_rules'         => $links['wiki']['rules'],
    'mnu_statistics'    => '/articles.php?page=stat',

];
