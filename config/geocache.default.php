<?php

/**
 * DEFAULT config for geocache properties
 */

use src\Models\GeoCache\GeoCacheCommons;

$geocache = [];

/**
 * Theses sizes are available for
 *    - creating caches
 *    - changing the size of a cache to this size
 *
 * (All other size-related features will automatically adjust to the sizes
 * which are in use for existing caches.)
 *
 * The order does not matter.
 */
$geocache['enabledSizes'] = [
    GeoCacheCommons::SIZE_MICRO,
    GeoCacheCommons::SIZE_SMALL,
    GeoCacheCommons::SIZE_REGULAR,
    GeoCacheCommons::SIZE_LARGE,
    GeoCacheCommons::SIZE_XLARGE,
    GeoCacheCommons::SIZE_NONE,
];

/**
 * Types of geocache which are forbidden on creation (it is possible thatsuch geocaches are still in DB,
 * but no NEW caches of this can be created
 */
$geocache['noNewCachesOfTypes'] = [];

