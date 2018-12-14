<?php
use Controllers\Cron\Jobs\Job;
use Controllers\Cron\NotifyController;

class NewCachesNotifyJob extends Job
{
    public function run()
    {
        $controller = new NotifyController;
        $controller->index();
    }
}
