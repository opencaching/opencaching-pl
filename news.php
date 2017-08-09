<?php
use Controllers\News\NewsListController;

require_once ('./lib/common.inc.php');

$ctrl = new NewsListController();
$ctrl->index();
exit();
