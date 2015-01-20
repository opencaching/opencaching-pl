<?php

namespace lib\Controllers;

/**
 * Description of medals
 *
 * @author Łza
 */
class MedalsController
{
    const MEDAL_CHILD_GEOGRAPHICAL = 1;
    const MEDAL_CHILD_CACHEFOUND = 2;
    const MEDAL_CHILD_GEOPATHCOMPLETED = 3;
    const MEDAL_CHILD_MAXALTITUDE = 4;

    public $config;

    public function __construct()
    {
        $this->config = \lib\Objects\OcConfig\OcConfig::Instance();
    }

    public function checkMedalConditions(\lib\Objects\User\User $user)
    {
        // $cache = new \lib\Objects\GeoCache(array('cacheId' => $params['cacheId']));
        $allPossibleMedals = $this->allMedals();
        foreach ($allPossibleMedals as $medal) {
            $medal->checkConditionsForUser($user);
        }
    }

    /**
     * get all today's active users
     */
    public function checkAllUsersMedals()
    {
        $query = 'SELECT user_id, username, founds_count, notfounds_count, hidden_count, latitude, longitude, country, email FROM `user` WHERE (`last_login` BETWEEN DATE_SUB(NOW(), INTERVAL 24 HOUR) AND NOW()) ';
        /* @var $db \dataBase */
        $db = \lib\Database\DataBaseSingleton::Instance();
        $db->simpleQuery($query);
        d($db->rowCount());
        $timeStart = microtime();
        $usersToCheck = $db->dbResultFetchAll();
        foreach ($usersToCheck as $userDbRow) {
            $user = new \lib\Objects\User\User(null, $userDbRow);
            $this->checkMedalConditions($user);
        }
        $timeEnd = microtime() - $timeStart;
        d($timeEnd);
    }

    public static function getMedalTypeDetails($medalType)
    {
        $medalConfig = self::getConfig();
        return $medalConfig[$medalType];
    }

    public static function getConfig()
    {
        return include __DIR__ . '/config/medals.config.php';
    }

    private function allMedals()
    {
        foreach (self::getConfig() as $type => $medalDetails) {
            $medalDetails['type'] = $type;
            $medals[] = $this->buildMedalObject($medalDetails);
        }
        return $medals;
    }

    private function buildMedalObject($medalDetails)
    {
        switch ($medalDetails['child']) {
            case self::MEDAL_CHILD_GEOGRAPHICAL:
                return new \lib\Objects\Medals\MedalGeographical($medalDetails);
            case self::MEDAL_CHILD_CACHEFOUND:
                return new \lib\Objects\Medals\MedalCachefound($medalDetails);
            case self::MEDAL_CHILD_GEOPATHCOMPLETED:
                return new \lib\Objects\Medals\MedalGeopathCompleted($medalDetails);
            case self::MEDAL_CHILD_MAXALTITUDE:
                return new \lib\Objects\Medals\MedalMaxAltitude($medalDetails);
            default:
                d('error - undefinied medal');
                break;
        }
    }

}
