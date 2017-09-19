<?php
use Controllers\Admin\ReportsController;

require_once './lib/common.inc.php';

$ctrl = new ReportsController();
if (isset($_REQUEST['ajax']) && isset($_REQUEST['action']) && isset($_REQUEST['id'])) {
    switch ($_REQUEST['action']) {
        case 'watchOn':
            $ctrl->turnWatchReportOnAjax($_REQUEST['id']);
            exit();
        case 'watchOff':
            $ctrl->turnWatchReportOffAjax($_REQUEST['id']);
            exit();
        case 'changeStatus':
            if (isset($_REQUEST['status'])) {
                $ctrl->changeStatusAjax($_REQUEST['id'], $_REQUEST['status']);
                exit();
            }
            break;
        case 'changeLeader':
            if (isset($_REQUEST['leader'])) {
                $ctrl->changeLeaderAjax($_REQUEST['id'], $_REQUEST['leader']);
                exit();
            }
            break;
    }
}
$ctrl->index();
exit();
