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

/*
 * Links config is present under $links durring load of this script
 * - feel free to use it
 */

// DON'T CHANGE $menu var name!
$menu = [
    /* 'translation key' => 'url' */

    'mnu_main_page' => '/index.php',
    'mnu_abcOfGCaching' => $links['wiki']['main'],
    //'forum'       => $links['forum'],
    //'mnu_blog'    => $links['blog'],
    //'geokrets'    => $links['geokrety'],
    'links'         => '/articles.php?page=links',
    'contact'       => '/articles.php?page=contact',
    'guides'        => '/cacheguides.php',
    'mnu_clipboard' => 'mylist.php',      /* counters added in MainLayoutCtrl */

];
