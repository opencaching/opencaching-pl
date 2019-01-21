<?php

use lib\Objects\GeoCache\GeoCacheCommons;

/**
  * Configuration of general site properties of OC NL
 */

$site['primaryCountries'] = ['NL', 'BE', 'LU'];

$site['enabledCacheSizes'][] = GeoCacheCommons::SIZE_NANO;
