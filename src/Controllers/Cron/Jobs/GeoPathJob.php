<?php

use src\Controllers\Cron\Jobs\Job;
use src\Controllers\PowerTrailController;

class GeoPathJob extends Job
{
    public function run()
    {
        $powerTrailController = new PowerTrailController();
        $powerTrailController->cleanPowerTrailsCronjob();
    }
}
