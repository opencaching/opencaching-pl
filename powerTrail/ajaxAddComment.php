<?php

use lib\Objects\User\User;
use lib\Objects\PowerTrail\PowerTrail;
use lib\Controllers\PowerTrailController;


$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/common.inc.php';

if(!isset($_SESSION['user_id'])){
    print 'no hacking please! Fuck You!';
    exit;
}

$text = htmlspecialchars($_REQUEST['text']);
$dateTime = new DateTime($_REQUEST['datetime']);
$user = new User(array('userId' => (int) $usr['userid']));
$powerTrail = new PowerTrail(array('id' => (int) $_REQUEST['projectId']));
$type = (int) $_REQUEST['type'];

$ptController = new PowerTrailController();
$result = $ptController->addComment($powerTrail, $user, $dateTime, $type, $text);

$resultArray = array (
    'result' => $result,
);

echo json_encode($resultArray);
