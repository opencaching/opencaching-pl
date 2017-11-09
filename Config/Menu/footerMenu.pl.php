<?php

/**
 * This is simple configuration of links presented in footer of the OCPL page.
 * This is configuration for OCPL only.
 *
 * Every record of $menu table should be table record in form:
 *
 *  '<translation-key-used-as-link-text>', '<url>',
 *
 */

// OcConfig::$links var is accessible in this scope!


// DON'T CHANGE $menu var name!
$menu = [

    /* 'translation key' => 'url' */

    'impressum' => $links['wiki']['impressum'],
    'history'   => $links['wiki']['history'],

    'api'       => '/okapi',
    'rss'       => 'articles.php?page=rss',
    'contact'   => 'articles.php?page=contact',
    'mnu_main_page' => '/index.php?page=sitemap',

];


