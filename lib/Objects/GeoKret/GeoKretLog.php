<?php

namespace lib\Objects\GeoKret;

use lib\Objects\User\User;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\BaseObject;

/**
 * GeokretLog represents GK-logs-queue entry stored in DB
 */
class GeoKretLog extends BaseObject
{
    private $id;

    /**
     *  @var \DateTime
     */
    private $logDateTime;

    /**
     *  @var \DateTime
     */
    private $enqueueDatetime;

    /* @var User */
    private $user;

    /**
     * @var GeoCache
     */
    private $geoCache;

    /**
     * @var integer
     */
    private $logType;

    /**
     * @var string
     */
    private $comment;
    private $trackingCode;
    private $geoKretId;
    private $geoKretName;

    public function __construct()
    {
        parent::__construct();
    }

    private static function FromDbRowFactory($row)
    {
        $geoKretyLog = new self();

        $geoKretyLog
            ->setId($row['id'])
            ->setLogDateTime(new \DateTime($row['log_date_time']))
            ->setEnqueueDatetime(new \DateTime($row['enqueue_date_time']))
            ->setUser(new User(['userId' => $row['user_id']]))
            ->setGeoCache(GeoCache::fromCacheIdFactory($row['geocache_id']) )
            ->setLogType($row['log_type'])
            ->setComment($row['comment'])
            ->setTrackingCode($row['tracking_code'])
            ->setGeoKretId($row['geokret_id'])
            ->setGeoKretName($row['geokret_name']);

        return $geoKretyLog;
    }

    public function getLogDateTime()
    {
        return $this->logDateTime;
    }

    public function getEnqueueDatetime()
    {
        return $this->enqueueDatetime;
    }

    /**
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @return GeoCache
     */
    public function getGeoCache()
    {
        return $this->geoCache;
    }

    public function getLogType()
    {
        return $this->logType;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function getTrackingCode()
    {
        return $this->trackingCode;
    }

    public function getGeoKretId()
    {
        return $this->geoKretId;
    }

    public function getGeoKretName()
    {
        return $this->geoKretName;
    }

    public function setLogDateTime(\DateTime $logDateTime)
    {
        $this->logDateTime = $logDateTime;
        return $this;
    }

    public function setEnqueueDatetime(\DateTime $enqueueDatetime)
    {
        $this->enqueueDatetime = $enqueueDatetime;
        return $this;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function setGeoCache(GeoCache $geoCache)
    {
        $this->geoCache = $geoCache;
        return $this;
    }

    public function setLogType($logType)
    {
        $this->logType = (int) $logType;
        return $this;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    public function setTrackingCode($nr)
    {
        $this->trackingCode = $nr;
        return $this;
    }

    public function setGeoKretId($GeoKretId)
    {
        $this->geoKretId = (int) $GeoKretId;
        return $this;
    }

    public function setGeoKretName($GeoKretName)
    {
        $this->geoKretName = $GeoKretName;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getDescription()
    {
        return sprintf("\n%d: GK[%d]: %s ",
            $this->getId(), $this->getGeoKretId(), $this->getGeoKretName());
    }

    /**
     *
     * @param array $geoKretogs
     * (array of GeoKretLog)
     */
    public static function EnqueueLogs($geoKretogs)
    {
        /* @var $geoKretLog GeoKretLog */
        $query = 'INSERT INTO geokret_log (
                    log_date_time, enqueue_date_time, user_id, geocache_id, log_type,
                    comment, tracking_code, geokret_id, geokret_name)
                  VALUES ';
        $paramId = 1;
        foreach ($geoKretogs as $geoKretLog) {
            $query .= '(:'.$paramId++.', NOW(), :'.$paramId++.','
                . ' :'.$paramId++.', :'.$paramId++.', :'.$paramId++.', :'.$paramId++.','
                    . ' :'.$paramId++.', :'.$paramId++.'),';
                    $queryParams[] = $geoKretLog->getLogDateTime()->format('Y-m-d H:i:s');
                    $queryParams[] = $geoKretLog->getUser()->getUserId();
                    $queryParams[] = $geoKretLog->getGeoCache()->getCacheId();
                    $queryParams[] = $geoKretLog->getLogType();
                    $queryParams[] = $geoKretLog->getComment();
                    $queryParams[] = $geoKretLog->getTrackingCode();
                    $queryParams[] = $geoKretLog->getGeoKretId();
                    $queryParams[] = $geoKretLog->getGeoKretName();
        }
        $query = rtrim($query,',');

        self::db()->multiVariableQuery($query, $queryParams);
    }

    public static function RemoveFromQueueByIds(array $ids)
    {
        if( count($ids) > 0 ){
            self::db()->query("DELETE FROM geokret_log WHERE id IN (".implode(',', $ids).")");
        }
    }

    public static function UpdateLastTryForIds(array $ids){
        if( count($ids) > 0 ){
            self::db()->query("UPDATE geokret_log SET last_try = NOW() WHERE id IN (".implode(',', $ids).")");
        }
    }

    public static function GetLast50LogsFromDb()
    {
        // get first fresh (not processed) records, then the older ones + no more than 50 logs at once
        $stmt = self::db()->query(
            'SELECT * FROM geokret_log
             ORDER BY last_try IS NULL DESC, last_try ASC
             LIMIT 50');

        $result = [];
        while($row = self::db()->dbResultFetch($stmt)){
            $result[] = self::FromDbRowFactory($row);
        }

        return $result;
    }

    public static function GetDbQueueLength()
    {
        return self::db()->simpleQueryValue(
            'SELECT COUNT(*) FROM geokret_log', 0);
    }

}
