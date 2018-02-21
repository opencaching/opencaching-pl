<?php

namespace lib\Objects\GeoCache;

use lib\Objects\BaseObject;

/**
 * This class should contains mostly static, READ-ONLY queries
 * used to generates statistics etc. around cache_logs db table
 */


class MultiLogStats extends BaseObject
{

    public static function getTotalSearchesNumber()
    {
        $countedTypes = implode(',',[
            GeoCacheLog::LOGTYPE_FOUNDIT,
            GeoCacheLog::LOGTYPE_DIDNOTFIND
        ]);

        return self::db()->simpleQueryValue(
            "SELECT COUNT(*) FROM cache_logs
            WHERE type IN ($countedTypes)
                AND deleted = 0", 0);
    }

    public static function getLastSearchesCount($fromLastDays)
    {
        $days = (int) $fromLastDays;

        $countedTypes = implode(',',[
            GeoCacheLog::LOGTYPE_FOUNDIT,
            GeoCacheLog::LOGTYPE_DIDNOTFIND
        ]);

        return self::db()->simpleQueryValue(
            "SELECT COUNT(*) FROM cache_logs
            WHERE type IN ($countedTypes)
                AND deleted = 0
                AND date_created > DATE_SUB(NOW(), INTERVAL $days day)", 0);
    }

    public static function getLastRecomendationsCount($fromLastDays)
    {
        $days = (int) $fromLastDays;

        return self::db()->multiVariableQueryValue(
            "SELECT COUNT(*) FROM cache_logs
            INNER JOIN cache_rating USING (cache_id, user_id)
            WHERE type = :1
                AND deleted = 0
                AND date_created > DATE_SUB(NOW(), INTERVAL $days day)",
            0, GeoCacheLog::LOGTYPE_FOUNDIT);
    }

    /**
     * Returns array with last logs data for each given cacheId
     * @param array $cacheIds
     * @param array $logFields - optional list of log fields
     */
    public static function getLastLogForEachCache(array $cacheIds, array $logFields=null)
    {
        if(empty($cacheIds)){
            return [];
        }

        if( empty($logFields) ){
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

    /**
     *
     * @param int $userId
     * @param array $cacheIds
     */
    public static function getStatusForUser($userId, array $cacheIds)
    {
        if(empty($cacheIds)){
            return [];
        }

        $db = self::db();

        $cacheIdsStr = $db->quoteString(implode(',', $cacheIds));
        $logTypes = implode(',',
            [GeoCacheLog::LOGTYPE_FOUNDIT, GeoCacheLog::LOGTYPE_DIDNOTFIND]);

        $rs = $db->multiVariableQuery(
            "SELECT cache_id, type, date
             FROM cache_logs
             INNE JOIN (
                SELECT MAX(id) as id
                FROM cache_logs
                WHERE cache_id IN ($cacheIdsStr) AND deleted = 0
                    AND user_id = :1
                    AND type IN ($logTypes)
                GROUP BY cache_id) x
             USING (id)", $userId);

        return $db->dbResultFetchAll($rs);
    }
}
