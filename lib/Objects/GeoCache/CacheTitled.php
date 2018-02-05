<?php

namespace lib\Objects\GeoCache;

use lib\Objects\BaseObject;

class CacheTitled extends BaseObject
{

    private $cacheId;
    private $logId;
    /**
     * @var \DateTime
     */
    private $dateTitled;

    public function __construct(array $params = [])
    {
        parent::__construct();
        if (isset($params['cacheId'])) { // load from DB if cachId param is set
            $this->loadByCacheId($params['cacheId']);
        }
    }

    public function getCacheId()
    {
        return $this->cacheId;
    }

    public function getLogId()
    {
        return $this->logId;
    }

    /**
     * @return \DateTime
     */
    public function getTitledDate()
    {
        return $this->dateTitled;
    }

    /**
     * Factory
     * @param int $cacheId
     * @return CacheTitled|null (null if no such CacheTitled entry in DB)
     */
    public static function fromCacheIdFactory($cacheId){
        try {
            return new self( array('cacheId' => $cacheId) );
        } catch (\Exception $e){
            return null;
        }
    }

    private function loadFromRow(array $cacheTitledDbRow)
    {
        //TODO: refactor it!

        $this->cacheId = $cacheTitledDbRow['cache_id'];
        $this->logId = $cacheTitledDbRow['log_id'];
        $this->dateTitled = new \DateTime($cacheTitledDbRow['date_alg']);

        return $this;
    }

    public static function isTitled($cacheId)
    {
        return (1 == self::db()->multiVariableQueryValue(
            'SELECT COUNT(*) FROM cache_titled WHERE cache_id= :1 LIMIT 1',
            0, $cacheId));
    }


    /**
     * Returns rich info about last titled cache
     * @return CacheTitled object of last titledCache
     */
    public static function getLastCacheTitled()
    {
        $db = self::db();

        $rs = $db->simpleQuery(
            'SELECT * FROM cache_titled ORDER BY date_alg DESC LIMIT 1');

        $row = $db->dbResultFetchOneRowOnly($rs);
        if(!empty($row)){
            $obj = new self();
            $obj->loadFromRow($row);
            return $obj;
        }else{
            // strange - titledCaches table is empty ?!
            return null;
        }
    }

    private function loadByCacheId($cacheId)
    {
        $s = $this->db->multiVariableQuery("SELECT * FROM `cache_titled` WHERE cache_id = :1 LIMIT 1", $cacheId);
        $cacheTitledDbRow = $this->db->dbResultFetch($s);
        if (is_array($cacheTitledDbRow)) {
            $this->loadFromRow($cacheTitledDbRow);
        } else {
            throw new \Exception("CacheTitled row not found");
        }
    }

}

