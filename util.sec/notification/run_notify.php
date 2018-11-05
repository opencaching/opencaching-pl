<?php
use Controllers\Cron\NotifyController;

require_once (__DIR__.'/../../lib/common.inc.php');

$ctrl = new NotifyController();
$ctrl->index();
exit();