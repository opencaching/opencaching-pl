<?php

use Utils\Database\DbUpdates;

require_once(__DIR__.'/../lib/common.inc.php');

echo DbUpdates::run();
