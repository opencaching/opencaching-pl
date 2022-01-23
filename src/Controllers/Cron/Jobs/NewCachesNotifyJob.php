<?php

use src\Controllers\Cron\Jobs\Job;
use src\Controllers\Cron\NotifyController;

class NewCachesNotifyJob extends Job
{
    public function run()
    {
        $controller = new NotifyController();
        $controller->index();
    }
}
