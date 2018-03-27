<?php
use Utils\Uri\SimpleRouter;

/**
 * This is simple configuration of links presented in sidebar of the page
 * for non-authorized users only.
 *
 * This is a DEFAULT configuration for ALL nodes.
 *
 * If you want to customize footer for your node
 * create config for your node by copied this file and changing its name.
 *
 * Every record of $menu table should be table record in form:
 *  '<translation-key-used-as-link-text>' => '<url>',
 *
 * or if link needs to be open in a new window (use php array)
 *  '<translation-key-used-as-link-text>' => ['<url>'],
 *
 */

/*
 * Links config is present under $links durring load of this script
 * - feel free to use it
 */

$menu = [ // DON'T CHANGE $menu var name!

    /* 'translation key' => 'url' */
    'mnu_registration'     => SimpleRouter::getLink('userAuthorization','register'),
    'mnu_news'             => '/news.php',
    'mnu_rules'            => [$links['wiki']['rules']],
    'mnu_newCaches'        => '/newcaches.php',
    'mnu_newLogs'          => '/newlogs.php',
    'mnu_incommingEvents'  => '/newevents.php',

];
