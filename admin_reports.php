<?php
use Controllers\Admin\ReportsController;

require_once './lib/common.inc.php';

$ctrl = new ReportsController();
$ctrl->index();
exit();
