<?php
/**
 * This is simple configuration of links presented at horizontal bar
 * below the header of the page.
 *
 * This is a menu configuration for OCPL node only.
 *
 * Every record of $menu table should be table record in form:
 *  '<translation-key-used-as-link-text>' => '<url>',
 *
 * or if link needs to be open in a new window (use php array)
 *  '<translation-key-used-as-link-text>' => ['<url>'],
 */

// Links config is present under $links durring load this script - feel free to use it
$menu = [ // DON'T CHANGE $menu var name!

    /* 'translation key' => 'url' */
    'mnu_mainPage'      => '/index.php',
    'mnu_abcOfGCaching' => [$links['wiki']['main']],
    'mnu_forum'         => [$links['forum']],
    'mnu_blog'          => [$links['blog']],
    'mnu_geokrets'      => [$links['geokrety']],
    'mnu_download'      => [$links['wiki']['downloads']],
    'mnu_links'         => ['/articles.php?page=links'],
    'mnu_contact'       => ['/articles.php?page=contact'],
    'mnu_guides'        => '/cacheguides.php',
    'mnu_facebook'      => ['https://www.facebook.com/OpencachingPL/'],
    'mnu_clipboard'     => '/mylist.php', /* counters added in MainLayoutCtrl */

];