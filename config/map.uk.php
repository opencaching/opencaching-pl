<?php

/**
 * Configuration of maps in the OC code
 *
 * Those are configuration overrides for OCUK node only.
 */

/**
 * $map['jsConfig'] contains JS code with configuration for openlayers
 * Be sure that all changes here are well tested.
 */
$map['jsConfig'] = "
{
  OSM: new ol.layer.Tile ({
    source: new ol.source.OSM(),
  }),

  BingMap: new ol.layer.Tile ({
    source: new ol.source.BingMaps({
      key: '{Key-BingMap}',
      imagerySet: 'Road'
    })
  }),

  BingSatelite: new ol.layer.Tile ({
    source: new ol.source.BingMaps({
      key: '{Key-BingMap}',
      imagerySet: 'Aerial'
    })
  }),

}
";

/**
 * Bing map key is set in node-local config file
 */
$map['keys']['BingMap'] = 'NEEDS-TO-BE-SET-IN-LOCAL-CONFIG-FILE';

/**
 * This function is called to inject keys from "local" config (map.local.php)
 * to node-specific map config (for example map.pl.php).
 *
 * @param array Complete configuration merged from default + node + local configs
 * @return true on success
 */
$map['keyInjectionCallback'] = function(array &$mapConfig){
    // change string {Key-BingMap} to proper key value
    $mapConfig['jsConfig'] = str_replace(
        '{Key-BingMap}',
        $mapConfig['keys']['BingMap'],
        $mapConfig['jsConfig']
    );

    return true;
};

/**
 * Coordinates of the default map center - used by default by many maps in service
 * Format: float number
 */
$map['mapDefaultCenterLat'] = 54.1;
$map['mapDefaultCenterLon'] = -4.0;
$map['mapDefaultZoom'] = 5;

/**
 * Zoom of the static map from startPage
 */
$map['startPageMapZoom'] = 4;

/**
 * Dimensions of the static map from startPage[width,height]
 */
$map['startPageMapDimensions'] = [200, 240];

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
$map['external']["Flopp's Map"]['enabled'] = true;
