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
