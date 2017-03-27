<?php

use Controllers\GeoPath\GeoPathsListController;

require_once('./lib/common.inc.php');

$ctrl = new GeoPathsListController();
$ctrl->index();
exit();
