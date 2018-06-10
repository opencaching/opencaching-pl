<?php
namespace Utils\EventHandler;

use lib\Objects\Notify\Notify;
use lib\Objects\User\User;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\GeoCache\GeoCacheLog;

class EventHandler
{
    public static function cacheNew(GeoCache $cache)
    {
        User::deleteStatpic($cache->getOwnerId());
        Notify::generateNotifiesForCache($cache);
    }

    public static function cacheEdit(GeoCache $cache)
    {
        User::deleteStatpic($cache->getOwnerId());
    }

    public static function logNewByUserId($userId)
    {
        User::deleteStatpic($userId);
    }

    public static function logRemove(GeoCacheLog $log)
    {
        User::deleteStatpic($log->getUser()->getUserId());
    }

    // Old
    public static function event_change_log_type($userId)
    {
        User::deleteStatpic($userId);
    }
    public static function event_change_statpic($userId)
    {
        User::deleteStatpic($userId);
    }
}