<?php
namespace lib\Objects\ChunkModels\DynamicMap;


class CacheSetMarkerModel extends AbstractMarkerModelBase
{
    public function getJSMarkersMgr()
    {
        return self::CHUNK_DIR.'/cacheSetMarkerMgr';
    }

    public function getKey()
    {
        return 'CacheSetMarker';
    }

    public function getInfoWinTpl()
    {
        return '/cacheSetMarkerInfoWindow.tpl.php';
    }

    public $link;
    public $name;
    public $icon;
    public $lon;
    public $lat;



}

