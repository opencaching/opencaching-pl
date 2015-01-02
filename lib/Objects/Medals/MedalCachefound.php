<?php

namespace lib\Objects\Medals;

/**
 * Description of medalGeographical
 *
 * @author Łza
 */
class MedalCachefound extends Medal implements \lib\Objects\Medals\MedalInterface
{

    public function checkConditionsForUser(\lib\Objects\User\User $user)
    {
        $foundCount = $this->getFoundCacheCount($user);
        $placedCount = $this->getPlacedCacheCount($user);
        $levelSummary = array();
        $this->prizedTime = false;
        foreach ($this->conditions['cacheCountToAward'] as $level => $conditions) {
            if ($foundCount >= $conditions['cacheCount']['found']) {
                $foundConditionPassed[$level] = true;
            } else {
                $foundConditionPassed[$level] = false;
            }
            if ($placedCount >= $conditions['cacheCount']['placed']) {
                $placedConditionPassed[$level] = true;
            } else {
                $placedConditionPassed[$level] = false;
            }
            $levelSummary[$level] = ($placedConditionPassed[$level] && $foundConditionPassed[$level]);
            if ($levelSummary[$level] === true) {
                $this->setMedalPrizedTimeAndAcheivedLevel($level);
            }
        }
        $this->storeMedalStatus($user);
    }

    private function getFoundCacheCount(\lib\Objects\User\User $user)
    {
        $db = \lib\Database\DataBaseSingleton::Instance();
        $query = "SELECT count(id) as cacheCount FROM cache_logs, caches "
                . "WHERE cache_logs.cache_id = caches.cache_id "
                . "AND cache_logs.user_id = :1 "
                . "AND cache_logs.type = :2 "
                . "AND cache_logs.date > :3 "
                . "AND caches.type IN (" . $this->buildCacheTypesSqlString() . ")";
        $db->multiVariableQuery($query, $user->getUserId(), \lib\Objects\GeoCache\geoCacheLog::LOGTYPE_FOUNDIT, $this->dateIntroduced);
        $dbResult = $db->dbResultFetchOneRowOnly();
        return $dbResult['cacheCount'];
    }

    private function getPlacedCacheCount(\lib\Objects\User\User $user)
    {
        $query = 'SELECT count(caches.cache_id) as cacheCount FROM `caches` '
                . 'WHERE `caches`.`user_id` = :1 AND `caches`.`status` = :2 AND `caches`.`date_created` > :3 '
                . 'AND `caches`.`type` IN (' . $this->buildCacheTypesSqlString() . ') ';
        $db = \lib\Database\DataBaseSingleton::Instance();
        $db->multiVariableQuery($query, $user->getUserId(), \cache::STATUS_READY, $this->dateIntroduced);
        $dbResult = $db->dbResultFetchOneRowOnly();
        return $dbResult['cacheCount'];
    }

}
