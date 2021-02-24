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

use src\Controllers\CacheAdoptionController;
use src\Controllers\CacheController;
use src\Controllers\CacheLogController;
use src\Controllers\UserUtilsController;
use src\Utils\Uri\SimpleRouter;

/** @var array $links OcConfig::$links is accessible in within this scope */

$menu = [
    'mnu_cacheAdoption' => SimpleRouter::getLink(CacheAdoptionController::class),
    'mnu_searchUser' => '/searchuser.php',

    'mnu_newCaches' => SimpleRouter::getLink(CacheController::class, 'newCaches'),
    'mnu_newLogs' => SimpleRouter::getLink(CacheLogController::class, 'lastLogsList'),
    'mnu_incommingEvents' => SimpleRouter::getLink(CacheController::class, 'incomingEvents'),
    'mnu_recoCaches' => SimpleRouter::getLink(CacheController::class, 'recommended'),

    'mnu_FloppMap' => 'https://flopp-caching.de',
    'mnu_massLogsSave' => '/log_cache_multi_send.php',
    'mnu_openchecker' => '/openchecker.php',
    'mnu_qrCode' => SimpleRouter::getLink(UserUtilsController::class, 'qrCodeGen'),
];
