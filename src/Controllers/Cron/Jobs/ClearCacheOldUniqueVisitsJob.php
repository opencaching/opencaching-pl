<?php

use src\Controllers\Cron\Jobs\Job;
use src\Models\GeoCache\CacheVisits;

/**
 * Removes obsolete last unique user visits from cache_visits2 table
 */
class ClearCacheOldUniqueVisitsJob extends Job
{
    public function run()
    {
        CacheVisits::clearOldUniqueVisits();
    }
}
