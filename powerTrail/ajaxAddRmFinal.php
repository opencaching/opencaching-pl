<?php

use src\Utils\Database\OcDb;

require __DIR__ . '/../vendor/autoload.php';

session_start();

if (! isset($_SESSION['user_id'])) {
    exit('No hacking please!');
}

$powerTrailId = (int) $_REQUEST['projectId'];
$isFinal = (int) $_REQUEST['isFinal'];
$cacheId = (int) $_REQUEST['cacheId'];

// check if user is owner of selected power Trail
if (powerTrailBase::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $powerTrailId) == 1) {
    $query = 'UPDATE `powerTrail_caches` SET `isFinal`= :1 WHERE `cacheId`=:2 AND `PowerTrailId`=:3';
    $db = OcDb::instance();
    $db->multiVariableQuery($query, $isFinal, $cacheId, $powerTrailId);
}
