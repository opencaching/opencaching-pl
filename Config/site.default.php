<?php

use lib\Objects\GeoCache\GeoCacheCommons;

/**
  * DEFAULT configuration of general site properties
 */

/**
 * Theses sizes are not available for
 *    - creating caches
 *    - changing the type of a cache to this size
 *    - searching for caches
 */
$site['disabledCacheSizes'] = [
    GeoCacheCommons::SIZE_OTHER   // Do not enable before OKAPI #519 is fixed!
];
