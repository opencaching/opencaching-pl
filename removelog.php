<?php

require_once __DIR__ . '/lib/common.inc.php';

if (!isset($_SESSION['user_id'])) {
    print 'no hacking please!';
    exit;
} else {
    $ocController = new lib\Controllers\OcController;
    $request = $_REQUEST;
    $result = $ocController->removeLog($request);
    echo json_encode($result);
}
