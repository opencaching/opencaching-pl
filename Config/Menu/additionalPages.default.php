<?php
/**
 * This is simple configuration of links presented in sidebar of the page
 * for authorized users only.
 *
 * This is a DEFAULT configuration for ALL nodes.
 *
 * If you want to customize footer for your node
 * create config for your node by copied this file and changing its name.
 *
 * Every record of $menu table should be table record in form:
 *
 *  '<translation-key-used-as-link-text>', '<url>',
 *
 */

$menu = [ // DON'T CHANGE $menu var name!
    /* 'translation key' => 'url' */
    'mnu_cacheAdoption' => '/chowner.php',
    'mnu_searchUser'    => '/searchuser.php',

    'mnu_newCaches'         => '/newcaches.php',
    'mnu_newLogs'           => '/newlogs.php',
    'mnu_incommingEvents'   => '/newevents.php',
    'mnu_recoCaches'        => '/cacheratings.php',

    'mnu_FloppMap'      => 'https://www.flopp-caching.de',
    'mnu_massLogsSave'  => '/log_cache_multi_send.php',
    'mnu_openchecker'   => '/openchecker.php',
    'mnu_qrCode'        => '/qrcode.php',
];
