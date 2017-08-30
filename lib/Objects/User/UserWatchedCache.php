<?php

namespace lib\Objects\User;

use lib\Objects\BaseObject;
use Utils\Debug\Debug;

class UserWatchedCache extends BaseObject
{

    public static function addCacheToWatched($userId, $cacheWp){
        $params = [
            'cache_code' => $cacheWp,
            'watched' => 'true' // true need to be a string!!!
        ];

        $okapiResp = self::callOkapi('services/caches/mark', $params);

        return (isset($okapiResp['success']) && $okapiResp['success'] == true );
    }


    public static function removeFromWatched($userId, $cacheWp){

        $params = [
           'cache_code' => $cacheWp,
           'watched' => 'false' // false need to be a string!!!
        ];

        $okapiResp = self::callOkapi('services/caches/mark', $params);

        return (isset($okapiResp['success']) && $okapiResp['success'] == true );

    }

    public static function getWatchedCachesCount($userId){
        return self::db()->multiVariableQueryValue(
            "SELECT COUNT(*) FROM cache_watches
            WHERE user_id = :1  ", 0, $userId);
    }

    public static function getWatchedCachesWithLastLogs(
        $userId, $limit = null, $offset = null
    ){
        $db = self::db();

        list($limit, $offset) = $db->quoteLimitOffset($limit, $offset);

        $stmt = $db->multiVariableQuery(
            "SELECT c.cache_id, c.name, c.type, c.status, c.wp_oc,
                    cl.llog_id, cl.llog_text, cl.llog_type, cl.llog_date, cl.llog_user_id,
                    u.username AS llog_username,
                    sts.user_sts
                FROM cache_watches AS cw
                INNER JOIN caches AS c
                    ON (cw.cache_id = c.cache_id)
                LEFT OUTER JOIN (
                    SELECT cache_id,
                    id AS llog_id,
                    text AS llog_text,
                    type AS llog_type,
                    user_id AS llog_user_id,
                    max(date) as llog_date
                    FROM cache_logs
                    WHERE cache_id in (
                            SELECT cache_id FROM cache_watches
                            WHERE user_id = :1
                        ) AND deleted = 0
                    GROUP BY cache_id
                ) cl ON ( cw.cache_id = cl.cache_id )
                LEFT OUTER JOIN user AS u
                    ON (u.user_id = cl.llog_user_id)
                LEFT OUTER JOIN (
                    SELECT cache_id, type as user_sts,
                    max(date) as sts_date
                    FROM cache_logs
                    WHERE cache_id in (
                        SELECT cache_id FROM cache_watches
                        WHERE user_id = :1
                    ) AND deleted = 0 AND user_id = :1 AND type IN (1,2)
                    GROUP BY cache_id
                ) sts ON sts.cache_id = cw.cache_id
                WHERE cw.user_id = :1
                LIMIT $limit OFFSET $offset", $userId );

        return $db->dbResultFetchAll($stmt);
    }


}


