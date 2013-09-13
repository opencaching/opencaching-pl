<?php
require_once __DIR__.'/powerTrailBase.php';
require_once __DIR__.'/sendEmail.php';
if(!isset($_SESSION['user_id'])){
	print 'no hacking please! Fuck You!';
	exit;
}
require_once __DIR__.'/../lib/db.php';


$db = new dataBase(false);
if($_REQUEST['type'] == 2){ // check if PT is already conquested by user
	$mySqlRequest = 'SELECT count( * ) AS `ptConquestCount` FROM `PowerTrail_comments` WHERE `commentType` =2 AND `deleted` =0 AND `userId` =:1 ';
	$db->multiVariableQuery($mySqlRequest, $_SESSION['user_id']);
	$mySqlResult = $db->dbResultFetch();
	if ($mySqlResult['ptConquestCount'] > 0) {
		echo 'pt conquested before';
		exit;
	}
}
$projectId = $_REQUEST['projectId'];
$text = htmlspecialchars($_REQUEST['text']);
$query = 
'INSERT INTO `PowerTrail_comments`(`userId`, `PowerTrailId`, `commentType`, `commentText`, `logDateTime`, `dbInsertDateTime`, `deleted`) 
                           VALUES (:1,        :2,             :3            ,:4 ,           :5,           NOW(), 			   0 )';
$db->multiVariableQuery($query, $_SESSION['user_id'], $projectId, $_REQUEST['type'],  $text, $_REQUEST['datetime'] );

if($_REQUEST['type'] == 2){
	$q = 'UPDATE `PowerTrail` SET `PowerTrail`.`conquestedCount`= (SELECT COUNT(*) FROM `PowerTrail_comments` WHERE `PowerTrail_comments`.`PowerTrailId` = :1 AND `PowerTrail_comments`.`commentType` = 2 AND `PowerTrail_comments`.`deleted` = 0 ) WHERE `PowerTrail`.`id` = :1 ';
	$db->multiVariableQuery($q, $projectId);
}

emailOwners($projectId, $_REQUEST['type'], $_REQUEST['datetime'], $text, 'newComment');


?>