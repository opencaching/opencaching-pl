<?php

/**
 * GeoCache properties configuration
 *
 * Those are configuration overrides for OCUS node only.
 */

use src\Models\GeoCache\CacheAttribute;
use src\Models\GeoCache\GeoCacheCommons;

$geocache['enabledSizes'][] = GeoCacheCommons::SIZE_NANO;

/**
 * List of attributes supported by the node. The order is significant - the same order is used on sites.
 * Use CacheAttribute::* notation for more clear definition.
 */
$geocache['supportedAttributes'] = [
    CacheAttribute::FEE, CacheAttribute::CHILDREN, CacheAttribute::WINTER,
    CacheAttribute::POISON, CacheAttribute::ANIMALS, CacheAttribute::TICKS,
    CacheAttribute::DANGER, CacheAttribute::WHEELCHAIR, CacheAttribute::THORNS,
    CacheAttribute::STEALTH, CacheAttribute::FLASHLIGHT, CacheAttribute::TRUCK,
    CacheAttribute::TOOLS, CacheAttribute::NIGHTONLY, CacheAttribute::BEACON,
    /*CacheAttribute::CHALLENGE ???, */ CacheAttribute::OCONLY, CacheAttribute::LETTERBOX,
    CacheAttribute::COMPASS, CacheAttribute::QUICK, CacheAttribute::GEOHOTEL,
    CacheAttribute::PEN, CacheAttribute::MAGNETIC, CacheAttribute::MP3,
    CacheAttribute::OFFSET, CacheAttribute::USB, CacheAttribute::BENCHMARK,
    CacheAttribute::NATURE, CacheAttribute::HISTORIC, CacheAttribute::MUNZEE,
    CacheAttribute::ADS, /*CacheAttribute::BITCACHE ???*/ /*CacheAttribute::GUESTBOOK??*/
    CacheAttribute::NOTAVAILABLE247
];
