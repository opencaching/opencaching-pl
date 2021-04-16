<?php

namespace src\Models\User;

use src\Models\BaseObject;
use src\Models\GeoCache\UserIgnoredCache;
use src\Models\Search\UserSavedQueries;
use src\Models\User\UserPreferences\UserPreferences;
use src\Models\GeoCache\UserCacheCoords;
use src\Models\GeoCache\CacheNote;
use src\Models\Neighbourhood\Neighbourhood;

class UserAdmin extends BaseObject {

    /**
     * (Un)bans $user
     * Sets is_active_flag to $state for $user in the user table
     *
     * @param User $user
     * @param boolean $state
     */
    public static function setBanStatus(User $user, bool $state): void
    {
        if ($state) {
            // ban this user
            $isActive = User::STATUS_BANNED;
        } else {
            // un-ban this user
            $isActive = User::STATUS_ACTIVE;
        }

        self::db()->multiVariableQuery("
            UPDATE `user`
            SET `is_active_flag` = :1, `activation_code` = ''
            WHERE `user_id` = :2 LIMIT 1",
            $isActive, $user->getUserId());
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
        ", boolval($state), $user->getUserId()));
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
        ", boolval($state), $user->getUserId()));
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
        ", boolval($state), $user->getUserId()));
    }

    public static function removeUserSpecificSettings(User $user) : void
    {
        // first check if user is removed
        if (!$user->isAlreadyRemoved()) {
            // do not remove data from non-removed accounts
            return;
        }

        // acount is removed - the rest of user-specific data can also be removed now

        // cleanup up watches caches list
        UserWatchedCache::removeAllWatchesForUser($user);

        // clean up ignores list
        UserIgnoredCache::removeAllIgnoresForUser($user);

        // clean up userPreferences user_preferences
        UserPreferences::removeAllUserPreferences($user);

        // badge_user ??

        // cleanup user modified coords
        UserCacheCoords::removeAllCoordsForUser($user);

        // clean up cache_notes
        CacheNote::removeAllForUser($user);

        // cleanup saved earch queries
        UserSavedQueries::removeAllQueriesForUser($user);

        // routes ??

        // cleanup user_neighbourhoods
        Neighbourhood::removeAllUserNeighbourhood($user);

        // user_nick_history
        UserNickHistory::removeAllHistoryForUser($user);

        // user_settings ??
    }
}
