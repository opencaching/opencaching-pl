<?php
namespace src\Models\ChunkModels\DynamicMap;

use \ReflectionClass;

/**
 * This is base class for all dynamic map markers.
 */
abstract class AbstractMarkerModelBase
{
    public $lat;            // lat. of marker
    public $lon;            // lon. of marker
    public $icon;           // icon of marker

    public function getMarkerTypeName(){
        $str = (new ReflectionClass(static::class))->getShortName();
        return preg_replace('/Model$/', '', lcfirst($str));
    }

    public function getMarkerJsData()
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    /**
     * Check if all necessary data is set in this marker class
     * @return boolean
     */
    public function checkMarkerData()
    {
        return isset($this->lat) && isset($this->lon) && isset($this->icon);
    }

}
