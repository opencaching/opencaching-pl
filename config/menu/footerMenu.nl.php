<?php

/**
 * Links presented in the footer.
 *
 * This is a configuration for OCNL node only.
 */

/** @var array $links OcConfig::$links is accessible in within this scope */

$menu = [
    'mnu_history' => [$links['wiki']['history']],
    'mnu_api' => '/okapi',
    'mnu_rss' => '/RSS',
    'mnu_contact' => '/articles.php?page=contact',
    'mnu_mainPage' => '/',
    'mnu_privacyPolicy' => [$links['wiki']['privacyPolicy']],
];
