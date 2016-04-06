<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    print 'no hacking please!';
    exit;
}
require_once __DIR__ . '/../lib/ClassPathDictionary.php';
$ptAPI = new powerTrailBase;
$db = \Utils\Database\OcDb::instance();

$projectId = $_REQUEST['projectId'];
$userId = $_REQUEST['userId'];

if (is_numeric($userId)) {
    $queryParam = ' user_id = ';
} else {
    $queryParam = ' username LIKE ';
}
$query = 'SELECT user_id, username FROM user WHERE ' . $queryParam . ' :1';
$db->multiVariableQuery($query, $userId);
$userResult = $db->dbResultFetch();
$addQuery = "INSERT INTO `PowerTrail_owners`(`PowerTrailId`, `userId`, `privileages`) VALUES (:1,:2,:3)";
$db->multiVariableQuery($addQuery, $projectId, $userResult['user_id'], 1);

$logQuery = 'INSERT INTO `PowerTrail_actionsLog`(`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`) VALUES (:1,:2,NOW(),4,:3,:4)';
$db->multiVariableQuery($logQuery, $projectId, $_SESSION['user_id'], $ptAPI->logActionTypes[4]['type'] . ' new owner is: ' . $userResult['user_id'], $userResult['user_id']);

$powerTrail = new \lib\Objects\PowerTrail\PowerTrail(array('id' => $projectId));
$ptOwners = displayPtOwnerList($powerTrail->getOwners());

echo $ptOwners;

function displayPtOwnerList($ptOwners)
{
    $ownerList = '';
    foreach ($ptOwners as $user) {
        $ownerList .= '<a href="viewprofile.php?userid=' . $user->getUserId() . '">' . $user->getUsername() . '</a>';
        if ($user->getUserId() != $_SESSION['user_id']) {
            $ownerList .= '<span style="display: none" class="removeUserIcon"><img onclick="ajaxRemoveUserFromPt(' . $user->getUserId() . ');" src="tpl/stdstyle/images/free_icons/cross.png" width=10 " /></span>, ';
        } else {
            $ownerList .= ', ';
        }
    }
    return substr($ownerList, 0, -2);
}
