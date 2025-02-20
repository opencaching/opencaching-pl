<?php
namespace src\Models\User;

use src\Models\BaseObject;
use src\Models\PowerTrail\PowerTrail;
use src\Models\GeoCache\GeoCacheLog;
use src\Utils\Cache\OcMemCache;

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
        OcMemCache::getOrCreate('UserStats:getGeoPathsStarted'.$userId, 31 * 24 * 60 * 60, function() use ($userId) {
            UserStats::updateProgressWithFoundCaches($userId);
            return true;
        });

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

    private static function getGeoPathsFoundCaches($userId)
    {
        $geoPathsFoundCaches = new \ArrayObject();

        $query = '
        SELECT 
            pt.id AS powerTrailId,
            COUNT(DISTINCT ptc.cacheId) AS foundCaches
        FROM 
            PowerTrail pt
        JOIN 
            powerTrail_caches ptc ON pt.id = ptc.PowerTrailId
        JOIN 
            cache_logs cl ON ptc.cacheId = cl.cache_id
        WHERE 
            pt.status = 1
            AND pt.id NOT IN (
                SELECT PowerTrailId
                FROM PowerTrail_comments
                WHERE commentType = 2
                  AND userId = :1
            )
            AND cl.user_id = :1
            AND cl.type = 1
        GROUP BY 
            pt.id
        ORDER BY 
            foundCaches DESC';

        $stmt = self::db()->multiVariableQuery($query, array($userId));
        $geoPathsFoundCaches = self::db()->dbResultFetchAll($stmt);

        return $geoPathsFoundCaches;
    }

    public static function updateProgressWithFoundCaches($userId)
    {
        $geoPathsFoundCaches = self::getGeoPathsFoundCaches($userId);

        foreach ($geoPathsFoundCaches as $geoPath) {
            $powerTrailId = $geoPath['powerTrailId'];
            $foundCaches = $geoPath['foundCaches'];

            $queryCheck = '
            SELECT 1
            FROM powertrail_progress
            WHERE user_id = :1 AND pt_id = :2';

            $stmtCheck = self::db()->multiVariableQuery($queryCheck, array($userId, $powerTrailId));
            $exists = self::db()->dbResultFetchOneRowOnly($stmtCheck);

            if ($exists) {
                $queryUpdate = '
                UPDATE powertrail_progress
                SET founds = :3
                WHERE user_id = :1 AND pt_id = :2';
                self::db()->multiVariableQuery($queryUpdate, array($userId, $powerTrailId, $foundCaches));
            } else {
                $queryInsert = '
                INSERT INTO powertrail_progress (user_id, pt_id, founds)
                VALUES (:1, :2, :3)';
                self::db()->multiVariableQuery($queryInsert, array($userId, $powerTrailId, $foundCaches));
            }
        }
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
