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

$menu = [];

$menu['mnu_news'] = SimpleRouter::getLink('News.NewsList');
$menu['mnu_cacheMap'] = SimpleRouter::getLink('MainMap', 'embeded');

$menu['mnu_myNeighborhood'] = SimpleRouter::getLink('MyNeighbourhood','index');
$menu['mnu_searchCache'] = '/search.php';
$menu['mnu_geoPaths'] = '/powerTrail.php';

global $config;
if (! empty($config['meritBadges'])) {
    $menu['mnu_badges'] = SimpleRouter::getLink('Badges.Badges');
}

$menu['mnu_rules'] = $links['wiki']['rules'];
$menu['mnu_statistics'] = '/articles.php?page=stat';
