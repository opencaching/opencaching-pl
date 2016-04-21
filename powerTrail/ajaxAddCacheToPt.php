<?php

use lib\Objects\GeoCache\GeoCache;
use Utils\Database\OcDb;
use Utils\Database\XDb;

/**
 * ajaxAddCacheToPt.php
 * this script add or remove cache to specified power Trail.
 * do neccesary checks and calculations like gometrical power trail lat/lon.
 *
 * works via Ajax call.
 */
$rootpath = __DIR__ . '/../';
require_once __DIR__ . '/../lib/common.inc.php';

if (!isset($_SESSION['user_id'])) {
    print 'no hacking please!';
    exit;
}
$ptAPI = new powerTrailBase;

if (isset($_REQUEST['rcalcAll'])) {
    recalculateOnce();
    print '<br><br><b>cachePoints were updated</b>';
    exit;
}


isset($_REQUEST['projectId']) ? $projectId = $_REQUEST['projectId'] : exit;
isset($_REQUEST['cacheId']) ? $cacheId = (int) $_REQUEST['cacheId'] : exit;
isset($_REQUEST['rmOtherUserCacheFromPt']) ? $rmOtherUserCacheFromPt = true : $rmOtherUserCacheFromPt = false;

$db = OcDb::instance();

// check if cache is already cannected with any power trail
$resultPowerTrailId['PowerTrailId'] = XDb::xMultiVariableQueryValue(
    'SELECT `PowerTrailId` FROM `powerTrail_caches` WHERE `cacheId` = :1',
    false, $cacheId);

if (isset($_REQUEST['removeByCOG']) && $_SESSION['ptRmByCog'] === 1) {
    removeCacheFromPowerTrail($cacheId, $resultPowerTrailId, $db, $ptAPI);
    recalculate($resultPowerTrailId['PowerTrailId']);
    print 'removedByCOG';
    exit;
}

if (isset($resultPowerTrailId['PowerTrailId']) && $resultPowerTrailId['PowerTrailId'] != 0) {
    $geocacheIsAttaschedToPt = true;
} else {
    $geocacheIsAttaschedToPt = false;
}

if ($rmOtherUserCacheFromPt === true) {
    if ($geocacheIsAttaschedToPt === true) {
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
if (!$loggeduserCache) {
    $isCacheCanditate = isCacheCanditate($projectId, $cacheId);
    if (!$isCacheCanditate && !$geocacheIsAttaschedToPt) {
        addCacheToCacheCandidate($cacheId, $projectId);
    } else {
        print 'cache is already candidate or belongs to other pt';
        exit;
    }
}
if (isset($_REQUEST['calledFromConfirm']) && $_REQUEST['calledFromConfirm'] === 1) {
    addCacheToPowerTrail($cacheId, $projectId, OcDb::instance(), $ptAPI);
    $cacheAddedToPt = true;
    return;
}

$ptDbRow = powerTrailBase::getPtDbRow($resultPowerTrailId['PowerTrailId']);

if ($projectId > 0 && $geocacheIsAttaschedToPt === false) {
    addCacheToPowerTrail($cacheId, $projectId, $db, $ptAPI);
}
if ($ptDbRow['conquestedCount'] > 0) { // cache bellongs to PT wchcich was 'completed'
    print "this cache cannot be removed";
}
if ($projectId <= 0 && $ptDbRow['conquestedCount'] == 0) {
    removeCacheFromPowerTrail($cacheId, $resultPowerTrailId, $db, $ptAPI);
    recalculate($resultPowerTrailId['PowerTrailId']);
    print 'removed';
}
if ($projectId > 0 && $geocacheIsAttaschedToPt === true && $ptDbRow['conquestedCount'] == 0) {
    removeCacheFromPowerTrail($cacheId, $resultPowerTrailId, $db, $ptAPI);
    addCacheToPowerTrail($cacheId, $projectId, $db, $ptAPI);
    recalculate($resultPowerTrailId['PowerTrailId']);
}

function geocacheStatusCheck($cacheId)
{
    $geocache = new GeoCache(array('cacheId' => $cacheId));
    $forbidenCacheStatuses = array(
        GeoCache::STATUS_BLOCKED,
        GeoCache::STATUS_ARCHIVED,
        GeoCache::STATUS_WAITAPPROVERS,
    );
    if (in_array($geocache->getStatus(), $forbidenCacheStatuses)) {
        print 'geocache of this status cannot be added';
        return false;
    }
    return true;
}

function addCacheToPowerTrail($cacheId, $projectId, $db, $ptAPI)
{
    if (geocacheStatusCheck($cacheId) === false) {
        return false;
    }

    $db->multiVariableQuery(
        'INSERT INTO `powerTrail_caches`(`cacheId`, `PowerTrailId`, `points`)
        VALUES (:1,:2,:3) ON DUPLICATE KEY UPDATE PowerTrailId=VALUES(PowerTrailId)',
        $cacheId, $projectId, getCachePoints($cacheId));

    $stmt = $db->multiVariableQuery(
        'SELECT longitude, latitude FROM caches WHERE cache_id = :1 LIMIT 1',
        $cacheId);
    $insertedCacheData = $db->dbResultFetchOneRowOnly($stmt);

    $stmt = $db->multiVariableQuery(
        'SELECT centerLatitude, centerLongitude FROM PowerTrail WHERE `id` = :1 LIMIT 1',
        $projectId);

    $result = $db->dbResultFetch($stmt);

    $cachePoints = getCachePoints($cacheId);
    if ($result['centerLatitude'] == 0 && $result['centerLongitude'] == 0) {

        $db->multiVariableQuery(
            'UPDATE `PowerTrail`
            SET `cacheCount`=`cacheCount`+1, `centerLatitude` = :2, `centerLongitude` = :3, `points` = :4
            WHERE `id` = :1',
            $projectId, $insertedCacheData['latitude'], $insertedCacheData['longitude'], $cachePoints);

    } else {
        $query = 'UPDATE `PowerTrail` SET `cacheCount`=`cacheCount`+1,
                         `centerLatitude` = (`centerLatitude` + ' . $insertedCacheData['latitude'] . ')/2,
                         `centerLongitude` = (`centerLongitude` + ' . $insertedCacheData['longitude'] . ')/2,
                         `points` = `points` + ' . $cachePoints . '
                 WHERE `id` = :1';
        $db->multiVariableQuery($query, $projectId);
    }

    $db->multiVariableQuery(
        'INSERT INTO `PowerTrail_actionsLog`(`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`)
        VALUES (:1,:2,NOW(),2,:3,:4)'
        $logQuery, $projectId, $_SESSION['user_id'], $ptAPI->logActionTypes[2]['type'], $cacheId);

    print 'cacheAddedToPt';
}

function removeCacheFromPowerTrail($cacheId, $resultPowerTrailId, $db, $ptAPI)
{
    $db->multiVariableQuery(
        'DELETE FROM `powerTrail_caches` WHERE `cacheId` = :1 AND `PowerTrailId` = :2',
        $cacheId, $resultPowerTrailId['PowerTrailId']);

    $db->multiVariableQuery(
        'UPDATE `PowerTrail` SET `cacheCount`=`cacheCount`-1 WHERE `id` = :1',
        $resultPowerTrailId['PowerTrailId']);

    $db->multiVariableQuery(
        'INSERT INTO `PowerTrail_actionsLog`(`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`)
        VALUES (:1,:2,NOW(),3,:3,:4)',
        $resultPowerTrailId['PowerTrailId'], $_SESSION['user_id'], $ptAPI->logActionTypes[3]['type'], $cacheId);
}

function getCachePoints($cacheId)
{
    $db = OcDb::instance();

    $stmt = $db->multiVariableQuery(
        'SELECT * FROM caches WHERE cache_id = :1 LIMIT 1',
        $cacheId);
    $cacheData = $db->dbResultFetch($stmt);

    $typePoints = powerTrailBase::cacheTypePoints();
    $sizePoints = powerTrailBase::cacheSizePoints();
    $typePoints = $typePoints[$cacheData['type']];
    $sizePoints = $sizePoints[$cacheData['size']];
    $url = 'http://maps.googleapis.com/maps/api/elevation/xml?locations=' . $cacheData['latitude'] . ',' . $cacheData['longitude'] . '&sensor=false';
    $altitude = simplexml_load_file($url);
    $altitude = round($altitude->result->elevation);
    if ($altitude <= 400)
        $altPoints = 1;
    else
        $altPoints = 1 + ($altitude - 400) / 200;
    $difficPoint = round($cacheData['difficulty'] / 3, 2);
    $terrainPoints = round($cacheData['terrain'] / 3, 2);
    return ($altPoints + $typePoints + $sizePoints + $difficPoint + $terrainPoints);
}

function recalculate($projectId)
{
    $db = OcDb::instance();
    $stmt = $db->multiVariableQuery(
        'SELECT * FROM `caches` where `cache_id` IN (SELECT `cacheId` FROM `powerTrail_caches` WHERE `PowerTrailId` =:1 )',
        $projectId);
    $allCaches = $db->dbResultFetchAll($stmt);

    $newData = powerTrailBase::recalculateCenterAndPoints($allCaches);

    $db->multiVariableQuery(
        'UPDATE `PowerTrail` SET `cacheCount`= :1, `centerLatitude` = :3, `centerLongitude` = :4, `points` = :5 WHERE `id` = :2',
        $newData['cacheCount'], $projectId, $newData['avgLat'], $newData['avgLon'], $newData['points']);
}

/**
 * just for recalculate all points in powerTrail_caches if any problem occur
 * or points algo were changed.
 */
function recalculateOnce()
{
    $db = OcDb::instance();

    $stmt = $db->multiVariableQuery(
        'SELECT * FROM `caches` where `cache_id` IN (SELECT `cacheId` FROM `powerTrail_caches` WHERE 1)');

    $allCaches = $db->dbResultFetchAll($stmt);

    foreach ($allCaches as $cache) {
        $cachePoints = powerTrailBase::getCachePoints($cache);

        $db->multiVariableQuery(
            'UPDATE `powerTrail_caches` SET `points`=:1 WHERE `cacheId`=:2',
            $cachePoints, $cache['cache_id']);

        print $cache['wp_oc'] . ' updated ' . $cachePoints . '<br/>';
    }
}

function isCacheOwnByLoggedUser($geocacheId)
{

    $db = OcDb::instance();
    $stmt = $db->multiVariableQuery(
        'SELECT  `user_id` AS `userId`  FROM  `caches` WHERE  `cache_id` = :1 LIMIT 1',
        $geocacheId);
    $result = $db->dbResultFetch($stmt);

    if ($result['userId'] == $_SESSION['user_id'])
        return true;
    else
        return false;
}

function isCacheCanditate($ptId, $cacheId)
{

    $db = OcDb::instance();
    $stmt = $db->multiVariableQuery(
        "SELECT count(*) AS `c` FROM `PowerTrail_cacheCandidate` WHERE `PowerTrailId` = :1 AND `cacheId` =:2",
        $ptId, $cacheId);
    $result = $db->dbResultFetch($stmt);

    if ($result['c'] > 0)
        return true;
    else
        return false;
}

function addCacheToCacheCandidate($cacheId, $ptId)
{
    $linkCode = randomPassword(50);

    $db = OcDb::instance();
    $db->multiVariableQuery(
        "INSERT INTO `PowerTrail_cacheCandidate`(`PowerTrailId`, `cacheId`, `link`, `date`) VALUES (:1,:2,:3,NOW())",
        $ptId, $cacheId, $linkCode);

    require_once 'sendEmailCacheCandidate.php';
    emailCacheOwner($ptId, $cacheId, $linkCode);
    print 'cache added as cache candidate';
    exit;
}

function randomPassword($passLenght)
{
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $passLenght; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
