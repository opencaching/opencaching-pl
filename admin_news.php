<?php
use Controllers\News\NewsAdminController;

require_once ('./lib/common.inc.php');

$ctrl = new NewsAdminController();
$ctrl->index();
exit();
