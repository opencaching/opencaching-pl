<?php

/**
 * Links presented on the sidebar for non-authorized users only.
 *
 * This is a configuration for OCNL node only.
 */

use src\Controllers\CacheController;
use src\Utils\Uri\SimpleRouter;

/** @var array $links OcConfig::$links is accessible in within this scope */

$menu = [
    'mnu_registration' => SimpleRouter::getLink('UserRegistration'),
    'mnu_news' => SimpleRouter::getLink('News.NewsList'),
    'mnu_rules' => [$links['wiki']['rules']],
    'mnu_PhoneApps' => '/articles.php?page=apps',
    'mnu_newCaches' => SimpleRouter::getLink(CacheController::class, 'newCaches'),
    'mnu_incommingEvents' => SimpleRouter::getLink(CacheController::class, 'incomingEvents'),
];
