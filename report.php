<?php
use src\Controllers\Admin\ReportCacheController;

require_once (__DIR__.'/lib/common.inc.php');

$ctrl = new ReportCacheController();
$ctrl->index();
exit();
