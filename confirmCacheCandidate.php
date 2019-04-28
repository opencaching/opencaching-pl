<?php
/**
 * This script is only to cover legacy candidate to geopath requests.
 * It should be safe to remove it after a month or two after merge it.
 *
 */
use src\Controllers\GeoPathController;

require_once(__DIR__.'/lib/common.inc.php');

$code = isset($_REQUEST['code'])?$_REQUEST['code']:null;
$ownerDecision = ((int) $_REQUEST['result']) === 1;

if(!$code){
    exit;
}

$ctrl = new GeoPathController();
$ctrl->legacyCacheCandidate($code, $ownerDecision);
exit;
