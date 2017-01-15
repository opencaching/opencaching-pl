<?php

namespace lib\Controllers;

use lib\Objects\PowerTrail\PowerTrail;
use lib\Objects\PowerTrail\Log;
use lib\Objects\User\User;
use Utils\Database\OcDb;

class PowerTrailController
{

    const MINIMUM_PERCENT_REQUIRED = 67;

    private $config;
    private $serverUrl;
    private $user;

    public function __construct()
    {
        include __DIR__ . '/../settings.inc.php';
        $this->config = $powerTrailMinimumCacheCount;
        $this->serverUrl = $absolute_server_URI;

        foreach ($this->config['old'] as &$date) {
            $date['dateFrom'] = strtotime($date['dateFrom']);
            $date['dateTo'] = strtotime($date['dateTo']);
        }
    }

    public static function getEntryTypes(){
        return array (
            Log::TYPE_COMMENT => array ( //comment
                'translate' => 'pt056',
                'color' => '#000000',
            ),
            Log::TYPE_CONQUESTED => array ( // conquested
                'translate' => 'pt057',
                'color' => '#00CC00',
            ),
            Log::TYPE_OPENING => array ( // geoPath Publishing
                'translate' => 'pt214',
                'color' => '#0000CC',
            ),
            Log::TYPE_DISABLING => array ( // geoPath temp. closed
                'translate' => 'pt216',
                'color' => '#CC0000',
            ),
            Log::TYPE_CLOSING => array ( // geoPath Closure (permanent)
                'translate' => 'pt213',
                'color' => '#CC0000',
            ),
            Log::TYPE_ADD_WARNING => array ( // oc team comment (permanent)
                'translate' => 'pt237',
                'color' => '#CC0000',
            ),
        );
    }

    /**
     * Adds comment to specified PowerTrail
     *
     * @param PowerTrail $powerTrail
     * @param User $user
     * @param \DateTime $dateTime
     * @param type $type
     * @param type $text
     * @return boolean
     */
    public function addComment(PowerTrail $powerTrail, User $user, \DateTime $dateTime, $type, $text )
    {
        $log = new Log();
        $result = $log->setPowerTrail($powerTrail)
            ->setDateTime($dateTime)
            ->setUser($user)
            ->setType($type)
            ->setText($text)
            ->storeInDb();
        if($result){
            \sendEmail::emailOwners($powerTrail->getId(), $log->getType(), $dateTime->format('Y-m-d H:i'), $text, 'newComment');
        }
        return $result;
    }


    /**
     * used to set geoPath status to inactive, when has too small amount of caches,
     * etc.
     */
    public function cleanPowerTrailsCronjob()
    {
//         disabled until full automated geopaths-calening machine works finished
        $getPtQuery = 'SELECT * FROM `PowerTrail` WHERE `status` =1';
        $db = OcDb::instance();
        $s = $db->simpleQuery($getPtQuery);
        $ptToClean = $db->dbResultFetchAll($s);
        $this->user = new User(['userId' => -1]);
        foreach ($ptToClean as $dbRow) {
            $powerTrail = new PowerTrail(array('dbRow' => $dbRow));
            $powerTrail->setPowerTrailConfiguration($this->config)->checkCacheCount();
            $checkUncompletableResult = $powerTrail->disableUncompletablePt($this->serverUrl);
            $this->checkUncompletableResults($checkUncompletableResult, $powerTrail);

            $powerTrail->disablePowerTrailBecauseCacheCountTooLow();
        }
        
        $this->archiveAbandonPowerTrails();
        $this->freeCacheCandidates();
    }

    private function checkUncompletableResults($checkUncompletableResult, PowerTrail $powerTrail)
    {
        if($checkUncompletableResult['disablePowerTrail'] === true){
            $powerTrail->setAndStoreStatus($checkUncompletableResult['newStatus']);
            if($checkUncompletableResult['newStatus'] === PowerTrail::STATUS_INSERVICE){
                $type = Log::TYPE_DISABLING;
            } elseif ($checkUncompletableResult['newStatus'] === PowerTrail::STATUS_CLOSED){
                $type = Log::TYPE_CLOSING;
            }
            $this->addComment($powerTrail, $this->user, new \DateTime(), $type, $checkUncompletableResult['commentText']);
        }

    }

    private function archiveAbandonPowerTrails()
    {
        $db = OcDb::instance();
        $archiveAbandonQuery = 'SELECT `id` FROM `PowerTrail` WHERE `id` NOT IN (SELECT PowerTrailId FROM `PowerTrail_owners` WHERE 1 GROUP BY PowerTrailId)';
        $s = $db->simpleQuery($archiveAbandonQuery);
        if ($db->rowCount($s) > 0) { // close all abandon geoPaths
            $ptToClose = $db->dbResultFetchAll($s);
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
        $db = OcDb::instance();
        $query = 'DELETE FROM `PowerTrail_cacheCandidate` WHERE `date` < DATE_SUB(curdate(), INTERVAL 2 WEEK)';
        $db->simpleQuery($query);
    }

    /**
     * here power Trail status
     */
    public static function getPowerTrailStatus(){
        return array (
            1 => array ( // public
                'translate' => 'pt006',
            ),
            2 => array ( // not yet available
                'translate' => 'pt007',
            ),
            4 => array ( // service
                'translate' => 'pt219',
            ),
            3 => array ( // archived
                'translate' => 'pt212',
            ),
        );
    }

}
