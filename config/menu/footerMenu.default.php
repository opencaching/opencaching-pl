<?php

/**
 * Links presented in the footer.
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

/** @var array $links OcConfig::$links is accessible in within this scope */

$menu = [
    'mnu_api' => '/okapi',
    'mnu_rss' => '/RSS',
    'mnu_contact' => '/articles.php?page=contact',
    'mnu_mainPage' => '/',
];
