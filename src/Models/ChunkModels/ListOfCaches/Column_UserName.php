<?php

namespace src\Models\ChunkModels\ListOfCaches;
/**
 * This is column which displays user name.
 * $date arg has to contains:
 * [
 *   'userId' => user identifier
 *   'userName' => user nickname
 * ]
 */
class Column_UserName extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/userNameColumn";
    }

    public function getCssClass() {
        return 'left';
    }
}
