<?php
session_start();
if(!isset($_SESSION['user_id'])){
    print 'no hacking please!';
    exit;
}
require_once __DIR__.'/../lib/db.php';
require_once __DIR__.'/powerTrailController.php';
require_once __DIR__.'/powerTrailBase.php';
$ptAPI = new powerTrailBase;
$db = new dataBase(false);

$projectId = $_REQUEST['projectId'];
$userId = $_REQUEST['userId'];

if (is_numeric($userId)) {
    $queryParam = ' user_id = ';
} else {
    $queryParam = ' username LIKE ';
}
$query = 'SELECT user_id, username FROM user WHERE '.$queryParam .' :1';
$db->multiVariableQuery($query, $userId);
$userResult = $db->dbResultFetch();
$addQuery = "INSERT INTO `PowerTrail_owners`(`PowerTrailId`, `userId`, `privileages`) VALUES (:1,:2,:3)";
$db->multiVariableQuery($addQuery, $projectId, $userResult['user_id'], 1);

$logQuery = 'INSERT INTO `PowerTrail_actionsLog`(`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`) VALUES (:1,:2,NOW(),4,:3,:4)';
$db->multiVariableQuery($logQuery, $projectId, $_SESSION['user_id'] ,$ptAPI->logActionTypes[4]['type'].' new owner is: '.$userResult['user_id'], $userResult['user_id']);

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
            $ownerList .= '<span style="display: none" class="removeUserIcon"><img onclick="ajaxRemoveUserFromPt('.$userId.');" src="tpl/stdstyle/images/free_icons/cross.png" width=10 " /></span>, ';
        } else {
            $ownerList .= ', ';
        }
    }
    $ownerList = substr($ownerList, 0, -2);
    return $ownerList;
}
?>