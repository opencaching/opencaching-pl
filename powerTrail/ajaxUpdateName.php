<?php
// ajaxUpdateDemandPercent.php
session_start();
if(!isset($_SESSION['user_id'])){
    print 'no hacking please!';
    exit;
}
require_once __DIR__.'/../lib/ClassPathDictionary.php';

$ptAPI = new powerTrailBase;
$powerTrailId = (int) $_REQUEST['projectId'];
$newName = strip_tags($_REQUEST['newNamePt']);
if($newName == '') {
    echo 'error - no name was entered';
    exit;
}

// check if user is owner of selected power Trail
if($ptAPI::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $powerTrailId) == 1) {
    $query = 'UPDATE `PowerTrail` SET `name` = :1 WHERE `id` = :2';
    $db = new dataBase();
    $db->multiVariableQuery($query, $newName, $powerTrailId);
    echo $newName;
}
?>