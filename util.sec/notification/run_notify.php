<?php
use Controllers\Cron\NotifyController;

$rootpath = __DIR__ . '/../../';
require_once ($rootpath . 'lib/common.inc.php');

$ctrl = new NotifyController();
$ctrl->index();
exit();