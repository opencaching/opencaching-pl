<?php

include 'util.sec/geokrety/processGeokretyErrors.php';


exit;
require_once 'lib/db.php';
//require_once 'region_class.php';
//require_once 'lib/settings.inc.php';

$url = "http://geokrety.org/export2.php?wpt=OP1234";
$opts = array('http' =>
	array(
		'header'  => 'Content-type: application/x-www-form-urlencoded',
		'timeout' => 5,
	)
);
$context = stream_context_create($opts);
@$result1 = file_get_contents($url, false, $context);
if($result1){
	$result = simplexml_load_string($result1);
	echo '<pre>';
print_r($result);
} else print 'dup[a]';


exit;

$db = new dataBase;
$query = 
'SELECT c.wp_oc, c.cache_id, c.`status`, cl.adm3, c.latitude, c.longitude
FROM caches c
JOIN cache_location cl ON c.cache_id = cl.cache_id
JOIN user u ON u.user_id = c.user_id
WHERE cl.adm3 IS NULL
AND adm1 = "Polska"
AND c.status NOT
IN ( 3, 5 )';

$db->simpleQuery($query);
$cfix = $db->dbResultFetchAll();
$region = new GetRegions();

echo '<pre>';
print_r($cfix); 

foreach ($cfix as $key => $cf) {
	$regiony = $region->GetRegion($opt, $lang, $cf['latitude'], $cf['longitude']);
	var_dump($regiony);
	exit;
	$q = "UPDATE `cache_location` SET adm1 = :2, adm3 = :3, code1=:4, code3=:5 WHERE cache_id = :1";
	$db->multiVariableQuery($q,$cf['cache_id'],$regiony['adm1'],$regiony['adm3'],$regiony['code1'],$regiony['code3']);
	print "$q ".$cf['cache_id']." <br>";	
	echo '<pre><br>';
	print_r($regiony);

	unset($regiony, $q);
	exit;
}

