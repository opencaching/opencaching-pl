<?php
namespace src\Models\GeoCache;

use src\Models\BaseObject;

class GeoCacheScore extends BaseObject
{

    public static function getVotesScoreForCache($cacheId)
    {

        $db = self::db();

        $stmt = $db->multiVariableQuery(
            "SELECT COUNT(*) AS votes, AVG(score) AS avgScore FROM scores WHERE cache_id= :1", $cacheId);

        $row = $db->dbResultFetchOneRowOnly($stmt);

        if ($row['votes'] == 0) {
            return [0,0];
        } else {
            return [$row['votes'], $row['avgScore']];
        }

    }

    public static function updateScoreOnLogRemove(GeoCacheLog $log)
    {
        self::db()->multiVariableQuery("DELETE FROM scores WHERE user_id=:1 AND cache_id=:2 LIMIT 1",
            self::getCurrentUser()->getUserId(), $log->getGeoCache()->getCacheId());

        $log->getGeoCache()->recalculateCacheScore();
    }


}
