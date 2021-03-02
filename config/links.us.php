<?php

/**
 * Configuration of external links
 *
 * Those are configuration overrides for OCUS node only.
 */

// former $blogsite_url from settings.inc.php
$links['blog'] = 'https://blog.opencaching.us';

// former $forum_url from settings.inc.php
$links['forum'] = 'https://forum.opencaching.us';

// former $config['geokrety_url']
$links['geokrety'] = 'https://geokrety.org';

/**
 * This is local base for wiki links
 * (former $wiki_url in /lib/setting.inc.php)
 */
$_wiki  = 'https://wiki.opencaching.us';

$links['wiki']['main'] = $_wiki;

$links['wiki']['additionalWaypoints'] = $_wiki . '/index.php/Additional_waypoints';
$links['wiki']['cacheAttrib'] = $_wiki . '/index.php/Cache_parameters#Cache_attributes';
$links['wiki']['cacheLogPass'] = $_wiki . '/index.php/Cache_parameters#Log_password';
$links['wiki']['cacheNotes'] = $_wiki . '/index.php/Cache_notes';
$links['wiki']['cacheParams'] = $_wiki . '/index.php/Cache_parameters';
$links['wiki']['cacheTypes'] = $_wiki . '/index.php/Cache_parameters#Cache_type';
//$links['wiki']['cachingCode'] = $_wiki . '/index.php/';
$links['wiki']['forBeginers'] = $_wiki . '/index.php/Getting_Started';
$links['wiki']['geoPaths'] = $_wiki . '/index.php/GeoPaths';
//$links['wiki']['myRoutes'] = $_wiki . '/index.php/';
$links['wiki']['placingCache'] = $_wiki . '/index.php/Cache_Placement_Guidelines';
$links['wiki']['ratingDesc'] = $_wiki . '/index.php/Cache_rating';
$links['wiki']['rules'] = $_wiki . '/index.php/Terms_of_Use';

// optional items - used in node-specific menu only
$links['wiki']['downloads'] = $_wiki . '/index.php/Downloads';
//$links['wiki']['history'] = $_wiki . '/index.php/Historie';
//$links['wiki']['privacyPolicy'] = $_wiki . '/index.php/Privacyverklaringen';
