<?php

require_once __DIR__ . '/../../lib/ClassPathDictionary.php';

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

