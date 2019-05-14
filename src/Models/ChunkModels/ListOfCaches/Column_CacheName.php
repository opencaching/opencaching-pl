<?php

namespace src\Models\ChunkModels\ListOfCaches;

/**
 * This is column which displays cache name.
 * $date arg has to contain:
 * - cacheWp - OC waypoint, for example: OP1234
 * - cacheName - name of the cache
 * - isStatusAware  - whether to adjust style depending in cache status
 * - cacheStatus - status of the geocache
 *
 */
class Column_CacheName extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/cacheNameColumn";
    }

    public function getCssClass(){
        return 'left';
    }
}
