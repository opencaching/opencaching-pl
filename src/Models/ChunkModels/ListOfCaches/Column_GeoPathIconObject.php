<?php

namespace src\Models\ChunkModels\ListOfCaches;
/**
 * GeoPath icon for the cache.
 *
 * $date needs to be GeoCache object
 */
class Column_GeoPathIconObject extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/geoPathIconObjectColumn";
    }

    public function getCssClass(){
        return 'center';
    }
}
