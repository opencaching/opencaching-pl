<?php

namespace lib\Objects\User;

use lib\Objects\BaseObject;
use lib\Controllers\Php7Handler;

class UserNotify extends BaseObject
{
    // Frequency of sending notifications
    const SEND_NOTIFICATION_DAILY = 0;

    const SEND_NOTIFICATION_HOURLY = 1;

    const SEND_NOTIFICATION_WEEKLY = 2;

    /**
     * Returns notify_logs status for given $userId
     *
     * @param int $userId
     * @return boolean
     */
    public static function getUserLogsNotify($userId)
    {
        return Php7Handler::Boolval(self::db()->multiVariableQueryValue('
            SELECT `notify_logs`
            FROM `user`
            WHERE `user_id` = :1
            LIMIT 1
        ', 0, $userId));
    }

    /**
     * Sets notify_caches flag in user table - depending on $state
     *
     * @param User $user
     * @param boolean $state
     * @return boolean
     */
    public static function setUserCachesNotify(User $user, $state)
    {
        return (null !== self::db()->multiVariableQuery('
            UPDATE `user`
            SET `notify_caches` = :1
            WHERE `user_id` = :2
            LIMIT 1
        ', Php7Handler::Boolval($state), $user->getUserId()));
    }

    /**
     * Sets notify_logs flag in user table - depending on $state
     *
     * @param User $user
     * @param boolean $state
     * @return boolean
     */
    public static function setUserLogsNotify(User $user, $state)
    {
        return (null !== self::db()->multiVariableQuery('
            UPDATE `user`
            SET `notify_logs` = :1
            WHERE `user_id` = :2
            LIMIT 1
        ', Php7Handler::Boolval($state), $user->getUserId()));
    }
}