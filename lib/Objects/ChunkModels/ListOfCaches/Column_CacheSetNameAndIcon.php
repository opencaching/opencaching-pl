<?php

namespace lib\Objects\ChunkModels\ListOfCaches;

class Column_CacheSetNameAndIcon extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/cacheSetNameAndIconColumn";
    }

    public function getCssClass(){
        return 'left';
    }
}


