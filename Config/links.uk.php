<?php
/**
 * Thee are UK node overrides of external links config used in many places in the OC code
 * for example in menu, descriptions (translations) etc.
 *
 * This is configuration for OCUK node only and contains only overrided values
 * from links.default.php.
 *
 */

//former $blogsite_url from settings.inc.php
$links['blog'] = null; //there is no blog

//former $forum_url from settings.inc.php
$links['forum'] = null;

// former $config['geokrety_url']
$links['geokrety'] = 'https://geokrety.org/index.php?lang=en_EN.UTF-8';

/**
 * This is local base for wiki links
 * (former $wiki_url in /lib/setting.inc.php)
 */
$_wiki = 'https://wiki.opencache.uk';

$links['wiki']['main'] = $_wiki;

// overrides of defaults

$links['wiki']['additionalWaypoints']   = $_wiki.'/index.php/Cache_Parameters#Additional_waypoints';
$links['wiki']['cacheAttrib']           = $_wiki.'/index.php/Cache_Attributes';
$links['wiki']['cacheAttrib_en']        = $_wiki.'/index.php/Cache_Attributes';
$links['wiki']['cacheLogPass']          = $_wiki.'/index.php/Cache_Log_Passwords';
$links['wiki']['cacheLogPass_en']       = $_wiki.'/index.php/Cache_Log_Passwords';
$links['wiki']['cacheNotes']            = $_wiki.'/index.php/Cache_Notes';
$links['wiki']['cacheParams']           = $_wiki.'/index.php/Cache_Parameters';
$links['wiki']['cacheParams_en']        = $_wiki.'/index.php/Cache_Parameters';
$links['wiki']['cacheTypes']            = $_wiki.'/index.php/Cache_Types';
$links['wiki']['cachingCode']           = $_wiki.'/index.php/OC_UK_Mission_statement';
$links['wiki']['forBeginers']           = $_wiki.'/index.php/Main_Page';
$links['wiki']['geoPaths']              = $_wiki.'/index.php/GeoPath';
$links['wiki']['myRoutes']              = $_wiki.'/index.php/My_Routes';
$links['wiki']['placingCache']          = $_wiki.'/index.php/How_to_create_a_cache';
$links['wiki']['ratingDesc']            = $_wiki.'/index.php/Cache_Ratings';
$links['wiki']['ratingDesc_en']         = $_wiki.'/index.php/Cache_Ratings';
$links['wiki']['rules']                 = $_wiki.'/index.php/Terms_of_Use_OC_UK';
$links['wiki']['rules_en']              = $_wiki.'/index.php/Terms_of_Use_OC_UK';

/**
 * additional links - not used in main code - used only in node-custom menu
 */
$links['wiki']['downloads']             = $_wiki.'/index.php/Downloads';
$links['wiki']['history']               = $_wiki.'/index.php/Main_Page';
$links['wiki']['impressum']             = $_wiki.'/index.php/Main_Page';
