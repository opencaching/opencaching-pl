<?php

namespace lib\Objects\ChunkModels\ListOfCaches;

class Column_CacheLastLog extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/cacheLastLogColumn";
    }

    public function getCssClass(){
        return 'left';
    }
}


