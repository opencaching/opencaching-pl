<?php

/**
 * GeoCache properties configuration
 *
 * This is a default configuration.
 * It may be customized in node-specific configuration file.
 */

use src\Models\GeoCache\GeoCacheCommons;
use src\Models\GeoCache\CacheAttribute;

$geocache = [];

/**
 * These sizes are available when creating new cache or changing the size
 * of an existing cache. All other size-related features will automatically
 * adjust to the sizes which are in use for existing caches.
 */
$geocache['enabledSizes'] = [
    GeoCacheCommons::SIZE_MICRO,
    GeoCacheCommons::SIZE_SMALL,
    GeoCacheCommons::SIZE_REGULAR,
    GeoCacheCommons::SIZE_LARGE,
    GeoCacheCommons::SIZE_XLARGE,
    GeoCacheCommons::SIZE_NONE,
];

/**
 * Geocache types which are forbidden on creation (it is possible that
 * such geocaches are still in DB, but no new ones can be created).
 */
$geocache['noNewCachesOfTypes'] = [];


/**
 * Titled caches algorythm period;
 * possible values: 'none' | 'week' | 'month'
 * ('none' to disable titled caches)
 */
$geocache['titledCachePeriod'] = 'none';

/**
 * Minimum number of founds necessary for titled cache
 */
$geocache['titledCacheMinFounds'] = 10;

/**
 * Hide coordinates for non-logged users
 */
$geocache['coordsHiddenForNonLogged'] = true;

/**
 * If reactivation rules are enabled (present) in geocache editor
 */
$geocache['reactivationRulesEnabled'] = false;

/**
 * This has sense ONLY if option $geocache['reactivationRulesEnabled'] == true.
 *
 * List of the translation keys with predefined reactivation rules texts.
 * Order of the keys here will be reflacted in the geocache editor.
 * If the list is empty only custom (user defined) option will be active.
 */
$geocache['reactivationRulesPredefinedOpts'] = [];

/**
 * List of attributes supported by the node. The order is significant - the same order is used on sites.
 * Use CacheAttribute::* notation for more clear definition.
 */
$geocache['supportedAttributes'] = [
    CacheAttribute::FEE, CacheAttribute::BOAT, CacheAttribute::CHILDREN
];
