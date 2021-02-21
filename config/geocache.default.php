<?php

/**
 * GeoCache properties configuration
 *
 * This is a default configuration.
 * It may be customized in node-specific configuration file.
 */

use src\Models\GeoCache\GeoCacheCommons;

$geocache = [];

/**
 * These sizes are available when creating new cache or changing the size
 * of an existing cache. All other size-related features will automatically
 * adjust to the sizes which are in use for existing caches.
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
 * Geocache types which are forbidden on creation (it is possible that
 * such geocaches are still in DB, but no new ones can be created).
 */
$geocache['noNewCachesOfTypes'] = [];
