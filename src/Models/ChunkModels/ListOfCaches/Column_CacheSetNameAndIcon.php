<?php

namespace src\Models\ChunkModels\ListOfCaches;

/**
 *
 * Data extractor needs to returns:
 * [
 *   $row['type'] => '',
 *   $row['id'] => '',
 *   $row['name'] => '',
 * ]
 */
class Column_CacheSetNameAndIcon extends AbstractColumn {

    protected function getChunkName()
    {
        return "listOfCaches/cacheSetNameAndIconColumn";
    }

    public function getCssClass(){
        return 'left';
    }
}
