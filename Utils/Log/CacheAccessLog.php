<?php

namespace Utils\Log;

use Utils\Database\XDb;

/**
 * Class used for save on-access logs
 *
 */

class CacheAccessLog
{

    const SOURCE_BROWSER = "B";
    const SOURCE_MOBILE = "M";
    const SOURCE_OKAPI = "O";

    public static function logCacheAccess($cacheId, $userId, $event, $source){
        $accessLog = @$_SESSION['CACHE_ACCESS_LOG_VC_' . $userId];
        if ($accessLog === null) {
            $_SESSION['CACHE_ACCESS_LOG_VC_' . $userId] = array();
            $accessLog = $_SESSION['CACHE_ACCESS_LOG_VC_' . $userId];
        }
        if (@$accessLog[$cacheId] !== true) {
            $db = XDb::instance();
            $db->multiVariableQuery(
                'INSERT INTO CACHE_ACCESS_LOGS
                    ( event_date, cache_id, user_id, source, event, ip_addr, user_agent, forwarded_for)
                VALUES
                    ( NOW(), :1, :2, :3, :4, :5, :6, :7)',
                $cacheId, $userId, $source, $event, $_SERVER['REMOTE_ADDR'],
                ( isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '' ),
                ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '' )
                );
            $accessLog[$cacheId] = true;
            $_SESSION['CACHE_ACCESS_LOG_VC_' . $userId] = $accessLog;
        }
    }

    public static function logBrowserCacheAccess($cacheId, $userId, $event){
        return self::logCacheAccess($cacheId, $userId, $event, self::SOURCE_BROWSER);
    }

}