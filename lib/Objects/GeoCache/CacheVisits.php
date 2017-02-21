<?php

namespace lib\Objects\GeoCache;

use Utils\Database\XDb;

/**
 * Class for operations on cache_visits table
 *
 */
class CacheVisits
{

    // typer of cache_visits records:
    const TYPE_CACHE_VISITS = 'C';        //counter for cache visits
    const TYPE_LAST_USER_UNIQUE_VISIT = 'U'; //counter to save last unique user visit
    const TYPE_PREPUBLICATION_VISIT = 'P'; //counter to store pre-publication visits

    // time in sek between unique visits
    const UNIQUE_VISIT_PERIOD = 604800; //[sek] -one week


    /**
     * Returns number of unique visits for cache
     * Unique means that between the same user visits was at least UNIQUE_VISIT_PERIOD seconds...
     *
     * @param unknown $cacheId
     * @return unknown|mixed
     */
    public static function GetCacheVisits($cacheId)
    {
        return XDb::xMultiVariableQueryValue(
            "SELECT `count` FROM `cache_visits2`
            WHERE `cache_id`=:1 AND type = :2 LIMIT 1",
            0, $cacheId, self::TYPE_CACHE_VISITS);
    }

    /**
     * Returns array of userIds which accessed cache before publication
     * @param unknown $cacheId
     * @return array
     */
    public static function GetPrePublicationVisits($cacheId)
    {
        $s = XDb::xSql(
            "SELECT user_id_ip FROM `cache_visits2`
            WHERE `cache_id`= ? AND type = ?",
            $cacheId, self::TYPE_PREPUBLICATION_VISIT);

        $result = array();
        while($row = XDb::xFetchArray($s)){
            $result[] = $row['user_id_ip'];
        }
        return $result;

    }

    /**
     * Inc stats for chache before publication
     * @param unknown $userIdOrIp
     * @param unknown $cacheId
     */
    public static function CountCachePrePublicationVisit($userIdOrIp, $cacheId)
    {
        // add user-visit record
        XDb::xSql(
            "INSERT INTO cache_visits2 (cache_id, user_id_ip, type, visit_date)
            VALUES (?, ?, ?, NOW() ) ON DUPLICATE KEY UPDATE count = count + 1",
            $cacheId, $userIdOrIp, self::TYPE_PREPUBLICATION_VISIT);
    }

    /**
     * inc stats for cache after publication
     *
     * @param unknown $userIdOrIp
     * @param unknown $cacheId
     */
    public static function CountCacheVisit($userIdOrIp, $cacheId)
    {

        //ocasionally clean table
        if( 0 == rand(0, 1000) ){
            self::clearOldUniqueVisits();
        }

        // check if this is unique visit
        if(0==XDb::xMultiVariableQueryValue(
            "SELECT COUNT(*) FROM cache_visits2
            WHERE cache_id = :1 AND user_id_ip = :2 AND type = :3 AND visit_date > NOW() - :4 LIMIT 1",
            0, $cacheId, $userIdOrIp, self::TYPE_LAST_USER_UNIQUE_VISIT, self::UNIQUE_VISIT_PERIOD)){

            // this is unique visit

            // add user-visit record
            XDb::xSql(
                "REPLACE INTO cache_visits2 (cache_id, user_id_ip, type, visit_date)
                VALUES (?, ?, ?, NOW())", $cacheId, $userIdOrIp, self::TYPE_LAST_USER_UNIQUE_VISIT);

            // inc cache stat
            XDb::xSql(
                "INSERT INTO cache_visits2 (cache_id, type, count)
                VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE count = count + 1",
                $cacheId, self::TYPE_CACHE_VISITS);
        }
    }

    /**
     * Remove unnecessary records - should be called from time-to-time to remove records older than
     * time between unique visits to free the space on the disk
     */
    private static function ClearOldUniqueVisits()
    {
        XDb::xSql(
            "DELETE FROM `cache_visits2` WHERE type = ? AND visit_date < NOW() - ?",
            self::TYPE_LAST_USER_UNIQUE_VISIT, self::UNIQUE_VISIT_PERIOD);
    }
}

