<?php

namespace lib\Objects\GeoCache;

use Utils\Database\OcDb;

class CacheLocation {

    private $code1;
    private $code2;
    private $code3;
    private $code4;

    private $adm1;
    private $adm2;
    private $adm3;
    private $adm4;

    public function __construct($cacheId){
        $this->loadCacheLocation($cacheId);
    }

    public function getLocationDesc($separator='-'){

        // try to translate country name
        if (tr_available($this->code1)){
            $country = tr($this->code1);
        } else {
            $country = $this->adm1;
        }

        // try to detect region in order 3-2-4
        if($this->code3!=''){
            $region = $this->adm3;

        }elseif ($this->code2!=''){
            $region = $this->adm2;

        }else{
            $region = $this->adm4;
        }

        return $country . $separator . $region;
    }

    private function loadCacheLocation($cacheId){

        $db = OcDb::instance();

        $stmt = $db->multiVariableQuery(
            'SELECT `code1`, `code2`, `code3`, `code4`, `adm1`, `adm2`, `adm3`, `adm4`  FROM `cache_location` WHERE `cache_id` =:1 LIMIT 1', $cacheId);

        $dbResult = $db->dbResultFetch($stmt);
        if(is_array($dbResult)){
            $this->code1 = $dbResult['code1'];
            $this->code2 = $dbResult['code2'];
            $this->code3 = $dbResult['code3'];
            $this->code4 = $dbResult['code4'];

            $this->adm1 = $dbResult['adm1'];
            $this->adm2 = $dbResult['adm2'];
            $this->adm3 = $dbResult['adm3'];
            $this->adm4 = $dbResult['adm4'];

        }

    }
}

