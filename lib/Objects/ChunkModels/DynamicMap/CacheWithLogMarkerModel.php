<?php
namespace lib\Objects\ChunkModels\DynamicMap;

use lib\Objects\GeoCache\GeoCacheLog;
use lib\Objects\User\User;
use Utils\Text\Formatter;

/**
 * This is map marker which has log information in infowindow
 * Cache properties are inherited from CacheMarkerModel
 */
class CacheWithLogMarkerModel extends CacheMarkerModel
{
    public $log_link = null; // if there is no link there is no log :)
    public $log_text;
    public $log_icon;
    public $log_typeName;
    public $log_username;
    public $log_date;

    public function getKey()
    {
        return 'CacheWithLogMarker';
    }

    public function getJSMarkersMgr()
    {
        return self::CHUNK_DIR.'/cacheWithLogMarkerMgr';

    }

    public function getInfoWinTpl()
    {
        return '/cacheWithLogMarkerInfoWindow.tpl.php';
    }

    public static function fromGeoCacheLogFactory(GeoCacheLog $log, User $user = null)
    {
        $marker = new self();
        $marker->wp = $log->getGeoCache()->getGeocacheWaypointId();
        $marker->link = $log->getGeoCache()->getCacheUrl();
        $marker->name = $log->getGeoCache()->getCacheName();
        $marker->username = $log->getGeoCache()->getOwner()->getUserName();
        $marker->icon = $log->getGeoCache()->getCacheIcon($user);
        $marker->lon = $log->getGeoCache()->getCoordinates()->getLongitude();
        $marker->lat = $log->getGeoCache()->getCoordinates()->getLatitude();
        $marker->log_link = $log->getLogUrl();
        $text = strip_tags($log->getText(),'<br><p>');
        $textLen = mb_strlen($text);
        if ($textLen > 200) {
            $text = mb_strcut($text, 0, 200);
            $text .= '...';
        }
        $marker->log_text = $text;
        $marker->log_icon = $log->getLogIcon();
        $marker->log_typeName = tr(GeoCacheLog::typeTranslationKey($log->getType()));
        $marker->log_username = $log->getUser()->getUserName();
        $marker->log_date = Formatter::date($log->getDateCreated());
        return $marker;
    }

}

