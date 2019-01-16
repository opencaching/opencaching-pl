<?php

use lib\Objects\GeoCache\GeoCacheCommons;

/**
  * Configuration of general site properties of OC RO
 */

$site['primaryCountries'] = ['RO'];

// enable nano and "other" caches
$site['disabledCacheSizes'] = array_diff(
    $site['disabledCacheSizes'],
    [GeoCacheCommons::SIZE_NANO, GeoCacheCommons::SIZE_OTHER]
);
