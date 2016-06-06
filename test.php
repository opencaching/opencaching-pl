<?php
require_once 'lib/common.inc.php';


$geoCache = new \lib\Objects\GeoCache\GeoCache(['cacheId' => (int) $_GET['cid']]);
d($geoCache->getAltitude());
$geoCache->getAltitude()->pickAndStoreAltitude(null);

ddd($geoCache->getAltitude());

echo "TEST:<hr/>";

echo "<hr/>END!";