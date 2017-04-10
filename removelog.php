<?php

use Controllers\CacheLogController;

require_once __DIR__ . '/lib/common.inc.php';

$ctrl = new CacheLogController();
$ctrl->removeLog();
