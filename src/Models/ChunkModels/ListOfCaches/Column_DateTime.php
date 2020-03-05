<?php


namespace src\Models\ChunkModels\ListOfCaches;

/**
 * This is column which displays cache name.
 *
 * $data should contain:
 * - date - date ;) - DateTime type or string which can be converted to DateTime
 * - showTime - bool
 *
 */
class Column_DateTime extends AbstractColumn
{

    protected function getChunkName()
    {
        return "listOfCaches/dateTimeColumn";
    }

    public function getCssClass()
    {
        return 'left';
    }

}
