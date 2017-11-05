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
    'cache_map'     => '/cachemap3.php',

    'my_neighborhood' => '/myneighborhood.php',
    'search_cache'  => '/search.php',
    'gp_mainTitile' => '/powerTrail.php',


    'rules'         => $links['wiki']['rules'],
    'statistics'    => '/articles.php?page=stat',

];
