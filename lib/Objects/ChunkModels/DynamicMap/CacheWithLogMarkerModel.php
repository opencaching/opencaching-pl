<?php
namespace lib\Objects\ChunkModels\DynamicMap;

/**
 * This is map marker which has log information in infowindow
 * Cache properties are inherited from CacheMarkerModel
 */
class CacheWithLogMarkerModel extends CacheMarkerModel
{
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

    public $log_link = null; // if there is no link there is no log :)
    public $log_userLink;
    public $log_text;
    public $log_icon;
    public $log_typeName;
    public $log_username;

}

