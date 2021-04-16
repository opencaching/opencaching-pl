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

$menu = [
    'mnu_newCache' => '/newcache.php',
    'mnu_myCaches' => '/mycaches.php',
    'mnu_myStats' => '/viewprofile.php',
    'mnu_myAccount' => '/myprofile.php',

    'mnu_myRoutes' => '/myroutes.php',
    'mnu_myCacheNotes' => SimpleRouter::getLink(CacheNotesController::class),
    'mnu_watchedCaches' => SimpleRouter::getLink(UserWatchedCachesController::class),
    'mnu_ignoredCaches' => SimpleRouter::getLink(UserIgnoredCachesController::class),
    'mnu_myRecommends' => SimpleRouter::getLink(MyRecommendationsController::class, 'recommendations'),
    'mnu_savedQueries' => '/query.php',
    'mnu_okapiExtApps' => '/okapi/apps/?langpref='.I18n::getCurrentLang(),
];
