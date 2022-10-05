<?php

use src\Models\ApplicationContainer;
use src\Utils\Database\OcDb;

require_once __DIR__ . '/../lib/common.inc.php';

if (! isset($_SESSION['user_id'])) {
    exit('No hacking please!');
}

$callingUser = (int) $_REQUEST['callingUser'];

if ($callingUser != $_SESSION['user_id']) {
    exit('Wrong user!');
}

$powerTrailId = (int) $_REQUEST['ptId'];
$commentId = (int) $_REQUEST['commentId'];
$restore = (int) $_REQUEST['restore'];

//get selected comment and check if it is $callingUser comment.
$commentDbRow = powerTrailBase::getSingleComment($commentId);

// check if user is owner of selected power Trail
if (powerTrailBase::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $powerTrailId) == 1
    || $commentDbRow['userId'] == $callingUser
    || ($commentDbRow['deleted'] && ApplicationContainer::GetAuthorizedUser()->hasOcTeamRole())
) {
    $query = 'UPDATE `PowerTrail_comments` SET `deleted` = :2 WHERE `id` = :1';
    $db = OcDb::instance();
    $db->multiVariableQuery($query, $commentId, $restore ? 0 : 1);

    if ($commentDbRow['commentType'] == 2) {
        echo '2';
        $q = '
            UPDATE `PowerTrail`
            SET `PowerTrail`.`conquestedCount`= (
                SELECT COUNT(*) FROM `PowerTrail_comments`
                WHERE `PowerTrail_comments`.`PowerTrailId` = :1
                  AND `PowerTrail_comments`.`commentType` = 2
                  AND `PowerTrail_comments`.`deleted` = 0
                )
            WHERE `PowerTrail`.`id` = :1 ';
        $db->multiVariableQuery($q, $powerTrailId);
    }

    sendEmail::emailOwners(
        $powerTrailId,
        $commentDbRow['commentType'],
        $commentDbRow['logDateTime'],
        $commentDbRow['commentText'],
        $restore ? 'restoreComment' : 'delComment',
        $commentDbRow['userId'],
        $_REQUEST['delReason']
    );
}
