<?php

namespace src\Models\ChunkModels\ListOfCaches;

/**
 * This is column which displays cache region.
 * $cache arg has to contain GeoCache object
 *
 */
class Column_CacheRegionObject extends AbstractColumn
{

    protected function getChunkName(): string
    {
        return "listOfCaches/cacheRegionObjectColumn";
    }

    public function getCssClass(): string
    {
        return 'left';
    }
}
