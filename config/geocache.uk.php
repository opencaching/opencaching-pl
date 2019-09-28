<?php

use src\Models\GeoCache\GeoCacheCommons;

/**
 * Configuration of geocache properties for OCUK
 */

/**
 * Types of geocache which are forbidden on creation (it is possible thatsuch geocaches are still in DB,
 * but no NEW caches of this can be created
 */
$geocache['noNewCachesOfTypes'][] = GeoCacheCommons::TYPE_GEOPATHFINAL;
