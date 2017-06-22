<?php

use Controllers\CacheSet\CacheSetDetailsController;

require_once('./lib/common.inc.php');

$ctrl = new CacheSetDetailsController();
$ctrl->index();
exit();
