<?php


use lib\Controllers\PowerTrailController;
use Utils\I18n\I18n;

//TODO: how to remove it from here?

$GLOBALS['rootpath'] = "../../";
require_once(__DIR__.'/../../lib/common.inc.php');

PowerTrailCronJobController::run();

class PowerTrailCronJobController
{

    public static function run()
    {
        self::cleanPowerTrails();
    }

    private static function cleanPowerTrails()
    {
        $powerTrailController = new PowerTrailController();
        $powerTrailController->cleanPowerTrailsCronjob();
    }

}
