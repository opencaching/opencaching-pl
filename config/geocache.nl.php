<?php

/**
 * GeoCache properties configuration
 *
 * Those are configuration overrides for OCNL node only.
 */

use src\Models\GeoCache\GeoCacheCommons;

$geocache['enabledSizes'][] = GeoCacheCommons::SIZE_NANO;
