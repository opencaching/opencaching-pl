<?php
/**
 * This is simple configuration of external links used in many places in the OC code
 * for example in menu, descriptions (translations) etc.
 *
 * This is a DEFAULT configuration for ALL nodes, which contains necessary vars.
 *
 * If you want to customize links for your node
 * create config for your node and there override $links array values as needed.
 *
 */

// main links table - this is the only variable which is a returned of this file.
$links = [];

//former $blogsite_url from settings.inc.php
$links['blog'] = 'http://blog.opencaching.pl';

//former $forum_url from settings.inc.php
$links['forum'] = 'https://forum.opencaching.pl';

// former $config['geokrety_url']
$links['geokrety'] = 'https://geokrety.org';

$_wiki = 'https://wiki.opencaching.pl';
$links['wiki'] = [
    'main'              => $_wiki,
    'rules'             => $_wiki.'/index.php/Regulamin_OC_PL',
    'rules_en'          => $_wiki.'/index.php/OC_PL_Conditions_of_Use',
    'cacheParams'       => $_wiki.'/index.php/Parametry_skrzynki',
    'cacheParams_en'    => $_wiki.'/index.php/Cache_parameters',
    'ratingDesc'        => $_wiki.'/index.php/Oceny_skrzynek',
    'ratingDesc_en'     => $_wiki.'/index.php/Cache_rating',
    'forBeginers'       => $_wiki.'/index.php/Dla_pocz%C4%85tkuj%C4%85cych',
    'placingCache'      => $_wiki.'/index.php/Zak%C5%82adanie_skrzynki',
    'makingCaches'      => $_wiki.'/index.php/Jakość_skrzynki',
    'makingRoutes'      => $_wiki.'/index.php/Moje_trasy',
    'cacheQuality'      => $_wiki.'/index.php/Jako%C5%9B%C4%87_skrzynki',
    'myRoutes'          => $_wiki.'/index.php/Moje_trasy',
    'cacheNotes'        => $_wiki.'/index.php/Notatki_skrzynki',
    'additionalWaypoints' => $_wiki.'/index.php/Dodatkowe_waypointy_w_skrzynce',
    'cachingCode'       => $_wiki.'/index.php/Kodeks_geocachera',
    'usefulFiles'       => $_wiki.'/index.php/U%C5%BCyteczne_pliki_zwi%C4%85zane_z_OC_PL',
    'ocSiteRules'       => $_wiki.'/index.php/Zasady_funkcjonowania_Serwisu_OC_PL',
    'cacheTypes'        => $_wiki.'/index.php/Typ_skrzynki',
    'cacheAttrib'       => $_wiki.'/index.php/Parametry_skrzynki#Atrybuty_skrzynki',
    'cacheAttrib_en'    => $_wiki.'/index.php/Cache_parameters#Attributes',
    'cacheLogPass'      => $_wiki.'/index.php/Parametry_skrzynki#Has.C5.82o_do_wpisu_do_Logu',
    'cacheLogPass_en'   => $_wiki.'/index.php/Cache_parameters#Log_password',
    'geoPaths'          => $_wiki.'/index.php/Geo%C5%9Bcie%C5%BCka',
];



