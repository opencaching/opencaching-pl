<?php

/*
 * This file contains default settings. All settings can be overriden in the
 * settings.inc.php file (e.g. $config['debugDB'] = true;).
 */

require_once __dir__ . '/cache.php';

$config = array(
    /**
     *Add button to a shop. Set true otherwise false
     *Add link to the shop of choise.
     */
    'showShopButton' => false,
    'showShopButtonUrl' => 'http://www.shop of choise',
    /** url where xml witch most recent blog enterie are placed */
    'blogMostRecentRecordsUrl' => 'http://blog.opencaching.pl/feed/',
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
    /** default country in user registration form */
    'defaultCountry' => 'PL',
    /* Enable referencing waypoints from other sites */
    'otherSites_geocaching_com' => 1,
    'otherSites_terracaching_com' => 1,
    'otherSites_navicache_com' => 1,
    'otherSites_gpsgames_org' => 1,
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
    /**
     * If set to true, all database queries will be reported in the page
     * output. (Note, that this will cause most of the AJAX actions to stop
     * functioning properly.)
     */
    'debugDB' => false,
    /** The filter fragment selecting provinces from nuts_codes table. */
    'provinceNutsCondition' => '`code` like \'PL__\'',
    /**/
    'medalsModuleSwitchedOn' => true,
    /** Nature2000 link - used in viewcache.php */
    'nature2000link' => '<a style="color:blue;" target="_blank" href="http://obszary.natura2000.org.pl/index.php?s=obszar&amp;id={linkid}">{sitename}&nbsp;&nbsp;-&nbsp;&nbsp;{sitecode}</a>',
    /** See settings-example.inc.php for explanation */
    'mapsConfig' => array(
        'OSM' => array(
            'name' => 'OSM',
            'attribution' => '&copy; <a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> contributors <a href="http://creativecommons.org/licenses/by-sa/2.0/" target="_blank">CC BY-SA</a>',
            'tileUrl' => 'http://tile.openstreetmap.org/{z}/{x}/{y}.png',
            'maxZoom' => 18,
            'tileSize' => '256x256'
        ),
    ),
    /**
        customization of cache-attribute icons
    */
    'search-attr-icons' => array(
        'password' => array (
            // has attribute
            'images/attributes/password.png',
            // does not have attribute
            'images/attributes/password-no.png',
            // does not care
            'images/attributes/password-undef.png'
        )
    )
);
