<?php

require_once __DIR__.'/lib/common.inc.php';

// $_SESSION['user_id'] = 1;
// removelog.php?logid=103

if(!isset($_SESSION['user_id'])){
    print 'no hacking please!';
    exit;
} else {
	$ocController = new lib\Controllers\OcController;
	$request = $_REQUEST;
	$result = $ocController->removeLog($request);
	echo json_encode($result);
}






