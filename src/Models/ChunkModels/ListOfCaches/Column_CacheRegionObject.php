<?php

namespace src\Models\ChunkModels\ListOfCaches;

/**
 * This is column which displays cache region.
 * $cache arg has to contain GeoCache object
 *
 */
class Column_CacheRegionObject extends AbstractColumn
{

    /**
     * @return string
     */
    protected function getChunkName()
    {
        return "listOfCaches/cacheRegionObjectColumn";
    }

    /**
     * @return string
     */
    public function getCssClass()
    {
        return 'left';
    }
}