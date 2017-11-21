<?php
/**
 * Thee are pl node overrides of external links config used in many places in the OC code
 * for example in menu, descriptions (translations) etc.
 *
 * This is configuration for OCRO node only and contains only overrided values
 * from links.default.php.
 *
 */


//former $blogsite_url from settings.inc.php
$links['blog'] = 'http://blog.opencaching.ro';

//former $forum_url from settings.inc.php
$links['forum'] = null; // no-link

// former $config['geokrety_url']
$links['geokrety'] = 'https://geokrety.org/';

/**
 * This is local base for wiki links
 * (former $wiki_url in /lib/setting.inc.php)
 */
$_wiki = 'http://wiki.opencaching.ro';

// former $wikiLinks
$links['wiki']['main'] = $_wiki;

$links['wiki']['main'] = $_wiki;

$links['wiki']['additionalWaypoints']   = $_wiki.'/index.php/Dodatkowe_waypointy_w_skrzynce';
$links['wiki']['cacheAttrib']           = $_wiki.'/index.php/Parametry_skrzynki#Atrybuty_skrzynki';
$links['wiki']['cacheAttrib_en']        = $_wiki.'/index.php/Cache_parameters#Attributes';
$links['wiki']['cacheLogPass']          = $_wiki.'/index.php/Parametry_skrzynki#Has.C5.82o_do_wpisu_do_Logu';
$links['wiki']['cacheLogPass_en']       = $_wiki.'/index.php/Cache_parameters#Log_password';
$links['wiki']['cacheNotes']            = $_wiki.'/index.php/Notatki_skrzynki';
$links['wiki']['cacheParams']           = $_wiki.'/index.php/Parametry_skrzynki';
$links['wiki']['cacheParams_en']        = $_wiki.'/index.php/Cache_parameters';
$links['wiki']['cacheTypes']            = $_wiki.'/index.php/Typ_skrzynki';
$links['wiki']['cachingCode']           = $_wiki.'/index.php/Kodeks_geocachera';
$links['wiki']['forBeginers']           = $_wiki.'/index.php/Dla_pocz%C4%85tkuj%C4%85cych';
$links['wiki']['geoPaths']              = $_wiki.'/index.php/Geo%C5%9Bcie%C5%BCka';
$links['wiki']['myRoutes']              = $_wiki.'/index.php/Moje_trasy';
$links['wiki']['placingCache']          = $_wiki.'/index.php/Zak%C5%82adanie_skrzynki';
$links['wiki']['ratingDesc']            = $_wiki.'/index.php/Oceny_skrzynek';
$links['wiki']['ratingDesc_en']         = $_wiki.'/index.php/Cache_rating';
$links['wiki']['rules']                 = $_wiki.'/index.php/Regulamin_OC_PL';
$links['wiki']['rules_en']              = $_wiki.'/index.php/OC_PL_Conditions_of_Use';

// optional items - used in node-specific menu only
$links['wiki']['downloads']             = $_wiki.'/index.php/U%C5%BCyteczne_pliki_zwi%C4%85zane_z_OC_PL';

