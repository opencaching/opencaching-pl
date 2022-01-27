<?php

use src\Controllers\Cron\CronJobsController;

require_once __DIR__ . '/../../lib/common.inc.php';

// This script can be called with a single cronjob name as argument
// for testing that job.
if (PHP_SAPI == 'cli' && $argc == 2) {
    $job = $argv[1];
} else {
    $job = null;
}

$ctrl = new CronJobsController($job);
$ctrl->index();

exit();
