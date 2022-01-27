<?php

use src\Utils\Database\OcDb;
use src\Controllers\PowerTrailController;

require __DIR__ . '/../vendor/autoload.php';

session_start();

if (! isset($_SESSION['user_id'])) {
    exit('No hacking please!');
}

$powerTrailId = (int) $_REQUEST['projectId'];
$newPercent = (int) $_REQUEST['newPercent'];

if ($newPercent < PowerTrailController::MINIMUM_PERCENT_REQUIRED || $newPercent > 100) {
    exit('Error');
}

// check if user is owner of selected power Trail
if (powerTrailBase::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $powerTrailId) == 1) {
    $query = 'UPDATE `PowerTrail` SET `perccentRequired`= :1 WHERE `id` = :2';
    $db = OcDb::instance();
    $db->multiVariableQuery($query, $newPercent, $powerTrailId);
    echo $newPercent;
}
