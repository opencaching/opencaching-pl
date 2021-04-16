<?php

use src\Models\User\User;
use src\Models\PowerTrail\PowerTrail;
use src\Controllers\PowerTrailController;
use src\Models\ApplicationContainer;

require_once __DIR__.'/../lib/common.inc.php';

$loggedUser = ApplicationContainer::GetAuthorizedUser();

if (!$loggedUser){
    echo "User not authorized!";
    exit;
}

$text = htmlspecialchars($_REQUEST['text']);
try{
    $dateTime = new DateTime($_REQUEST['datetime']);
} catch (Exception $e) {
    // improper request
    echo "improper datetime format";
    exit;
}

$powerTrail = new PowerTrail(array('id' => (int) $_REQUEST['projectId']));
$type = (int) $_REQUEST['type'];

$ptController = new PowerTrailController();
$result = $ptController->addComment($powerTrail, $loggedUser, $dateTime, $type, $text);

$resultArray = array (
    'result' => $result,
);

echo json_encode($resultArray);
