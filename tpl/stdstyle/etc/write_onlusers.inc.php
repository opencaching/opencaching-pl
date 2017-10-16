<?php

use Controllers\Cron\OnlineUsersController;

$rootpath = '../../../';
require_once($rootpath . 'lib/common.inc.php');

OnlineUsersController::dumpOnlineUsers();
