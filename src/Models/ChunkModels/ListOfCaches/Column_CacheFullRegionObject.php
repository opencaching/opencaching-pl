<?php

namespace src\Models\ChunkModels\ListOfCaches;

/**
 * This is column which displays cache country & region.
 * $cache arg has to contain GeoCache object
 *
 */
class Column_CacheFullRegionObject extends AbstractColumn
{

    protected function getChunkName(): string
    {
        return "listOfCaches/cacheFullRegionObjectColumn";
    }

    public function getCssClass(): string
    {
        return 'left';
    }
}
