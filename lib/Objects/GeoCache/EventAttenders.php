<?php
namespace lib\Objects\GeoCache;

use lib\Objects\BaseObject;

class EventAttenders extends BaseObject
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns array(username, user_id) of attenders of $cache event
     * If event has date in the future - counted are "will attend" logs
     * If event has date in the past - counted are "attended" logs
     *
     * @param GeoCache $cache
     * @return array
     */
    public static function getEventAttenders(GeoCache $cache)
    {
        if (! $cache->isEvent()) {
            return [];
        }
        if ($cache->getDatePlaced() > new \DateTime()) {
            $logTypes = GeoCacheLog::LOGTYPE_WILLATTENDED;
        } else {
            $logTypes = GeoCacheLog::LOGTYPE_ATTENDED;
        }

        $stmt = self::db()->multiVariableQuery(
            'SELECT DISTINCT `user`.`username`, `user`.`user_id`
            FROM `cache_logs`
            INNER JOIN `user` ON (`user`.`user_id` = `cache_logs`.`user_id`)
            WHERE `cache_logs`.`type` = :1
                AND `cache_logs`.`deleted` = 0
                AND `cache_logs`.`cache_id` = :2
            ORDER BY `user`.`username`',
            $logTypes, $cache->getCacheId());
        $result = self::db()->dbResultFetchAll($stmt);
        if ($result === false) {
            return [];
        }
        return $result;
    }
}