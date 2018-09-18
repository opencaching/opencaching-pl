<?php

use Controllers\GuideController;

require_once('./lib/common.inc.php');

$ctrl = new GuideController();
$ctrl->index();
exit();
