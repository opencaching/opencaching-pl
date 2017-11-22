<?php
/**
 * Thee are pl node overrides of external links config used in many places in the OC code
 * for example in menu, descriptions (translations) etc.
 *
 * This is configuration for OCNL node only and contains only overrided values
 * from links.default.php.
 *
 */


//former $blogsite_url from settings.inc.php
$links['blog'] = 'http://blog.opencaching.nl';

//former $forum_url from settings.inc.php
$links['forum'] = 'http://forum.opencaching.nl';

// former $config['geokrety_url']
$links['geokrety'] = 'https://geokrety.org';

/**
 * This is local base for wiki links
 * (former $wiki_url in /lib/setting.inc.php)
 */
$_wiki  = 'http://wiki.opencaching.nl';

$links['wiki']['main'] = $_wiki;

$links['wiki']['additionalWaypoints']   = $_wiki.'/index.php/Extra_waypointen';
$links['wiki']['cacheAttrib']           = $_wiki.'/index.php/Cache_eigenschappen#Cache_attributen';
$links['wiki']['cacheAttrib_en']        = $_wiki.'/index.php/Cache_parameters#Cache_attributes';
$links['wiki']['cacheLogPass']          = $_wiki.'/index.php/Cache_eigenschappen#Log_wachtwoord';
$links['wiki']['cacheLogPass_en']       = $_wiki.'/index.php/Cache_parameters#Log_password';
$links['wiki']['cacheNotes']            = $_wiki.'/index.php/Persoonlijk_cache_notities';
$links['wiki']['cacheParams']           = $_wiki.'/index.php/Cache_eigenschappen';
$links['wiki']['cacheParams_en']        = $_wiki.'/index.php/Cache_parameters';
$links['wiki']['cacheTypes']            = $_wiki.'/index.php/Cache_eigenschappen#Cache_soort';
$links['wiki']['cachingCode']           = $_wiki.'/index.php/Gedragscode';
$links['wiki']['forBeginers']           = $_wiki.'/index.php/Beginnen_met_Geocaching';
$links['wiki']['geoPaths']              = $_wiki.'/index.php/GeoPath';
$links['wiki']['myRoutes']              = $_wiki.'/index.php/Mijn_routes';
$links['wiki']['placingCache']          = $_wiki.'/index.php/Richtlijnen_en_attentiepunten_bij_het_plaatsen_van_een_cache';
$links['wiki']['ratingDesc']            = $_wiki.'/index.php/Cache_beoordeling';
$links['wiki']['ratingDesc_en']         = $_wiki.'/index.php/Cache_rating';
$links['wiki']['rules']                 = $_wiki.'/index.php/Gebruikersvoorwaarden';
$links['wiki']['rules_en']              = $_wiki.'/index.php/Terms_of_Use';

// optional items - used in node-specific menu only
$links['wiki']['downloads']             = $_wiki.'/index.php/Downloads';
$links['wiki']['history']               = $_wiki.'/index.php/Historie';
