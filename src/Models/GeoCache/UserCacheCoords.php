<?php
namespace src\Models\GeoCache;

use src\Models\BaseObject;
use src\Models\Coordinates\Coordinates;
use src\Models\User\User;

class UserCacheCoords extends BaseObject
{

    public static function getCoords($userId, $cacheId)
    {

        $rs = self::db()->multiVariableQuery(
            "SELECT longitude AS lon, latitude AS lat FROM cache_mod_cords
            WHERE cache_id = :1 AND user_id = :2 LIMIT 1",
            $cacheId, $userId);

        if($row = self::db()->dbResultFetchOneRowOnly($rs)){
            return Coordinates::FromCoordsFactory($row['lat'], $row['lon']);
        }else{
            return null;
        }
    }

    public static function storeCoords($userId, $cacheId, Coordinates $coords)
    {
        //TODO: Table cache_mod_cords should have index on cache_id/user_id instead of autoincrement index!
        //      Then it could be possible to use INSERT ... ON DUPLICATE KEY UPDATE Syntax
        //      DELETE old coords to be sure there is no duplicates...
        self::deleteCoords($cacheId, $userId);

        self::db()->multiVariableQuery(
            "INSERT INTO cache_mod_cords
                (cache_id, user_id, longitude, latitude, date)
            VALUES(:1, :2, :3, :4, NOW() );",
            $cacheId, $userId, $coords->getLongitude(), $coords->getLatitude());
    }

    public static function deleteCoords($cacheId, $userId)
    {
        self::db()->multiVariableQuery(
            "DELETE FROM cache_mod_cords
            WHERE cache_id = :1 AND user_id = :2 LIMIT 1",
            $cacheId, $userId);
    }

    public static function getCoordsByCacheIds(array $cacheIds, $userId)
    {
        if(empty($cacheIds)){
            return [];
        }
        $db = self::db();

        $cacheIdsStr = implode(',', $cacheIds);

        $rs = $db->multiVariableQuery(
            "SELECT cache_id, latitude AS lat, longitude AS lot, date
            FROM cache_mod_cords
            WHERE user_id = :1 AND cache_id IN ($cacheIdsStr)", $userId);

        return $db->dbResultFetchAll($rs);
    }

    /**
     * Remove all saved ccordinates associated with given user account
     * @param User $user
     */
    public static function removeAllCoordsForUser(User $user): void
    {
        self::db()->multiVariableQuery(
            "DELETE FROM cache_mod_cords WHERE user_id = :1", $user->getUserId());
    }
}
