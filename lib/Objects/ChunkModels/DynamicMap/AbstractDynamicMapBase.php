<?php
namespace lib\Objects\ChunkModels\DynamicMap;

use lib\Objects\Coordinates\Coordinates;
use Utils\Debug\Debug;
use Utils\View\View;

abstract class AbstractDynamicMapBase
{
		// place where maps flavour are stored
    const CHUNK_DIR = __DIR__.'/../../../../tpl/stdstyle/chunks/dynamicMap';

    /** @var Coordinates */
    private $coords;    // center of the map
    private $zoom;      // zoom of the map, int,

    private $mapTypeName; // name of the map for example 'satellite' or 'Topo' from OC config

    protected $dataRows = [];

    protected $dataExtractor = null;

    public function __construct(){
        $this->coords = Coordinates::FromCoordsFactory(54,18); //todo
        $this->zoom = 11;
        $this->mapTypeName = 'roadmap';
    }


    protected abstract function getMarkerObjectJsTpl();


    public function addMarkersDataRows(array $data){
        $this->dataRows = $data;
    }

    public function setDataRowExtractor(callable $extractor){
        $this->dataExtractor = $extractor;
    }

    public function getJsDynamicMapMarkerObject(){

        $func = View::getChunkFunc($this->getMarkerObjectJsTpl());
        call_user_func($func, $this);
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

    public function getDataRows(){
        $result = [];

        if(is_null($this->dataExtractor) || !is_callable($this->dataExtractor)){
            Debug::errorLog(__METHOD__.': Improper -Data-Extractor- !');
            return $result;
        }

        foreach($this->dataRows as $row){
            $result[] = call_user_func($this->dataExtractor, $row);
        }

        return $result;
    }
}


