<?php

/*
 * This file contains default settings. All settings can be overriden in the
 * settings.inc.php file (e.g. $config['debugDB'] = true;).
 */

require_once (__DIR__.'/cache.php');

// enable detailed cache access logging
$enable_cache_access_logs = false;

$config = array(
    /**
     * country-id of the running node: pl|ro|nl...
     */
    'ocNode' => 'pl', // pl is a default


    /**
     *Add button to a shop. Set true otherwise false
     *Add link to the shop of choise.
     */
    //NOT USED: 'showShopButton' => false, - to display/hide mapv2 change menu conf.
    //NOT USED: 'showShopButtonUrl' => 'http://www.shop of choise', - to display/hide mapv2 change menu conf.


    /** to switch cache map v2 on set true otherwise false */
    //NOT USED: 'map2SwithedOn' => true, - to display/hide mapv2 change menu conf.


    /** to switch flopp's map on set true otherwise false */
    //NOT USED: 'FloppSwithedOn' => false, - to display/hide Flopp's map change menu conf.



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
    /** Language list for new caches and for GPX */
    'defaultLanguageList' => array(
        'PL', 'EN', 'FR', 'DE', 'NL', 'RO'
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
        cache::TYPE_BITCACHE,
        cache::TYPE_GUESTBOOK,
        cache::TYPE_BENCHMARK,
        cache::TYPE_CHALLENGE
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
    /** The filter fragment selecting provinces from nuts_codes table. */
    'provinceNutsCondition' => '`code` like \'PL__\'',
    /** Nature2000 link - used in viewcache.php */
    'nature2000link' => '<a style="color:blue;" target="_blank" href="http://obszary.natura2000.org.pl/index.php?s=obszar&amp;id={linkid}">{sitename}&nbsp;&nbsp;-&nbsp;&nbsp;{sitecode}</a>',
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
    'dateformat' => '%Y-%m-%d'
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


// available map source (for osm based  static map): mapnik, cycle, sterrain, stoner
$config['maps']['main_page_map']['source'] = 'mapnik';

 /* ***********************
  * Search box on top page
  * ***********************/

$config['quick_search']['geopath'] = true;
$config['quick_search']['byowner'] = false;
$config['quick_search']['byfinder'] = false;
$config['quick_search']['byuser'] = true;

// Minimum age to register (see GDPR policy)
$config['limits']['minimum_age'] = 16;

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
$config['okapi']['tilemap_font_path'] = null;
// $config['okapi']['admin_emails'] = array('rygielski@mimuw.edu.pl', 'following@online.de');

// Number of minutes to edit cache log without increment the "edit_count" field. Usefull eg. to correct spelling errors.
// https://github.com/opencaching/opencaching-pl/issues/696
$config['cache_log']['edit_time'] = 5;

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

// customization of the start page
$config['startPage']['latestCacheSetsCount'] = 3;

// logo displayed as apple-touch-icon-precomposed
$config['header']['appleLogo'] = '/images/oc_logo_144.png';

// FB login conf
$config['oAuth']['facebook']['prodEnabled'] = false;
$config['oAuth']['facebook']['testEnabled'] = false;
$config['oAuth']['facebook']['appId'] = null;
$config['oAuth']['facebook']['appSecret'] = null;

// Google login conf
$config['oAuth']['google']['prodEnabled'] = false;
$config['oAuth']['google']['testEnabled'] = false;
$config['oAuth']['google']['clientId'] = null;
$config['oAuth']['google']['clientSecret'] = null;

// MapQuest Key - used to access MapQuest API - to obtain key see: https://developer.mapquest.com/
$config['maps']['mapQuestKey'] = null;
