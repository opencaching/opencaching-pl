<?php
namespace lib\Objects\ChunkModels\DynamicMap;

/**
 * This is base class for all dynami map markers.
 */
abstract class AbstractMarkerModelBase
{
    // dir to look for tpl for marker models
    const CHUNK_DIR = 'dynamicMap';

    /**
     * Return link to Java-Script marker manager
     */
    abstract public function getJSMarkersMgr();

    /**
     * Return unique key for this type of markers
     */
    abstract public function getKey();

    /**
     * Return link to template used to generate infoWindow
     */
    abstract public function getInfoWinTpl();
}

