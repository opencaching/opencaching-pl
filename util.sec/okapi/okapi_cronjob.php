<?php
/**
 * Does OKAPI housekeeping, should be run in short intervals from cron.
 */
use src\Controllers\Cron\OkapiController;

require_once (__DIR__.'/../../lib/common.inc.php');

$ctrl = new OkapiController();
$ctrl->index();
exit();
