<?php
namespace src\Models\ChunkModels\DynamicMap;

use src\Models\GeoCache\GeoCache;
use src\Models\User\User;

/**
 * This is model of geocache marker
 */

class CacheMarkerModel extends AbstractMarkerModelBase
{
    // lat/lon/icon inherited from parent!

    public $wp;
    public $link;
    public $name;
    public $username;


    /**
     * Creates marker model from Geocache model
     * @param GeoCache $c
     * @param User $user
     * @return CacheMarkerModel
     */
    public static function fromGeocacheFactory(GeoCache $c, User $user=null)
    {
        $marker = new self();
        $marker->importDataFromGeoCache( $c, $user);
        return $marker;
    }

    protected function importDataFromGeoCache(GeoCache $c, User $user=null)
    {
        // Abstract-Marker data
        $this->icon = $c->getCacheIcon($user);
        $this->lat = $c->getCoordinates()->getLatitude();
        $this->lon = $c->getCoordinates()->getLongitude();

        $this->wp = $c->getGeocacheWaypointId();
        $this->link = $c->getCacheUrl();
        $this->name = $c->getCacheName();
        $this->username = $c->getOwner()->getUserName();
    }

    /**
     * Check if all necessary data is set in this marker class
     * @return boolean
     */
    public function checkMarkerData()
    {
        return parent::checkMarkerData() &&
        isset($this->wp) &&
        isset($this->link) &&
        isset($this->name) &&
        isset($this->username);
    }

}
