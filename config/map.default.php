<?php

/**
 * This is simple configuration of maps in the OC code
 *
 * This is a DEFAULT configuration for ALL nodes, which contains necessary vars.
 *
 * If you want to customize links for your node
 * create config for your node and there override $map array values as needed.
 *
 */

/**
 * This is main map config array
 */
$map = [];

/**
 * This array contains keys used by maps
 */
$map['keys'] = [];

/**
 * This variable should contain api_key created for https://openrouteservice.org
 * DO NOT add your key to this file - add it in your map.local.php file.
 *
 * OpenRouteService is used to search (geocode) on MainMap - without the key search is disabled.
 *
 */
$map['keys']['OpenRouteService'] = '';

/**
 * $map['jsConfig'] constains JS code with configuration for openlayers
 * This is very simple config - OSM map only.
 *
 * See map.default.pl for more examples.
 */
$map['jsConfig'] = "
    {
      OSM: new ol.layer.Tile ({
        source: new ol.source.OSM(),
      })
    }
";

/**
 * This is function which is called to inject keys from "local" config (map.local.php)
 * to default node-configurations map config (for example map.pl.php).
 *
 * Here this is only a simple stub.
 *
 *
 * @param array complete configureation merged from default + node-default + local configs
 * @return true on success
 */
$map['keyInjectionCallback'] = function ($mapConfig) {

    // example:
    // $mapConfig['jsConfig'] = str_replace('{NICE-MAP-KEY}', $mapConfig['keys']['NiceMap'], $mapConfig['jsConfig']);

    return true;
};

/**
 * Coordinates of the default map center - used by default by many maps in service
 * Format: float number
 */
$map['mapDefaultCenterLat'] = 52.13;
$map['mapDefaultCenterLon'] = 19.20;


/**
 * Zoom of the static map from startPage
 */
$map['startPageMapZoom'] = 5;

/**
 * Dimensions of the static map from startPage[width,height]
 */
$map['startPageMapDimensions'] = [250, 260];

/**
 * Links to external maps used at least at viewpage
 * (to disable map - just add key $map['external']['OSMapa']['enabled'] = false;)
 *
 * Url rules:
 *  The following parameters are available for replacement using
 * printf style syntax, in this order
 *
 *    1          2         3            4           5         6
 * latitude, longitude, cache_id, cache_code, cache_name, link_text
 *
 * coordinates are float numbers (%f), the rest are strings (%s)
 * cache_name is urlencoded
 * escape % using %% (printf syntax)
 *
 * The level 3 key is also used as link_text.
 */
$map['external']['Opencaching']['url'] = '/MainMap/fullscreen?lat=%1$f&lon=%2$f&openPopup';
$map['external']['OSM']['url'] = 'https://www.openstreetmap.org/index.html?mlat=%1$f&mlon=%2$f&zoom=16&layers=M';

$map['external']['OSMapa']['enabled'] = false;  // PL specific
$map['external']['OSMapa']['url'] = 'http://osmapa.pl?zoom=16&lat=%1$f&lon=%2$f&z=14&o=TFFT&map=1';

$map['external']['UMP']['enabled'] = false;     // PL specific
$map['external']['UMP']['url'] = 'http://mapa.ump.waw.pl/ump-www/?zoom=14&lat=%1$f&lon=%2$f&layers=B00000T&mlat=%1$f&mlon=%2$f';

$map['external']['Google Maps']['url'] = 'https://maps.google.com/maps?hl=UTF-8&q=%1$f+%2$f+(%5$s)';

$map['external']['Szukacz']['enabled'] = false; // PL specific
$map['external']['Szukacz']['url'] = 'https://mapa.szukacz.pl/?n=%1$f&e=%2$f&z=4&t=Skrzynka%%20Geocache';

$map['external']['Flopp\'s Map']['enabled'] = false;
$map['external']['Flopp\'s Map']['url'] = 'http://flopp.net/?c=%1$f:%2$f&z=16&t=OSM&f=g&m=&d=&g=%4$s';
