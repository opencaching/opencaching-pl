<?php
namespace lib\Objects\GeoCache;

use lib\Controllers\MeritBadgeController;
use lib\Objects\BaseObject;
use okapi\Facade;

class CacheRecommendation extends BaseObject
{
    /**
     *
     * Returns recommended caches for given user
     *
     * @param $userId
     * @return \Utils\Database\all
     */
    public static function getCachesRecommendedByUser($userId, $limit, $offset)
    {
        $db = self::db();
        list($limit, $offset) = $db->quoteLimitOffset($limit, $offset);
        $stmt = self::db()->multiVariableQuery(
            "SELECT c.cache_id, c.name, c.type, c.user_id, c.status, c.wp_oc, u.username, u.user_id
            FROM cache_rating AS cr, caches AS c, user AS u
            WHERE cr.cache_id = c.cache_id
            AND c.user_id = u.user_id
            AND cr.user_id = :1 ORDER BY c.name ASC
            LIMIT $offset, $limit", $userId);

        return self::db()->dbResultFetchAllAsDict($stmt);
    }

    /**
     * Delete selected user recommendation
     *
     * @param int $userId
     * @param int $cacheId
     */
    public static function deleteRecommendation($userId, $cacheId)
    {
        $stmt = self::db()->multiVariableQuery(
            "DELETE FROM cache_rating
             WHERE cache_id = :1 AND user_id = :2 LIMIT 1", $cacheId, $userId);

        if($stmt->rowCount() > 0) {
            if (self::OcConfig()->isMeritBadgesEnabled()) {
                $ctrlMeritBadge = new MeritBadgeController;
                $ctrlMeritBadge->updateTriggerCacheAuthor($cacheId);
            }

            // Notify OKAPI's replicate module of the change.
            // Details: https://github.com/opencaching/okapi/issues/265
            Facade::schedule_user_entries_check($cacheId, $userId);
            Facade::disable_error_handling();
        }
    }

    /**
     * Returns the count of recommendations given by the user
     * @param $userId
     * @return int count
     */
    public static function getCountOfUserRecommendations($userId) {
        return self::db()->multiVariableQueryValue(
            "SELECT COUNT(cache_id) FROM cache_rating 
                    WHERE user_id = :1", 0, $userId);
    }

}
