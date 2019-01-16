<?php

use lib\Objects\GeoCache\GeoCacheCommons;

/**
  * Configuration of general site properties of OC NL
 */

$site['primaryCountries'] = ['NL', 'BE', 'LU'];

// enable nano caches
$site['disabledCacheSizes'] = array_diff(
    $site['disabledCacheSizes'],
    [GeoCacheCommons::SIZE_NANO]
);
