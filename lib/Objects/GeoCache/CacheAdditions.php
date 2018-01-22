<?php
namespace lib\Objects\GeoCache;

/**
 * This class allow to read/write data stored in caches_additions DB table.
 */

use lib\Objects\BaseObject;

class CacheAdditions extends BaseObject
{
    private $cacheId;
    private $altitude = null;


    public function __construct($cacheId)
    {
        parent::__construct();
        $this->cacheId = $cacheId;
        $this->loadFromDb();
    }

    /**
     * Returns cache altitude
     * @return int
     */
    public function getAltitude()
    {
        return $this->altitude;
    }

    /**
     * update cache altitude in DB
     *
     * @param int $newAltitude
     */
    public function updateAltitude($newAltitude)
    {
        $this->altitude = $newAltitude;
        $this->storeToDb();
    }

    /**
     * Load local data from DB
     */
    private function loadFromDb()
    {
        $rs = $this->db->multiVariableQuery(
                "SELECT * FROM caches_additions
                WHERE cache_id = :1 LIMIT 1", $this->cacheId);

        $row = $this->db->dbResultFetchOneRowOnly($rs);
        if($row){
            $this->altitude = $row['altitude'];
        }
    }

    /**
     * Store local data to DB
     */
    private function storeToDb()
    {
        $this->db->multiVariableQuery(
            "INSERT INTO caches_additions
            (cache_id, altitude)
            VALUES (:1, :2)
            ON DUPLICATE KEY UPDATE cache_id = VALUES(cache_id),
            altitude = VALUES(altitude)", $this->cacheId, $this->altitude);
    }
}
