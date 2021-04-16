<?php

/**
 * Links presented on the horizontal bar below the header.
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
    'mnu_mainPage' => '/index.php',
    'mnu_abcOfGCaching' => [$links['wiki']['main']],
    'mnu_forum' => isset($links['forum']) ? [$links['forum']] : null,
    'mnu_blog' => isset($links['blog']) ? [$links['blog']] : null,
    'mnu_geokrets' => isset($links['geokrety']) ? [$links['geokrety']] : null,
    'mnu_links' => '/articles.php?page=links',
    'mnu_contact' => ['/articles.php?page=contact'],
    'mnu_guides' => '/guide',
    'mnu_clipboard' => '/printList', // counters added in MainLayoutCtrl
];
