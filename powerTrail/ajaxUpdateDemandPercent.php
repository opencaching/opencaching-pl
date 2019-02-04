<?php
use Utils\Database\OcDb;
// ajaxUpdateDemandPercent.php
session_start();
if(!isset($_SESSION['user_id'])){
    print 'no hacking please!';
    exit;
}
require_once __DIR__.'/../lib/ClassPathDictionary.php';
$ptAPI = new powerTrailBase;

$powerTrailId = (int) $_REQUEST['projectId'];
$newPercent = (int) $_REQUEST['newPercent'];
if($newPercent < \lib\Controllers\PowerTrailController::MINIMUM_PERCENT_REQUIRED || $newPercent > 100) {
    echo 'error';
    exit;
}

// check if user is owner of selected power Trail
if($ptAPI::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $powerTrailId) == 1) {
    $query = 'UPDATE `PowerTrail` SET `perccentRequired`= :1 WHERE `id` = :2';
    $db = OcDb::instance();
    $db->multiVariableQuery($query, $newPercent, $powerTrailId);
    echo $newPercent;
}
