<?php

require_once __DIR__ . '/../../lib/ClassPathDictionary.php';

// Check if another instance of the script is running
$ocConfig = \lib\Objects\OcConfig\OcConfig::instance();
$lockFile = fopen($ocConfig->getDynamicFilesPath()."tmp/geokretyCronJob.lock", "w");
if (!flock($lockFile, LOCK_EX | LOCK_NB)) { // Another instance of the script is running - exit
    echo "Another instance of geokretyCronJob is currently running.\nExiting.\n";
    fclose($lockFile);
    exit;
}

$geoKretyController = new \lib\Controllers\GeoKretyController();
$errors = $geoKretyController->logGeokretyFromQueue();

if(count($errors) > 0){
    /* @var $geoKretLog lib\Objects\GeoKret\GeoKretLog */
    foreach ($errors as $geoKretLog) {
        echo $geoKretLog->getGeoKretName() . ' Error(s): ';
        $gklogErrors = $geoKretLog->getGeoKretLogErrors();
        foreach ($gklogErrors as $gklogError) {
            echo $gklogError->errorMessage . PHP_EOL;
        }
    }

}

fclose($lockFile);

