<?php

use Utils\Cron\CronExternalWrapper;

$rootpath = __DIR__ . '/../../';
require_once ($rootpath . 'lib/common.inc.php');

exit( CronExternalWrapper::wrap() );
