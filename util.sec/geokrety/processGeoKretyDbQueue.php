<?php
require_once __DIR__ . '/../../lib/ClassPathDictionary.php';

use Controllers\GeoKretyLogController;


$GLOBALS['rootpath'] = "../../"; //TODO: how to remove it from here?

$gkCtrl = new GeoKretyLogController();

// add debug var to url if debug messages are needed
if(isset($_REQUEST['debug'])){
    $gkCtrl->enableDebugMsgs();
}

$gkCtrl->runQueueProcessing();

// /srv/cron/cron-defs/do-wget-url util.sec/geokrety/logGeokretyCronJob.php logGeokretyCronJob.html
