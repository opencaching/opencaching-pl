<?php

namespace lib\Objects\GeoCache;

use lib\Objects\BaseObject;
use lib\Objects\Coordinates\NutsLocation;

class CacheLocation extends BaseObject{

    /** @var NutsLocation */
    private $location;

    public function __construct($cacheId){
        parent::__construct();
        $this->location = new NutsLocation();

        $this->loadCacheLocation($cacheId);
    }

    public function getLocationDesc($separator='-'){
        return $this->location->getDescription($separator);
    }

    /**
     * Load saved in DB cache location
     * @param integer $cacheId
     */
    private function loadCacheLocation($cacheId){

        $stmt = $this->db->multiVariableQuery(
            'SELECT `code1`, `code2`, `code3`, `code4`, `adm1`,
                    `adm2`, `adm3`, `adm4`  FROM `cache_location`
            WHERE `cache_id` =:1 LIMIT 1', $cacheId);

        $dbResult = $this->db->dbResultFetch($stmt);
        if(is_array($dbResult)){
            $this->location->setLevel(NutsLocation::LEVEL_COUNTRY,
                $dbResult['code1'], $dbResult['adm1']);

            $this->location->setLevel(NutsLocation::LEVEL_1,
                $dbResult['code2'], $dbResult['adm2']);

            $this->location->setLevel(NutsLocation::LEVEL_2,
                $dbResult['code3'], $dbResult['adm3']);

            $this->location->setLevel(NutsLocation::LEVEL_3,
                $dbResult['code4'], $dbResult['adm4']);

        }

    }
}

