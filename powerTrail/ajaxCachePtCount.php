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
$newData = powerTrailBase::recalculateCenterAndPoints($allCaches);

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

?>