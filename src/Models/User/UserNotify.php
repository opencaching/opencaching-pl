<?php

namespace src\Models\User;

use src\Models\BaseObject;

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
    public static function getUserLogsNotify($userId, $countInactiveUsers=false)
    {
        return boolval(self::db()->multiVariableQueryValue('
            SELECT notify_logs FROM user
            WHERE user_id=:1
              AND ('.($countInactiveUsers?'TRUE':'user.is_active_flag = 1').')
            LIMIT 1', 0, $userId));
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
        ', boolval($state), $user->getUserId()));
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
        ', boolval($state), $user->getUserId()));
    }
}
