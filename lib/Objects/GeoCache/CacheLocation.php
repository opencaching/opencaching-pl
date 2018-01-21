<?php

namespace lib\Objects\GeoCache;

use lib\Objects\BaseObject;
use lib\Objects\Coordinates\NutsLocation;
use Utils\Debug\Debug;

class CacheLocation extends BaseObject{

    /** @var NutsLocation */
    private $location;
    private $cacheId;


    public function __construct($cacheId=null){
        parent::__construct();
        $this->location = new NutsLocation();
        $this->cacheId = $cacheId;


        if($this->cacheId){
            $this->loadCacheLocation($cacheId);
        }
    }

    public static function fromDbRowFactory($dbRow)
    {
        $instance = new self();
        $instance->loadFromDbRow($dbRow);
        return $instance;
    }

    /**
     * Create new fresh CacheLocation object based on Geocache instance
     *
     * @param GeoCache $cache
     * @return \lib\Objects\GeoCache\CacheLocation
     */
    public static function createForCache(GeoCache $cache)
    {
        $instance = new self();
        $instance->cacheId = $cache->getCacheId();
        $instance->location = NutsLocation::fromCoordsFactory($cache->getCoordinates());

        return $instance;
    }

    public function getLocationDesc($separator='-'){
        return $this->location->getDescription($separator);
    }

    /**
     * Save (or update) current object to DB
     */
    public function updateInDb(){

        if( is_null($this->cacheId)){
            Debug::errorLog('Trying to update CacheLocation of unknown cache!');
            return;
        }

        $db->multiVariableQuery(
            "INSERT INTO cache_location
             (cache_id, last_modified, adm1, adm2, adm3, adm4, code1, code2, code3, code4)
             VALUES(:1, NOW(), :2, :3, :4, :5, :6, :7, :8, :9 )
             ON DUPLICATE KEY UPDATE last_modified = NOW(), adm1 = VALUES(adm1),
             adm2 = VALUES(adm2), adm3 = VALUES(adm3), adm4 = VALUES(adm4),
             code1 = VALUES(code1), code2 = VALUES(code2), code3 = VALUES(code3),
             code4 = VALUES(code4)",
             $this->cacheId,
             $this->location->getName(NutsLocation::LEVEL_COUNTRY),
             $this->location->getName(NutsLocation::LEVEL_1),
             $this->location->getName(NutsLocation::LEVEL_2),
             $this->location->getName(NutsLocation::LEVEL_3),
             $this->location->getCode(NutsLocation::LEVEL_COUNTRY),
             $this->location->getCode(NutsLocation::LEVEL_1),
             $this->location->getCode(NutsLocation::LEVEL_2),
             $this->location->getCode(NutsLocation::LEVEL_3)
        );
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

