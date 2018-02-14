<?php
namespace lib\Objects\ChunkModels\DynamicMap;


class CacheMarkerModel extends AbstractMarkerModelBase
{
    public function getKey()
    {
        return 'CacheMarkers';
    }

    public function getJSMarkersMgr()
    {
        return self::CHUNK_DIR.'/cacheMarkerMgr';
    }

    public function getInfoWinTpl()
    {
        return '/cacheMarkerInfoWindow.tpl.php';
    }

    public $id;
    public $type;
    public $name;
    public $icon;
    public $lon;
    public $lat;



}

