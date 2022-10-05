<?php

use src\Utils\Database\OcDb;

require __DIR__ . '/../vendor/autoload.php';

session_start();

if (! isset($_SESSION['user_id'])) {
    exit('No hacking please!');
}

$powerTrailId = (int) $_REQUEST['projectId'];
$newName = strip_tags($_REQUEST['newNamePt']);

if ($newName == '') {
    exit('Error - no name was entered');
}

// check if user is owner of selected power Trail
if (powerTrailBase::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $powerTrailId) == 1) {
    $query = 'UPDATE `PowerTrail` SET `name` = :1 WHERE `id` = :2';
    $db = OcDb::instance();
    $db->multiVariableQuery($query, $newName, $powerTrailId);
    echo $newName;
}
