<?php
session_start();
if(!isset($_SESSION['user_id'])){
	print 'no hacking please!';
	exit;
}
require_once __DIR__.'/../lib/db.php';
require_once __DIR__.'/powerTrailBase.php';
require_once __DIR__.'/sendEmail.php';

$powerTrailId = (int) $_REQUEST['ptId'];
$commentId = (int) $_REQUEST['commentId'];
$callingUser = (int) $_REQUEST['callingUser'];

if($callingUser != $_SESSION['user_id']) {
	print 'wrong user!';	
	exit;
}

//get selected comment and check if it is $callingUser comment.
$commentDbRow = powerTrailBase::getSingleComment($commentId);

// check if user is owner of selected power Trail
if(powerTrailBase::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $powerTrailId) == 1 || 
   $commentDbRow['userId'] == $callingUser
) {
	print "usuwam $commentId z $powerTrailId";
	$query = 'UPDATE `PowerTrail_comments` SET `deleted` = 1 WHERE `id` = :1';
	// AND `PowerTrailId` = :2
	$db = new dataBase(true);
	$db->multiVariableQuery($query, $commentId);
//TODO update ilości znalezień
}


emailOwners($powerTrailId, $commentDbRow['commentType'], $commentDbRow['logDateTime'], $commentDbRow['commentText'], 'delComment');

?>