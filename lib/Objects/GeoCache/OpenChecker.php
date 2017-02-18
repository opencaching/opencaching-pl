<?php

namespace lib\Objects\GeoCache;


use Utils\Database\XDb;

class OpenChecker
{
    private $hits;
    private $tries;



    public function __construct($cacheId)
    {
        $s = XDb::xSql(
            "SELECT opensprawdzacz.proby AS tries, opensprawdzacz.sukcesy AS hits
             FROM waypoints, opensprawdzacz
             WHERE waypoints.cache_id = ?
                AND waypoints.type = ?
                AND waypoints.opensprawdzacz = ?
                AND waypoints.cache_id = opensprawdzacz.cache_id
             LIMIT 1", $cacheId, Waypoint::TYPE_FINAL, Waypoint::OPENCHECKER_ENABLED);

        if($row = XDb::xFetchArray($s)){
            $this->loadFromDbRow($row);
        }else{
            throw new \Exception();
        }

    }


    public static function ForCacheIdFactory($cacheId){

        try{
          return new OpenChecker($cacheId);
        } catch(\Exception $e){
            return null;
        }
    }

    private function loadFromDbRow(array $row){

        //TODO: this method is used in viewcache only now and probably needs to be extended...

        $this->hits = $row['hits'];
        $this->tries = $row['tries'];
    }

    public function getHits()
    {
        return $this->hits;
    }

    public function getTries()
    {
        return $this->tries;
    }

    /**
     * returns true if OpenChecker is enabled in config
     * @return boolean
     */
    public static function isEnabledInConfig(){
        global $config;
        if($config['module']['openchecker']['enabled']) return true;
        else return false;
    }


}


