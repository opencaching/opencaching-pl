<?php
namespace lib\Objects\ChunkModels\DynamicMap;

use lib\Objects\CacheSet\CacheSet;

/**
 * This is model of cacheSet marker
 */

class CacheSetMarkerModel extends AbstractMarkerModelBase
{
    public $link; // fulllink to CS
    public $name; // cs name
    public $icon; // cs icon
    public $lon;  // cs coords - lon
    public $lat;  // cs coords - lat

    /**
     * {@inheritDoc}
     * @see \lib\Objects\ChunkModels\DynamicMap\AbstractMarkerModelBase::getJSMarkersMgr()
     */
    public function getJSMarkersMgr()
    {
        return self::CHUNK_DIR.'/cacheSetMarkerMgr';
    }

    /**
     * {@inheritDoc}
     * @see \lib\Objects\ChunkModels\DynamicMap\AbstractMarkerModelBase::getKey()
     */
    public function getKey()
    {
        return 'CacheSetMarker';
    }

    /**
     * {@inheritDoc}
     * @see \lib\Objects\ChunkModels\DynamicMap\AbstractMarkerModelBase::getInfoWinTpl()
     */
    public function getInfoWinTpl()
    {
        return '/cacheSetMarkerInfoWindow.tpl.php';
    }

    /**
     * This can be used for fast convert of CacheSet model to marker model
     * @param CacheSet $cs
     */
    public static function fromCacheSetFactory(CacheSet $cs)
    {
        $marker = new self();
        $marker->icon = $cs->getIcon();
        $marker->lat = $cs->getCoordinates()->getLatitude();
        $marker->lon = $cs->getCoordinates()->getLongitude();
        $marker->link = $cs->getUrl();
        $marker->name = $cs->getName();
        return $marker;
    }

}

