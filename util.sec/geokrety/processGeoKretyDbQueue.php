<?php
require_once __DIR__ . '/../../lib/ClassPathDictionary.php';

use Controllers\GeoKretyLogController;

/**
 * This script run GK queue processing.
 * Please note:
 * - GK processing needs a lock to prevent run this procedure at the same time.
 *   Lock is released at the end of procedure but after server faults (or other errors)
 *   lock needs to be removed manually.
 *
 * - GK-errors stays in DB and are proceded at every call. It could be a good idea
 *   to run this script with debug param from time to time and manually delete
 *   broken records from GK queue in table geokret_log in DB.
 *
 */

$GLOBALS['rootpath'] = "../../"; //TODO: how to remove it from here?

$gkCtrl = new GeoKretyLogController();

// add debug var to url if debug messages are needed
if(isset($_REQUEST['debug'])){
    $gkCtrl->enableDebugMsgs();
}

$gkCtrl->runQueueProcessing(__FILE__);
