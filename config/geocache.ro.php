<?php

/**
 * Configuration of geocache properties for OC RO
 */

use src\Models\GeoCache\GeoCacheCommons;

$geocache['enabledSizes'][] = GeoCacheCommons::SIZE_NANO;
$geocache['enabledSizes'][] = GeoCacheCommons::SIZE_OTHER;

/**
 * Types of geocache which are forbidden on creation (it is possible thatsuch geocaches are still in DB,
 * but no NEW caches of this can be created
 */
$geocache['noNewCachesOfTypes'][] = GeoCacheCommons::TYPE_GEOPATHFINAL;
