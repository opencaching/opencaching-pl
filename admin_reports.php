<?php
use Controllers\Admin\ReportsController;

require_once './lib/common.inc.php';

$ctrl = new ReportsController();
if (isset($_REQUEST['ajax'])) {
    if (isset($_REQUEST['action'])) {
        switch ($_REQUEST['action']) {
            case 'watchOn':
                if (isset($_REQUEST['id'])) {
                    $ctrl->turnWatchReportOnAjax($_REQUEST['id']);
                } else {
                    $ctrl->ajaxError("No ID", 400);
                }
                exit();
            case 'watchOff':
                if (isset($_REQUEST['id'])) {
                    $ctrl->turnWatchReportOffAjax($_REQUEST['id']);
                } else {
                    $ctrl->ajaxError("No ID", 400);
                }
                exit();
            case 'changeStatus':
                if (isset($_REQUEST['status']) && isset($_REQUEST['id'])) {
                    $ctrl->changeStatusAjax($_REQUEST['id'], $_REQUEST['status']);
                } else {
                    $ctrl->ajaxError("Incorrect params", 400);
                }
                exit();
            case 'changeLeader':
                if (isset($_REQUEST['leader']) && isset($_REQUEST['id'])) {
                    $ctrl->changeLeaderAjax($_REQUEST['id'], $_REQUEST['leader']);
                } else {
                    $ctrl->ajaxError("Incorrect params", 400);
                }
                exit();
            case 'getTemplates':
                if (isset($_REQUEST['recipient']) && isset($_REQUEST['objecttype'])) {
                    $ctrl->getEmailTemplatesAjax($_REQUEST['recipient'], $_REQUEST['objecttype']);
                } else {
                    $ctrl->ajaxError("Incorrect params", 400);
                }
                exit();
            case 'getTemplate':
                if (isset($_REQUEST['id'])) {
                    $ctrl->getTemplateByIdAjax($_REQUEST['id']);
                } else {
                    $ctrl->ajaxError("No template ID", 400);
                }
                exit();
            default:
                $ctrl->ajaxError("Invalid action", 400);
                exit();
        }
    } else {
        $ctrl->ajaxError("No action param", 400);
        exit();
    }
}

$ctrl->index();
exit();
