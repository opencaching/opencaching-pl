<?php

namespace src\Models\ChunkModels\ListOfCaches;
/**
 * Geopatch icon for the cache.
 * $date needs to contain:
 * - ptId - id of the powertrails
 * - ptType - type of the powertrail
 * - ptName - name of the powertrails
 */
class Column_GeoPathIcon extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/geoPathIconColumn";
    }

    public function getCssClass(){
        return 'center';
    }
}
