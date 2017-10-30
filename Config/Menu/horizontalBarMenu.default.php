<?php

/**
 * This is simple configuration of links presented at horizontal bar
 * below the header of the page.
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

// DON'T CHANGE $config var name!
$menu = [
    /* 'translation key' => 'url' */
    'main_page' => '/index.php',
    'abc' => $GLOBALS['wikiLinks']['main'],
    //'forum' => $forum_url,
    //'Blog' => $blogsite_url,
    'geokrets' => config['geokrety_url'],
    'Download' => $GLOBALS['wikiLinks']['downloads'],
    'links' => '/articles.php?page=links',
    'contact' => '/articles.php?page=contact',
    'guides' => '/cacheguides.php',
    //'shop' => $config['showShopButtonUrl'],
    //'clipboard' => 'mylist.php',

];
