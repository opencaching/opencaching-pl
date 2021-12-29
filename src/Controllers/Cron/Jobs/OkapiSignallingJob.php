<?php

use src\Controllers\Cron\Jobs\Job;
use src\Controllers\Cron\OkapiController;

class OkapiSignallingJob extends Job
{
    public function isReentrant(): bool
    {
        return true;
    }

    public function run()
    {
        $controller = new OkapiController();
        $controller->index();
    }
}
