<?php

namespace lib\Objects\User;

use lib\Objects\BaseObject;
use lib\Controllers\Php7Handler;

class UserAdmin extends BaseObject {

    /**
     * (Un)bans $user
     * Sets is_active_flag to $state for $user in the user table
     *
     * @param User $user
     * @param boolean $state
     * @return boolean
     */
    public static function setBanStatus(User $user, $state)
    {
        return (null !== self::db()->multiVariableQuery("
            UPDATE `user`
            SET `is_active_flag` = :1
            WHERE `user_id` = :2
            LIMIT 1
        ", ! Php7Handler::Boolval($state), $user->getUserId()));
    }

    /**
     * (Un)bans stats of $user
     * Sets stat_ban to $state for $user in the user table
     *
     * @param User $user
     * @param boolean $state
     * @return boolean
     */
    public static function setStatBanStatus(User $user, $state)
    {
        return (null !== self::db()->multiVariableQuery("
            UPDATE `user`
            SET `stat_ban` = :1
            WHERE `user_id` = :2
            LIMIT 1
        ", Php7Handler::Boolval($state), $user->getUserId()));
    }

    /**
     * Switches on/off verify of all new caches for $user
     * Sets verify_all to $state for $user in the user table
     *
     * @param User $user
     * @param boolean $state
     * @return boolean
     */
    public static function setVerifyAllStatus(User $user, $state)
    {
        return (null !== self::db()->multiVariableQuery("
            UPDATE `user`
            SET `verify_all` = :1
            WHERE `user_id` = :2
            LIMIT 1
        ", Php7Handler::Boolval($state), $user->getUserId()));
    }

    /**
     * Switches on/off possibility to create new caches without meeting minimum of findings
     * Adds/modifies newcaches_no_limit into user_settings table
     *
     * @param User $user
     * @param boolean $state
     * @return boolean
     */
    public static function setCreateWithoutLimitStatus(User $user, $state)
    {
        // INSERT INTO user_settings (user_id, newcaches_no_limit) VALUES (:2, :1)
        return (null !== self::db()->multiVariableQuery("
            INSERT INTO `user_settings` (`user_id`, `newcaches_no_limit`)
            VALUES (:2, :1)
            ON DUPLICATE KEY UPDATE `newcaches_no_limit` = :1
        ", Php7Handler::Boolval($state), $user->getUserId()));
    }

}