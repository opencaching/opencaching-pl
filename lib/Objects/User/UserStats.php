<?php
namespace lib\Objects\User;

use lib\Objects\BaseObject;
use lib\Objects\PowerTrail\PowerTrail;
use lib\Objects\GeoCache\GeoCacheLog;

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
