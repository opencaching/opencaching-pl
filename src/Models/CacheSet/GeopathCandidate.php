<?php

namespace src\Models\CacheSet;

use src\Models\BaseObject;
use src\Models\GeoCache\MultiCacheStats;
use src\Models\User\User;
use src\Models\GeoCache\GeoCache;
use src\Utils\Debug\Debug;


class GeopathCandidate extends BaseObject
{
    private $id;
    private $date;

    private $geopath;
    private $geopathId;

    private $cache;
    private $cacheId;

    public static function fromCandidateIdFactory($candidateId)
    {
        $candidate = new self();
        $candidate->id = $candidateId;

        if( $candidate->loadDataFromDb() ){
            return $candidate;
        }

        return null;
    }

    public static function createNewCandidate(CacheSet $geopath, GeoCache $cache)
    {
        self::db()->multiVariableQuery(
            "INSERT INTO PowerTrail_cacheCandidate
                (PowerTrailId, cacheId, date)
             VALUES (:1, :2, NOW())",
             $geopath->getId(), $cache->getCacheId());
    }

    private function loadDataFromDb()
    {
        $stmt = $this->db->multiVariableQuery(
            "SELECT * FROM PowerTrail_cacheCandidate
             WHERE id = :1 LIMIT 1", $this->id);

        $data = $this->db->dbResultFetchOneRowOnly($stmt);
        if(!$data) {
            return false;
        }
        $this->loadFromDbRow($data);
        return true;
    }

    private function loadFromDbRow($dbRow)
    {
        foreach ($dbRow as $key => $val) {
            switch ($key) {
                case 'id':
                    $this->id = $val;
                    break;
                case 'date':
                    $this->date = $val;
                    break;
                case 'cacheId':
                    $this->cacheId = $val;
                    break;
                case 'PowerTrailId':
                    $this->geopathId = $val;
                    break;
                case 'link':
                    // there is nothing to do with it.
                    break;
                default:
                    Debug::errorLog("Unknown column: $key");
            }
        }
    }

    /**
     * Returns offer identifier
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retuns geocache object
     * @return GeoCache
     */
    public function getGeoCache(){

        if(!$this->cache) {
            $this->cache = GeoCache::fromCacheIdFactory($this->cacheId);
        }
        return $this->cache;
    }

    /**
     * Retur the offser submiting date
     * @return string
     */
    public function getSubmitedDate()
    {
        return $this->date;
    }

    /**
     * Returns geopath object
     * @return CacheSet
     */
    public function getGeopath()
    {
        if(!$this->geopath){
            $this->geopath = CacheSet::fromCacheSetIdFactory($this->geopathId);
        }
        return $this->geopath;
    }

    /**
     * Returns the list of Geopath candidates
     * @return GeopathCandidate[]
     */
    public static function getCacheCandidates(CacheSet $geopath)
    {
        $db = self::db();

        $rs = $db->multiVariableQuery(
            "SELECT id, cacheId, date FROM PowerTrail_cacheCandidate
             WHERE PowerTrailId = :1 ORDER BY date ASC", $geopath->getId());

        $result = [];
        $cacheIds = [];
        while($row = $db->dbResultFetch($rs)){
            $candidate = new self();
            $candidate->id = $row['id'];
            $candidate->date = $row['date'];
            $candidate->cacheId = $row['cacheId'];
            $candidate->geopath = $geopath;

            $cacheIds[] = $row['cacheId'];
            $result[$row['cacheId']] = $candidate;
        }

        // find geocaches
        foreach(MultiCacheStats::getGeocachesById($cacheIds) as $geoCache){
            $result[$geoCache->getCacheId()]->cache = $geoCache;
        }
        return $result;
    }

    public static function getUserGeopathCandidates(User $user)
    {
        // find user candidates
        $db = self::db();
        $rs = $db->multiVariableQuery(
            "SELECT gpc.*
             FROM PowerTrail_cacheCandidate AS gpc
                JOIN caches AS c ON c.cache_id = gpc.cacheId
             WHERE c.user_id = :1
             ORDER BY PowerTrailId ASC, date ASC", $user->getUserId());

        $result = [];
        $cacheIds = [];
        $geopathIds = [];
        while($row = $db->dbResultFetch($rs)){
            $candidate = new self();
            $candidate->id = $row['id'];
            $candidate->date = $row['date'];
            $candidate->cacheId = $row['cacheId'];
            $candidate->geopathId = $row['PowerTrailId'];

            $cacheIds[] = $row['cacheId'];
            $geopathIds[] = $row['PowerTrailId'];

            $result[$row['cacheId']] = $candidate;
        }

        // find geopaths
        $geopaths = MultiGeopathsStats::getGeopathsByIds($geopathIds);

        // add geocaches and geopaths
        foreach(MultiCacheStats::getGeocachesById($cacheIds) as $geoCache){
            $candidate = $result[$geoCache->getCacheId()];
            $candidate->cache = $geoCache;
            $candidate->geopath = $geopaths[$candidate->geopathId];
        }

        return $result;
    }

    public function prepareForSerialization()
    {
        if($this->cache){
            $this->cache->prepareForSerialization();
        }

        if($this->geopath){
            $this->geopath->prepareForSerialization();
        }
        $this->db = null;
    }

    public function restoreAfterSerialization()
    {
        if($this->cache){
            $this->cache->restoreAfterSerialization();
        }

        if($this->geopath){
            $this->geopath->restoreAfterSerialization();
        }
        $this->db = self::db();
    }

    /**
     * Cancel this offer of assigning cache to the geopath
     */
    public function cancelOffer() {
        $this->db->multiVariableQuery(
            "DELETE FROM PowerTrail_cacheCandidate
             WHERE id = :1 LIMIT 1", $this->id);
    }

    /**
     * Refuse this offer of assigning cache to the geopath
     */
    public function refuseOffer() {
        $this->cancelOffer();
    }

    /**
     * Accept this offer of assigning cache to the geopath
     */
    public function acceptOffer() {

        $geoPath = $this->getGeopath();
        $geoPath->addCache($this->getGeoCache());
        $this->cancelOffer();
    }

}
