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

    static private $idCounter = 1;
    private $id = 0;

    public function __construct()
    {
        $this->id = self::$idCounter;
        self::$idCounter += 1;
    }

    public function getId()
    {
        return $this->id;
    }

    public function disableHeader()
    {
        $this->displayHeader = false;
    }

    /**
     * @param AbstractColumn $column
     * @return ListOfCachesModel
     */
    public function addColumn(AbstractColumn $column)
    {
        $this->columns[] = $column;

        return $this;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param array $rows
     * @return ListOfCachesModel
     */
    public function addDataRows(array $rows)
    {
        $this->rows = $rows;

        return $this;
    }

    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param PaginationModel $paginationModel
     * @return ListOfCachesModel
     */
    public function setPaginationModel(PaginationModel &$paginationModel)
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
