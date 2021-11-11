<?php

/**
 * GeoCache properties configuration
 *
 * Those are configuration overrides for OCUK node only.
 */

use src\Models\GeoCache\CacheAttribute;
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
 * List of attributes supported by the node. The order is significant - the same order is used on sites.
 * Use CacheAttribute::* notation for more clear definition.
 */
$geocache['supportedAttributes'] = [
    CacheAttribute::FEE, CacheAttribute::RAPELLING, CacheAttribute::BOAT,
    CacheAttribute::DIVING, CacheAttribute::CHILDREN, CacheAttribute::HIKING,
    CacheAttribute::CLIMBING, CacheAttribute::WADING, CacheAttribute::SWIMMING, CacheAttribute::AVAILABLE247,
    CacheAttribute::WINTER, CacheAttribute::POISON, CacheAttribute::ANIMALS,
    CacheAttribute::TICKS, CacheAttribute::MINE, CacheAttribute::CLIFF,
    CacheAttribute::HUNTING, CacheAttribute::DANGER, CacheAttribute::PARKING,
    CacheAttribute::TRANSPORT, CacheAttribute::WATER, CacheAttribute::RESTROOMS,
    CacheAttribute::PHONE, CacheAttribute::THORNS, CacheAttribute::FLASHLIGHT,
    CacheAttribute::RIDDLE, CacheAttribute::TOOLS, CacheAttribute::NIGHTONLY,
    CacheAttribute::DRIVEIN, CacheAttribute::OCONLY, CacheAttribute::LETTERBOX,
    CacheAttribute::TRAIN, CacheAttribute::FIRSTAID, CacheAttribute::STEEP,
    CacheAttribute::HISTORIC, CacheAttribute::MOVING, CacheAttribute::WEBCAM,
    CacheAttribute::INDOOR, CacheAttribute::UNDERWATER, CacheAttribute::NOGPS,
    CacheAttribute::OVERNIGHT, CacheAttribute::TIDE, CacheAttribute::PRESERVE,
    CacheAttribute::COMPASS, CacheAttribute::CAVE,
    CacheAttribute::WIKI, CacheAttribute::MATH,
    CacheAttribute::HANDICACHING, CacheAttribute::MUNZEE,
    CacheAttribute::NOTAVAILABLE247, CacheAttribute::DAY, CacheAttribute::ALLSEASONS
];
