<?php

namespace lib\Objects\ChunkModels\ListOfCaches;

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

    public function addColumn(AbstractColumn $column){
        $this->columns[] = $column;
    }

    public function getColumns(){
        return $this->columns;
    }

    public function addRows(array $rows){
        $this->rows = $rows;
    }

    public function getRows(){
        return $this->rows;
    }

}

