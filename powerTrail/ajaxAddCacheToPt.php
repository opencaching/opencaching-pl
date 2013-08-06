<?php
/**
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
require_once __DIR__.'/powerTrailAPI.php';
$ptAPI = new powerTrailApi;
$projectId = $_REQUEST['projectId'];
$cacheId = $_REQUEST['cacheId'];
$db = new dataBase();
// check if cache is already cannected with any power trail
$query = 'SELECT `PowerTrailId` FROM `powerTrail_caches` WHERE `cacheId` = :1';
$db->multiVariableQuery($query, $cacheId);
$resultPowerTrailId=$db->dbResultFetch();

// $dump = print_r($resultPowerTrailId, true);
// file_put_contents('ptId', $dump);

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
}
if ($projectId > 0 && $caheIsAttaschedToPt===true) {
	removeCacheFromPowerTrail($cacheId, $resultPowerTrailId, $db, $ptAPI);
	addCacheToPowerTrail($cacheId, $projectId, $db, $ptAPI);
}

echo 'OK';


function addCacheToPowerTrail($cacheId, $projectId, $db, $ptAPI) {
	$query = 'INSERT INTO `powerTrail_caches`(`cacheId`, `PowerTrailId`) VALUES (:1,:2) ON DUPLICATE KEY UPDATE PowerTrailId=VALUES(PowerTrailId)';
	$db->multiVariableQuery($query, $cacheId, $projectId);
	$queryInsertedCacheData = 'SELECT longitude, latitude FROM caches WHERE cache_id = :1';
	$db->multiVariableQuery($queryInsertedCacheData, $cacheId);
	$insertedCacheData = $db->dbResultFetch();
	$query = 'SELECT centerLatitude, centerLongitude FROM PowerTrail WHERE `id` = :1 ';
	$db->multiVariableQuery($query, $projectId);
	$result=$db->dbResultFetch();
	if($result['centerLatitude']==0 && $result['centerLongitude']==0){
		$query = '	UPDATE 	`PowerTrail` SET `cacheCount`=`cacheCount`+1, `centerLatitude` = :2, `centerLongitude` = :3
	 				WHERE 	`id` = :1';
		$db->multiVariableQuery($query, $projectId, $insertedCacheData['latitude'], $insertedCacheData['longitude']);			
	} else {
		$query = 'UPDATE 	`PowerTrail` SET `cacheCount`=`cacheCount`+1,
							`centerLatitude` = (`centerLatitude` + '.$insertedCacheData['latitude'].')/2,	
							`centerLongitude` = (`centerLongitude` + '.$insertedCacheData['longitude'].')/2
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
