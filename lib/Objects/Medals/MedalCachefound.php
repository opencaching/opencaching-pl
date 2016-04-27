<?php

namespace lib\Objects\Medals;


use \lib\Objects\User\User;
use Utils\Database\OcDb;
/**
 * Description of medalGeographical
 * @author Łza
 */
class MedalCachefound extends Medal implements MedalInterface
{

    public function checkConditionsForUser(User $user)
    {
        $foundCount = $this->getFoundCacheCount($user);
        $placedCount = $this->getPlacedCacheCount($user);
        $this->prizedTime = false;
        $this->findMedalLevelByCacheCount($foundCount, $placedCount);
        $this->storeMedalStatus($user);
    }

    public function getMedalProfile()
    {
        $result = array(
            'medalDescription' => $this->description,
            'cacheType' => $this->conditions['cacheType'],
        );
        return $result;
    }


    public function getLevelInfo($level = null)
    {
        if($level === null){
            $level = $this->level;
        }

        $result = array (
            _('Found caches') => $this->conditions['cacheCountToAward'][$level]['cacheCount']['found'],
            _('Placed caches') => $this->conditions['cacheCountToAward'][$level]['cacheCount']['placed'],
        );


        return $result;
    }

    private function getFoundCacheCount(User $user)
    {
        $db = OcDb::instance();
        $query = "SELECT count(id) as cacheCount FROM cache_logs, caches "
                . "WHERE cache_logs.cache_id = caches.cache_id "
                . "AND cache_logs.user_id = :1 "
                . "AND cache_logs.type = :2 "
                . "AND cache_logs.date > :3 "
                . "AND caches.type IN (" . $this->buildCacheTypesSqlString() . ")";
        $s = $db->multiVariableQuery($query, $user->getUserId(), $this->conditions['logType'], $this->dateIntroduced);
        $dbResult = $db->dbResultFetchOneRowOnly($s);
        return $dbResult['cacheCount'];
    }

    private function getPlacedCacheCount(User $user)
    {
        $query = 'SELECT count(caches.cache_id) as cacheCount FROM `caches` '
                . 'WHERE `caches`.`user_id` = :1 AND `caches`.`status` IN ( :2 ) AND `caches`.`date_created` > :3 '
                . 'AND `caches`.`type` IN ( :4 ) ';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($query, $user->getUserId(), $this->buildCacheStatusSqlString(), $this->dateIntroduced, $this->buildCacheTypesSqlString() );
        $dbResult = $db->dbResultFetchOneRowOnly($s);
        return $dbResult['cacheCount'];
    }

}
