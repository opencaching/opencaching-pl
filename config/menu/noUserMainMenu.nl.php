<?php

use src\Controllers\CacheController;
use src\Utils\Uri\SimpleRouter;

/**
 * This is simple configuration of links presented in sidebar of the page
 * for non-authorized users only.
 *
 * This is the configuration for OCNL node.
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
    'mnu_registration' => SimpleRouter::getLink('UserRegistration'),
    'mnu_news' => SimpleRouter::getLink('News.NewsList'),
    'mnu_rules' => [$links['wiki']['rules']],
    'mnu_PhoneApps' => '/articles.php?page=apps',
    'mnu_newCaches' => SimpleRouter::getLink(CacheController::class, 'newCaches'),
    'mnu_incommingEvents' => SimpleRouter::getLink(CacheController::class, 'incomingEvents'),

];
