<?php

namespace src\Models\ChunkModels\DynamicMap;

use src\Models\Coordinates\Coordinates;
use src\Models\OcConfig\OcConfig;
use src\Utils\Debug\Debug;

/**
 * This is model of the dynamic map
 * This class contains data which describes the map
 */
class DynamicMapModel
{
    private $ocConfig;

    /** @var Coordinates */
    private $coords;         // center of the map

    private $swCorner;       // for initial extent
    private $neCorner;       // for initial extent
    private $startExtent;  // set if sw/ne corner coords are present

    private $zoom;           // zoom of the map, int,
    private $forceZoom;      // force given zoom even if some markers will be hidden
    private $mapLayerName;   // name of the default map layer

    private $infoMessage;    // short message to display at map

    private $markerModels = [];

    public function __construct(){

        $this->ocConfig = OcConfig::instance();

        $this->coords = OcConfig::getMapDefaultCenter();

        $this->zoom = OcConfig::getStartPageMapZoom();
        $this->forceZoom = false;
        $this->startExtent = false;
        $this->mapLayerName = 'OSM';
    }

    /**
     * Add markers of one type
     *
     * @param string $markerClass - class returned by Extractor by 'CacheSetMarkerModel::class'
     * @param array $dataRows - rows of data - every row describes one marker
     * @param callable $rowExtractor - function which can create marekrClass based on given row
     */
    public function addMarkersWithExtractor($markerClass, array $dataRows, callable $rowExtractor)
    {
        foreach($dataRows as $row){

            $markerModel = call_user_func($rowExtractor, $row);

            if(!($markerModel instanceof $markerClass)) {
                Debug::errorLog("Extractor returns something different than $markerClass");
                return;
            }

            if(!is_subclass_of($markerModel, AbstractMarkerModelBase::class)){
                Debug::errorLog("Marker class $markerClass is not a child of ".AbstractMarkerModelBase::class);
                return;
            }

            $this->addMarker($markerModel);
        } // foreach
    }

    /**
     * Add one marker to internal base of markers
     *
     * @param AbstractMarkerModelBase $model
     */
    public function addMarker(AbstractMarkerModelBase $model)
    {
        $type = $model->getMarkerTypeName();

        if(!$model->checkMarkerData()){
            $type = $model->getMarkerTypeName();
            Debug::errorLog("Marker of $type has incomplete data!");
        }

        if(!isset($this->markerModels[$type])){
            $this->markerModels[$type] = [];
        }
        $this->markerModels[$type][] = $model;
    }

    /**
     * Read OC map config from config and return map config JS
     */
    public static function getMapLayersJsConfig(){
        return OcConfig::getMapJsConfig();
    }

    public function getMarkersDataJson(){
        return json_encode($this->markerModels, JSON_PRETTY_PRINT);
    }

    public function getMarkerTypes(){
        return array_keys($this->markerModels);
    }

    /**
     * @return Coordinates
     */
    public function getCoords(){
        return $this->coords;
    }

    public function getZoom(){
        return $this->zoom;
    }

    public function isZoomForced()
    {
        return $this->forceZoom;
    }

    public function getSelectedLayerName(){
        return $this->mapLayerName;
    }

    public function setZoom($zoom)
    {
        $this->zoom = $zoom;
        $this->forceZoom = true;
    }

    public function setInitLayerName($name){
        $this->mapLayerName = $name;
    }

    public function forceDefaultZoom()
    {
        $this->forceZoom = true;
    }

    public function setCoords(Coordinates $cords)
    {
        $this->coords = $cords;
    }

    public function setStartExtent(Coordinates $swCorner, Coordinates $neCorner)
    {
        $this->swCorner = $swCorner;
        $this->neCorner = $neCorner;
        $this->startExtent = true;
    }

    public function getStartExtentJson()
    {
        if($this->startExtent){
            $sw = $this->swCorner->getAsOpenLayersFormat();
            $ne = $this->neCorner->getAsOpenLayersFormat();
            return "{ sw:$sw, ne:$ne }";
        }else{
            return "null";
        }
    }

    public function setInfoMessage($msg)
    {
        $this->infoMessage = $msg;
    }

    public function getInfoMessage()
    {
        return $this->infoMessage;
    }

}
