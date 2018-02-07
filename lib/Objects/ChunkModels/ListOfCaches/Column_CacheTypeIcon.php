<?php

namespace lib\Objects\ChunkModels\ListOfCaches;

/**
 * This is column with cache icon.
 *
 * $date arg needs to contains:
 * - type 	- type of the cache (for example multi or virtual)
 * - status - status of the cache (for example temp-unavailable or archived
 * - user_sts - status for current user - for example found or not found etc.
 */
class Column_CacheTypeIcon extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/cacheTypeIconColumn";
    }

    public function getCssClass(){
        return 'center';
    }
}


