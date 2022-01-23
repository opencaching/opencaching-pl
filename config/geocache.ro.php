<?php

/**
 * GeoCache properties configuration
 *
 * Those are configuration overrides for OCRO node only.
 */

use src\Models\GeoCache\CacheAttribute;
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
 * List of attributes supported by the node. The order is significant - the same order is used on sites.
 * Use CacheAttribute::* notation for more clear definition.
 */
$geocache['supportedAttributes'] = [
    CacheAttribute::FEE, CacheAttribute::BOAT, CacheAttribute::CHILDREN,
    CacheAttribute::HIKING, CacheAttribute::AVAILABLE247, CacheAttribute::NIGHT,
    CacheAttribute::TICKS, CacheAttribute::HUNTING, CacheAttribute::DANGER,
    CacheAttribute::WHEELCHAIR, CacheAttribute::PARKING, CacheAttribute::WATER,
    CacheAttribute::BIKE, CacheAttribute::THORNS, CacheAttribute::STEALTH,
    CacheAttribute::FLASHLIGHT, CacheAttribute::RIDDLE, CacheAttribute::TOOLS,
    CacheAttribute::BEACON, CacheAttribute::OCONLY, CacheAttribute::LETTERBOX,
    CacheAttribute::COMPASS, CacheAttribute::QUICK, CacheAttribute::GEOHOTEL,
    CacheAttribute::PEN, CacheAttribute::MAGNETIC, CacheAttribute::MP3,
    CacheAttribute::OFFSET, CacheAttribute::USB, CacheAttribute::BENCHMARK,
    CacheAttribute::WHERIGO, CacheAttribute::NATURE, CacheAttribute::MONUMENT,
    CacheAttribute::SHOVEL, CacheAttribute::WALK, CacheAttribute::NOTAVAILABLE247,
];
