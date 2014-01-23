<?php
session_start();
if(!isset($_SESSION['user_id'])){
    print 'no hacking please!';
    exit;
}
require_once __DIR__.'/../lib/db.php';
require_once __DIR__.'/powerTrailController.php';
$ptAPI = new powerTrailBase;

$powerTrailId = $_REQUEST['projectId'];
$ptDescription = htmlspecialchars($_REQUEST['ptDescription']);


// check if user is owner of selected power Trail
if($ptAPI::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $powerTrailId) == 1) {
    $query = 'UPDATE `PowerTrail` SET `description`= :1 WHERE `id` = :2';
    $db = new dataBase(false);
    $db->multiVariableQuery($query, $ptDescription, $powerTrailId);
}


?>