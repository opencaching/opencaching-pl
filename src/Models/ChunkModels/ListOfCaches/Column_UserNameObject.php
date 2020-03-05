<?php

namespace src\Models\ChunkModels\ListOfCaches;

/**
 * This is column which displays user name.
 * $date arg has to contains User object
 */
class Column_UserNameObject extends AbstractColumn
{

    /**
     * @return string
     */
    protected function getChunkName()
    {
        return "listOfCaches/userNameObjectColumn";
    }

    /**
     * @return string
     */
    public function getCssClass()
    {
        return 'left';
    }
}