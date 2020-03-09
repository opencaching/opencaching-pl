<?php

namespace src\Models\ChunkModels\ListOfCaches;

/**
 * GeoPath icon for the cache.
 *
 * $date needs to be GeoCache object
 */
class Column_GeoPathIconObject extends AbstractColumn
{

    /**
     * @return string
     */
    protected function getChunkName()
    {
        return "listOfCaches/geoPathIconObjectColumn";
    }

    /**
     * @return string
     */
    public function getCssClass()
    {
        return 'center';
    }
}