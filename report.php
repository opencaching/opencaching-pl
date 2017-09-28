<?php
use Controllers\Admin\ReportCacheController;

require_once './lib/common.inc.php';

$ctrl = new ReportCacheController();
$ctrl->index();
exit();
