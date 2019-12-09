<?php

namespace src\Models\ChunkModels\ListOfCaches;
/**
 * This is column which displays user name.
 * $date arg has to contains User object
 */
class Column_UserNameObject extends AbstractColumn
{

    protected function getChunkName(): string
    {
        return "listOfCaches/userNameObjectColumn";
    }

    public function getCssClass(): string 
    {
        return 'left';
    }
}
