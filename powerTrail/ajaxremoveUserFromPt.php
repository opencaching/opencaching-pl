<?php

use src\Utils\Database\OcDb;

require __DIR__ . '/../vendor/autoload.php';

session_start();

if (! isset($_SESSION['user_id'])) {
    exit('No hacking please!');
}

$ptAPI = new powerTrailBase();
$db = OcDb::instance();

$projectId = $_REQUEST['projectId'];
$userId = $_REQUEST['userId'];

//check if user is only one owner
if (count($ptAPI::getPtOwners($projectId)) > 1 && $ptAPI::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $projectId) == 1) {
    $addQuery = 'DELETE FROM `PowerTrail_owners` WHERE `userId` = :1 AND  `PowerTrailId` = :2';
    $db->multiVariableQuery($addQuery, $userId, $projectId);

    $logQuery = 'INSERT INTO `PowerTrail_actionsLog`(`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`) VALUES (:1,:2,NOW(),5,:3,:4)';
    $db->multiVariableQuery($logQuery, $projectId, $_SESSION['user_id'], $ptAPI::getActionType(5) . ' removed owner is: ' . $userId, $userId);
}
$ptOwners = displayPtOwnerList(powerTrailBase::getPtOwners($projectId));

// $result = json_encode($cacheCountResult);
// sleep(5);
echo $ptOwners;

function displayPtOwnerList($ptOwners)
{
    $ownerList = '';

    foreach ($ptOwners as $userId => $user) {
        $ownerList .= '<a href="viewprofile.php?userid=' . $userId . '">' . $user['username'] . '</a>';

        if ($userId != $_SESSION['user_id']) {
            $ownerList .= '<span style="display: none" class="removeUserIcon"><img onclick="ajaxRemoveUserFromPt(' . $userId . ')" src="images/free_icons/cross.png" width=10 alt=""></span>, ';
        } else {
            $ownerList .= ', ';
        }
    }

    return substr($ownerList, 0, -2);
}
