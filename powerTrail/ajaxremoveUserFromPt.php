<?php
session_start();
if(!isset($_SESSION['user_id'])){
	print 'no hacking please!';
	exit;
}
require_once __DIR__.'/../lib/db.php';
require_once __DIR__.'/powerTrailController.php';
$ptAPI = new powerTrailBase;
$db = new dataBase(false);

$projectId = $_REQUEST['projectId'];
$userId = $_REQUEST['userId'];


//check if user is only one owner
if(count(powerTrailBase::getPtOwners($projectId)) > 1 && $ptAPI::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $projectId) == 1) {
	$addQuery = 'DELETE FROM `PowerTrail_owners` WHERE `userId` = :1 AND  `PowerTrailId` = :2';
	$db->multiVariableQuery($addQuery, $userId, $projectId);
	
	$logQuery = 'INSERT INTO `PowerTrail_actionsLog`(`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`) VALUES (:1,:2,NOW(),5,:3,:4)';
	$db->multiVariableQuery($logQuery, $projectId, $_SESSION['user_id'] ,$ptAPI->logActionTypes[5]['type'].' removed owner is: '.$userId, $userId);
}
$pt = new powerTrailController($_SESSION['user_id']);
$pt->findPtOwners($projectId);
$ptOwners = displayPtOwnerList($pt->getPtOwners());

// $result = json_encode($cacheCountResult);
// sleep(5);
echo $ptOwners;

function displayPtOwnerList($ptOwners)
{
	$ownerList = '';
	foreach ($ptOwners as $userId => $user) {
		$ownerList .= '<a href="viewprofile.php?userid='.$userId.'">'.$user['username'].'</a>';
		if($userId != $_SESSION['user_id']) {
			$ownerList .= '<span style="display: none" class="removeUserIcon"><img onclick="ajaxRemoveUserFromPt('.$userId.');" src="tpl/stdstyle/images/free_icons/cross.png" width=10 /></span>, ';
		} else {
			$ownerList .= ', ';
		}
	}
	$ownerList = substr($ownerList, 0, -2);
	return $ownerList;
}
?>