<?php
namespace src\Models\User;

use src\Models\BaseObject;
use src\Models\PowerTrail\PowerTrail;
use src\Models\GeoCache\GeoCacheLog;

class UserStats extends BaseObject
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns all GeoPaths completed by $userId
     *
     * @param integer $userId
     * @return \ArrayObject
     */
    public static function getGeoPathsCompleted($userId)
    {
        $geoPathsCompleted = new \ArrayObject();
        $query = '
                SELECT *
                FROM `PowerTrail`
                WHERE `id` IN
                    (SELECT `PowerTrailId`
                    FROM `PowerTrail_comments`
                    WHERE `commentType` = 2
                    AND `deleted` = 0
                    AND `userId` = :1
                    ORDER BY `logDateTime` DESC)';
        $stmt = self::db()->multiVariableQuery($query, $userId);
        $list = self::db()->dbResultFetchAll($stmt);

        foreach ($list as $row) {
            $geoPathsCompleted->append(new PowerTrail(array(
                'dbRow' => $row
            )));
        }
        return $geoPathsCompleted;
    }

    /**
     * Returns all GeoPaths started by $userId
     * @param $userId
     * @return \ArrayObject
     */
    public static function getGeoPathsStarted($userId)
    {
        $geoPathsStarted = new \ArrayObject();
        $query = '
        SELECT 
            PowerTrail.*
        FROM 
            powertrail_progress
        JOIN 
            PowerTrail ON powertrail_progress.pt_id = PowerTrail.id
        WHERE 
            powertrail_progress.user_id = :1
            AND PowerTrail.status = 1
            AND PowerTrail.id NOT IN (
                SELECT PowerTrailId 
                FROM PowerTrail_comments 
                WHERE commentType = 2 
                AND userId = :1
            )
            AND ((powertrail_progress.founds / PowerTrail.cacheCount) * 100) > 10
        ORDER BY 
            (powertrail_progress.founds / PowerTrail.cacheCount) * 100 DESC';

        $stmt = self::db()->multiVariableQuery($query, array($userId));
        $list = self::db()->dbResultFetchAll($stmt);

        foreach ($list as $row) {
            $geoPathsStarted->append(new PowerTrail(array(
                'dbRow' => $row
            )));
        }

        return $geoPathsStarted;
    }


    /**
     * Returns all GeoPaths owned by $userId
     *
     * @param integer $userId
     * @return \ArrayObject
     */
    public static function getGeoPathsOwned($userId)
    {
            $geoPathsOwned = new \ArrayObject();

            $stmt = self::db()->multiVariableQuery(
                "SELECT `PowerTrail`.*
                FROM `PowerTrail`, `PowerTrail_owners`
                WHERE `PowerTrail_owners`.`userId` = :1
                    AND `PowerTrail_owners`.`PowerTrailId` = `PowerTrail`.`id`
                ORDER BY `PowerTrail`.`id`",
                $userId);

            $list = self::db()->dbResultFetchAll($stmt);
            foreach ($list as $row) {
                $geoPathsOwned->append(new PowerTrail(array('dbRow' => $row)));
            }
        return $geoPathsOwned;
    }

    /**
     * Returns all events attended by $userId
     *
     * @param integer $userId
     * @return integer
     */
    public static function getEventsAttendsCount($userId)
    {
        return self::db()->multiVariableQueryValue(
            "SELECT COUNT(*)
            FROM `cache_logs`
            WHERE `user_id`= :1
                AND type = :2
                AND deleted=0",
            0, (int) $userId, GeoCacheLog::LOGTYPE_ATTENDED);
    }

}
