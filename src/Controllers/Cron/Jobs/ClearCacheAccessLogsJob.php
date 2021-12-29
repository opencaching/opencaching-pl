<?php

use src\Controllers\Cron\Jobs\Job;
use src\Utils\Log\CacheAccessLog;

class ClearCacheAccessLogsJob extends Job
{

    public function run()
    {
        CacheAccessLog::purgeOldEntries();
    }
}