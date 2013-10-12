<?php
require_once(__DIR__.'/lib/db.php');;
require_once __DIR__.'/region_class.php';

$cacheId = 2;

$db = new dataBase;
$queryCacheData = 'SELECT * FROM caches WHERE cache_id = :1 LIMIT 1';
$db->multiVariableQuery($queryCacheData, $cacheId);
$cacheData = $db->dbResultFetch();

$region = new GetRegions();
$regiony = $region->GetRegion('', '', $cacheData['latitude'], $cacheData['longitude']);
print '<pre>';
var_dump($regiony);

exit;


require_once 'powerTrail/powerTrailBase.php';
// powerTrailBase::writePromoPt4mainPage();	35	// powerTrailBase::writePromoPt4mainPage();
require_once 'util.sec/geokrety/processGeokretyErrors.php';	
require_once 'util.sec/geokrety/processGeokretyErrors.php';
$g = new processGeokretyErrors;	
$g->run();
?>