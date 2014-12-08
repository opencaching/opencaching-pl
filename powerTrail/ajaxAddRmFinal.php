<?php
//ajaxAddRmFinal.php
session_start();
if(!isset($_SESSION['user_id'])){
    print 'no hacking please!';
    exit;
}
require_once __DIR__.'/../lib/ClassPathDictionary.php';
$ptAPI = new powerTrailBase;

$powerTrailId = (int) $_REQUEST['projectId'];
$isFinal = (int) $_REQUEST['isFinal'];
$cacheId = (int) $_REQUEST['cacheId'];

// check if user is owner of selected power Trail
if($ptAPI::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $powerTrailId) == 1) {
    $query = 'UPDATE `powerTrail_caches` SET `isFinal`= :1 WHERE `cacheId`=:2 AND `PowerTrailId`=:3';
    $db = \lib\Database\DataBaseSingleton::Instance();
    $db->multiVariableQuery($query, $isFinal, $cacheId, $powerTrailId);
}

?>