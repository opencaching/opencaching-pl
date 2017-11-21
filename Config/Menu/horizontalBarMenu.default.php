<?php
/**
 * This is simple configuration of links presented at horizontal bar
 * below the header of the page.
 *
 * This is a DEFAULT configuration used in lack of node-specific file.
 *
 * If you want to customize footer for your node
 * create config for your node by copied this file and changing its name.
 *
 * Every record of $menu table should be table record in form:
 *
 *  '<translation-key-used-as-link-text>', '<url>',
 *
 */

/*
 * Links config is present under $links durring load of this script
 * - feel free to use it
 */

// DON'T CHANGE $menu var name!
$menu = [

    /* 'translation key' => 'url' */
    'mnu_mainPage'      => '/index.php',
    'mnu_abcOfGCaching' => $links['wiki']['main'],
    'mnu_forum'         => isset($links['forum'])?$links['forum']:null,
    'mnu_blog'          => isset($links['blog'])?$links['blog']:null,
    'mnu_geokrets'      => isset($links['geokrety'])?$links['geokrety']:null,
    'mnu_links'         => '/articles.php?page=links',
    'mnu_contact'       => '/articles.php?page=contact',
    'mnu_guides'        => '/cacheguides.php',
    'mnu_clipboard'     => 'mylist.php', /* counters added in MainLayoutCtrl */
];

