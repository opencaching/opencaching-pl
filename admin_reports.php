<?php
use Controllers\Admin\ReportsController;

require_once __DIR__.'/lib/common.inc.php';

$ctrl = new ReportsController();
$ctrl->index();
exit();
