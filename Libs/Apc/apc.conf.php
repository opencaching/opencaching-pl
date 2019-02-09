<?php

use src\Controllers\SysController;

/**
 * This is config file for apc.php script - it allows to override its config
 * without any modification in apc.php
 */

require_once __DIR__.'/../../lib/common.inc.php';

$ctrl = new SysController();
$ctrl->apc(false);
