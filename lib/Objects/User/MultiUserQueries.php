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
     * Returns arraywhere row[userId] = username
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

        $s = $db->simpleQuery(
            "SELECT latitude,longitude,username,user_id,description
             FROM user
             WHERE guru != 0
                 AND (
                     user_id IN (
                         SELECT user_id FROM cache_logs
                         WHERE type = ".GeoCacheLogCommons::LOGTYPE_FOUNDIT."
                             AND date_created > DATE_ADD(NOW(), INTERVAL -90 DAY)
                     )
                     OR
                     user_id IN (
                         SELECT user_id FROM caches
                         WHERE status IN ($cacheActiveStatusList)
                             AND date_created > DATE_ADD(NOW(), INTERVAL -90 DAY)
                     )
                 )
                 AND is_active_flag != 0
                 AND longitude IS NOT NULL
                 AND latitude IS NOT NULL");

        return $db->dbResultFetchAll($s);
    }

}