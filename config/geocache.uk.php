<?php

/**
 * GeoCache properties configuration
 *
 * Those are configuration overrides for OCUK node only.
 */

use src\Models\GeoCache\GeoCacheCommons;

/**
 * Types of geocache which are forbidden on creation (it is possible
 * that such geocaches are still in DB, but no NEW caches of this can be created).
 */
$geocache['noNewCachesOfTypes'][] = GeoCacheCommons::TYPE_GEOPATHFINAL;

/**
 * Titled caches algorythm period;
 * possible values: 'week' | 'month'
 */
$geocache['titledCachePeriod'] = 'month';

/**
 * Minimum number of founds necessary for titled cache
 */
$geocache['titledCacheMinFounds'] = 7;

/**
 * The number of founds which user needs to log to create its own new geocache
 */
$geocache['minUserFoundsForNewCache'] = 3;
