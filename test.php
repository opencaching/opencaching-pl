<?php


use lib\Objects\ChunkModels\ListOfCaches\ListOfCachesModel;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\ChunkModels\ListOfCaches\Column_CacheName;

require_once 'lib/common.inc.php';


echo "TEST:<hr/>";

$model = new ListOfCachesModel();
$model->addColumn(
    new Column_CacheName(function( $row ){
        return [
            'url'  => $row->getCacheUrl(),
            'name' => $row->getCacheName()
            ];
}));

$cache = GeoCache::fromCacheIdFactory(13647);


$rows = [ $cache ];

$model->addRows($rows);

$view = tpl_getView();

$view->callChunk('listOfCaches/listOfCaches', $model);


echo "<hr/>END!";





