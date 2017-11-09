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
$links['geokrety'] = 'https://geokrety.org/index.php?lang=en_EN.UTF-8';

// former $wiki_url
$_wiki = 'https://wiki.opencache.uk';

// former $wikiLinks
$links['wiki'] = [
    'rules' => $_wiki.'/index.php/Terms_of_Use_OC_UK',
    'rules_en' => $_wiki.'/index.php/Terms_of_Use_OC_UK',
    'cachingCode' => $_wiki.'/index.php/OC_UK_Mission_statement',
    'placingCache' => $_wiki.'/index.php/How_to_create_a_cache',
    'makingRoutes' => $_wiki.'/index.php/My_Routes',
    'myRoutes' => $_wiki.'/index.php/My_Routes',
    'usefulFiles' => $_wiki.'/index.php/Downloads',
    'downloads' => $_wiki.'/index.php/Downloads',
    'cacheTypes' => $_wiki.'/index.php/Cache_Types',
    'cacheParams' => $_wiki.'/index.php/Cache_Parameters',
    'cacheParams_en' => $_wiki.'/index.php/Cache_Parameters',
    'cacheAttrib' => $_wiki.'/index.php/Cache_Attributes',
    'cacheAttrib_en' => $_wiki.'/index.php/Cache_Attributes',
    'cacheLogPass' => $_wiki.'/index.php/Cache_Log_Passwords',
    'cacheLogPass_en' => $_wiki.'/index.php/Cache_Log_Passwords',
    'cacheNotes' => $_wiki.'/index.php/Cache_Notes',
    'history' => $_wiki.'/index.php/Main_Page',
    'impressum' => $_wiki.'/index.php/Main_Page',
];
