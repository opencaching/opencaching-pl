<?php

/**
 * Links presented in the footer.
 *
 * This is a configuration for OCPL node only.
 */

/** @var array $links OcConfig::$links is accessible in within this scope */

$menu = [
    'mnu_impressum' => [$links['wiki']['impressum']],
    'mnu_history' => [$links['wiki']['history']],
    'mnu_api' => '/okapi',
    'mnu_rss' => '/RSS',
    //'mnu_contact' => '/articles.php?page=contact',
    'mnu_contact' => '/News.RawNews/show/280',
    'mnu_Cooperation' => 'https://wiki.opencaching.pl/index.php?title=Wsp%C3%B3%C5%82praca',
    'mnu_mainPage' => '/',
];
