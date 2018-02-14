<?php

namespace lib\Objects\ChunkModels\DynamicMap;

use lib\Objects\Coordinates\Coordinates;
use lib\Objects\OcConfig\OcConfig;
use Utils\Debug\Debug;

/**
 * This is model of the dynamic map
 * This class contains data which describes the map
 */

class DynamicMapModel
{

    /** @var Coordinates */
    private $coords;        // center of the map
    private $zoom;          // zoom of the map, int,
    private $mapTypeName;   // name of the default map for example 'satellite' or 'Topo' from OC config

    private $markerModels = [];
    private $markerMgrs = [];
    private $infoWindowTemplatesToLoad = [];

    public function __construct(){

        $this->coords = Coordinates::FromCoordsFactory(
            OcConfig::instance()->getMainPageMapCenterLat(),
            OcConfig::instance()->getMainPageMapCenterLon());

        $this->zoom = 11;
        $this->mapTypeName = 'roadmap';
    }

    /**
     * Add markers of one type
     *
     * @param string $markerClass - class returned by Extractor by 'CacheSetMarkerModel::class'
     * @param array $dataRows - rows of data - every row describes one marker
     * @param callable $rowExtractor - function which can create marekrClass from row
     */
    public function addMarkers($markerClass, array $dataRows, callable $rowExtractor)
    {
        // extract rows from rows set
        $markerClassInstance = new $markerClass();
        if(!is_subclass_of($markerClassInstance, AbstractMarkerModelBase::class)){
            Debug::errorLog("Marker class $markerClass is not a child of ".AbstractMarkerModelBase::class);
            return;
        }

        foreach($dataRows as $row){

            $markerModel = call_user_func($rowExtractor, $row);
            if($markerModel instanceof $markerClass){
                $this->addMarker($markerModel);
            }else{
                Debug::errorLog("Extractor returns something else than $markerClass");
            }
        } // foreach
    }

    /**
     * Add one marker to internal base of markers
     *
     * @param string $markerClass - marker class
     * @param AbstractMarkerModelBase $model
     */
    private function addMarker(AbstractMarkerModelBase $model)
    {
        $key = $model->getKey();
        if( !isset($this->markerModels[$key]) ){
            $this->markerModels[$key] = [];
            $this->markerMgrs[$key] = $model->getJSMarkersMgr();
            if($infoWinTpl = $model->getInfoWinTpl()){
                $this->infoWindowTemplatesToLoad[$key] = $infoWinTpl;
            }
        }
        $this->markerModels[$key][] = $model;
    }

    public function getMarkersData()
    {
        return $this->markerModels;
    }

    public function getMarkersMrgs($key)
    {
        return $this->markerMgrs[$key];
    }

    public function getInfoWindowTemplates()
    {
        return $this->infoWindowTemplatesToLoad;
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

    public function getMapTypeName(){
        return $this->mapTypeName;
    }

}

