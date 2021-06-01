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


/**
 * Titled caches algorythm period;
 * possible values: 'none' | 'week' | 'month'
 * ('none' to disable titled caches)
 */
$geocache['titledCachePeriod'] = 'none';

/**
 * Minimum number of founds necessary for titled cache
 */
$geocache['titledCacheMinFounds'] = 10;

/**
 * Hide coordinates for non-logged users
 */
$geocache['coordsHiddenForNonLogged'] = true;

/**
 * The number of founds which user needs to log to create its own new geocache
 */
$geocache['minUserFoundsForNewCache'] = 10;

/**
 * The minimum number of active geocaches owned by user to skip OCTEAM
 * verification of every new geocache
 */
$geocache['minCachesToSkipNewCacheVerification'] = 3;

