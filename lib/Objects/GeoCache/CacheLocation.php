<?php

namespace lib\Objects\GeoCache;

use lib\Objects\BaseObject;
use lib\Objects\Coordinates\NutsLocation;

class CacheLocation extends BaseObject{

    /** @var NutsLocation */
    private $location;

    public function __construct($cacheId=null){
        parent::__construct();
        $this->location = new NutsLocation();

        if($cacheId){
            $this->loadCacheLocation($cacheId);
        }
    }

    public static function fromDbRowFactory($dbRow)
    {
        $instance = new self();
        $instance->loadFromDbRow($dbRow);
        return $instance;
    }

    public function getLocationDesc($separator='-'){
        return $this->location->getDescription($separator);
    }

    private function loadFromDbRow($dbRow)
    {
        if(is_array($dbRow)){
            if(isset($dbRow['code1'], $dbRow['adm1'])){
                $this->location->setLevel(NutsLocation::LEVEL_COUNTRY,
                    $dbRow['code1'], $dbRow['adm1']);
            }

            if(isset($dbRow['code2'], $dbRow['adm2'])){
                $this->location->setLevel(NutsLocation::LEVEL_1,
                    $dbRow['code2'], $dbRow['adm2']);
            }

            if(isset($dbRow['code3'], $dbRow['adm3'])){
                $this->location->setLevel(NutsLocation::LEVEL_2,
                    $dbRow['code3'], $dbRow['adm3']);
            }

            if(isset($dbRow['code4'], $dbRow['adm4'])){
                $this->location->setLevel(NutsLocation::LEVEL_3,
                    $dbRow['code4'], $dbRow['adm4']);
            }
        }
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
        $this->loadFromDbRow($dbResult);

    }
}

