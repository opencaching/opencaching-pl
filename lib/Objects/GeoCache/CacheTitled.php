<?php

namespace lib\Objects\GeoCache;

use lib\Objects\BaseObject;

class CacheTitled extends BaseObject
{

    private $cacheId;
    private $logId;
    private $dateAlg;

    public function getCacheId()
    {
        return $this->cacheId;
    }

    public function getLogId()
    {
        return $this->logId;
    }

    public function getTitledDate()
    {
        return $this->dateAlg;
    }

    private function loadFromRow(array $cacheTitledDbRow)
    {
        //TODO: refactor it!

        $this->cacheId = $cacheTitledDbRow['cache_id'];
        $this->logId = $cacheTitledDbRow['log_id'];
        $this->dateAlg = $cacheTitledDbRow['date_alg'];

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

}

