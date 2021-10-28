<?php

namespace src\Models\GeoCache;

use src\Models\OcConfig\OcConfig;
use src\Utils\Database\OcDb;

/**
 * Class for operations on cache_visits table
 */
class CacheVisits
{
    // types of cache_visits records:
    // counter for cache visits
    public const TYPE_CACHE_VISITS = 'C';

    //counter to save last unique user visit
    public const TYPE_LAST_USER_UNIQUE_VISIT = 'U';

    //counter to store pre-publication visits
    public const TYPE_PREPUBLICATION_VISIT = 'P';

    // time in sek between unique visits
    public const UNIQUE_VISIT_PERIOD_MINIMAL = 3600; //[seconds] one hour

    /**
     * Returns number of unique visits for cache
     * Unique means that between the same user visits was at least
     * UNIQUE_VISIT_PERIOD seconds...
     *
     * @param unknown $cacheId
     * @return unknown|mixed
     */
    public static function getCacheVisits($cacheId)
    {
        return (OcDb::instance())->multiVariableQueryValue(
            'SELECT `count` FROM `cache_visits2`
            WHERE `cache_id`= :1 AND type = :2 LIMIT 1',
            0,
            $cacheId,
            self::TYPE_CACHE_VISITS
        );
    }

    /**
     * Returns array of userIds which accessed cache before publication
     * @param unknown $cacheId
     * @return array
     */
    public static function getPrePublicationVisits($cacheId)
    {
        $db = OcDb::instance();
        $s = $db->multiVariableQuery(
            'SELECT user_id_ip FROM `cache_visits2`
            WHERE `cache_id`= :1 AND type = :2',
            $cacheId,
            self::TYPE_PREPUBLICATION_VISIT
        );

        $result = [];

        while ($row = $db->dbResultFetch($s, OcDb::FETCH_BOTH)) {
            $result[] = $row['user_id_ip'];
        }

        return $result;
    }

    /**
     * Inc stats for chache before publication
     * @param unknown $userIdOrIp
     * @param unknown $cacheId
     */
    public static function countCachePrePublicationVisit($userIdOrIp, $cacheId)
    {
        // add user-visit record
        (OcDb::instance())->multiVariableQuery(
            'INSERT INTO cache_visits2 (cache_id, user_id_ip, type, visit_date)
            VALUES (:1, :2, :3, NOW())
            ON DUPLICATE KEY UPDATE count = count + 1',
            $cacheId,
            $userIdOrIp,
            self::TYPE_PREPUBLICATION_VISIT
        );
    }

    /**
     * inc stats for cache after publication
     *
     * @param unknown $userIdOrIp
     * @param unknown $cacheId
     */
    public static function countCacheVisit($userIdOrIp, $cacheId)
    {
        $db = OcDb::instance();

        // check if this is unique visit
        if (0 == $db->multiVariableQueryValue(
            'SELECT COUNT(*) FROM cache_visits2
            WHERE cache_id = :1 AND user_id_ip = :2 AND type = :3
            AND visit_date >= NOW() - INTERVAL :4 SECOND LIMIT 1',
            0,
            $cacheId,
            $userIdOrIp,
            self::TYPE_LAST_USER_UNIQUE_VISIT,
            self::getUniqueVisitPeriod()
        )) {
            // this is unique visit

            // add user-visit record
            $db->multiVariableQuery(
                'REPLACE INTO cache_visits2
                (cache_id, user_id_ip, type, visit_date)
                VALUES (:1, :2, :3, NOW())',
                $cacheId,
                $userIdOrIp,
                self::TYPE_LAST_USER_UNIQUE_VISIT
            );

            // inc cache stat
            $db->multiVariableQuery(
                'INSERT INTO cache_visits2 (cache_id, type, count, visit_date)
                VALUES (:1, :2, 1, NOW())
                ON DUPLICATE KEY UPDATE count = count + 1',
                $cacheId,
                self::TYPE_CACHE_VISITS
            );
        }
    }

    /**
     * Remove unnecessary records - should be called from time-to-time
     * to remove records older than time between unique visits
     * to free the space on the disk
     */
    public static function clearOldUniqueVisits()
    {
        (OcDb::instance())->multiVariableQuery(
            'DELETE FROM `cache_visits2`
            WHERE type = :1 AND visit_date < NOW() - INTERVAL :2 SECOND',
            self::TYPE_LAST_USER_UNIQUE_VISIT,
            self::getUniqueVisitPeriod()
        );
    }

    /**
     * Retrieves uniqie visit period from config and ensures it is not below
     * UNIQUE_VISIT_PERIOD_MINIMAL
     *
     * @return int max(config unique visit period, UNIQUE_VISIT_PERIOD_MINIMAL)
     */
    protected static function getUniqueVisitPeriod(): int
    {
        $configUVP = OcConfig::getUniqueVisitPeriod();

        return
            $configUVP > self::UNIQUE_VISIT_PERIOD_MINIMAL
            ? $configUVP
            : self::UNIQUE_VISIT_PERIOD_MINIMAL;
    }
}
