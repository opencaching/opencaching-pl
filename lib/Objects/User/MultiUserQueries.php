<?php
namespace lib\Objects\User;

use lib\Objects\BaseObject;
use lib\Objects\GeoCache\GeoCacheLog;
use lib\Objects\GeoCache\GeoCacheLogCommons;
use lib\Objects\GeoCache\GeoCacheCommons;
use lib\Objects\GeoCache\GeoCache;

/**
 * This class should contains mostly static, READ-ONLY queries
 * used to generates statistics etc. around user db table
 */
class MultiUserQueries extends BaseObject
{

    /**
     * Number of users which create at least one cache
     * or at least one found/not-found log
     */
    public static function getActiveUsersCount()
    {

        $countedTypes = implode(',',[
            GeoCacheLog::LOGTYPE_FOUNDIT,
            GeoCacheLog::LOGTYPE_DIDNOTFIND
        ]);

        return self::db()->simpleQueryValue(
            "SELECT COUNT(*) AS users
             FROM (
                    SELECT DISTINCT user_id
                    FROM cache_logs
                    WHERE type IN ($countedTypes) AND deleted=0
                UNION DISTINCT
                    SELECT DISTINCT user_id FROM caches
            ) AS activeUsers", 0);
    }

    public static function getUsersRegistratedCount($fromLastdays)
    {
        $days = (int) $fromLastdays;

        return self::db()->simpleQueryValue(
            "SELECT COUNT(*) FROM user
             WHERE date_created > DATE_SUB(NOW(), INTERVAL $days day) ", 0);
    }

    /**
     * Returns array, where row[userId] = username
     *
     * @param array $userIds - array of userIds
     * @return array|mixed[]
     */
    public static function GetUserNamesForListOfIds(array $userIds)
    {

        if(empty($userIds)){
            return array();
        }

        $db = self::db();

        $userIdsStr = $db->quoteString(implode($userIds, ','));

        $s = $db->simpleQuery(
            "SELECT user_id, username FROM user
            WHERE user_id IN ( $userIdsStr )");

        return $db->dbFetchAsKeyValArray($s, 'user_id', 'username' );
    }

    /**
     * Returns array of user which are guides now
     */
    public static function getCurrentGuidesList()
    {
        $db = self::db();

        $cacheActiveStatusList = implode(',',
            [GeoCacheCommons::STATUS_READY,
             GeoCache::STATUS_UNAVAILABLE,
             GeoCache::STATUS_ARCHIVED]);

        $config = self::OcConfig()->getGuidesConfig();

        $guideActivePeriod = (int) $config['guideActivePeriod'];
        $guideGotRecommendations = (int) $config['guideGotRecommendations'];

        // get active guides
        $s = $db->simpleQuery(
            "SELECT latitude,longitude,username,user_id,description
             FROM user
             WHERE guru <> 0
                 AND is_active_flag <> 0
                 AND longitude IS NOT NULL
                 AND latitude IS NOT NULL
                 AND (
                     user_id IN (
                         SELECT DISTINCT user_id FROM cache_logs
                         WHERE type = ".GeoCacheLogCommons::LOGTYPE_FOUNDIT."
                             AND date_created > DATE_ADD(NOW(), INTERVAL -$guideActivePeriod DAY)
                     )
                     OR
                     user_id IN (
                         SELECT DISTINCT user_id FROM caches
                         WHERE status IN ($cacheActiveStatusList)
                             AND date_created > DATE_ADD(NOW(), INTERVAL -$guideActivePeriod DAY)
                     )
                 )"
        );

        $activeGuidesDict = $db->dbResultFetchAllAsDict($s, function($row){
            return [$row['user_id'], $row];
        });

        if(empty($activeGuidesDict) ){
            // there is no guides...
            return [];
        }

        // filter users with too low number of recomendations
        $userIds = implode(',', array_keys($activeGuidesDict));

        $s = $db->simpleQuery(
            "SELECT user_id, SUM(topratings) AS recos
            FROM caches
            WHERE user_id IN ($userIds)
                AND type <> ".GeoCache::TYPE_EVENT."
            AND status IN ($cacheActiveStatusList)
            GROUP BY user_id
            HAVING recos >= $guideGotRecommendations");

        $result = [];
        while($row = $db->dbResultFetch($s)){
            $userData = $activeGuidesDict[$row['user_id']];
            $userData['recomendations'] = $row['recos'];
            $result[] = $userData;
        }
        return $result;

    }


    public static function getOcTeamMembersArray()
    {
        $query = "
            SELECT user_id, username
            FROM user
            WHERE role & ".User::ROLE_OC_TEAM.">0 AND is_active_flag = 1
            ORDER BY username";
        $stmt = self::db()->simpleQuery($query);
        return self::db()->dbResultFetchAll($stmt);
    }

}
