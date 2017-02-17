<?php
require_once 'lib/common.inc.php';


$geoCache = new \lib\Objects\GeoCache\GeoCache(['cacheId' => (int) $_GET['cid']]);
d($geoCache->getAltitudeObj());
$geoCache->getAltitudeObj()->pickAndStoreAltitude(null);

ddd($geoCache->getAltitudeObj());

echo "TEST:<hr/>";

echo "<hr/>END!";