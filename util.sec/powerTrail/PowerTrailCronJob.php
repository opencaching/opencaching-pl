<?php
use src\Controllers\PowerTrailController;
use src\Utils\I18n\I18n;

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
