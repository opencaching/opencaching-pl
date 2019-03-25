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
$map['keyInjectionCallback'] = function (array $mapConfig) {

    // example:
    // $mapConfig['jsConfig'] = str_replace('{NICE-MAP-KEY}', $mapConfig['keys']['NiceMap'], $mapConfig['jsConfig']);

    return true;
};

