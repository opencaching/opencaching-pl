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
