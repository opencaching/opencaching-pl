<?php

namespace src\Models\ChunkModels\InteractiveMap;

use src\Models\Coordinates\Coordinates;
use src\Models\OcConfig\OcConfig;
use src\Utils\Debug\Debug;

/**
 * This is model of an interactive map
 * This class contains data which describes the map
 */
class InteractiveMapModel
{
    // Default section name if none is used explicitly. Please be careful not to
    // use this value for real meaningful section naming
    const DEFAULT_SECTION = "_DEFAULT_";

    // Available markers style families for user to choose one.
    // Family name should be an identifier compliant (no spaces etc.)
    const MARKERS_FAMILIES = [ "simple", "okapi" ];
    
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

    /**
     * A family of marker styling used in interactive map view.
     */
    private $markersFamily;

    /**
     * Markers data placed within sections
     */
    private $markerModels = [];

    /**
     * Additional properties of sections, convinient for external use
     */
    private $sectionsProperties = [];

    /**
     * An array of (section key => translation key) for sections used in
     * markerModels
     */
    private $sectionsKeys = [];

    public function __construct()
    {

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
    public function addMarkersWithExtractor(
        $markerClass, array $dataRows, callable $rowExtractor
    ) {
        foreach ($dataRows as $row) {

            $markerModel = call_user_func($rowExtractor, $row);

            if (!($markerModel instanceof $markerClass)) {
                Debug::errorLog(
                    "Extractor returns something different than $markerClass"
                );
                return;
            }

            if (!is_subclass_of($markerModel, AbstractMarkerModelBase::class)) {
                Debug::errorLog(
                    "Marker class $markerClass is not a child of "
                    . AbstractMarkerModelBase::class
                );
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

        if (!$model->checkMarkerData()) {
            $type = $model->getMarkerTypeName();
            Debug::errorLog("Marker of $type has incomplete data!");
        }
        $section = (
            isset($model->section) ? $model->section : self::DEFAULT_SECTION
        );
        if (!isset($this->markerModels[$section][$type])) {
            $this->markerModels[$section][$type] = [];
        }
        $this->markerModels[$section][$type][] = $model;
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

    public function getMarkerSections(){
        return array_keys($this->markerModels);
    }

    public function getMarkerTypes($section = null) {
        $result = [];
        if ($section != null) {
            $result =
                isset($this->markerModels[$section])
                ? array_keys($this->markerModels[$section])
                : [];
        } else {
            foreach($this->markerModels as $s) {
                foreach($s as $markerType => $markers) {
                    if (!in_array($markerType, $result)) {
                        $result[] = $markerType;
                    }
                }
            }
            /*
            // an alternative way but seems to be too complicated:
            array_walk($this->markerModels, function($v) use (&$result) {
                $result = array_merge($result, array_keys($v));
            });
            $result = array_values(array_unique($result));
            */
        }
        return $result;
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

    public function setMarkersFamily($markersFamily)
    {
        $this->markersFamily = $markersFamily;
    }

    public function getMarkersFamily()
    {
        return $this->markersFamily;
    }

    public function setSectionProperties($section, $properties)
    {
        $this->sectionsProperties[$section] = $properties;
    }

    public function getSectionsPropertiesJson()
    {
        return json_encode($this->sectionsProperties, JSON_PRETTY_PRINT);
    }

    public function setSectionsKeys($sectionsKeys)
    {
        $this->sectionsKeys = $sectionsKeys;
    }

    public function getSectionsKeys()
    {
        return $this->sectionsKeys;
    }
}
