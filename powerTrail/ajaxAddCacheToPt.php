<?php
/**
 * ajaxAddCacheToPt.php
 * this script add or remove cache to specified power Trail.
 * do neccesary checks and calculations like gometrical power trail lat/lon.
 *
 * works via Ajax call.
 */
$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/common.inc.php';
db_disconnect();
if(!isset($_SESSION['user_id'])){
    print 'no hacking please!';
    exit;
}
$ptAPI = new powerTrailBase;

if(isset($_REQUEST['rcalcAll'])){
    recalculateOnce();
    print '<br><br><b>cachePoints were updated</b>';
    exit;
}


isset($_REQUEST['projectId']) ? $projectId = $_REQUEST['projectId'] : exit;
isset($_REQUEST['cacheId']) ? $cacheId = $_REQUEST['cacheId'] : exit;
isset($_REQUEST['rmOtherUserCacheFromPt']) ? $rmOtherUserCacheFromPt = true : $rmOtherUserCacheFromPt = false;

$db = \lib\Database\DataBaseSingleton::Instance();
// check if cache is already cannected with any power trail
$query = 'SELECT `PowerTrailId` FROM `powerTrail_caches` WHERE `cacheId` = :1';
$db->multiVariableQuery($query, $cacheId);
$resultPowerTrailId=$db->dbResultFetch();

if(isset($_REQUEST['removeByCOG']) && $_SESSION['ptRmByCog'] === 1){
    removeCacheFromPowerTrail($cacheId, $resultPowerTrailId, $db, $ptAPI);
    recalculate($resultPowerTrailId['PowerTrailId']);
    print 'removedByCOG';
    exit;
}

if(isset($resultPowerTrailId['PowerTrailId']) && $resultPowerTrailId['PowerTrailId'] != 0){
    $caheIsAttaschedToPt = true;
} else {
    $caheIsAttaschedToPt = false;
}

if($rmOtherUserCacheFromPt === true){
    if ($caheIsAttaschedToPt === true){
        removeCacheFromPowerTrail($cacheId, $resultPowerTrailId, $db, $ptAPI);
        recalculate($resultPowerTrailId['PowerTrailId']);
        print 'Removed';
    } else {
        print 'cache not assigned to Power Trail';
    }
    exit;
}

// check if adding cache is a logged user cache (boolean)
$loggeduserCache = isCacheOwnByLoggedUser($cacheId);
if(!$loggeduserCache) {
    $isCacheCanditate = isCacheCanditate($projectId, $cacheId);
    if(!$isCacheCanditate && !$caheIsAttaschedToPt) {
        addCacheToCacheCandidate($cacheId, $projectId);
    } else {
        print 'cache is already candidate or belongs to other pt';
        exit;
    }
}
if (isset($_REQUEST['calledFromConfirm']) && $_REQUEST['calledFromConfirm'] === 1 ){
    addCacheToPowerTrail($cacheId, $projectId, new dataBase(), $ptAPI);
    $cacheAddedToPt = true;
    return;
}

$ptDbRow = powerTrailBase::getPtDbRow($resultPowerTrailId['PowerTrailId']);

if ($projectId > 0 && $caheIsAttaschedToPt===false) {
    addCacheToPowerTrail($cacheId, $projectId, $db, $ptAPI);
    print 'cacheAddedToPt';
}
if($ptDbRow['conquestedCount'] > 0){ // cache bellongs to PT wchcich was 'completed'
    print "this cache cannot be removed";
}
if ($projectId <= 0 && $ptDbRow['conquestedCount'] == 0){
    removeCacheFromPowerTrail($cacheId, $resultPowerTrailId, $db, $ptAPI);
    recalculate($resultPowerTrailId['PowerTrailId']);
    print 'removed';
}
if ($projectId > 0 && $caheIsAttaschedToPt===true && $ptDbRow['conquestedCount'] == 0) {
    removeCacheFromPowerTrail($cacheId, $resultPowerTrailId, $db, $ptAPI);
    addCacheToPowerTrail($cacheId, $projectId, $db, $ptAPI);
    recalculate($resultPowerTrailId['PowerTrailId']);
    print 'cacheAddedToPt';
}


function addCacheToPowerTrail($cacheId, $projectId, $db, $ptAPI) {
    $query = 'INSERT INTO `powerTrail_caches`(`cacheId`, `PowerTrailId`, `points`) VALUES (:1,:2,:3) ON DUPLICATE KEY UPDATE PowerTrailId=VALUES(PowerTrailId)';
    $db->multiVariableQuery($query, $cacheId, $projectId, getCachePoints($cacheId));
    $queryInsertedCacheData = 'SELECT longitude, latitude FROM caches WHERE cache_id = :1 LIMIT 1';
    $db->multiVariableQuery($queryInsertedCacheData, $cacheId);
    $insertedCacheData = $db->dbResultFetch();
    $query = 'SELECT centerLatitude, centerLongitude FROM PowerTrail WHERE `id` = :1 LIMIT 1';
    $db->multiVariableQuery($query, $projectId);
    $result=$db->dbResultFetch();
    $cachePoints = getCachePoints($cacheId);
    if($result['centerLatitude']==0 && $result['centerLongitude']==0){
        $query = '  UPDATE  `PowerTrail` SET `cacheCount`=`cacheCount`+1, `centerLatitude` = :2, `centerLongitude` = :3, `points` = :4
                    WHERE   `id` = :1';
        $db->multiVariableQuery($query, $projectId, $insertedCacheData['latitude'], $insertedCacheData['longitude'], $cachePoints);
    } else {
        $query = 'UPDATE    `PowerTrail` SET `cacheCount`=`cacheCount`+1,
                            `centerLatitude` = (`centerLatitude` + '.$insertedCacheData['latitude'].')/2,
                            `centerLongitude` = (`centerLongitude` + '.$insertedCacheData['longitude'].')/2,
                            `points` = `points` + '.$cachePoints.'
                    WHERE   `id` = :1';
                    $db->multiVariableQuery($query, $projectId);
    }

    $logQuery = 'INSERT INTO `PowerTrail_actionsLog`(`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`) VALUES (:1,:2,NOW(),2,:3,:4)';
    $db->multiVariableQuery($logQuery, $projectId,$_SESSION['user_id'] ,$ptAPI->logActionTypes[2]['type'], $cacheId);
}

function removeCacheFromPowerTrail($cacheId, $resultPowerTrailId, $db, $ptAPI){
    $query = 'DELETE FROM `powerTrail_caches` WHERE `cacheId` = :1 AND `PowerTrailId` = :2';
    $db->multiVariableQuery($query, $cacheId, $resultPowerTrailId['PowerTrailId']);
    $query = 'UPDATE `PowerTrail` SET `cacheCount`=`cacheCount`-1 WHERE `id` = :1';
    $db->multiVariableQuery($query, $resultPowerTrailId['PowerTrailId']);
    $logQuery = 'INSERT INTO `PowerTrail_actionsLog`(`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`) VALUES (:1,:2,NOW(),3,:3,:4)';
    $db->multiVariableQuery($logQuery, $resultPowerTrailId['PowerTrailId'],$_SESSION['user_id'] ,$ptAPI->logActionTypes[3]['type'], $cacheId);
}

function getCachePoints($cacheId){
    $db = \lib\Database\DataBaseSingleton::Instance();
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
    return ($altPoints + $typePoints + $sizePoints + $difficPoint + $terrainPoints);
}

function recalculate($projectId) {
    $allCachesQuery = 'SELECT * FROM `caches` where `cache_id` IN (SELECT `cacheId` FROM `powerTrail_caches` WHERE `PowerTrailId` =:1 )';
    $db = \lib\Database\DataBaseSingleton::Instance();
    $db->multiVariableQuery($allCachesQuery, $projectId);
    $allCaches = $db->dbResultFetchAll();
    $newData = powerTrailBase::recalculateCenterAndPoints($allCaches);
    $updateQuery = 'UPDATE `PowerTrail` SET `cacheCount`= :1, `centerLatitude` = :3, `centerLongitude` = :4, `points` = :5 WHERE `id` = :2';
    $db->multiVariableQuery($updateQuery, $newData['cacheCount'], $projectId, $newData['avgLat'], $newData['avgLon'], $newData['points']);
}

/**
 * just for recalculate all points in powerTrail_caches if any problem occur
 * or points algo were changed.
 */
function recalculateOnce() {
    $allCachesQuery = 'SELECT * FROM `caches` where `cache_id` IN (SELECT `cacheId` FROM `powerTrail_caches` WHERE 1)';
    $db = \lib\Database\DataBaseSingleton::Instance();
    $db->multiVariableQuery($allCachesQuery);
    $allCaches = $db->dbResultFetchAll();

    foreach ($allCaches as $cache) {
        $cachePoints = powerTrailBase::getCachePoints($cache);
        $updateQuery = 'UPDATE `powerTrail_caches` SET `points`=:1 WHERE `cacheId`=:2';
        $db->multiVariableQuery($updateQuery, $cachePoints, $cache['cache_id']);
        print $cache['wp_oc'].' updated '.$cachePoints.'<br/>';
    }
    // print '<pre>';
    // print_r($allCaches);
}


function isCacheOwnByLoggedUser($caheId){
    $query = 'SELECT  `user_id` AS `userId`  FROM  `caches` WHERE  `cache_id` = :1 LIMIT 1';
    $db = \lib\Database\DataBaseSingleton::Instance();
    $db->multiVariableQuery($query, $caheId);
    $result = $db->dbResultFetch();
    if($result['userId'] == $_SESSION['user_id']) return true;
    else return false;
}

function isCacheCanditate($ptId, $cacheId){
    $q = "SELECT count(*) AS `c` FROM `PowerTrail_cacheCandidate` WHERE `PowerTrailId` = :1 AND `cacheId` =:2";
    $db = \lib\Database\DataBaseSingleton::Instance();
    $db->multiVariableQuery($q, $ptId, $cacheId);
    $result = $db->dbResultFetch();
    if($result['c']>0) return true;
    else return false;
}

function addCacheToCacheCandidate($cacheId, $ptId){
    $linkCode = randomPassword(50);
    $q = "INSERT INTO `PowerTrail_cacheCandidate`(`PowerTrailId`, `cacheId`, `link`, `date`) VALUES (:1,:2,:3,NOW())";

    $db = \lib\Database\DataBaseSingleton::Instance();
    $db->multiVariableQuery($q, $ptId, $cacheId, $linkCode);

    require_once 'sendEmailCacheCandidate.php';
    emailCacheOwner($ptId, $cacheId, $linkCode);
    print 'cache added as cache candidate';
    exit;
}

function randomPassword($passLenght) {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $passLenght; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
