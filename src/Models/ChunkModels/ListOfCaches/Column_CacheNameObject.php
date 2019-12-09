<?php

namespace src\Models\ChunkModels\ListOfCaches;

/**
 * This is column which displays cache name.
 * $date arg has to contain GeoCache object
 *
 */
class Column_CacheNameObject extends AbstractColumn
{

    protected function getChunkName(): string
    {
        return "listOfCaches/cacheNameObjectColumn";
    }

    public function getCssClass(): string
    {
        return 'left';
    }
}
