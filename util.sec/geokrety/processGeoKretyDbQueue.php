<?php
require_once __DIR__ . '/../../lib/ClassPathDictionary.php';

use Controllers\GeoKretyLogController;


$GLOBALS['rootpath'] = "../../"; //TODO: how to remove it from here?

$gkCtrl = new GeoKretyLogController();

// Uncomment if necessary - then script will return debug info with errors etc.
// $gkCtrl->enableDebugMsgs();

$gkCtrl->runQueueProcessing();

// /srv/cron/cron-defs/do-wget-url util.sec/geokrety/logGeokretyCronJob.php logGeokretyCronJob.html
