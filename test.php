<?php

use Controllers\TestController;
use Controllers\Cron\OnlineUsersController;

require_once 'lib/common.inc.php';


$ctrl = new TestController();

if(isset($_GET['action'])){
    $action = $_GET['action'];
}else{
    $action = '';
}

switch($action){
    case 'newLayout':
        $ctrl->newLayout();
        break;
    case 'onlineUsers':
        OnlineUsersController::dumpOnlineUsers();
        break;
    default:
        $ctrl->index();
}


exit;

