<?php

namespace lib\Medals;

/**
 * Description of medalGeographical
 *
 * @author Łza
 */
class MedalGeographical extends medal implements \lib\Medals\MedalInterface
{

    protected $conditions;

    public function checkConditionsForUser(\lib\User\User $user)
    {
        $db = \lib\Database\DataBaseSingleton::Instance();
        $query = "SELECT count(id) as cacheCount FROM cache_logs, caches, cache_location "
                . "WHERE cache_location.code3 = :1 "
                . $this->buildLocationCode4QueryString()
                . "AND cache_location.cache_id = caches.cache_id "
                . "AND cache_logs.cache_id = caches.cache_id "
                . "AND cache_logs.user_id = :2 "
                . "AND cache_logs.type = :3 "
                . "AND cache_logs.date > :4 ";
        $code4 = isset($this->conditions['cacheLocation']['code4']) ? $this->conditions['cacheLocation']['code4'] : false;
        if ($code4) {
            $db->multiVariableQuery($query, $this->conditions['cacheLocation']['code3'], $user->getUserId(), \lib\geoCache\geoCacheLog::LOGTYPE_FOUNDIT, $this->dateIntroduced, $code4);
        } else {
            $db->multiVariableQuery($query, $this->conditions['cacheLocation']['code3'], $user->getUserId(), \lib\geoCache\geoCacheLog::LOGTYPE_FOUNDIT, $this->dateIntroduced);
        }
        $cacheCountArr = $db->dbResultFetchOneRowOnly();
        if ($cacheCountArr['cacheCount'] >= $this->conditions['cacheCountToAward']) {
            $this->prizedTime = date($this->config->getDbDateTimeFormat());
        } else {
            $this->prizedTime = false;
        }
        $this->storeMedalStatus($user);
    }

    private function buildLocationCode4QueryString()
    {
        if (isset($this->conditions['cacheLocation']['code4'])) {
            return 'AND cache_location.code4 = :5 ';
        } else {
            return '';
        }
    }

}
