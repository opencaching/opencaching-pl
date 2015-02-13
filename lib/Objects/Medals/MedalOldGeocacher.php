<?php

namespace lib\Objects\Medals;

use \lib\Objects\User\User;

/**
 * Medal type HighlandCaches,
 *
 * @author Andrzej 'Łza' Woźniak
 */
class MedalOldGeocacher extends Medal implements MedalInterface
{
    public function checkConditionsForUser(User $user)
    {
        $months = $this->getGeocacherDays($user);
        foreach ($this->conditions['cacheCountToAward'] as $level => $condition) {
            if($months >= $condition['months']){
                $this->setMedalPrizedTimeAndAcheivedLevel($level);
            }
        }
        $this->storeMedalStatus($user);
    }
    public function getLevelInfo($level = null)
    {}
    private function getGeocacherDays(User $user)
    {
        $db = \lib\Database\DataBaseSingleton::Instance();
        $query = 'SELECT period_diff(date_format(now(), "%Y%m"), date_format( `date_created`, "%Y%m")) as months FROM `user` WHERE user_id = :1 LIMIT 1';
        $db->multiVariableQuery($query, $user->getUserId());
        $dbResult = $db->dbResultFetchOneRowOnly();
        return $dbResult['months'];
    }
}
