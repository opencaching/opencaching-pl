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

/**
 * If reactivation rules are enabled (present) in geocache editor
 */
$geocache['reactivationRulesEnabled'] = true;

/**
 * This has sense ONLY if option $geocache['reactivationRulesEnabled'] == true.
 *
 * List of the translation keys with predefined reactivation rules texts.
 * Order of the keys here will be reflacted in the geocache editor.
 * If the list is empty only custom (user defined) option will be active.
 */
$geocache['reactivationRulesPredefinedOpts'] = [
    'editDesc_reactivRuleOptNoReactivations',
    'editDesc_reactivRuleOptAfterContact',
    'editDesc_reactivRuleOptReactAllowed'];
