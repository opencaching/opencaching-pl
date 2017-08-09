<?php
use Controllers\News\NewsListController;

$rootpath = '../';
require_once($rootpath . 'lib/common.inc.php');

$ctrl = new NewsListController();
$ctrl->showRss();
exit();