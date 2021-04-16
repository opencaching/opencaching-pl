<?php

/**
 * Links presented on the sidebar for authorized users only.
 *
 * This is a configuration for OCNL node only.
 */

use src\Utils\Uri\SimpleRouter;

/** @var array $links OcConfig::$links is accessible in within this scope */

$menu = [
    'mnu_news' => SimpleRouter::getLink('News.NewsList'),
    'mnu_PhoneApps' => '/articles.php?page=apps',
    'mnu_cacheMap' => SimpleRouter::getLink('MainMap', 'embeded'),
    'mnu_myNeighborhood' => SimpleRouter::getLink('MyNeighbourhood','index'),
    'mnu_searchCache' => '/search.php',
    'mnu_geoPaths' => '/powerTrail.php',
    'mnu_rules' => $links['wiki']['rules'],
    'mnu_statistics' => '/articles.php?page=stat',
];
