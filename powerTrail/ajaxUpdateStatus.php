<?php
use Utils\Database\OcDb;
$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/common.inc.php';

$statusArr = powerTrailBase::getPowerTrailStatus();

if(!isset($_SESSION['user_id'])){
    print 'no hacking please!';
    exit;
}
$ptAPI = new powerTrailBase;

$powerTrailId = (int) $_REQUEST['projectId'];
$newStatus = (int) $_REQUEST['newStatus'];
if(isset($_REQUEST['commentTxt'])) $commentText = htmlspecialchars($_REQUEST['commentTxt']); else $commentText = false;

// check if user is owner of selected power Trail
if($ptAPI::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $powerTrailId) == 1 || (isset($usr['admin']) && $usr['admin']== 1)) {


    switch ($newStatus) {
        case 1: // publish
            $commentType = 3;
            if(!$commentText) $commentText = tr('pt215').'!';
            break;
        case 4: // in service
            $commentType = 4;
            if(!$commentText) $commentText = tr('pt217').'!';
            break;
        case 3: // permannet Closure
            $commentType = 5;
            if(!$commentText) $commentText = tr('pt218').'!';
            break;
        default:
            $commentType = 1;
            if(!$commentText) $commentText = tr('pt056').'!';
            break;
    }

    // update geoPatch status
    $query = 'UPDATE `PowerTrail` SET `status`= :1 WHERE `id` = :2';
    $db = OcDb::instance();
    $db->multiVariableQuery($query, $newStatus, $powerTrailId);

    // add comment
    $query =
    'INSERT INTO `PowerTrail_comments`(`userId`, `PowerTrailId`, `commentType`, `commentText`, `logDateTime`, `dbInsertDateTime`, `deleted`)
                               VALUES (:1,        :2,             :3            ,:4 ,           NOW(),           NOW(),                0 )';
    $db->multiVariableQuery($query, (int) $_SESSION['user_id'], $powerTrailId, $commentType,  $commentText );

    // add action log
    $logQuery = 'INSERT INTO `PowerTrail_actionsLog`(`PowerTrailId`, `userId`, `actionDateTime`, `actionType`, `description`, `cacheId`) VALUES (:1,:2,NOW(),6,:3,:4)';
    $db->multiVariableQuery($logQuery, $powerTrailId,(int)$_SESSION['user_id'] ,$ptAPI->logActionTypes[6]['type'], 0);


}
print tr($statusArr[$newStatus]['translate']);
?>