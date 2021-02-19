<?php

/**
 * Links presented on the sidebar for authorized users only.
 *
 * This is a configuration for OCNL node only.
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

    'mnu_FloppMap' => ['https://flopp-caching.de'],
    'mnu_massLogsSave' => '/log_cache_multi_send.php',
    'mnu_openchecker' => '/openchecker.php',
    'mnu_qrCode' => SimpleRouter::getLink(UserUtilsController::class, 'qrCodeGen'),
];
