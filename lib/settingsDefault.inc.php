<?php

/*
 * This file contains default settings. All settings can be overriden in the
 * settings.inc.php file (e.g. $config['debugDB'] = true;).
 */

require_once __dir__ . '/cache.php';

// OC specific email addresses for international use
// override in settings.inc.php to values you want to locally use

$mail_cog = 'cog@localhost';    // OCPL COG = reviewers and regional service for cachers
$mail_rt = 'root@localhost';    // OCPL technical contact
$mail_oc = 'ocpl@localhost';    // OCPL general contact

// enable detailed cache access logging
$enable_cache_access_logs = false;

$config = array(
    /**
     *Add button to a shop. Set true otherwise false
     *Add link to the shop of choise.
     */
    'showShopButton' => false,
    'showShopButtonUrl' => 'http://www.shop of choise',
    /* link to geokrety site
     * to disable geokrety in main menu set:
     * $config['geokrety_url'] = NULL;
     * */
    'geokrety_url' => 'https://geokrety.org',
    /** to switch cache map v2 on set true otherwise false */
    'map2SwithedOn' => true,
    /** to switch flopp's map on set true otherwise false */
    'FloppSwithedOn' => false,
    /* === Node personalizations === */

    /** main logo picture (to be placed in /images/) */
    'headerLogo' => 'oc_logo.png',
    /** main logo; winter version, displayed during december and january. */
    'headerLogoWinter' => 'oc_logo_winter.png',
    /** main logo; prima aprilis version (april fools), displayed only on april 1st. */
    'headerLogo1stApril' => 'oc_logo_1A.png',
    /** qrcode logo: show qrcode image and link the prefered way.  */
    'qrCodeLogo' => 'qrcode_bg.jpg',
    'qrCodeUrl' => 'http://opencaching.pl/viewcache.php?wp=OP3C90',
    /**
     * website icon (favicon); (to be placed in /images/)
     * Format: 16x16 pixels; PNG 8bit indexed or 24bit true color, transparency supported
     * A file /favicon.ico (windows icon ICO format, 16x16) should also exist as fallback
     * mainly for MSIE
     */
    'headerFavicon' => 'oc_icon.png',
    /** Language list for new caches */
    'defaultLanguageList' => array(
        'PL', 'EN', 'FR', 'DE', 'NL', 'RO'
    ),
    /** Languages supported by OC node -
     * for those languages translations are supported both in file and db-tables
     * IMPORTANT: use lower letters only! */
    'supportedLanguages' => array (
        'pl', 'en', 'nl', 'ro'
    ),
    /** default country in user registration form */
    'defaultCountry' => 'PL',

    /* Enable referencing waypoints from other sites */
    'otherSites_geocaching_com' => 1,
    'otherSites_terracaching_com' => 1,
    'otherSites_navicache_com' => 1,
    'otherSites_gpsgames_org' => 1,
    'otherSites_qualitycaching_com' => 0, // BeNeLux only

    /**
     * Minimum number of finds a user must have to see a cache's waypoint on
     * another site.
     */
    'otherSites_minfinds' => 100,
    /**
     * not allowed cache types (user cannot create caches of this types).
     *
     * Cachetypes must be lib/cache.php constant TYPE_*
     */
    'forbidenCacheTypes' => array(
        cache::TYPE_VIRTUAL,
        cache::TYPE_WEBCAM,
        cache::TYPE_GEOPATHFINAL
    ),
    /**
     * cache limits for user. If user is allowed to place limited nomber of specified cache type,
     * place cachetype and limit here.
     *
     * Cachetypes must be lib/cache.php constant TYPE_*
     */
    'cacheLimitByTypePerUser' => array(
        cache::TYPE_OWNCACHE => 1,
    ),
    /**
     * not allowed cache sizes (user cannot create caches of this sizes).
     *
     * Cachesizes must be lib/cache.php constant SIZE_*
     */
    'forbiddenCacheSizes' => array(
        //cache::SIZE_MICRO
    ),
    /** The filter fragment selecting provinces from nuts_codes table. */
    'provinceNutsCondition' => '`code` like \'PL__\'',
    /** Nature2000 link - used in viewcache.php */
    'nature2000link' => '<a style="color:blue;" target="_blank" href="http://obszary.natura2000.org.pl/index.php?s=obszar&amp;id={linkid}">{sitename}&nbsp;&nbsp;-&nbsp;&nbsp;{sitecode}</a>',
    /** See settings-example.inc.php for explanation */
    'mapsConfig' => array(
        'OSM' => array(
            'name' => 'OSM',
            'attribution' => '&copy; <a href="//www.openstreetmap.org/" target="_blank">OpenStreetMap</a> contributors <a href="//creativecommons.org/licenses/by-sa/2.0/" target="_blank">CC BY-SA</a>',
            'tileUrl' => 'http://tile.openstreetmap.org/{z}/{x}/{y}.png',
            'maxZoom' => 18,
            'tileSize' => '256x256'
        ),
    ),
    /** customization of cache-attribute icons */
    'search-attr-icons' => array(
        'password' => array (
            // has attribute
            'images/attributes/password.png',
            // does not have attribute
            'images/attributes/password-no.png',
            // does not care
            'images/attributes/password-undef.png'
        )
    ),
    'numberFormatDecPoint' => '.',
    'numberFormatThousandsSep' => ',',
    'meritBadges' => false,

    /** default style - in fact we don't heve any other style... */
    'style' => 'stdstyle',

    /**
     * Common datetime and date format
     */
    'datetimeformat' => '%Y-%m-%d %H:%M:%S',
    'dateformat' => '%Y-%m-%d',

    /** size limit in bytes for buffering downloads in a file, or 0 to disable buffering **/
    'downloadMemorylimit' => 0,
);

// *** Repository automatic updates script location
$config['server']['update']['script'] = '/var/www/ocpl-update.sh';

/* ************************************************************************
 * Modules
 * Activate and configure modules.
 */

// *** OpenChecker ********************************************************
$config['module']['openchecker']['enabled'] = true;
// Limit number of checks
$config['module']['openchecker']['limit'] = 10;
// Time period for checks limit (minutes)
$config['module']['openchecker']['time'] = 60;
// Pagination - how many caches per page
$config['module']['openchecker']['page'] = 25;
// Show final waypoint description when user got correct answer?
$config['module']['openchecker']['show_final'] = true;

/* ************************************************************************
 * Cache page mini map
 * ************************************************************************ */

/* Cache page small map, fixed, clickable to open minimap.                  */
// available options are roadmap, terrain, map, satellite, hybrid
$config['maps']['cache_page_map']['layer'] = 'terrain';
$config['maps']['cache_page_map']['zoom'] = 8;
// choose color according to https://developers.google.com/maps/documentation/static-maps/intro#Markers
$config['maps']['cache_page_map']['marker_color'] = 'blue';

// available map source (for osm based  static map): mapnik, cycle, sterrain, stoner
$config['maps']['cache_page_map']['source'] = 'mapnik';
// available map source (for osm based  static map): mapnik, cycle, sterrain, stoner
$config['maps']['main_page_map']['source'] = 'mapnik';

/* Cache page minimap                                                       */
$config['maps']['cache_mini_map']['zoom'] = 14;

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
$config['maps']['external']['Opencaching'] = 1;
$config['maps']['external']['Opencaching_URL'] = 'cachemap3.php?lat=%1$f&lon=%2$f&cacheid=%3$s&inputZoom=14';
$config['maps']['external']['OSM'] = 1;
$config['maps']['external']['OSM_URL'] = 'https://www.openstreetmap.org/index.html?mlat=%1$f&mlon=%2$f&zoom=16&layers=M';
$config['maps']['external']['OSMapa'] = 0;
$config['maps']['external']['OSMapa_URL'] = 'http://osmapa.pl?zoom=16&lat=%1$f&lon=%2$f&z=14&o=TFFT&map=1';
$config['maps']['external']['UMP'] = 1;
$config['maps']['external']['UMP_URL'] = 'http://mapa.ump.waw.pl/ump-www/?zoom=14&lat=%1$f&lon=%2$f&layers=B00000T&mlat=%1$f&mlon=%2$f';
$config['maps']['external']['Google Maps'] = 1;
$config['maps']['external']['Google Maps_URL'] = 'https://maps.google.com/maps?hl=UTF-8&q=%1$f+%2$f+(%5$s)';
$config['maps']['external']['Szukacz'] = 1;
$config['maps']['external']['Szukacz_URL'] = 'http://mapa.szukacz.pl/?n=%1$f&e=%2$f&z=4&t=Skrzynka%%20Geocache';
$config['maps']['external']['Flopp\'s Map'] = 0;
$config['maps']['external']['Flopp\'s Map_URL'] = 'http://flopp.net/?c=%1$f:%2$f&z=16&t=OSM&f=g&m=&d=&g=%4$s';

 /* ***********************
  * Search box on top page
  * ***********************/

$config['quick_search']['byowner'] = false;
$config['quick_search']['byfinder'] = false;
$config['quick_search']['byuser'] = true;

  /** Limit for uplading pictures per node. */

// Image file size limit in MB
$config['limits']['image']['filesize'] = 3.5;
// Resize large images ? (1=yes; 0=no)
$config['limits']['image']['resize'] = 1;
// If resize large images = 1
// only resize files larger then this, in MB
$config['limits']['image']['resize_larger'] = 0.1;
// Image maximum width in pixels (aspect ratio preserved)
$config['limits']['image']['width'] = 640;
// Image maximum height in pixels (aspect ratio preserved)
$config['limits']['image']['height'] = 640;
// Image recommended size in pixels (for translations)
$config['limits']['image']['pixels_text'] = '640 x 480';
// Allowed extensions (image formats)
$config['limits']['image']['extension'] = ';jpg;jpeg;gif;png;';
$config['limits']['image']['extension_text'] = 'JPG, PNG, GIF';

// Minimum distance between caches (physical containers) in meters
$config['oc']['limits']['proximity'] = 150;

// Maximum radius around home coordinates within which user can receive
// notifications in km
$config['oc']['limits']['notification_radius'] = 150;

/*
 * OKAPI settings
 */

$config['okapi']['data_license_url'] = 'http://wiki.opencaching.pl/index.php/OC_PL_Conditions_of_Use';
$config['okapi']['admin_emails'] = false;
// $config['okapi']['admin_emails'] = array('rygielski@mimuw.edu.pl', 'following@online.de');

// Number of minutes to edit cache log without increment the "edit_count" field. Usefull eg. to correct spelling errors.
// https://github.com/opencaching/opencaching-pl/issues/696
$config['cache_log']['edit_time'] = 5;

// Configuration of the bottom menu
$config['bottom_menu']['impressum']['link'] = '';
$config['bottom_menu']['impressum']['visible'] = false;
$config['bottom_menu']['history']['link'] = '';
$config['bottom_menu']['history']['visible'] = false;
$config['bottom_menu']['api']['link'] = '/okapi';
$config['bottom_menu']['api']['visible'] = true;
$config['bottom_menu']['rss']['link'] = 'articles.php?page=rss';
$config['bottom_menu']['rss']['visible'] = true;
$config['bottom_menu']['contact']['link'] = 'articles.php?page=contact';
$config['bottom_menu']['contact']['visible'] = true;
$config['bottom_menu']['main_page']['link'] = '/index.php?page=sitemap';
$config['bottom_menu']['main_page']['visible'] = true;

// Configuration of license link at footer
$config['license_html'] = '';

// Configuration of feeds on the main page (for instruction - see setting-example.inc.php)
$config['feed']['enabled'] = array();
$config['feed']['forum']['url'] = '';
$config['feed']['forum']['posts'] = 5;
$config['feed']['forum']['showAuthor'] = true;
$config['feed']['blog']['url'] = '';
$config['feed']['blog']['posts'] = 5;
$config['feed']['blog']['showAuthor'] = true;

$subject_prefix_for_site_mails = "OCXX";
$subject_prefix_for_reviewers_mails = "";
