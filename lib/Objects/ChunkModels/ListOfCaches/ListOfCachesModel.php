<?php

namespace lib\Objects\ChunkModels\ListOfCaches;

use lib\Objects\ChunkModels\PaginationModel;
use Utils\View\View;

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

class ListOfCachesModel {

    private $columns = [];
    private $rows = [];
    private $paginationModel = null;

    private $displayHeader = true;


    public function disableHeader(){
        $this->displayHeader = false;
    }

    public function addColumn(AbstractColumn $column){
        $this->columns[] = $column;
    }

    public function getColumns(){
        return $this->columns;
    }

    public function addDataRows(array $rows){
        $this->rows = $rows;
    }

    public function getRows(){
        return $this->rows;
    }

    public function setPaginationModel(PaginationModel &$paginationModel){
        $this->paginationModel = $paginationModel;
    }

    public function callPaginationChunk(){
        if($this->paginationModel){
            View::callChunkInline('pagination', $this->paginationModel);
        }
    }

    public function isHeaderEnabled(){
        return $this->displayHeader;
    }
}

