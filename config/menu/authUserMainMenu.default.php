<?php

/**
 * Links presented on the sidebar for authorized users only.
 *
 * This is a default configuration, used when node-specific configuration
 * file does not exist. You may customize those values by copying this file
 * and changing 'default' in its filename to your node country code.
 *
 * Every record of $menu array should be in form:
 *  '<translation-key-used-as-link-text>' => '<url>',
 *
 * If link needs to be opened in a new window, wrap it in an array:
 *  '<translation-key-used-as-link-text>' => ['<url>'],
 *
 * Do NOT change $menu variable name!
 */

use src\Utils\Uri\SimpleRouter;

/** @var array $links OcConfig::$links is accessible in within this scope */

$menu = [
    'mnu_news' => SimpleRouter::getLink('News.NewsList'),
    'mnu_cacheMap' => SimpleRouter::getLink('MainMap', 'embeded'),

    'mnu_myNeighborhood' => SimpleRouter::getLink('MyNeighbourhood','index'),
    'mnu_searchCache' => '/search.php',
    'mnu_geoPaths' => '/powerTrail.php',

    'mnu_rules' => $links['wiki']['rules'],
    'mnu_statistics' => '/articles.php?page=stat',
];
