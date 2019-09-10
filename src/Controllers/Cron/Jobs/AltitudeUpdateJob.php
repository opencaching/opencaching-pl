<?php

use src\Controllers\Cron\Jobs\Job;
use src\Models\Coordinates\Altitude;
use src\Models\GeoCache\CacheAdditions;
use src\Models\GeoCache\GeoCache;
use src\Utils\Debug\Debug;

class AltitudeUpdateJob extends Job
{
    const RECORDS_TO_RUN_AT_ONCE = 3;

    public function run()
    {
        // first find $recordsToRunAtOnce geocaches without altitude
        for($i=0; $i < self::RECORDS_TO_RUN_AT_ONCE; $i++){
            $cacheId = CacheAdditions::getRandomCacheIdWithoutAltitude();
            if(!$cacheId){
                // there is no caches without altitude
                return;
            }

            $geocache = GeoCache::fromCacheIdFactory($cacheId);
            if(!$geocache){
                Debug::errorLog("Cache Additions present but there is no such geocache (cacheId=$cacheId)");
                continue;
            }

            $coords = $geocache->getCoordinates();
            if($coords){
                $altitude = Altitude::getAltitude($coords);
                if(is_null($altitude)){
                    Debug::errorLog("Can't find altitude for geocache (cacheId=$cacheId, coords={$coords->getAsText()})");
                    continue;
                }
                $geocache->updateAltitude($altitude);
            }
        } // for
    }
}
