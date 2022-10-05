<?php

use src\Utils\Database\OcDb;

require __DIR__ . '/../vendor/autoload.php';

session_start();

if (! isset($_SESSION['user_id'])) {
    exit(json_encode(['resultSuccess' => false, 'error' => 'User is not logged in']));
}

$powerTrailId = $_REQUEST['projectId'];

try {
    $newDate = new DateTime($_REQUEST['newDate']);
} catch (Exception $ex) {
    exit(json_encode(['resultSuccess' => false, 'error' => 'Wrong date']));
}

// check if user is owner of selected power Trail
if (powerTrailBase::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $powerTrailId) == 1) {
    $query = 'UPDATE `PowerTrail` SET `dateCreated`= :1 WHERE `id` = :2';
    $db = OcDb::instance();
    $db->multiVariableQuery($query, $newDate->format('Y-m-d H:i:s'), $powerTrailId);

    exit(json_encode(['resultSuccess' => true, 'error' => null]));
}

    exit(json_encode(['resultSuccess' => false, 'error' => 'User is not powerTrail owner']));
