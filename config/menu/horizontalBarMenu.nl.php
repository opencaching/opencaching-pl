<?php

/**
 * Links presented on the horizontal bar below the header.
 *
 * This is a configuration for OCNL node only.
 */

/** @var array $links OcConfig::$links is accessible in within this scope */

$menu = [
    'mnu_mainPage' => '/index.php',
    'mnu_abcOfGCaching' => [$links['wiki']['main']],
    'mnu_forum' => [$links['forum']],
    'mnu_blog' => [$links['blog']],
    'mnu_facebook' => ['https://www.facebook.com/OpencachingBenelux/'],
    'mnu_geokrets' => [$links['geokrety']],
    'mnu_download' => [$links['wiki']['downloads']],
    'mnu_links' => '/articles.php?page=links',
    'mnu_contact' => '/articles.php?page=contact',
    'mnu_guides' => '/guide',
    'mnu_shop' => 'https://www.geogeek.nl',
    'mnu_clipboard' => '/printList', // counters added in MainLayoutCtrl
];
