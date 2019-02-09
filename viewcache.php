<?php

use src\Controllers\ViewCacheController;

require_once (__DIR__.'/lib/common.inc.php');

$ctrl = new ViewCacheController();
$ctrl->index();
exit();
