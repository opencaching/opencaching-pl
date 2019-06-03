<?php

use src\Controllers\Cron\Jobs\Job;
use src\Models\Coordinates\Altitude;
use src\Models\GeoCache\CacheAdditions;
use src\Models\GeoCache\GeoCache;
use src\Utils\Debug\Debug;

class GeopathCandidatesJob extends Job
{
    public function run()
    {
        // archive offers older than 60 days
        // remove offers older than 1 year

    }
}
