<?php
use Controllers\UserWatchedCachesController;

require_once ('./lib/common.inc.php');

$ctrl = new UserWatchedCachesController();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = '';
}

switch ($action) {
    case 'remove':
        $ctrl->removeFromWatchesAjax($_GET['cacheWp']);
        break;
    case 'add':
        $ctrl->addToWatchesAjax($_GET['cacheWp']);
        break;
    case 'map':
        $ctrl->mapOfWatches();
        break;
    default:
        $ctrl->index();
}

exit();