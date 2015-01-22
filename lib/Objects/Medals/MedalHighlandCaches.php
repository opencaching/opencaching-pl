<?php

namespace lib\Objects\Medals;

use \lib\Objects\User\User;

/**
 * Description of HighlandCaches
 *
 * @author Łza
 */
class MedalHighlandCaches extends Medal implements MedalInterface
{
    public function checkConditionsForUser(User $user)
    {
        if (!in_array($this->config->getOcNodeId(), $this->conditions['ocNodeId'])) { /* this medal is not available in current node */
            return;
        }
        $foundCount = $this->getFoundCount($user);
        $placedCount = $this->getPlacedCount($user);
        $this->findMedalLevelByCacheCount($foundCount, $placedCount);
        $this->storeMedalStatus($user);
    }

    private function getFoundCount(User $user)
    {
        $db = \lib\Database\DataBaseSingleton::Instance();
        $foundCountQuery = 'SELECT count(*) as cacheCount FROM cache_logs, `caches_additions`, caches WHERE caches_additions.`altitude` > :1 AND cache_logs.cache_id = caches_additions.cache_id AND caches.cache_id = caches_additions.cache_id AND cache_logs.user_id = :2 AND caches.type IN (:3) AND cache_logs.type = :4 AND cache_logs.date > :5';
        $db->multiVariableQuery($foundCountQuery, $this->conditions['minimumAltitude'], $user->getUserId(), $this->buildCacheTypesSqlString(), \lib\Objects\GeoCache\GeoCacheLog::LOGTYPE_FOUNDIT, $this->dateIntroduced);
        $dbResult = $db->dbResultFetchOneRowOnly();
        return $dbResult['cacheCount'];
    }

    private function getPlacedCount(User $user)
    {
        $db = \lib\Database\DataBaseSingleton::Instance();
        $placedCountQuery = 'SELECT count(*) as cacheCount FROM `caches_additions`, caches WHERE caches_additions.`altitude` > :1 AND caches.cache_id = caches_additions.cache_id AND caches.user_id = :2 AND caches.type IN (:3) AND status = :4 AND `caches`.`date_created` > :5';
        $db->multiVariableQuery($placedCountQuery, $this->conditions['minimumAltitude'], $user->getUserId(), $this->buildCacheTypesSqlString(), \lib\Objects\GeoCache\GeoCache::STATUS_READY, $this->dateIntroduced);
        $dbResult = $db->dbResultFetchOneRowOnly();
        return $dbResult['cacheCount'];
    }
}
