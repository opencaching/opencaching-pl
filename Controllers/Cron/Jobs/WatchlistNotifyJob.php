<?php
use Controllers\Cron\Jobs\Job;
use Controllers\Cron\WatchlistController;

class WatchlistNotifyJob extends Job
{
    public function run()
    {
        $controller = new WatchlistController;
        $controller->index();
    }
}
