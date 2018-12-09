<?php
use Controllers\Cron\CronJobsController;

require_once (__DIR__.'/../../lib/common.inc.php');

// This script can be called with a single cronjob name as argument
// for testing that job.

$ctrl = new CronJobsController($argc == 2 ? $argv[1] : null);
$ctrl->index();
exit();
