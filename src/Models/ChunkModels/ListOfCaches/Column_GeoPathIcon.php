<?php

namespace src\Models\ChunkModels\ListOfCaches;

class Column_GeoPathIcon extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/geoPathIconColumn";
    }

    public function getCssClass(){
        return 'center';
    }
}
