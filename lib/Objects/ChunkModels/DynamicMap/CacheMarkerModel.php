<?php
namespace lib\Objects\ChunkModels\DynamicMap;

use lib\Objects\GeoCache\GeoCache;
use lib\Objects\User\User;

/**
 * This is model of geocache marker
 */

class CacheMarkerModel extends AbstractMarkerModelBase
{
    public $wp;
    public $link;
    public $name;
    public $username = null;
    public $icon;
    public $lon;
    public $lat;

    /**
     * {@inheritDoc}
     * @see \lib\Objects\ChunkModels\DynamicMap\AbstractMarkerModelBase::getKey()
     */
    public function getKey()
    {
        return 'CacheMarker';
    }

    /**
     * {@inheritDoc}
     * @see \lib\Objects\ChunkModels\DynamicMap\AbstractMarkerModelBase::getJSMarkersMgr()
     */
    public function getJSMarkersMgr()
    {
        return self::CHUNK_DIR.'/cacheMarkerMgr';
    }

    /**
     * {@inheritDoc}
     * @see \lib\Objects\ChunkModels\DynamicMap\AbstractMarkerModelBase::getInfoWinTpl()
     */
    public function getInfoWinTpl()
    {
        return '/cacheMarkerInfoWindow.tpl.php';
    }

    /**
     * Creates marker model from Geocache model
     * @param GeoCache $c
     * @param User $user
     * @return CacheMarkerModel
     */
    public static function fromGeocacheFactory(GeoCache $c, User $user=null)
    {
        $marker = new self();
        $marker->wp = $c->getGeocacheWaypointId();
        $marker->link = $c->getCacheUrl();
        $marker->name = $c->getCacheName();
        $marker->username = $c->getOwner()->getUserName();
        $marker->icon = $c->getCacheIcon($user);
        $marker->lon = $c->getCoordinates()->getLongitude();
        $marker->lat = $c->getCoordinates()->getLatitude();
        return $marker;
    }

}

