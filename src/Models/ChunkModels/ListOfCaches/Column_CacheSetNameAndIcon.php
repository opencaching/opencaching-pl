<?php

namespace src\Models\ChunkModels\ListOfCaches;

class Column_CacheSetNameAndIcon extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/cacheSetNameAndIconColumn";
    }

    public function getCssClass(){
        return 'left';
    }
}
