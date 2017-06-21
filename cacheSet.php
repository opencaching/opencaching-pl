<?php

use Controllers\CacheSet\CacheSetsListController;

require_once('./lib/common.inc.php');

$ctrl = new CacheSetsListController();
$ctrl->index();
exit();
