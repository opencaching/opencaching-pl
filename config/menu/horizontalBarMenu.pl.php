<?php

/**
 * Links presented on the horizontal bar below the header.
 *
 * This is a configuration for OCPL node only.
 */

/** @var array $links OcConfig::$links is accessible within this scope */

$menu = [
    'mnu_mainPage' => '/index.php',
    'mnu_abcOfGCaching' => [$links['wiki']['main']],
    'mnu_forum' => [$links['forum']],
    // 'mnu_blog' => [$links['blog']],
    'mnu_geokrets' => [$links['geokrety']],
    'mnu_links' => ['/articles.php?page=links'],
    //'mnu_contact' => ['/articles.php?page=contact'],
    'mnu_contact' => '/News.RawNews/show/280',
    'mnu_guides' => '/guide',
    'mnu_facebook' => ['https://www.facebook.com/OpencachingPL/'],
    'mnu_instagram' => ['https://www.instagram.com/opencachingpl/'],
    // 'mnu_kwidzyn' => ['https://open.kwidzyn.pl'],
    'mnu_clipboard' => '/printList', /* counters added in MainLayoutCtrl */

];
