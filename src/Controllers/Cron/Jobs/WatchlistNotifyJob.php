<?php
use src\Controllers\Cron\Jobs\Job;
use src\Controllers\Cron\WatchlistController;

class WatchlistNotifyJob extends Job
{
    public function run()
    {
        $controller = new WatchlistController;
        $controller->index();
    }
}
