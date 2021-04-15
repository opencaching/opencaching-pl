<?php

/**
 * GeoCache properties configuration
 *
 * Those are configuration overrides for OCPL node only.
 */

use src\Models\GeoCache\GeoCacheCommons;

/**
 * Types of geocache which are forbidden on creation (it is possible
 * that such geocaches are still in DB, but no NEW caches of this can be created).
 */
$geocache['noNewCachesOfTypes'][] = GeoCacheCommons::TYPE_VIRTUAL;
$geocache['noNewCachesOfTypes'][] = GeoCacheCommons::TYPE_GEOPATHFINAL;
$geocache['noNewCachesOfTypes'][] = GeoCacheCommons::TYPE_WEBCAM;

/**
 * Titled caches algorythm period;
 * possible values: 'none' | 'week' | 'month'
 */
$geocache['titledCachePeriod'] = 'week';

/**
 * Minimum number of founds necessary for titled cache
 */
$geocache['titledCacheMinFounds'] = 12;
