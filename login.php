<?php

use Controllers\UserAuthorizationController;

require_once('./lib/common.inc.php');

$ctrl = new UserAuthorizationController();

if(isset($_GET['action'])){
    $action = $_GET['action'];
}else{
    $action = '';
}

switch($action){
    case 'login':
        $ctrl->login();
        break;
    case 'logout':
        $ctrl->logout();
        break;
    case 'verifyAuthCookie':
        $ctrl->verifyAuthCookie();
        break;
    default:
        $ctrl->index();
}

exit;

