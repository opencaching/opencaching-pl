<?php
namespace lib\Objects\Stats;

use lib\Objects\BaseObject;
use lib\Objects\GeoCache\GeoCacheLog;

class UserStats extends BaseObject
{

    /**
     * Number of users which create at least one cache
     * or at least one found/not-found log
     */
    public static function getActiveUsersCount()
    {

        $countedTypes = implode(',',[
            GeoCacheLog::LOGTYPE_FOUNDIT,
            GeoCacheLog::LOGTYPE_DIDNOTFIND
        ]);

        return self::db()->simpleQueryValue(
            "SELECT COUNT(*) AS users
             FROM (
                    SELECT DISTINCT user_id
                    FROM cache_logs
                    WHERE type IN ($countedTypes) AND deleted=0
                UNION DISTINCT
                    SELECT DISTINCT user_id FROM caches
            ) AS activeUsers", 0);
    }
}