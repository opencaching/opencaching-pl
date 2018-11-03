<?php

namespace lib\Objects\ChunkModels\ListOfCaches;

class Column_UserName extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/userNameColumn";
    }

    public function getCssClass() {
        return 'left';
    }
}


