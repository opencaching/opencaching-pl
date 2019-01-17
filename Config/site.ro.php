<?php

use lib\Objects\GeoCache\GeoCacheCommons;

/**
  * Configuration of general site properties of OC RO
 */

$site['primaryCountries'] = ['RO'];

$site['enabledCacheSizes'][] = GeoCacheCommons::SIZE_NANO;
$site['enabledCacheSizes'][] = GeoCacheCommons::SIZE_OTHER;
