<?php

namespace src\Models\GeoCache;

use src\Models\BaseObject;
use src\Models\User\User;

/**
 * This class should contains mostly static, READ-ONLY queries
 * used to generates statistics etc. around cache_logs db table
 */
class MultiLogStats extends BaseObject
{

    public static function getTotalSearchesNumber()
    {
        $countedTypes = implode(',', [
            GeoCacheLog::LOGTYPE_FOUNDIT,
            GeoCacheLog::LOGTYPE_DIDNOTFIND,
        ]);

        return self::db()->simpleQueryValue(
            "SELECT COUNT(*) FROM cache_logs
            WHERE type IN ($countedTypes)
                AND deleted = 0", 0);
    }

    public static function getLastSearchesCount($fromLastDays)
    {
        $days = (int)$fromLastDays;

        $countedTypes = implode(',', [
            GeoCacheLog::LOGTYPE_FOUNDIT,
            GeoCacheLog::LOGTYPE_DIDNOTFIND,
        ]);

        return self::db()->simpleQueryValue(
            "SELECT COUNT(*) FROM cache_logs
            WHERE type IN ($countedTypes)
                AND deleted = 0
                AND date_created > DATE_SUB(NOW(), INTERVAL $days day)", 0);
    }

    public static function getLastRecommendationsCount($fromLastDays)
    {
        $days = (int)$fromLastDays;

        return self::db()->multiVariableQueryValue(
            "SELECT COUNT(*) FROM cache_logs
            INNER JOIN cache_rating USING (cache_id, user_id)
            WHERE type = :1
                AND deleted = 0
                AND date_created > DATE_SUB(NOW(), INTERVAL $days day)",
            0, GeoCacheLog::LOGTYPE_FOUNDIT);
    }

    public static function getUsersCountWithAtLeastOneLog($fromLastDays)
    {
        $days = (int)$fromLastDays;

        return self::db()->simpleQueryValue(
            "SELECT count(*) FROM (
                SELECT count(`user_id`) FROM `cache_logs`
                WHERE `date` > DATE_SUB(NOW(), INTERVAL $days day)
                GROUP BY `user_id`) a", 0);
    }

    /**
     * Returns array with last logs data for each given cacheId
     * @param array $cacheIds
     * @param array $logFields - optional list of log fields
     *
     * @return array
     */
    public static function getLastLogForEachCache(array $cacheIds, array $logFields = null)
    {
        if (empty($cacheIds)) {
            return [];
        }

        if (empty($logFields)) {
            $logFields = ['*'];
        }

        $db = self::db();

        $cacheIdsStr = $db->quoteString(implode(',', $cacheIds));
        $fieldsStr = $db->quoteString(implode(',', $logFields));

        $rs = $db->multiVariableQuery(
            "SELECT $fieldsStr
             FROM cache_logs
             INNER JOIN (
                SELECT MAX(id) as id
                FROM cache_logs
                WHERE cache_id IN ($cacheIdsStr) AND deleted = 0
                GROUP BY cache_id) x
             USING (id) ");

        return $db->dbResultFetchAll($rs);
    }

    private static function getAllowedCacheStatusesList()
    {
        return implode(',', [
            GeoCache::STATUS_READY,
            GeoCache::STATUS_UNAVAILABLE,
            GeoCache::STATUS_ARCHIVED,
        ]);
    }

    /**
     * This is same query as below (getLastLogs) but returns the number of all logs
     * which can taken to teh list
     */
    public static function getLastLogsNumber()
    {
        $allowedCacheStatuses = self::getAllowedCacheStatusesList();

        return self::db()->simpleQueryValue(
            "SELECT COUNT(*)
            FROM cache_logs AS cl
                STRAIGHT_JOIN caches AS c ON cl.cache_id = c.cache_id
            WHERE cl.deleted = 0
                AND c.status IN ($allowedCacheStatuses)",0);
    }

    public static function getLastLogs($numberOfLogs = 100, $offset = 0)
    {

        $allowedCacheStatuses = self::getAllowedCacheStatusesList();

        $db = self::db();

        list($limit, $offset) = $db->quoteLimitOffset($numberOfLogs, $offset);

        /*
         * This query has a VERY BAD performance without STRAIGHT_JOIN on mariaDB
         * mysql: 0.05s  mariaDB-without-straight-join: ~25s.
         */
        $stmt = $db->simpleQuery(
            "SELECT c.cache_id, c.type AS cacheType, c.status, c.wp_oc,
                    c.name, c.user_id AS cacheOwner, c.latitude, c.longitude,
                    cl.id, cl.user_id AS logAuthor, cl.text,
                    cl.type, cl.date, cl.date_created, cr.user_id AS recom
            FROM cache_logs AS cl
                STRAIGHT_JOIN caches AS c
                    ON cl.cache_id = c.cache_id
                LEFT JOIN cache_rating AS cr
                    ON cr.cache_id = cl.cache_id
                    AND cr.user_id = cl.user_id
                    AND cl.type = 1
            WHERE cl.deleted = 0
                AND c.status IN ($allowedCacheStatuses)
            ORDER BY  cl.date_created DESC
            LIMIT $limit OFFSET $offset");

        return $db->dbResultFetchAll($stmt);
    }

    /**
     *
     * @param int $userId
     * @param array $cacheIds
     */
    public static function getStatusForUser($userId, array $cacheIds)
    {
        if (empty($cacheIds)) {
            return [];
        }

        $db = self::db();

        $cacheIdsStr = $db->quoteString(implode(',', $cacheIds));
        $logTypes = implode(',',
            [GeoCacheLog::LOGTYPE_FOUNDIT, GeoCacheLog::LOGTYPE_DIDNOTFIND]);

        $rs = $db->multiVariableQuery(
            "SELECT cache_id, type, date
             FROM cache_logs
             INNER JOIN (
                SELECT MAX(id) as id
                FROM cache_logs
                WHERE cache_id IN ($cacheIdsStr) AND deleted = 0
                    AND user_id = :1
                    AND type IN ($logTypes)
                GROUP BY cache_id) x
             USING (id)", $userId);

        return $db->dbResultFetchAll($rs);
    }

    /**
     * Returns GeoCacheLog[] of newest logs
     *
     * @param integer $limit
     * @param integer $offset
     * @return GeoCacheLog[]|null
     */
    public static function getNewestLogs($limit, $offset = null)
    {
        list ($limit, $offset) = self::db()->quoteLimitOffset($limit, $offset);

        $stmt = self::db()->multiVariableQuery("
            SELECT `cache_logs`.`id`
            FROM `cache_logs`
            LEFT JOIN `caches` ON `cache_logs`.`cache_id` = `caches`.`cache_id`
            WHERE `cache_logs`.`deleted` = 0
                AND `caches`.`status` IN (:1, :2, :3)
            ORDER BY `cache_logs`.`date_created` DESC
            LIMIT $offset, $limit",
            GeoCache::STATUS_ARCHIVED,
            GeoCache::STATUS_READY,
            GeoCache::STATUS_UNAVAILABLE);

        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return GeoCacheLog::fromLogIdFactory($row['id']);
        });
    }

    /**
     * Returns GeoCacheLog[] of newest logs of given $user
     *
     * @param User $user
     * @param integer $limit
     * @param integer $offset
     * @return GeoCacheLog[]|null
     */
    public static function getNewestLogsForUser(User $user, $limit, $offset = null)
    {
        list ($limit, $offset) = self::db()->quoteLimitOffset($limit, $offset);

        $stmt = self::db()->multiVariableQuery("
            SELECT `cache_logs`.`id`
            FROM `cache_logs`
            LEFT JOIN `caches` ON `cache_logs`.`cache_id` = `caches`.`cache_id`
            WHERE `cache_logs`.`deleted` = 0
                AND `cache_logs`.`user_id` = :1
                AND `caches`.`status` IN (:2, :3, :4)
                AND `cache_logs`.`type` != :5
            ORDER BY `cache_logs`.`date_created` DESC
            LIMIT $offset, $limit",
            $user->getUserId(),
            GeoCache::STATUS_ARCHIVED,
            GeoCache::STATUS_READY,
            GeoCache::STATUS_UNAVAILABLE,
            GeoCacheLog::LOGTYPE_ADMINNOTE);

        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return GeoCacheLog::fromLogIdFactory($row['id']);
        });
    }

    public static function getNewestLogsForUserCaches(User $user, $limit, $offset = null)
    {
        list ($limit, $offset) = self::db()->quoteLimitOffset($limit, $offset);

        $stmt = self::db()->multiVariableQuery("
            SELECT `cache_logs`.`id`
            FROM `cache_logs`
            LEFT JOIN `caches` ON `cache_logs`.`cache_id` = `caches`.`cache_id`
            WHERE `cache_logs`.`deleted` = 0
                AND `caches`.`user_id` = :1
                AND `caches`.`status` IN (:2, :3, :4)
                AND `cache_logs`.`type` != :5
            ORDER BY `cache_logs`.`date_created` DESC
            LIMIT $offset, $limit",
            $user->getUserId(),
            GeoCache::STATUS_ARCHIVED,
            GeoCache::STATUS_READY,
            GeoCache::STATUS_UNAVAILABLE,
            GeoCacheLog::LOGTYPE_ADMINNOTE);

        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return GeoCacheLog::fromLogIdFactory($row['id']);
        });
    }

    public static function getUserFtfs ($userId)
    {
        $db = self::db();
        return $db->dbResultFetchAll(
            $db->multiVariableQuery(
                'SELECT clftf.cache_id, caches.name, clftf.date
                FROM (
                    SELECT cache_logs.cache_id, MIN(cache_logs.date) AS date, cache_logs.user_id
                    FROM cache_logs INNER JOIN (
                        SELECT DISTINCT cache_id FROM cache_logs WHERE deleted = 0 AND type = 1 AND user_id = :1
                    ) cl_u ON cache_logs.cache_id = cl_u.cache_id
                    WHERE cache_logs.deleted = 0 AND cache_logs.type = 1
                    GROUP BY cache_logs.cache_id) AS clftf INNER JOIN caches ON clftf.cache_id = caches.cache_id
                WHERE clftf.user_id = :1
                ORDER BY clftf.date', $userId));
    }
}
