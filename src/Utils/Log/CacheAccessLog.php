<?php

namespace src\Utils\Log;

use src\Models\GeoCache\GeoCache;
use src\Models\OcConfig\OcConfig;
use src\Models\User\User;
use src\Utils\Database\OcDb;

/**
 * Class used for save on-access logs
 */
class CacheAccessLog
{
    public const SOURCE_BROWSER = 'B';
    public const SOURCE_MOBILE = 'M';
    public const SOURCE_OKAPI = 'O';

    public const EVENT_VIEW_CACHE = 'view_cache';
    public const EVENT_VIEW_LOGS = 'view_logs';
    public const EVENT_DOWNLOAD_GPX = 'download_gpxgc';

    public static function logBrowserCacheAccess(GeoCache $cache, ?User $user, string $event)
    {
        $userId = (is_null($user)) ? null : $user->getUserId();
        self::logCacheAccess($cache->getCacheId(), $userId, $event, self::SOURCE_BROWSER);
    }

    public static function logCacheAccess(int $cacheId, ?int $userId, string $event, string $source)
    {
        if (OcConfig::isSiteCacheAccessLogEnabled() && !self::alreadyVisited($cacheId, $userId, $event)) {
            self::saveVisit($cacheId, $userId, $event, $source);
        }
    }

    /**
     * Purge old DB entries. Used by cron
     */
    public static function purgeOldEntries()
    {
        $purgeDays = OcConfig::getCacheAccessLogPurgeDays();
        if (0 == $purgeDays) {
            return;
        }
        $db = OcDb::instance();
        $db->multiVariableQuery(
            'DELETE FROM `CACHE_ACCESS_LOGS` WHERE `event_date` < NOW() - INTERVAL :1 DAY',
            $purgeDays
        );
    }

    /**
     * Check if user already visited this cache
     */
    private static function alreadyVisited(int $cacheId, ?int $userId, string $event): bool
    {
        $accessLog = $_SESSION[self::sessionKey($userId, $event)] ?? null;
        return (!empty($accessLog) && isset($accessLog[$cacheId]) && $accessLog[$cacheId] === true);
    }

    /**
     * Save visit both to DB and session
     */
    private static function saveVisit(int $cacheId, ?int $userId, string $event, string $source)
    {
        $userId = (is_null($userId)) ? 0 : $userId;
        $db = OcDb::instance();
        $db->multiVariableQuery(
            'INSERT INTO CACHE_ACCESS_LOGS
                    ( `event_date`, `cache_id`, `user_id`, `source`, `event`, `ip_addr`, `user_agent`, `forwarded_for`)
                VALUES
                    ( NOW(), :1, :2, :3, :4, :5, :6, :7)',
            $cacheId, $userId, $source, $event, $_SERVER['REMOTE_ADDR'],
            ($_SERVER['HTTP_USER_AGENT'] ?? ''),
            ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '')
        );

        $accessLog = $_SESSION[self::sessionKey($userId, $event)] ?? null;
        if (is_null($accessLog)) {
            $_SESSION[self::sessionKey($userId, $event)] = [];
        }
        $_SESSION[self::sessionKey($userId, $event)][$cacheId] = true;
    }

    /**
     * Returns unified $SESSION key
     */
    private static function sessionKey(?int $userId, string $event): string
    {
        $userId = (is_null($userId)) ? '0' : $userId;
        return 'CACHE_ACCESS_LOG_'.$event.'_'.$userId;
    }
}
