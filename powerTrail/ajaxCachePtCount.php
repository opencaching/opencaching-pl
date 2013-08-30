<?php
session_start();
if(!isset($_SESSION['user_id'])){
	print 'no hacking please!';
	exit;
}
require_once __DIR__.'/../lib/db.php';
require_once __DIR__.'/powerTrailBase.php';
$ptAPI = new powerTrailBase;
$db = new dataBase(false);

$projectId = $_REQUEST['projectId'];


$allCachesQuery = 'SELECT * FROM `caches` where `cache_id` IN (SELECT `cacheId` FROM `powerTrail_caches` WHERE `PowerTrailId` =:1 )';
$db->multiVariableQuery($allCachesQuery, $projectId);
$allCaches = $db->dbResultFetchAll();
$newData = recalculateCenterAndPoints($allCaches);

$query = 'SELECT count( `cacheId` ) AS cacheCount FROM `powerTrail_caches` WHERE `PowerTrailId` =:1';
$db->multiVariableQuery($query, $projectId);
$cacheCountResult = $db->dbResultFetch();
$cacheCountResult = $cacheCountResult['cacheCount'];
$updateQuery = 'UPDATE `PowerTrail` SET `cacheCount`= :1, 
`centerLatitude` = '.$newData['avgLat'].',
`centerLongitude` = '.$newData['avgLon'].',
`points` = '.$newData['points'].'

 WHERE `id` = :2';
$db->multiVariableQuery($updateQuery, $cacheCountResult, $projectId);

// $result = json_encode($cacheCountResult);
echo $cacheCountResult;

function recalculateCenterAndPoints($caches){
	
	$points = 0;
	$lat = 0;
	$lon = 0;
	$counter = 0;
	foreach ($caches as $cache){
		$points += powerTrailBase::getCachePoints($cache);
        $lat += $cache['latitude'];
		$lon += $cache['longitude'];
		$counter++;
	}

	if($counter>0){
		$result['avgLat'] = $lat/$counter;
		$result['avgLon'] = $lon/$counter;
	} else {
		$result['avgLat'] = 0;
		$result['avgLon'] = 0;
	}
	$result['points'] = $points;

	return $result;
}

function recalculatePoints(){
}
?>