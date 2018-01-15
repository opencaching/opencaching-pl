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
}
