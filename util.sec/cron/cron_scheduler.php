<?php

use Utils\Cron\CronScheduler;

$rootpath = __DIR__ . '/../../';
require_once ($rootpath . 'lib/common.inc.php');

(new CronScheduler())->scheduleAndPerform();
exit();
