<?php

namespace lib\Medals;

use lib\GeoCache;

/**
 * Description of medals
 *
 * @author Łza
 */
class MedalsController {

    const MEDAL_CHILD_GEOGRAPHICAL = 1;
    const MEDAL_CHILD_CACHEFOUND = 2;
    const MEDAL_CHILD_GEOPATHCOMPLETED = 3;

    const USER_TO_CACHE_OWNER = 1;
    const USER_TO_CACHE_FINDER = 2;

    static $medalTypes = array(
        1 => array(
            'name' => 'regionMalopolska',
            'child' => self::MEDAL_CHILD_GEOGRAPHICAL,
            'dateIntroduced' => '2014-04-09 10:30:00',
            'conditions' => array(
                'cacheType' => array(
                    \cache::TYPE_TRADITIONAL,
                    \cache::TYPE_MULTICACHE,
                ),
                'cacheLocation' => array (
                    'code3' => 'PL21',
                ),
                'cacheCountToAward' => 1,
                'userToCacheRelation' => self::USER_TO_CACHE_FINDER,
            ),
        ),
        2 => array(
            'name' => 'cityKrakow',
            'child' => self::MEDAL_CHILD_GEOGRAPHICAL,
            'dateIntroduced' => '2006-04-09 10:30:00',
            'conditions' => array(
                'cacheType' => array(
                    \cache::TYPE_TRADITIONAL,
                    \cache::TYPE_MULTICACHE,
                ),
                'cacheLocation' => array (
                    'code3' => 'PL21',
                    'code4' => 'PL213',
                ),
                'cacheCountToAward' => 5,
                'userToCacheRelation' => self::USER_TO_CACHE_FINDER,
            ),
        ),
        3 => array(
            'name' => '5thTraditionalCache',
            'child' => self::MEDAL_CHILD_CACHEFOUND,
            'dateIntroduced' => '2014-04-09 10:30:00',
            'conditions' => array(
                'cacheType' => array(
                   \cache::TYPE_TRADITIONAL,
                ),
                'cacheCountToAward' => 5,
                'userToCacheRelation' => self::USER_TO_CACHE_FINDER,
            ),
        ),
        4 => array(
            'name' => 'PTTK - Dookola Kotliny Jeleniogorskiej',
            'child' => self::MEDAL_CHILD_GEOPATHCOMPLETED,
            'dateIntroduced' => '2014-08-20 10:30:00',
            'conditions' => array(
                'geoPath' => array(
                    'ocNodeId' => 2,
                    'geoPathId' => 75
                ),
                'cacheCountToAward' => 5,
                'userToCacheRelation' => self::USER_TO_CACHE_FINDER,
            ),
        ),
    );

    public $config;

    public function __construct() {
        $this->config = \lib\Medals\OcConfig::Instance();
    }

    public function checkMedalConditions(\lib\User\User $user) {
        // $cache = new \lib\geoCache(array('cacheId' => $params['cacheId']));
        // $user = new \lib\User\User($params['userId']);
        $allPossibleMedals = $this->allMedals();
        foreach ($allPossibleMedals as $medal) {
            $medal->checkConditionsForUser($user);
        }
    }

    /**
     * get all today's active users
     */
    public function checkAllUsersMedals(){
        $query = 'SELECT user_id, username, founds_count, notfounds_count, hidden_count FROM `user` WHERE (`last_login` BETWEEN DATE_SUB(NOW(), INTERVAL 24 HOUR) AND NOW()) ';
        /* @var $db \dataBase */
        $db = \lib\Database\DataBaseSingleton::Instance();
        $db->simpleQuery($query);
        $usersToCheck = $db->dbResultFetchAll();
        foreach ($usersToCheck as $userDbRow) {
            $user = new \lib\User\User(null, $userDbRow);
            d($user);
            $this->checkMedalConditions($user);
        }
        dd($usersToCheck);

    }

    public static function getMedalTypeDetails($medalType){
        return self::$medalTypes[$medalType];
    }


    private function allMedals() {
        foreach (self::$medalTypes as $type => $medalDetails) {
            $medalDetails['type'] = $type;
            $medals[] = $this->buildMedalObject($medalDetails);
        }
        return $medals;
    }

    private function buildMedalObject($medalDetails){
        switch ($medalDetails['child']) {
            case self::MEDAL_CHILD_GEOGRAPHICAL:
                return new MedalGeographical($medalDetails);
            case self::MEDAL_CHILD_CACHEFOUND:
                return new MedalCachefound($medalDetails);
            case self::MEDAL_CHILD_GEOPATHCOMPLETED:
                return new MedalGeopathCompleted($medalDetails);
            default:
                d('błąd - medal niezadeklarowany');
                break;
        }
    }
}
