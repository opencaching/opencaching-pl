<?php

/**
 * This is simple configuration of links presented in footer of the page.
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

    'api'       => '/okapi',
    'rss'       => 'articles.php?page=rss',
    'contact'   => 'articles.php?page=contact',
    'main_page' => '/index.php?page=sitemap',
];
