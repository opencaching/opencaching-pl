<?php
/**
 * ajaxAddCacheToPt.php
 * this script add or remove cache to specified power Trail.
 * do neccesary checks and calculations like gometrical power trail lat/lon.
 * 
 * works via Ajax call.
 */
session_start();
if(!isset($_SESSION['user_id'])){
	print 'no hacking please!';
	exit;
}
require_once __DIR__.'/../lib/db.php';
require_once __DIR__.'/powerTrailBase.php';
$ptAPI = new powerTrailBase;
isset($_REQUEST['projectId']) ? $projectId = $_REQUEST['projectId'] : exit;
isset($_REQUEST['cacheId']) ? $cacheId = $_REQUEST['cacheId'] : exit;
$db = new dataBase();
// check if cache is already cannected with any power trail
$query = 'SELECT `PowerTrailId` FROM `powerTrail_caches` WHERE `cacheId` = :1';
$db->multiVariableQuery($query, $cacheId);
$resultPowerTrailId=$db->dbResultFetch();

if(isset($resultPowerTrailId['PowerTrailId']) && $resultPowerTrailId['PowerTrailId'] != 0){
	$caheIsAttaschedToPt = true;
} else {
	$caheIsAttaschedToPt = false;
}

if ($projectId > 0 && $caheIsAttaschedToPt===false) {
	addCacheToPowerTrail($cacheId, $projectId, $db, $ptAPI);
}
if ($projectId <= 0){
	removeCacheFromPowerTrail($cacheId, $resultPowerTrailId, $db, $ptAPI);
	recalculate($resultPowerTrailId['PowerTrailId']);
}
if ($projectId > 0 && $caheIsAttaschedToPt===true) {
	removeCacheFromPowerTrail($cacheId, $resultPowerTrailId, $db, $ptAPI);
	addCacheToPowerTrail($cacheId, $projectId, $db, $ptAPI);
	recalculate($resultPowerTrailId['PowerTrailId']);
}

echo 'OK';


function addCacheToPowerTrail($cacheId, $projectId, $db, $ptAPI) {
	$query = 'INSERT INTO `powerTrail_caches`(`cacheId`, `PowerTrailId`) VALUES (:1,:2) ON DUPLICATE KEY UPDATE PowerTrailId=VALUES(PowerTrailId)';
	$db->multiVariableQuery($query, $cacheId, $projectId);
	$queryInsertedCacheData = 'SELECT longitude, latitude FROM caches WHERE cache_id = :1 LIMIT 1';
	$db->multiVariableQuery($queryInsertedCacheData, $cacheId);
	$insertedCacheData = $db->dbResultFetch();
	$query = 'SELECT centerLatitude, centerLongitude FROM PowerTrail WHERE `id` = :1 LIMIT 1';
	$db->multiVariableQuery($query, $projectId);
	$result=$db->dbResultFetch();
	$cachePoints = getCachePoints($cacheId);
	if($result['centerLatitude']==0 && $result['centerLongitude']==0){
		$query = '	UPDATE 	`PowerTrail` SET `cacheCount`=`cacheCount`+1, `centerLatitude` = :2, `centerLongitude` = :3, `points` = :4
	 				WHERE 	`id` = :1';
		$db->multiVariableQuery($query, $projectId, $insertedCacheData['latitude'], $insertedCacheData['longitude'], $cachePoints);			
	} else {
		$query = 'UPDATE 	`PowerTrail` SET `cacheCount`=`cacheCount`+1,
							`centerLatitude` = (`centerLatitude` + '.$insertedCacheData['latitude'].')/2,	
							`centerLongitude` = (`centerLongitude` + '.$insertedCacheData['longitude'].')/2,
							`points` = `points` + '.$cachePoints.'
					WHERE 	`id` = :1';
					$db->multiVariableQuery($query, $projectId);
	}
	
	$logQuery = 'INSERT INTO `PowerTrail_actionsLog`(`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`) VALUES (:1,:2,NOW(),2,:3,:4)';
	$db->multiVariableQuery($logQuery, $projectId,$_SESSION['user_id'] ,$ptAPI->logActionTypes[2]['type'], $cacheId);
}

function removeCacheFromPowerTrail($cacheId, $resultPowerTrailId, $db, $ptAPI){
	$query = 'DELETE FROM `powerTrail_caches` WHERE `cacheId` = :1 AND `PowerTrailId` = :2';
	$db->multiVariableQuery($query, $cacheId, $resultPowerTrailId['PowerTrailId']);
	// TODO handle witch lat and lon when cache is removed from power trail. (undo average)
	$query = 'UPDATE `PowerTrail` SET `cacheCount`=`cacheCount`-1 WHERE `id` = :1';
	$db->multiVariableQuery($query, $resultPowerTrailId['PowerTrailId']);
	$logQuery = 'INSERT INTO `PowerTrail_actionsLog`(`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`) VALUES (:1,:2,NOW(),3,:3,:4)';
	$db->multiVariableQuery($logQuery, $resultPowerTrailId['PowerTrailId'],$_SESSION['user_id'] ,$ptAPI->logActionTypes[3]['type'], $cacheId);
	
}

function getCachePoints($cacheId){
	$db = new dataBase;
	$queryCacheData = 'SELECT * FROM caches WHERE cache_id = :1 LIMIT 1';
	$db->multiVariableQuery($queryCacheData, $cacheId);
	$cacheData = $db->dbResultFetch();
	$typePoints = powerTrailBase::cacheTypePoints();
	$sizePoints = powerTrailBase::cacheSizePoints();
	$typePoints = $typePoints[$cacheData['type']];
	$sizePoints = $sizePoints[$cacheData['size']];
	$url = 'http://maps.googleapis.com/maps/api/elevation/xml?locations='.$cacheData['latitude'].','.$cacheData['longitude'].'&sensor=false';
	$altitude = simplexml_load_file($url);
	$altitude = round($altitude->result->elevation);
	if ($altitude <= 400) $altPoints = 1;
	else $altPoints = 1+($altitude-400)/200 ;
	$difficPoint = round($cacheData['difficulty']/3,2);
	$terrainPoints = round($cacheData['terrain']/3,2);
	// print "alt: $altPoints / type: $typePoints / size: $sizePoints / dif: $difficPoint / ter: $difficPoint"; 
	return ($altPoints + $typePoints + $sizePoints + $difficPoint + $difficPoint);
}

function recalculate($projectId) {
	$allCachesQuery = 'SELECT * FROM `caches` where `cache_id` IN (SELECT `cacheId` FROM `powerTrail_caches` WHERE `PowerTrailId` =:1 )';
	$db = new dataBase();
	$db->multiVariableQuery($allCachesQuery, $projectId);
	$allCaches = $db->dbResultFetchAll();
	$newData = powerTrailBase::recalculateCenterAndPoints($allCaches);
	$updateQuery = 'UPDATE `PowerTrail` SET `cacheCount`= :1, `centerLatitude` = :3, `centerLongitude` = :4, `points` = :5 WHERE `id` = :2';
	$db->multiVariableQuery($updateQuery, $newData['cacheCount'], $projectId, $newData['avgLat'], $newData['avgLon'], $newData['points']);
}
