<?php
use src\Utils\Uri\SimpleRouter;

/**
 * This is simple configuration of links presented in sidebar of the page
 * for authorized users only.
 *
 * This is the configuration for OCUS node.
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
    'mnu_news'          => SimpleRouter::getLink('News.NewsList'),
    'mnu_PhoneApps'     => '/articles.php?page=apps',
    'mnu_cacheMap'      => SimpleRouter::getLink('MainMap', 'embeded'),
    'mnu_myNeighborhood'=> SimpleRouter::getLink('MyNeighbourhood','index'),
    'mnu_searchCache'   => '/search.php',
    'mnu_geoPaths'      => '/powerTrail.php',
    'mnu_rules'         => $links['wiki']['rules'],
    'mnu_statistics'    => '/articles.php?page=stat',

];
