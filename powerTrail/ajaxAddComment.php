<?php
session_start();
if(!isset($_SESSION['user_id'])){
	print 'no hacking please!';
	exit;
}
require_once __DIR__.'/../lib/db.php';

$projectId = $_REQUEST['projectId'];
$text = htmlspecialchars($_REQUEST['text']);

$query = 
'INSERT INTO `PowerTrail_comments`(`userId`, `PowerTrailId`, `commentType`, `commentText`, `logDateTime`, `dbInsertDateTime`, `deleted`) 
                           VALUES (:1,        :2,             :3            ,:4 ,           :5,           NOW(), 			   0 )';
$db = new dataBase(false);
$db->multiVariableQuery($query, $_SESSION['user_id'], $projectId, $_REQUEST['type'],  $text, $_REQUEST['datetime'] );
if($_REQUEST['type'] == 2){
	$q = 'UPDATE PowerTrail SET conquestedCount = (SELECT COUNT( * ) FROM `PowerTrail_comments` WHERE `PowerTrailId` =:1 AND `commentType` = 2 AND `deleted` = 0)';
	$db->multiVariableQuery($query, $projectId);
}

?>