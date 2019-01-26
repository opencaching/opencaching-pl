<?php

use lib\Objects\GeoCache\GeoCacheCommons;

/**
 * DEFAULT general site properties for ALL nodes
 */

$site = [];


/**
 * Primary country (or countries) for this node
 */
$site['primaryCountries'] = [];

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

