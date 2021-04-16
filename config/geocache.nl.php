<?php

/**
 * GeoCache properties configuration
 *
 * Those are configuration overrides for OCNL node only.
 */

use src\Models\GeoCache\GeoCacheCommons;

$geocache['enabledSizes'][] = GeoCacheCommons::SIZE_NANO;

/**
 * Titled caches algorythm period;
 * possible values: 'week' | 'month'
 */
$geocache['titledCachePeriod'] = 'month';

/**
 * Minimum number of founds necessary for titled cache
 */
$geocache['titledCacheMinFounds'] = 2;

/**
 * The number of founds which user needs to log to create its own new geocache
 */
$geocache['minUserFoundsForNewCache'] = 0;
