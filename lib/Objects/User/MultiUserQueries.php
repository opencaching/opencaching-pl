<?php
namespace lib\Objects\User;

use lib\Objects\BaseObject;
use lib\Objects\GeoCache\GeoCacheLog;

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

}