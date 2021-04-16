<?php

require __DIR__ . '/settingsDefault.inc.php';

//Replace localhost to you own domain site

// define timezone (newer versions of PHP require it)
date_default_timezone_set('Europe/Warsaw');

// country-id of the running node: pl|ro|nl...
$config['ocNode'] = 'pl';


//OC Waypoint for your site for example OX
$GLOBALS['oc_waypoint'] = 'OP';

//name of the cookie
$config['cookie']['name'] = 'oc';
$config['cookie']['path'] = '/';
$config['cookie']['domain'] = '.localhost';

//block register new cache before first find xx nuber caches value -1 off this feature
$NEED_FIND_LIMIT = 10;

$NEED_APPROVE_LIMIT = 3;

//if you are running this site on a other domain than staging.opencaching.de, you can set
//this in private_db.inc.php, but don't forget the ending /
$absolute_server_URI = '//localhost/';
//If your server has another URI than OKAPI (i.e. OC server uses https, but OKAPI http only)
//then you can set $OKAPI_server_URI to another than $absolute_server_URI address.
//$OKAPI_server_URI = 'http://localhost/';


// location for dynamically generated files
$dynbasepath = '/var/www/ocpl-data/';

// location of cache mp3 files
if (!isset($mp3dir))
    $mp3dir = $dynbasepath . 'mp3';
if (!isset($mp3url))
    $mp3url = '//localhost/mp3';

// maximal size of mp3 for PodCache 5 Mb ?
if (!isset($maxmp3size))
    $maxmp3size = 5000000;

// allowed extensions of images
if (!isset($mp3extensions))
    $mp3extensions = ';mp3;';

//local database settings
$dbserver = 'localhost';
$dbname = 'ocpl';
$dbusername = 'ocdbu';
$dbpasswd = 'PassworD';

$opt['db']['admin_username'] = 'ocdbua';        //username for automatic DB updates
$opt['db']['admin_password'] = 'AdminPassword';


// Your own Google map API key
$googlemap_key = "";

//Links to blog page on oc site
//NOT-USED: $blogsite_url = 'http://blog.opencaching.pl';

//links to forum page on oc site
//NOT-USED: $forum_url = 'http://forum.opencaching.pl';

//links to wiki page on oc site
// they are available in tpl files under {wiki_link_<name>}, i.e. {wiki_link_forBeginers}
// protocol agnostic links - just for fun
//NOT-USED: $wiki_url  = '//wiki.opencaching.pl';

/* NOT-USED: $wikiLinks = array(
    'main'  => $wiki_url,
    'rules' => $wiki_url.'/index.php/Regulamin_OC_PL',
    'rules_en' => $wiki_url.'/index.php/OC_PL_Conditions_of_Use',
    'cacheParams' => $wiki_url.'/index.php/Parametry_skrzynki',
    'cacheParams_en' => $wiki_url.'/index.php/Cache_parameters',
    'ratingDesc' => $wiki_url.'/index.php/Oceny_skrzynek',
    'ratingDesc_en' => $wiki_url.'/index.php/Cache_rating',
    'forBeginers' => $wiki_url.'/index.php/Dla_pocz%C4%85tkuj%C4%85cych',
    'placingCache' => $wiki_url.'/index.php/Zak%C5%82adanie_skrzynki',
    'makingCaches' => $wiki_url.'/index.php/Jakość_skrzynki',
    'makingRoutes' => $wiki_url.'/index.php/Moje_trasy',
    'cacheQuality' => $wiki_url.'/index.php/Jako%C5%9B%C4%87_skrzynki',
    'myRoutes' => $wiki_url.'/index.php/Moje_trasy',
    'cacheNotes' => $wiki_url.'/index.php/Notatki_skrzynki',
    'additionalWaypoints' => $wiki_url.'/index.php/Dodatkowe_waypointy_w_skrzynce',
    'cachingCode' => $wiki_url.'/index.php/Kodeks_geocachera',
    'usefulFiles' => $wiki_url.'/index.php/U%C5%BCyteczne_pliki_zwi%C4%85zane_z_OC_PL',
    'ocSiteRules' => $wiki_url.'/index.php/Zasady_funkcjonowania_Serwisu_OC_PL',
    'cacheTypes' => $wiki_url.'/index.php/Typ_skrzynki',
    'cacheAttrib' => $wiki_url.'/index.php/Parametry_skrzynki#Atrybuty_skrzynki',
    'cacheAttrib_en' => $wiki_url.'/index.php/Cache_parameters#Attributes',
    'cacheLogPass' => $wiki_url.'/index.php/Parametry_skrzynki#Has.C5.82o_do_wpisu_do_Logu',
    'cacheLogPass_en' => $wiki_url.'/index.php/Cache_parameters#Log_password',
    // optional item
    'downloads' => $wiki_url.'/index.php/U%C5%BCyteczne_pliki_zwi%C4%85zane_z_OC_PL',
);
*/

// DO NOT use it - used only OCRO translations now - used wiki links in /config/links*
$cache_params_url = 'http://wiki.opencaching.pl/index.php/Parametry_skrzynki';

// Contact data definition START
/*
  Possible array entries are listed below. All the entries are optional.
  + groupName
  HTML header with a group name. Group name can be either raw, html code;
  or a reference to the translation file.
  + emailAddress
  E-mail address, which will be printed just below the groupName.
  + groupDescription
  Group description is an actual text of the group, which is placed under the groupName
  and e-mail. This entry can be in one of the following types/formats:
  - an array - if so, each array entry is processed as one of those two types below;
  - raw, html code;
  - reference to the translation file.
  + subgroup
  A nested array of the same structure. HTML headers for nested groups
  are one level lower.
  + other_keys
  They are used to substitute {other_keys} references in both groupName and
  groupDescription. Those keys do not propagate to subgroups.

 */

// Configuration for OC.PL contact page
// Translated to Polish and English only :/
$contactDataPL = array(
    array(
        'groupName' => 'contact_pl_about_title',
        'groupDescription' => array(
            'contact_pl_about_description_1',
            'contact_pl_about_description_2'
        )
    ),
    array(
        'groupName' => 'OpenCaching PL Team',
        'subgroup' => array(
            array(
                'groupName' => 'Rada Rejsu',
                'groupDescription' => 'contact_pl_rr_description',
                'emailAddress' => 'rr at opencaching.pl',
                'link' => 'http://forum.opencaching.pl/viewtopic.php?f=19&t=6297'
            ),
            array(
                'groupName' => 'Rada Techniczna',
                'groupDescription' => 'contact_pl_rt_description',
                'emailAddress' => 'rt at opencaching.pl',
                'link' => 'https://code.google.com/p/opencaching-pl/people/list'  # No longer valid!
            ),
            array(
                'groupName' => 'Centrum Obsługi Geocachera',
                'groupDescription' => 'contact_pl_cog_description',
                'emailAddress' => 'cog at opencaching.pl',
                'link' => 'http://forum.opencaching.pl/viewtopic.php?f=19&t=6297'
            ),
        ),
    ),
    array(
        'groupName' => 'contact_pl_other_title',
        'groupDescription' => 'contact_pl_other_description'
    ),
    array(
        'groupName' => 'contact_ocpl_title',
        'groupDescription' => array(
            'contact_ocpl_description_1',
            'contact_ocpl_description_2',
            'contact_ocpl_description_3',
        )
    )
);

// Configuration from OC.DE contact page
// This is only a template, to be translated/updated for OC.NL
$contactDataDE = array(
    array(
        'groupName' => 'Allgemeine Fragen zu Opencaching.de und zum Thema Geocaching',
        'groupDescription' => array(
            'Für Fragen rund um Opencaching und zum Thema Geocaching ist das <a href="http://wiki.opencaching.de/">Opencaching-Wiki</a> eine gute Anlaufstelle. Weitere Informationen zum Geocaching gibt es auf <a href="http://www.geocaching.de">www.geocaching.de</a>.',
            'Wenn du ein spezielles Problem hast und darauf keine Antwort findest, kannst du dir unter <a href="http://forum.opencaching-network.org">forum.opencaching-network.org</a> ein passendes Forum raussuchen und dich dort erkundigen.'
        )
    ),
    array(
        'groupName' => 'Bedienung der Website, Anregungen und Kritik',
        'groupDescription' => 'Hierfür gibt es ein eigenes Unterforum auf <a href="http://forum.opencaching-network.org/index.php?board=33.0">forum.opencaching-network.org</a>. Dort findest du auch weitere Informationen, falls du in unserem Team mitmachen möchtest.'
    ),
    array(
        'groupName' => 'Sonstiges',
        'groupDescription' => array(
            'Sollten die oben genannten Möglichkeiten nicht ausreichen oder die Betreiber von <i>opencaching.de</i> direkt kontaktiert werden, kannst du auch eine Email an <a href="mailto:contact@opencaching.de">contact@opencaching.de</a> schreiben.',
            'Bitte werde nicht ungeduldig wenn nicht sofort eine Antwort kommt, <i>opencaching.de</i> wird von Freiwilligen betreut, die leider nicht immer und sofort zur Verfügung stehen können.',
        )
    )
);
//
$contactData = $contactDataPL;
// Contact data definition END

// Configuration of license link at footer
// You can select license and generate HTML at https://creativecommons.org/choose/
$config['license_html'] = '<a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-sa/4.0/88x31.png" /></a>';

// Show date and date/time correct way.
$dateFormat = 'Y-m-d';
$datetimeFormat = 'Y-m-d H:i';


// map of garmin keys,
// key: domain name, value: garmin key value
// the map may contain only one entry
$config['garmin-key'] = array(
        'http://opencaching.pl' => '0fe1300131fcc0e417bb04de798c5acf',
        'http://www.opencaching.nl' => 'b01f02cba1c000fe034471d2b08044c6'
);

/* ************************************************************************
 * Cache page mini map
 * ************************************************************************ */

/* Cache page small map, fixed, clickable to open minimap.                  */
// available options are roadmap, terrain, map, satellite, hybrid
$config['maps']['cache_page_map']['layer'] = 'terrain';
$config['maps']['cache_page_map']['zoom'] = 8;

// available source for osm static map: mapnik,cycle, sterrain, stoner
$config['maps']['main_page_map']['source'] = 'mapnik';


// Configuration of feeds displayed on the main page
$config['feed']['enabled'] = array('forum', 'blog');    // This array defines which feeds to display and in what order.
                                                        // You can increase feeds number,
                                                        // but remember to add feed description as feed_{feedname} to language files
$config['feed']['forum']['url'] = 'https://forum.opencaching.pl/feed.php';  // URL of the feed. System supports RSS and Atom feeds.
$config['feed']['forum']['posts'] = 5;  // How many newest posts to display
$config['feed']['forum']['showAuthor'] = true;  // Do display author of post?
$config['feed']['blog']['url'] = 'http://blog.opencaching.pl/feed/atom/';
$config['feed']['blog']['posts'] = 5;
$config['feed']['blog']['showAuthor'] = true;
