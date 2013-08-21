<?php
$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/common.inc.php';
// session_start();
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
	// $q = 'UPDATE PowerTrail SET conquestedCount = (SELECT COUNT(*) FROM `PowerTrail_comments` WHERE `PowerTrailId` =:1 AND `commentType` = 2 AND `deleted` = 0)';
	$q = 'UPDATE `PowerTrail` SET `PowerTrail`.`conquestedCount`= (SELECT COUNT(*) FROM `PowerTrail_comments` WHERE `PowerTrail_comments`.`PowerTrailId` = :1 AND `PowerTrail_comments`.`commentType` = 2 AND `PowerTrail_comments`.`deleted` = 0 ) WHERE `PowerTrail`.`id` = :1 ';
	$db->multiVariableQuery($q, $projectId);
}

emailOwners($projectId, $_REQUEST['type'], $_REQUEST['datetime'], $text);

function emailOwners($ptId, $commentType, $commentDateTime, $commentText){
	global $octeam_email, $usr, $absolute_server_URI;
	require_once __DIR__.'/powerTrailBase.php';
	$owners = powerTrailBase::getPtOwners($ptId);
	$commentTypes = powerTrailBase::getPowerTrailComments();
	// var_dump($usr);
	
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
	$headers .= "From: OpenCaching <".$octeam_email.">\r\n";
	$headers .= "Reply-To: ".$octeam_email. "\r\n";
	
	$subject = "nowy wpis dla power traila $ptId";
	
	$commentText = htmlspecialchars_decode($commentText);
	$message = '<a href="'.$absolute_server_URI.'viewprofile.php?userid='.$usr['userid'].'>'.$usr['username'].'</a> '.tr('pt127');
	$message .= ' '.tr($commentTypes[$commentType]['translate'])." - $commentDateTime <br /><br /> $commentText";
	
	foreach ($owners as $owner) {
		$to = $owner['email'];
		$to = 'user@ocpl-devel';
		mb_send_mail($to, $subject, $message, $headers);
	}
	//debug only
	mb_send_mail('lza@tlen.pl', $subject, $message, $headers);
}
?>