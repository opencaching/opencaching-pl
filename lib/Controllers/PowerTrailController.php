<?php

namespace lib\Controllers;

use lib\Database\DataBaseSingleton;
use lib\Objects\PowerTrail\PowerTrail;

class PowerTrailController
{
	const MINIMUM_PERCENT_REQUIRED = 66.6;

    private $config;
    private $serverUrl;

    function __construct()
    {
        include __DIR__.'/../settings.inc.php';
        $this->config = $powerTrailMinimumCacheCount;
        $this->serverUrl = $absolute_server_URI;

        foreach ($this->config['old'] as &$date) {
            $date['dateFrom'] = strtotime($date['dateFrom']);
            $date['dateTo'] = strtotime($date['dateTo']);
        }
    }

    /**
     * used to set geoPath status to inactive, when has too small amount of caches,
     * etc.
     */
    public function cleanPowerTrailsCronjob()
    {
        $getPtQuery = 'SELECT * FROM `PowerTrail` WHERE `status` =1';
        $db = DataBaseSingleton::Instance();
        $db->simpleQuery($getPtQuery);
        $ptToClean = $db->dbResultFetchAll();
        foreach ($ptToClean as $dbRow) {
            $powerTrail = new PowerTrail(array('dbRow' => $dbRow));
            $powerTrail->setPowerTrailConfiguration($this->config)->checkCacheCount();
            if(!$powerTrail->disableUncompletablePt($this->serverUrl)){
                $powerTrail->disablePowerTrailBecauseCacheCountTooLow();
            }
        }
        $this->archiveAbandonPowerTrails();
        $this->freeCacheCandidates();
    }

    private function archiveAbandonPowerTrails()
    {
        $db = DataBaseSingleton::Instance();
        $archiveAbandonQuery = 'SELECT `id` FROM `PowerTrail` WHERE `id` NOT IN (SELECT PowerTrailId FROM `PowerTrail_owners` WHERE 1 GROUP BY PowerTrailId)';
        $db->simpleQuery($archiveAbandonQuery);
        if($db->rowCount()>0) { // close all abandon geoPaths
            $ptToClose = $db->dbResultFetchAll();
            $updateArr = array();
            foreach ($ptToClose as $pt) {
                array_push($updateArr, $pt['id']);
            }
            $updateArr = implode(',', $updateArr);
            $updQuery = 'UPDATE `PowerTrail` SET `status` =3 WHERE `id` IN ( :1 )';
            $db->multiVariableQuery($updQuery, $updateArr);
        }
    }

    private function freeCacheCandidates()
    {
        $db = DataBaseSingleton::Instance();
        $query = 'DELETE FROM `PowerTrail_cacheCandidate` WHERE `date` < DATE_SUB(curdate(), INTERVAL 2 WEEK)';
        $db->simpleQuery($query);
        $db->reset();
    }
}