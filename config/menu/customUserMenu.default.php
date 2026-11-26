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

use src\Controllers\MyRecommendationsController;
use src\Utils\Uri\SimpleRouter;
use src\Controllers\CacheNotesController;
use src\Utils\I18n\I18n;
use src\Controllers\UserIgnoredCachesController;
use src\Controllers\UserWatchedCachesController;

/** @var array $links OcConfig::$links is accessible in within this scope */

global $config;

$menu = [];

$menu['mnu_newCache'] = '/newcache.php';
$menu['mnu_myCaches'] = '/mycaches.php';
$menu['mnu_myStats'] = '/viewprofile.php';
$menu['mnu_myAccount'] = '/myprofile.php';

$menu['mnu_myRoutes'] = '/myroutes.php';
$menu['mnu_myCacheNotes'] = SimpleRouter::getLink(CacheNotesController::class);
$menu['mnu_watchedCaches'] = SimpleRouter::getLink(UserWatchedCachesController::class);
$menu['mnu_ignoredCaches'] = SimpleRouter::getLink(UserIgnoredCachesController::class);
$menu['mnu_myRecommends'] = SimpleRouter::getLink(MyRecommendationsController::class, 'recommendations');

if (! empty($config['meritBadges'])) {
    $menu['mnu_myBadgesNew'] = SimpleRouter::getLink('Badges.MyBadges');
    $menu['mnu_myBadgesOld'] = '/user_badges.php?user_id=0';
}

$menu['mnu_savedQueries'] = '/query.php';
$menu['mnu_okapiExtApps'] = '/okapi/apps/?langpref=' . I18n::getCurrentLang();
