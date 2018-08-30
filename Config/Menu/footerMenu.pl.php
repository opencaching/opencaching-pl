<?php

/**
 * This is simple configuration of links presented in footer of the OCPL page.
 * This is configuration for OCPL only.
 *
 * Every record of $menu table should be table record in form:
 *  '<translation-key-used-as-link-text>' => '<url>',
 *
 * or if link needs to be open in a new window (use php array)
 *  '<translation-key-used-as-link-text>' => ['<url>'],
 *
 */
// OcConfig::$links var is accessible in this scope!

$menu = [ // DON'T CHANGE $menu var name!

    /* 'translation key' => 'url' */

    'mnu_impressum' => [ $links['wiki']['impressum'] ],
    'mnu_history'   => [ $links['wiki']['history'] ],
    'mnu_api'       => '/okapi',
    'mnu_rss'       => '/RSS',
    'mnu_contact'   => '/articles.php?page=contact',
    'mnu_mainPage' => '/',

];