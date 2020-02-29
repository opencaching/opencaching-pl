<?php
namespace src\Utils\EventHandler;

use src\Models\Notify\Notify;
use src\Models\User\User;
use src\Models\GeoCache\GeoCache;

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

    // Old
    public static function event_change_log_type($userId)
    {
        User::deleteStatpic($userId);
    }
}
