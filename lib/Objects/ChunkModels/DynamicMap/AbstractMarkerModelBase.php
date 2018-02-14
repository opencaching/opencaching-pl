<?php
namespace lib\Objects\ChunkModels\DynamicMap;


abstract class AbstractMarkerModelBase
{
    // dir to look for tpl for marker models
    const CHUNK_DIR = 'dynamicMap';

    abstract public function getJSMarkersMgr();
    abstract public function getKey();
    abstract public function getInfoWinTpl();
}

