<?php

use lib\Objects\GeoCache\GeoCacheCommons;

/**
 * This is simple configuration of maps in the OC code
 *
 * This is a DEFAULT configuration for ALL nodes, which contains necessary vars.
 *
 * If you to customize it for your node
 * create config for your node and there override array values as needed.
 */

/**
 * Configuration of general site properties in defaut version
 */

/**
 * Configuration of general site properties in defaut version
 */
$site = [];


/**
 * Primary country (or countries) for this node
 */
$site['primaryCountries'] = ['PL'];

/**
 * Theses sizes are available for
 *    - creating caches
 *    - changing the type of a cache to this size
 *    - searching for caches.
 *
 * The order does not matter.
 */
$site['enabledCacheSizes'] = [
    GeoCacheCommons::SIZE_MICRO,
    GeoCacheCommons::SIZE_SMALL,
    GeoCacheCommons::SIZE_REGULAR,
    GeoCacheCommons::SIZE_LARGE,
    GeoCacheCommons::SIZE_XLARGE,
    GeoCacheCommons::SIZE_NONE,
];

