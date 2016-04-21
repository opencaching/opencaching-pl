<?php
use Utils\Database\OcDb;
session_start();
if(!isset($_SESSION['user_id'])){
    die(json_encode(array('resultSuccess' => false, 'error' => 'User is not logged in')));
}
require_once __DIR__.'/../lib/ClassPathDictionary.php';
$ptAPI = new powerTrailBase;

$powerTrailId = $_REQUEST['projectId'];
try{
    $newDate = new DateTime($_REQUEST['newDate']);
} catch (Exception $ex) {
    die(json_encode(array('resultSuccess' => false, 'error' => 'Wrong date')));
}

// check if user is owner of selected power Trail
if($ptAPI::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $powerTrailId) == 1) {
    $query = 'UPDATE `PowerTrail` SET `dateCreated`= :1 WHERE `id` = :2';
    $db = OcDb::instance();
    $db->multiVariableQuery($query, $newDate->format('Y-m-d H:i:s'), $powerTrailId);
    die(json_encode(array('resultSuccess' => true, 'error' => null)));
} else {
    die(json_encode(array('resultSuccess' => false, 'error' => 'User is not powerTrail owner')));
}
