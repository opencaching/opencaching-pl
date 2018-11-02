<?php

namespace lib\Objects\ChunkModels\ListOfCaches;

class Column_CacheName extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/cacheNameColumn";
    }

    public function getCssClass(){
        return 'left';
    }
}


