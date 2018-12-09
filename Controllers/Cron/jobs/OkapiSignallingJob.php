<?php
use Controllers\Cron\Jobs\Job;
use Controllers\Cron\OkapiController;

class OkapiSignallingJob extends Job
{
    public function isReentrant()
    {
        return true;
    }

    public function run()
    {
        $controller = new OkapiController;
        $controller->index();
    }
}
