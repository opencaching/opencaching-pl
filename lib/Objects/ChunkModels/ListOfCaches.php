<?php
/**
 * This class represents model of data for ListOfCaches chunk.
 * Check myProvince.php for example of use it.
 *
 */
namespace lib\Objects\ChunkModels;

class ListOfCaches{

    private $listOfCaches = array();
    private $recomendationColumn = false;
    private $logTooltipEnabled = false;

    public function caches(){
        return $this->listOfCaches;
    }

    public function recoCol(){
        return $this->recomendationColumn;
    }

    public function getCacheModel(){
        return new ListOfCachesCacheModel();
    }

    public function addCache(ListOfCachesCacheModel $c){
        array_push($this->listOfCaches, $c);
    }

    public function enableRecoColumn(){
        $this->recomendationColumn = true;
    }

    public function getCachesCount(){
        return count($this->listOfCaches);
    }

    public function enableLogTooltip(){
        $this->logTooltipEnabled = true;
    }

    public function logTooltipEnabled(){
        return $this->logTooltipEnabled;
    }
}

class ListOfCachesCacheModel{

    public $icon = '';
    public $date = '';
    public $cacheName = '';
    public $cacheId = '';
    public $userName = '';
    public $recoNum = '-';

    public $logIcon = null;
    public $logText = null;

    public $ptEnabled = false;
    public $ptId = '';
    public $ptName = '';
    public $ptIcon = '';
}

