<?php
declare(strict_types=1);

namespace src\Models\ChunkModels\InteractiveMap;

use \ReflectionClass;

/**
 * This is a base class for all interactive map markers.
 */
abstract class AbstractMarkerModelBase
{
    public $id;             // id of marker
    public $lat;            // lat. of marker
    public $lon;            // lon. of marker
    public $icon;           // icon of marker
    public $section;        // [optional] section the marker belongs to

    public function getMarkerTypeName(): string
    {
        $str = (new ReflectionClass(static::class))->getShortName();
        return preg_replace('/Model$/', '', lcfirst($str));
    }

    public function getMarkerJsData(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    /**
     * Check if all necessary data is set in this marker class
     * @return boolean
     */
    public function checkMarkerData(): bool
    {
        return true
        && isset($this->id)
        && isset($this->lat)
        && isset($this->lon)
        && isset($this->icon)
        ;
    }

}
