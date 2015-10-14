<?php
$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/common.inc.php';
db_disconnect();

if(!isset($_SESSION['user_id'])){
    print 'no hacking please! Fuck You!';
    exit;
}

$text = htmlspecialchars($_REQUEST['text']);
$dateTime = new DateTime($_REQUEST['datetime']);
$user = new lib\Objects\User\User(array('userId' => (int) $_SESSION['user_id']));
$powerTrail = new lib\Objects\PowerTrail\PowerTrail(array('id' => (int) $_REQUEST['projectId']));
$log = new lib\Objects\PowerTrail\Log();
$log->setPowerTrail($powerTrail)
    ->setDateTime($dateTime)
    ->setUser($user)
    ->setType((int) $_REQUEST['type'])
    ->setText($text)
    ->storeInDb();
sendEmail::emailOwners($powerTrail->getId(), $log->getType(), $dateTime->format('Y-m-d H:i'), $text, 'newComment');
