<?php

use Controllers\TestController;
use Controllers\Cron\OnlineUsersController;
use Controllers\Admin\CacheSetAdminController;

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
    case 'cacheSetAdmin':
        $ctrl = new CacheSetAdminController();
        $ctrl->cacheSetsToArchive();
        break;
    default:
        $ctrl->index();
}


exit(0);

