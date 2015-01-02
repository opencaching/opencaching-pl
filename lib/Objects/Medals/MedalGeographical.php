<?php

namespace lib\Objects\Medals;

/**
 * Description of medalGeographical
 *
 * @author Łza
 */
class MedalGeographical extends Medal implements \lib\Objects\Medals\MedalInterface
{

    protected $conditions;

    public function checkConditionsForUser(\lib\Objects\User\User $user)
    {
        $foundCount = $this->getFoundCacheCount($user);
        $placedCount = $this->getPlacedCacheCount($user);
        $levelSummary = array();
        $this->prizedTime = false;
        foreach ($this->conditions['cacheCountToAward'] as $level => $levelConditions) {
            if ($foundCount >= $levelConditions['cacheCount']['found']) {
                $foundConditionPassed[$level] = true;
            } else {
                $foundConditionPassed[$level] = false;
            }
            if ($placedCount >= $levelConditions['cacheCount']['placed']) {
                $placedConditionPassed[$level] = true;
            } else {
                $placedConditionPassed[$level] = false;
            }
            $levelSummary[$level] = ($placedConditionPassed[$level] && $foundConditionPassed[$level]);
            if($levelSummary[$level] === true){
                $this->setMedalPrizedTimeAndAcheivedLevel($level);
            }
        }
        $this->storeMedalStatus($user);
    }

    private function getFoundCacheCount(\lib\Objects\User\User $user)
    {
        $db = \lib\Database\DataBaseSingleton::Instance();
        $query = "SELECT count(id) as cacheCount FROM cache_logs, caches, cache_location "
                . "WHERE cache_location.code3 = :1 "
                . $this->buildLocationCode4QueryString(5)
                . "AND cache_location.cache_id = caches.cache_id "
                . "AND cache_logs.cache_id = caches.cache_id "
                . "AND cache_logs.user_id = :2 "
                . "AND cache_logs.type = :3 "
                . "AND cache_logs.date > :4 "
                . "AND caches.type IN (" . $this->buildCacheTypesSqlString() . ")";
        $code4 = isset($this->conditions['cacheLocation']['code4']) ? $this->conditions['cacheLocation']['code4'] : false;
        if ($code4) {
            $db->multiVariableQuery($query, $this->conditions['cacheLocation']['code3'], $user->getUserId(), \lib\Objects\GeoCache\geoCacheLog::LOGTYPE_FOUNDIT, $this->dateIntroduced, $code4);
        } else {
            $db->multiVariableQuery($query, $this->conditions['cacheLocation']['code3'], $user->getUserId(), \lib\Objects\GeoCache\geoCacheLog::LOGTYPE_FOUNDIT, $this->dateIntroduced);
        }
        $dbResult = $db->dbResultFetchOneRowOnly();
        return $dbResult['cacheCount'];
    }

    private function getPlacedCacheCount(\lib\Objects\User\User $user)
    {
        $query = 'SELECT count(caches.cache_id) as cacheCount FROM `caches`, `cache_location` '
                . 'WHERE `caches`.`user_id` = :1 ' . $this->buildLocationCode4QueryString(5) . ' '
                . 'AND `caches`.`status` = :2 AND `caches`.`date_created` > :3 AND cache_location.code3 = :4 '
                . 'AND `caches`.`type` IN (' . $this->buildCacheTypesSqlString() . ') '
                . 'AND cache_location.cache_id = caches.cache_id ';
        $db = \lib\Database\DataBaseSingleton::Instance();
        $code4 = isset($this->conditions['cacheLocation']['code4']) ? $this->conditions['cacheLocation']['code4'] : false;
        if ($code4) {
            $db->multiVariableQuery($query, $user->getUserId(), \cache::STATUS_READY, $this->dateIntroduced, $this->conditions['cacheLocation']['code3'], $code4);
        } else {
            $db->multiVariableQuery($query, $user->getUserId(), \cache::STATUS_READY, $this->dateIntroduced, $this->conditions['cacheLocation']['code3']);
        }
        $dbResult = $db->dbResultFetchOneRowOnly();
        return $dbResult['cacheCount'];
    }

    private function buildLocationCode4QueryString($bindIdentifier)
    {
        if (isset($this->conditions['cacheLocation']['code4'])) {
            return 'AND cache_location.code4 = :' . $bindIdentifier . ' ';
        } else {
            return '';
        }
    }

}
