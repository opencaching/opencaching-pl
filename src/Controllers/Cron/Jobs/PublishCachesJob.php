<?php
/**
 * This script publishes the cache if its activation_date was set
 */

use src\Controllers\Cron\Jobs\Job;
use src\Models\GeoCache\GeoCache;

class PublishCachesJob extends Job
{
    public function run()
    {
        $stmt = $this->db->multiVariableQuery(
            "SELECT `cache_id`
             FROM `caches`
             WHERE `status` = :1
               AND `date_activate` != 0
               AND `date_activate` <= NOW()",
            GeoCache::STATUS_NOTYETAVAILABLE);

        while ($row = $this->db->dbResultFetch($stmt)) {
            $cache = GeoCache::fromCacheIdFactory($row['cache_id']);
            $cache->publishCache();
            unset($cache);
        }
    }
}
