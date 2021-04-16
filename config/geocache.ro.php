<?php

/**
 * GeoCache properties configuration
 *
 * Those are configuration overrides for OCRO node only.
 */

use src\Models\GeoCache\GeoCacheCommons;

$geocache['enabledSizes'][] = GeoCacheCommons::SIZE_NANO;
$geocache['enabledSizes'][] = GeoCacheCommons::SIZE_OTHER;

/**
 * Types of geocache which are forbidden on creation (it is possible
 * that such geocaches are still in DB, but no NEW caches of this can be created).
 */
$geocache['noNewCachesOfTypes'][] = GeoCacheCommons::TYPE_GEOPATHFINAL;

/**
 * Hide coordinates for non-logged users
 */
$geocache['coordsHiddenForNonLogged'] = false;

/**
 * The number of founds which user needs to log to create its own new geocache
 */
$geocache['minUserFoundsForNewCache'] = 1;

/**
 * The minimum number of active geocaches owned by user to skip OCTEAM
 * verification of every new geocache
 */
$geocache['minCachesToSkipNewCacheVerification'] = 0;

