<?php
namespace src\Models\ChunkModels\DynamicMap;

use src\Models\CacheSet\CacheSet;

/**
 * This is model of cacheSet marker
 */
class CacheSetMarkerModel extends AbstractMarkerModelBase
{
    // lat/lon/icon inherited from parent!

    public $link; // full link to CS
    public $name; // cs name

    /**
     * This can be used for fast convert of CacheSet model to marker model
     * @param CacheSet $cs
     */
    public static function fromCacheSetFactory(CacheSet $cs)
    {
        $marker = new self();

        // Abstract-Marker data
        $marker->icon = $cs->getIcon();
        $marker->lat = $cs->getCoordinates()->getLatitude();
        $marker->lon = $cs->getCoordinates()->getLongitude();

        $marker->link = $cs->getUrl();
        $marker->name = $cs->getName();

        return $marker;
    }

    /**
     * Check if all necessary data is set in this marker class
     * @return boolean
     */
    public function checkMarkerData()
    {
        return parent::checkMarkerData() &&
               isset($this->name) &&
               isset($this->link);
    }

}
