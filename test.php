<?php

require_once 'lib/common.inc.php';

use lib\Controllers\PowerTrailController;

error_reporting(-1);

$powerTrailController = new powerTrailController();
$powerTrailController->cleanPowerTrailsCronjob();


