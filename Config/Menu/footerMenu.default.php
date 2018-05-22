<?php
/**
 * This is simple configuration of links presented in footer of the page.
 *
 * This is a DEFAULT configuration (used if node-specific is not defined).
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

    /* 'translation key' => 'url' */
    'mnu_api'       => '/okapi',
    'mnu_rss'       => '/articles.php?page=rss',
    'mnu_contact'   => '/articles.php?page=contact',
    'mnu_mainPage'  => '/',
];