<?php

use Utils\Database\OcDb;
use lib\Objects\PowerTrail\PowerTrail;

$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/common.inc.php';

if(!isset($usr['userid'])){
    print 'no hacking please!';
    exit;
}
$ptAPI = new powerTrailBase;

$powerTrailId = (int) $_REQUEST['projectId'];
$powerTrail = new lib\Objects\PowerTrail\PowerTrail(array('id' => $powerTrailId));
$newStatus = (int) $_REQUEST['newStatus'];
if(isset($_REQUEST['commentTxt'])) {
    $commentText = htmlspecialchars($_REQUEST['commentTxt']);
} else {
    $commentText = false;
}

// check if user is owner of selected power Trail
if($ptAPI::checkIfUserIsPowerTrailOwner($usr['userid'], $powerTrailId) == 1 || (isset($usr['admin']) && $usr['admin']== 1)) {
    switch ($newStatus) {
        case PowerTrail::STATUS_OPEN: // publish
            $commentType = 3;
            if(!$commentText) {
                $commentText = tr('pt215').'!';
            }
            break;
        case PowerTrail::STATUS_INSERVICE: // in service
            $commentType = 4;
            if(!$commentText) {
                $commentText = tr('pt217').'!';
            }
            break;
        case PowerTrail::STATUS_CLOSED: // permannet Closure
            $commentType = 5;
            if(!$commentText) {
                $commentText = tr('pt218').'!';
            }
            break;
        default:
            $commentType = 1;
            if(!$commentText) {
                $commentText = tr('pt056').'!';
            }
            break;
    }

    // update geoPatch status
    $updateStatusResult = $powerTrail->setAndStoreStatus($newStatus);
    if($updateStatusResult['updateStatusResult'] === true){
        $db = OcDb::instance();
        // add comment
        $query = 'INSERT INTO `PowerTrail_comments`(`userId`, `PowerTrailId`, `commentType`, `commentText`, `logDateTime`, `dbInsertDateTime`, `deleted`) VALUES (:1, :2, :3, :4, NOW(), NOW(), 0 )';
        $db->multiVariableQuery($query, (int) $usr['userid'], $powerTrailId, $commentType,  $commentText );
        // add action log
        $logQuery = 'INSERT INTO `PowerTrail_actionsLog`(`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`) VALUES (:1,:2,NOW(),6,:3,:4)';
        $db->multiVariableQuery($logQuery, $powerTrailId,(int)$usr['userid'] ,$ptAPI->logActionTypes[6]['type'], 0);
    }
} else {
    $updateStatusResult = array (
        'updateStatusResult' => false,
        'message' => tr('pt241'),
    );
}

$updateStatusResult['currentStatus'] = $powerTrail->getStatus();
$updateStatusResult['currentStatusTranslation'] = $powerTrail->getStatusTranslation();

print json_encode($updateStatusResult);
