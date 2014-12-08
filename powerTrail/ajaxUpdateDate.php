<?php
session_start();
if(!isset($_SESSION['user_id'])){
    print 'no hacking please!';
    exit;
}
require_once __DIR__.'/../lib/ClassPathDictionary.php';
$ptAPI = new powerTrailBase;

$powerTrailId = $_REQUEST['projectId'];
$newDate = $_REQUEST['newDate'] . ' 0:00:01';


// check if user is owner of selected power Trail
if($ptAPI::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $powerTrailId) == 1) {
    $query = 'UPDATE `PowerTrail` SET `dateCreated`= :1 WHERE `id` = :2';
    $db = \lib\Database\DataBaseSingleton::Instance();
    $db->multiVariableQuery($query, $newDate, $powerTrailId);
}

?>