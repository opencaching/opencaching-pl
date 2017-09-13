<?php

namespace lib\Objects\ChunkModels\ListOfCaches;

class Column_CacheTypeIcon extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/cacheTypeIconColumn";
    }

    public function getCssClass(){
        return 'center';
    }
}


