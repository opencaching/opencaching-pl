<?php

namespace lib\Objects\ChunkModels\ListOfCaches;

class Column_SimpleText extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/simpleTextColumn";
    }

    public function getCssClass(){
        return 'center';
    }
}


