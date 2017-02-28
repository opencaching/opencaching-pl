<?php

use Controllers\ViewCacheController;

require_once('./lib/common.inc.php');

$ctrl = new ViewCacheController();
$ctrl->index();
exit();
