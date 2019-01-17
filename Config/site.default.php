<?php

use lib\Objects\GeoCache\GeoCacheCommons;

/**
  * DEFAULT configuration of general site properties
 */

/**
 * Theses sizes are not available for
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
