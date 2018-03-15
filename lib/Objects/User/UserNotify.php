<?php

namespace lib\Objects\User;

use lib\Objects\BaseObject;
use lib\Controllers\Php7Handler;

class UserNotify extends BaseObject
{
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