<?php
use Controllers\Cron\Jobs\Job;
use lib\Controllers\PowerTrailController;

class GeoPathJob extends Job
{
    public function run()
    {
        $powerTrailController = new PowerTrailController();
        $powerTrailController->cleanPowerTrailsCronjob();
    }
}
