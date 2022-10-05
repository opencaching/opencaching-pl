<?php

use src\Models\ApplicationContainer;
use src\Models\PowerTrail\PowerTrail;
use src\Utils\Database\OcDb;
use src\Utils\Generators\Uuid;

require_once __DIR__ . '/../lib/common.inc.php';

$loggedUser = ApplicationContainer::GetAuthorizedUser();

if (! $loggedUser) {
    exit('User not authorized!');
}

$ptAPI = new powerTrailBase();

$powerTrailId = (int) $_REQUEST['projectId'];
$powerTrail = new PowerTrail(['id' => $powerTrailId]);
$newStatus = (int) $_REQUEST['newStatus'];

if (isset($_REQUEST['commentTxt'])) {
    $commentText = htmlspecialchars($_REQUEST['commentTxt']);
} else {
    $commentText = false;
}

// check if user is owner of selected power Trail
if ($ptAPI::checkIfUserIsPowerTrailOwner($loggedUser->getUserId(), $powerTrailId) == 1
    || $loggedUser->hasOcTeamRole()) {
    switch ($newStatus) {
        case PowerTrail::STATUS_OPEN: // publish
            $commentType = 3;

            if (! $commentText) {
                $commentText = tr('pt215') . '!';
            }
            break;
        case PowerTrail::STATUS_INSERVICE: // in service
            $commentType = 4;

            if (! $commentText) {
                $commentText = tr('pt217') . '!';
            }
            break;
        case PowerTrail::STATUS_CLOSED: // permanent Closure
            $commentType = 5;

            if (! $commentText) {
                $commentText = tr('pt218') . '!';
            }
            break;
        default:
            $commentType = 1;

            if (! $commentText) {
                $commentText = tr('pt056') . '!';
            }
            break;
    }

    // update geoPatch status
    $updateStatusResult = $powerTrail->setAndStoreStatus($newStatus);

    if ($updateStatusResult['updateStatusResult'] === true) {
        $db = OcDb::instance();
        // add comment
        $query = 'INSERT INTO `PowerTrail_comments`
                  (`userId`, `PowerTrailId`, `commentType`, `commentText`,
                   `logDateTime`, `dbInsertDateTime`, `deleted`, uuid)
                  VALUES (:1, :2, :3, :4, NOW(), NOW(), 0, ' . Uuid::getSqlForUpperCaseUuid() . ' )';

        $db->multiVariableQuery($query, $loggedUser->getUserId(), $powerTrailId, $commentType, $commentText);
        // add action log
        $logQuery = 'INSERT INTO `PowerTrail_actionsLog`(
                        `PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`
                        ) VALUES (:1,:2,NOW(),6,:3,:4)';
        $db->multiVariableQuery($logQuery, $powerTrailId, $loggedUser->getUserId(), $ptAPI::getActionType(6), 0);
    }
} else {
    $updateStatusResult = [
        'updateStatusResult' => false,
        'message' => tr('pt241'),
    ];
}

$updateStatusResult['currentStatus'] = $powerTrail->getStatus();
$updateStatusResult['currentStatusTranslation'] = $powerTrail->getStatusTranslation();

if ($updateStatusResult['updateStatusResult']) {
    echo $updateStatusResult['currentStatusTranslation'];
} else {
    echo $updateStatusResult['message'];
}
