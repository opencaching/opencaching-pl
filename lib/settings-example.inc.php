<?php

require __DIR__ . '/settingsDefault.inc.php';

//Replace localhost to you own domain site

// define timezone (newer versions of PHP require it)
date_default_timezone_set('Europe/Warsaw');

//relative path to the root directory
if (!isset($rootpath))
    $rootpath = './';

//default used language
if (!isset($lang))
    $lang = 'pl';

//default used style
if (!isset($style))
    $style = 'stdstyle';

//pagetitle
if (!isset($pagetitle))
    $pagetitle = 'Geocaching Opencaching Polska';

//site name
$site_name = 'localhost';

// Used OC number nodes and name of waypoints :
// 1 Opencaching Germany http://www.opencaching.de OC
// 2 Opencaching Poland http://www.opencaching.pl OP
// 3 Opencaching Czech http://www.opencaching.cz OZ
// 4 Local Development AA
// 5 free
// 6 Opencaching Great Britain http://www.opencaching.org.uk OK
// 7 Opencaching Sweden http://www.opencaching.se OS =>OC Scandinavia
// 8 free
// 9 free
// 10 Opencaching United States http://www.opencaching.us OU
// 11 free
// 12 Opencaching Russia http://www.opencaching.org.ru  (I don't know current status???)
// 14 Opencaching Nederland http://www.opencaching.nl OB => OC Benelux
// 16 Opencaching Romania http://www.opencaching.ro OR
//
//id of the node 4 for local development
$oc_nodeid = 4;

//OC Waypoint for your site for example OX
$GLOBALS['oc_waypoint'] = 'OP';

//name of the cookie
$opt['cookie']['name'] = 'oc';
$opt['cookie']['path'] = '/';
$opt['cookie']['domain'] = '.localhost';

//name of the cookie
if (!isset($cookiename))
    $cookiename = 'oc';
if (!isset($cookiepath))
    $cookiepath = '/';
if (!isset($cookiedomain))
    $cookiedomain = '.locahost';

// Coordinates hidden for not-logged-ins?
global $hide_coords;
$hide_coords = false;

// scores range
$MIN_SCORE = 0;
$MAX_SCORE = 4;

// display online users on footer pages off=0 on=1
$onlineusers = 1;
// wlaczenie blokady liczby max zakladanych skrzynek typu owncache (by Marcin stryker)
$GLOBALS['owncache_limit'] = '1';


//block register new cache before first find xx nuber caches value -1 off this feature
$NEED_FIND_LIMIT = 10;

$NEED_APPROVE_LIMIT = 3;

//Debug?
if (!isset($debug_page))
    $debug_page = false;
if (!isset($debug))
    $debug = false;


//site in service? Set to false when doing bigger work on the database to prevent error's
if (!isset($site_in_service))
    $site_in_service = true;

//if you are running this site on a other domain than staging.opencaching.de, you can set
//this in private_db.inc.php, but don't forget the ending /
$absolute_server_URI = '//localhost/';
//If your server has another URI than OKAPI (i.e. OC server uses https, but OKAPI http only)
//then you can set $OKAPI_server_URI to another than $absolute_server_URI address.
//$OKAPI_server_URI = 'http://localhost/';

// EMail address of the sender
if (!isset($emailaddr))
    $emailaddr = 'noreply@localhost';

// location for dynamically generated files
$dynbasepath = '/var/www/ocpl-data/';
$dynstylepath = $dynbasepath . 'tpl/stdstyle/html/';

// location of cache images
if (!isset($picdir))
    $picdir = $dynbasepath . 'images/uploads';
if (!isset($picurl))
    $picurl = '//localhost/images/uploads';

// Thumbsize
$thumb_max_width = 175;
$thumb_max_height = 175;
// Small thumbsize
$thumb2_max_width = 64;
$thumb2_max_height = 64;

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



// default coordinates for cachemap, set to your country's center of gravity
$country_coordinates = "52.5,19.2";
// zoom at which your whole country/region is visible
$default_country_zoom = 6;

// Main page map parameters (customize as needed)
$main_page_map_center_lat = 52.13;
$main_page_map_center_lon = 19.20;
$main_page_map_zoom = 5;
$main_page_map_width = 250;
$main_page_map_height = 260;

// maximal size of images
if (!isset($maxpicsize))
    $maxpicsize = 152400;

// allowed extensions of images
if (!isset($picextensions))
    $picextensions = ';jpg;jpeg;gif;png;';

// news settings
$use_news_approving = true;
$news_approver_email = 'rr@localhost';

//local database settings
$dbpconnect = false;
$dbserver = 'localhost';
$dbname = 'ocpl';
$dbusername = 'ocdbu';
$dbpasswd = 'PassworD';
$opt['db']['server'] = $dbserver;
$opt['db']['name'] = $dbname;
$opt['db']['username'] = $dbusername;
$opt['db']['password'] = $dbpasswd;


$tmpdbname = 'test';

// warnlevel for sql-execution
$sql_errormail = 'rt@localhost';
$sql_warntime = 1;

// replacements for sql()
$sql_replacements['db'] = $dbname;
$sql_replacements['tmpdb'] = 'test';

// safemode_zip-binary
$safemode_zip = '/var/www/ocpl/bin/phpzip.php';
$zip_basedir = $dynbasepath . 'download/zip/';
$zip_wwwdir = '/download/zip/';

// Your own Google map API key
$googlemap_key = "";
$googlemap_type = "G_MAP_TYPE"; // alternativ: _HYBRID_TYPE
$googleAnalytics_key = '';      // google Analytics key: UA-XXXXX-Y - if not set google analytics will not be used

$dberrormail = 'rt@localhost';

$cachemap_mapper = "lib/mapper_okapi.php";

//Links to blog page on oc site
$blogsite_url = 'http://blog.opencaching.pl';

//links to forum page on oc site
$forum_url = 'http://forum.opencaching.pl';

//links to wiki page on oc site
// they are available in tpl files under {wiki_link_<name>}, i.e. {wiki_link_forBeginers}
// protocol agnostic links - just for fun
$wiki_url  = '//wiki.opencaching.pl';
$wikiLinks = array(
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

$rules_url = 'http://wiki.opencaching.pl/index.php/Regulamin_OC_PL';
$cache_params_url = 'http://wiki.opencaching.pl/index.php/Parametry_skrzynki';
$rating_desc_url = 'http://wiki.opencaching.pl/index.php/Oceny_skrzynek';

$contact_mail = 'ocpl (at) localhost';
// E-mail address group of people from OC Team who solve problems, verify cache
$octeam_email = 'cog@localhost';

// name of the sender for user-to-user notofications
$mailfrom = 'opencaching.pl';
$mailfrom_noreply = 'noreply@opencaching.pl';

// signature of e-mails send by system
$octeamEmailsSignature = "Pozdrawiamy, Zespół www.opencaching.pl";

// watchlist config:
$watchlistMailfrom = 'watch@opencaching.pl';

// email of GeoKrety developer (used in GeoKretyApi.php for error notifications)
$geoKretyDeveloperEmailAddress = 'stefaniak@gmail.com';

// New caches outside country where server is:
$SiteOutsideCountryString = 'poland_outside';
$countryParamNewcacherestPhp = " 'PL' ";

/* power Trail module switch and settings */

// true - swithed on; false - swithed off
$powerTrailModuleSwitchOn = true;

// minimum cache count for power trail to be public displayed
// (PT having less than $powerTrailMinimumCacheCount ) are visible only to owners.
$powerTrailMinimumCacheCount = array(
    'current' => 25,
    'old' => array(
        1 => array(
            'dateFrom' => '1970-01-01 01:00',
            'dateTo' => '2013-10-29 23:59:59',
            'limit' => 5,
        ),
// if limit change in future, just uncomment and place here current limit and period of time
//              2 => array (
//                  'dateFrom' => '2013-10-30 00:00:00',
//                  'dateTo' => '20??-??-?? 23:59:59',
//                  'limit' => 25,
//              ),
    ),
);


// minimum cahes Found count of user, to alow user set new Power Trail
// user who found less than $powerTrailUserMinimumCacheFoundToSetNewPowerTrail can't create new PT
$powerTrailUserMinimumCacheFoundToSetNewPowerTrail = 500;

// link to FAQ/info of power trail module
$powerTrailFaqLink = 'http://info.opencaching.pl/node/13';

/* end of power Trail module switch and settings */

// enables/disables linkage to blog in index.php
$BlogSwitchOn = true;

// enable detailed cache access logging
//$enable_cache_access_logs = true;

// OC specific email addresses for international use - here version for OC.PL.
$mail_cog = 'cog@opencaching.pl';   // OCPL: reviewers and regional service for cachers
$mail_rt = 'rt@opencaching.pl';     // OCPL: technical contact
$mail_rr = 'rr@opencaching.pl';     // OCPL: unused; former RR - to remove
$mail_oc = 'ocpl@opencaching.pl';   // OCPL: general contact


//Short sitename for international use.
$short_sitename = 'OC PL';

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

/*
 * Bottom menu
 * See settingsDefault.inc.php for default values
 */
// You can enable menu item by setting ['link'] and ['visible'] - for example:
$config['bottom_menu']['impressum']['link'] = 'https://wiki.opencaching.pl/index.php/Opencaching_PL';
$config['bottom_menu']['impressum']['visible'] = true;
// You can also use your configured Wiki links:
$config['bottom_menu']['history']['link'] = $wikiLinks['history'];
$config['bottom_menu']['history']['visible'] = true;
// You can disable single menu item:
$config['bottom_menu']['main_page']['visible'] = false;
// Or you can even add menu item. But remember - second index is a position from language file (/lib/languages/??.php)
// and should be added to translation files first (in below case - 'guides').
$config['bottom_menu']['guides']['link'] = '/cacheguides.php';
$config['bottom_menu']['guides']['visible'] = true;

// Show date and date/time correct way.
$dateFormat = 'Y-m-d';
$datetimeFormat = 'Y-m-d H:i';

$defaultCountryList = array("AT", "BE", "BY", "BG", "HR", "CZ", "DK", "EE", "FI", "FR", "GR", "ES", "NL", "IE", "LT", "MD", "DE", "NO", "PL", "PT", "SU", "RO", "SK", "SI", "CH", "SE", "TR", "UA", "IT", "HU", "GB",);

/**
 * Configuration for map v3 maps
 *
 * Two dimensional array:
 *
 * * first dimension
 * KEYS - internal names
 *
 * * second dimension
 * KEYS:
 *  - hidden: boolean attribute to hide the map entirerly, without removing it from config
 *  - showOnlyIfMore: show this map item only in large views (like full screen)
 *  - attribution: the HTML snippet that will be shown in bottom-right part of the map
 *  - imageMapTypeJS: the complete JS expression returning instance of google.maps.ImageMapType,
 *      if set, not other properties below will work
 *  - name: the name of the map
 *  - tileUrl: URL to the tile, may contain following substitutions
 *      - {z} - zoom, may include shifts, in form of i.e. {z+1}, {z-3}
 *      - {x}, {y} - point coordinates
 *  - tileUrlJS: the complete JS expression returning function for tileUrl retrieval,
 *      if set, tileUrl property will not work
 *  - tileSize: the tile size, either in form of WIDTHxHEIGHT, i.e. 256x128, or complete
 *      JS expression returning instance of google.maps.Size
 *  - maxZoom: maximum zoom available
 *  - minZoom: minimum zoom available
 *
 * Other keys, will be passed as is, given that
 *  - numerical and boolean values are passed as is to JS
 *  - other types are passed as strings, unless they start with raw: prefix. In that case,
 *      they are passed as JS expressions
 */

$mapsConfig = array(
    'OSMapa' => array(
        'attribution' => '&copy; <a href="//www.openstreetmap.org/" target="_blank">OpenStreetMap</a> contributors <a href="//creativecommons.org/licenses/by-sa/2.0/" target="_blank">CC BY-SA</a> | Hosting:<a href="http://trail.pl/" target="_blank">trail.pl</a> i <a href="http://centuria.pl/" target="_blank">centuria.pl</a>',
        'name' => 'OSMapa',
        'tileUrl' => 'http://tile.openstreetmap.pl/osmapa.pl/{z}/{x}/{y}.png',
        'maxZoom' => 18,
        'tileSize' => '256x256',
    ),
    'OSM' => array(
        'name' => 'OSM',
        'attribution' => '&copy; <a href="//www.openstreetmap.org/" target="_blank">OpenStreetMap</a> contributors <a href="//creativecommons.org/licenses/by-sa/2.0/" target="_blank">CC BY-SA</a>',
        'tileUrl' => 'http://tile.openstreetmap.org/{z}/{x}/{y}.png',
        'maxZoom' => 18,
        'tileSize' => '256x256',
        'showOnlyIfMore' => true
    ),
    'UMP' => array(
        'name' => 'UMP',
        'attribution' => '&copy; Mapa z <a href="http://ump.waw.pl/" target="_blank">UMP-pcPL</a>',
        'tileUrl' => 'http://tiles.ump.waw.pl/ump_tiles/{z}/{x}/{y}.png',
        'maxZoom' => 18,
        'tileSize' => '256x256',
    ),
    'Topo' => array(
        'attribution' => '&copy; <a href="http://geoportal.gov.pl/" target="_blank">geoportal.gov.pl</a>',
        'showOnlyIfMore' => true,
        'imageMapTypeJS' => 'new google.maps.ImageMapType(new WMSImageMapTypeOptions(
                                        "Topo",
                                        "http://mapy.geoportal.gov.pl:80/wss/service/img/guest/TOPO/MapServer/WmsServer",
                                        "Raster",
                                        "",
                                        "image/jpeg"))',
    ),
    'Orto' => array(
        'attribution' => '&copy; <a href="http://geoportal.gov.pl/" target="_blank">geoportal.gov.pl</a>',
        'showOnlyIfMore' => true,
        'imageMapTypeJS' => 'new google.maps.ImageMapType(new WMSImageMapTypeOptions(
                                        "Orto",
                                        "http://mapy.geoportal.gov.pl:80/wss/service/img/guest/ORTO/MapServer/WmsServer",
                                        "Raster",
                                        "",
                                        "image/jpeg"))',
    ),
);
$config['mapsConfig'] = $mapsConfig;

// map of garmin keys,
// key: domain name, value: garmin key value
// the map may contain only one entry
$config['garmin-key'] = array(
        'http://opencaching.pl' => '0fe1300131fcc0e417bb04de798c5acf',
        'http://www.opencaching.nl' => 'b01f02cba1c000fe034471d2b08044c6'
);

$titled_cache_nr_found=10;
$titled_cache_period_prefix='week';

// set this to true to disable automatic translation of cache descs
$disable_google_translation = false;

/* ************************************************************************
 * Cache page mini map
 * ************************************************************************ */

/* Cache page small map, fixed, clickable to open minimap.                  */
// available options are roadmap, terrain, map, satellite, hybrid
$config['maps']['cache_page_map']['layer'] = 'terrain';
$config['maps']['cache_page_map']['zoom'] = 8;
// choose color according to https://developers.google.com/maps/documentation/static-maps/intro#Markers
$config['maps']['cache_page_map']['marker_color'] = 'blue';

// available source for osm static map: mapnik,cycle, sterrain, stoner
$config['maps']['cache_page_map']['source'] = 'mapnik';

/* Cache page minimap                                                       */
$config['maps']['cache_mini_map']['zoom'] = 14;
$config['maps']['cache_mini_map']['width'] = '480';
$config['maps']['cache_mini_map']['height'] = '385';

/* ************************************************************************
 * External maps on which to view a cache
 *
 * The following parameters are available for replacement using
 * printf style syntax, in this order
 *    1          2         3            4           5         6
 * latitude, longitude, cache_id, cache_code, cache_name, link_text
 *
 * coordinates are float numbers (%f), the rest are strings (%s)
 * cache_name is urlencoded
 * escape % using %% (printf syntax)
 * The level 3 key is also used as link_text.
 *
 * Use this to define URLs to external mapping sites to display a cache
 * ************************************************************************ */

/* Example:
 * $config['maps']['external']['MyMap'] = 1; // 1 = enabled; 0 = disabled
 * $config['maps']['external']['MyMap_URL'] = '<a href="http://site/file?lat=%1$f&lon=%2$f&id=%3$s&name=%5$s">%6$s</a>';
 */

// Enable or disable the predefined external maps below:
$config['maps']['external']['Opencaching'] = 1;
$config['maps']['external']['OSMapa'] = 1;
$config['maps']['external']['UMP'] = 1;
$config['maps']['external']['Google Maps'] = 1;
$config['maps']['external']['Szukacz'] = 1;
$config['maps']['external']['Flopp\'s Map'] = 0;

//To all mails send from our service we can add few prefixes:
//If you don't want use global prefixes just set $value=""
//Prefix for all mails sent to users:
$subject_prefix_for_site_mails = "OCXX";
//Prefix for all notification and mails sent to cache reviewers
$subject_prefix_for_reviewers_mails = "R-Team";
?>
