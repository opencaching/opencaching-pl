<?php

namespace lib\Objects\Medals;

/**
 * medal to be awarded when use complete specified geopath
 *
 * @author Łza
 */
class MedalMaxAltitude extends Medal implements \lib\Objects\Medals\MedalInterface
{

    protected $conditions;

    public function checkConditionsForUser(\lib\Objects\User\User $user)
    {


		if (!in_array($this->config->getOcNodeId(), $this->conditions['ocNodeId'])) { /* this medal is not available in current node */
            return;
        }

        /* @var $db \dataBase */
        $db = \lib\Database\DataBaseSingleton::Instance();
		$queryFound = 'SELECT MAX(`altitude`) FROM `caches`, `caches_additions`, cache_logs
			WHERE caches.`cache_id` = caches_additions.`cache_id` AND cache_logs.cache_id = caches.`cache_id`
			AND cache_logs.type = 1 AND cache_logs.user_id = :1 AND caches.type IN(:2)';
		$cacheTypes = $this->buildCacheTypesSqlString();
        $db->multiVariableQuery($queryFound, $user->getUserId(), $cacheTypes);
        $FoundMaxAltitude = $db->dbResultFetchOneRowOnly();

		$queryPlaced = 'SELECT MAX(`altitude`) FROM `caches`, `caches_additions`
			WHERE caches.`cache_id` = caches_additions.`cache_id`
			AND cache.user_id = :1 AND caches.type IN(:2) AND cache.status = :3';
		$db->multiVariableQuery($queryPlaced, $user->getUserId(), $cacheTypes, \cache::STATUS_READY);
        $PlacedMaxAltitude = $db->dbResultFetchOneRowOnly();
		
		$this->findLevel($FoundMaxAltitude, $PlacedMaxAltitude);
        $this->storeMedalStatus($user);
    }

	private function findLevel($FoundMaxAltitude, $PlacedMaxAltitude){
		$this->prizedTime = false;
		foreach($this->conditions['altitudeToAward'] as $level) {
			if ($FoundMaxAltitude >= $level['altitude']['found'] && $PlacedMaxAltitude >= $level['altitude']['placed']) {
				$this->level = $level;
				$this->prizedTime = date($this->config->getDbDateTimeFormat());
			}
		}
	}

}
