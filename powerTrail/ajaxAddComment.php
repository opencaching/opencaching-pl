<?php

use src\Controllers\PowerTrailController;
use src\Models\ApplicationContainer;
use src\Models\PowerTrail\PowerTrail;

require_once __DIR__ . '/../lib/common.inc.php';

$loggedUser = ApplicationContainer::GetAuthorizedUser();

if (! $loggedUser) {
    exit('User not authorized!');
}

$text = htmlspecialchars($_REQUEST['text']);

try {
    $dateTime = new DateTime($_REQUEST['datetime']);
} catch (Exception $e) {
    // improper request
    exit('Improper datetime format');
}

$powerTrail = new PowerTrail(['id' => (int) $_REQUEST['projectId']]);
$type = (int) $_REQUEST['type'];

$ptController = new PowerTrailController();
$result = $ptController->addComment($powerTrail, $loggedUser, $dateTime, $type, $text);

$resultArray = [
    'result' => $result,
];

echo json_encode($resultArray);
