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



// former $wiki_url
$_wiki  = 'http://wiki.opencaching.nl';

// former $wikiLinks
$links['wiki'] = array(
    'main'  => $_wiki.'/index.php',
    'rules' => $_wiki.'/index.php/Gebruikersvoorwaarden',
    'rules_en' => $_wiki.'/index.php/Terms_of_Use',
    'cacheParams' => $_wiki.'/index.php/Cache_eigenschappen',
    'ratingDesc' => $_wiki.'/index.php/Cache_beoordeling',
    'ratingDesc_en' => $_wiki.'/index.php/Cache_rating',
    'forBeginers' => $_wiki.'/index.php/Beginnen_met_Geocaching',
    'placingCache' => $_wiki.'/index.php/Richtlijnen_en_attentiepunten_bij_het_plaatsen_van_een_cache',
    'myRoutes' => $_wiki.'/index.php/Mijn_routes',
    'cacheNotes' => $_wiki.'/index.php/Persoonlijk_cache_notities',
    'additionalWaypoints' => $_wiki.'/index.php/Extra_waypointen',
    'cachingCode' => $_wiki.'/index.php/Gedragscode',
    'cacheTypes' => $_wiki.'/index.php/Cache_eigenschappen#Cache_soort',
    'cacheTypes_en' => $_wiki.'/index.php/Cache_parameters#Cache_type',
    'cacheAttrib' => $_wiki.'/index.php/Cache_eigenschappen#Cache_attributen',
    'cacheAttrib_en' => $_wiki.'/index.php/Cache_parameters#Cache_attributes',
    'cacheLogPass' => $_wiki.'/index.php/Cache_eigenschappen#Log_wachtwoord',
    'cacheLogPass_en' => $_wiki.'/index.php/Cache_parameters#Log_password',
    //Optional item
    'downloads' => $_wiki.'/index.php/Downloads',
);




