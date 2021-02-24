<?php

/**
 * Links presented in the footer.
 *
 * This is a configuration for OCUK node only.
 */

/** @var array $links OcConfig::$links is accessible in within this scope */

$menu = [
    'mnu_impressum' => [$links['wiki']['impressum']],
    'mnu_api' => '/okapi',
    'mnu_rss' => '/RSS',
    'mnu_contact' => '/articles.php?page=contact',
    'mnu_mainPage' => '/',
];
