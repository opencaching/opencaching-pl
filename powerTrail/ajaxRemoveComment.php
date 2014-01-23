<?php
// session_start();
require_once __DIR__.'/../lib/db.php';
require_once __DIR__.'/powerTrailBase.php';
require_once __DIR__.'/sendEmail.php';

if(!isset($_SESSION['user_id'])){
    print 'no hacking please!';
    exit;
}
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
    $query = 'UPDATE `PowerTrail_comments` SET `deleted` = 1 WHERE `id` = :1';
    $db = new dataBase(false);
    $db->multiVariableQuery($query, $commentId);
    if($commentDbRow['commentType'] == 2){
        print '2';
        $q = 'UPDATE `PowerTrail` SET `PowerTrail`.`conquestedCount`= (SELECT COUNT(*) FROM `PowerTrail_comments` WHERE `PowerTrail_comments`.`PowerTrailId` = :1 AND `PowerTrail_comments`.`commentType` = 2 AND `PowerTrail_comments`.`deleted` = 0 ) WHERE `PowerTrail`.`id` = :1 ';
        $db->multiVariableQuery($q, $powerTrailId);
    }
    emailOwners($powerTrailId, $commentDbRow['commentType'], $commentDbRow['logDateTime'], $commentDbRow['commentText'], 'delComment', $commentDbRow['userId'], $_REQUEST['delReason']);
}
?>