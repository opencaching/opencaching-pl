<?php

/**
 * GeoCache properties configuration
 *
 * Those are configuration overrides for OCNL node only.
 */

use src\Models\GeoCache\CacheAttribute;
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
 * List of attributes supported by the node. The order is significant - the same order is used on sites.
 * Use CacheAttribute::* notation for more clear definition.
 */
$geocache['supportedAttributes'] = [
    CacheAttribute::BOAT, CacheAttribute::CHILDREN, CacheAttribute::HIKING,
    CacheAttribute::AVAILABLE247, CacheAttribute::NIGHT, CacheAttribute::TICKS,
    CacheAttribute::HUNTING, CacheAttribute::DANGER, CacheAttribute::WHEELCHAIR,
    CacheAttribute::PARKING, CacheAttribute::BIKE, CacheAttribute::THORNS,
    CacheAttribute::STEALTH, CacheAttribute::FLASHLIGHT, CacheAttribute::TOOLS,
    CacheAttribute::BEACON, CacheAttribute::OCONLY, CacheAttribute::LETTERBOX,
    CacheAttribute::COMPASS, CacheAttribute::QUICK, CacheAttribute::GEOHOTEL,
    CacheAttribute::PEN, CacheAttribute::MAGNETIC, CacheAttribute::MP3,
    CacheAttribute::USB, CacheAttribute::BENCHMARK, CacheAttribute::NATURE,
    CacheAttribute::HISTORIC, CacheAttribute::SHOVEL, CacheAttribute::WALK,
    CacheAttribute::NOTAVAILABLE247
];
