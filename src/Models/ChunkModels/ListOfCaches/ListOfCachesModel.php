<?php

namespace src\Models\ChunkModels\ListOfCaches;

use src\Models\ChunkModels\PaginationModel;
use src\Utils\View\View;

/**
 * This is model for listOfCaches chunk.
 *
 * Very simple example of usage:
 *
 * $model = new ListOfCachesModel();
 * $model->addColumn( //add only one column
 *     new Column_CacheName(function( $row ){
 *       return [
 *         'url'  => $row->getCacheUrl(),
 *         'name' => $row->getCacheName()
 *       ];
 * }));
 *
 * $rows = [ GeoCache::fromCacheIdFactory(13647) ];
 * $model->addRows($rows);
 * $view->callChunk('listOfCaches/listOfCaches', $model);
 *
 */
class ListOfCachesModel
{

    private $columns = [];
    private $rows = [];
    private $paginationModel = null;

    private $displayHeader = true;
    private $emptyListMessage = null;

    public function disableHeader()
    {
        $this->displayHeader = false;
    }

    public function addColumn(AbstractColumn $column): ListOfCachesModel
    {
        $this->columns[] = $column;

        return $this;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function addDataRows(array $rows): ListOfCachesModel
    {
        $this->rows = $rows;

        return $this;
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function setPaginationModel(PaginationModel &$paginationModel): ListOfCachesModel
    {
        $this->paginationModel = $paginationModel;

        return $this;
    }

    public function callPaginationChunk()
    {
        if ($this->paginationModel) {
            View::callChunkInline('pagination', $this->paginationModel);
        }
    }

    public function isHeaderEnabled()
    {
        return $this->displayHeader;
    }

    public function addEmptyListMessage($message)
    {
        $this->emptyListMessage = $message;
    }

    public function getEmptyListMessage()
    {
        if ($this->emptyListMessage) {
            return $this->emptyListMessage;
        }
        return tr('listOfCaches_defaultNoRowsMessage');
    }
}
