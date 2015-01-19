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

    static $medalTypes = array(
        1 => array(
            'name' => 'regionMalopolska',
            'child' => self::MEDAL_CHILD_GEOGRAPHICAL,
            'dateIntroduced' => '2006-04-09 10:30:00',
            'conditions' => array(
                'cacheType' => array(
                    \cache::TYPE_TRADITIONAL,
                    \cache::TYPE_MULTICACHE,
                ),
                'cacheLocation' => array(
                    'code3' => 'PL21',
                ),

                'cacheCountToAward' => array(
                    1 => array (
                        'levelName' => 'paper',
                        'cacheCount' => array(
                            'found' => 1,
                            'placed' => 0,
                        ),
                    ),
                    2 => array (
                        'levelName' => 'wooden',
                        'cacheCount' => array(
                            'found' => 10,
                            'placed' => 0,
                        ),
                    ),
                    3 => array (
                        'levelName' => 'iron',
                        'cacheCount' => array(
                            'found' => 30,
                            'placed' => 1,
                        ),
                    ),
                    4 => array (
                        'levelName' => 'iron',
                        'cacheCount' => array(
                            'found' => 60,
                            'placed' => 2,
                        ),
                    ),
                ),
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
                'cacheLocation' => array(
                    'code3' => 'PL21',
                    'code4' => 'PL213',
                ),

                 'cacheCountToAward' => array(
                    1 => array (
                        'levelName' => 'Smok Wawelski',
                        'cacheCount' => array(
                            'found' => 5,
                            'placed' => 1,
                        ),
                    ),
                    2 => array (
                        'levelName' => 'Lajkonik',
                        'cacheCount' => array(
                            'found' => 10,
                            'placed' => 1,
                        ),
                    ),
                ),
            ),
        ),
        3 => array(
            'name' => 'TraditionalCache',
            'child' => self::MEDAL_CHILD_CACHEFOUND,
            'dateIntroduced' => '2005-04-09 10:30:00',
            'conditions' => array(
                'cacheType' => array(
                    \cache::TYPE_TRADITIONAL,
                ),
                 'cacheCountToAward' => array(
                    1 => array (
                        'levelName' => 'Paper',
                        'cacheCount' => array(
                            'found' => 5,
                            'placed' => 0,
                        ),
                    ),
                    2 => array (
                        'levelName' => 'Wooden',
                        'cacheCount' => array(
                            'found' => 20,
                            'placed' => 0,
                        ),
                    ),
                    3 => array (
                        'levelName' => 'Iron',
                        'cacheCount' => array(
                            'found' => 50,
                            'placed' => 0,
                        ),
                    ),
                    4 => array (
                        'levelName' => 'Beril',
                        'cacheCount' => array(
                            'found' => 100,
                            'placed' => 1,
                        ),
                    ),
                    5 => array (
                        'levelName' => 'Bronze',
                        'cacheCount' => array(
                            'found' => 200,
                            'placed' => 2,
                        ),
                    ),
                    6 => array (
                        'levelName' => 'Silver',
                        'cacheCount' => array(
                            'found' => 500,
                            'placed' => 5,
                        ),
                    ),
                    7 => array (
                        'levelName' => 'Gold',
                        'cacheCount' => array(
                            'found' => 1000,
                            'placed' => 10,
                        ),
                    ),
                    8 => array (
                        'levelName' => 'Platinum',
                        'cacheCount' => array(
                            'found' => 2000,
                            'placed' => 20,
                        ),
                    ),
                    9 => array (
                        'levelName' => 'Perl',
                        'cacheCount' => array(
                            'found' => 5000,
                            'placed' => 50,
                        ),
                    ),
                    10 => array (
                        'levelName' => 'Crystal',
                        'cacheCount' => array(
                            'found' => 10000,
                            'placed' => 100,
                        ),
                    ),
                ),
            ),
        ),
        4 => array(
            'name' => 'PTTK - Dookola Kotliny Jeleniogorskiej',
            'child' => self::MEDAL_CHILD_GEOPATHCOMPLETED,
            'dateIntroduced' => '2014-08-20 10:30:00',
            'conditions' => array(
                'ocNodeId' => array(1,2,3,4,5,6,7,8,9,10),
                'geoPath' => array(
                    'geoPathId' => 75
                ),
                'cacheCountToAward' => array(),
                'userToCacheRelation' => 'found',
            ),
        ),
        5 => array(
            'name' => 'Lubelski Geocaching',
            'child' => self::MEDAL_CHILD_GEOGRAPHICAL,
            'dateIntroduced' => '2014-01-23 00:01:00',
            'conditions' => array(
                'ocNodeId' => array (2,3),
                'cacheType' => array(
                    \cache::TYPE_TRADITIONAL,
                    \cache::TYPE_MULTICACHE,
                    \cache::TYPE_QUIZ,
                    \cache::TYPE_GEOPATHFINAL,
                    \cache::TYPE_OTHERTYPE,
                ),
                'cacheLocation' => array(
                    'code3' => 'PL31',
                ),
                'cacheCountToAward' => array(
                    1 => array (
                        'levelName' => 'Popularny',
                        'cacheCount' => array(
                            'found' => 50,
                            'placed' => 0,
                        ),
                    ),
                    2 => array (
                        'levelName' => 'Brązowy',
                        'cacheCount' => array(
                            'found' => 100,
                             'placed' => 0,
                        ),
                    ),
                    3 => array (
                        'levelName' => 'Srebrny',
                        'cacheCount' => array(
                            'found' => 150,
                             'placed' => 0,
                        ),
                    ),
                    4 => array (
                        'levelName' => 'Złoty',
                        'cacheCount' => array(
                            'found' => 200,
                            'placed' => 10,
                        ),
                    ),
                    5 => array (
                        'levelName' => 'Honorowy',
                        'cacheCount' => array(
                            'found' => 300,
                            'placed' => 20,
                        ),
                    ),
                ),
            ),
        ),
        6 => array(
            'name' => 'Altitude Geocache',
            'child' => self::MEDAL_CHILD_MAXALTITUDE,
            'dateIntroduced' => '2005-01-01 00:01:00',
            'conditions' => array(
				'ocNodeId' => array (2),
                'cacheType' => array(
                    \cache::TYPE_TRADITIONAL,
                    \cache::TYPE_MULTICACHE,
                ),
                'altitudeToAward' => array(
					 1 => array (
                        'levelName' => 'Paper',
                        'altitude' => array(
                            'found' => 500,
                            'placed' => -9000,
                        ),
                    ),
                    2 => array (
                        'levelName' => 'Wooden',
                        'altitude' => array(
                            'found' => 700,
                            'placed' => -9000,
                        ),
                    ),
                    3 => array (
                        'levelName' => 'Iron',
                        'altitude' => array(
                            'found' => 900,
                            'placed' => -9000,
                        ),
                    ),
                    4 => array (
                        'levelName' => 'Beril',
                        'altitude' => array(
                            'found' => 1100,
                            'placed' => -9000,
                        ),
                    ),
                    5 => array (
                        'levelName' => 'Bronze',
                        'altitude' => array(
                            'found' => 1300,
                            'placed' => 500,
                        ),
                    ),
                    6 => array (
                        'levelName' => 'Silver',
                        'altitude' => array(
                            'found' => 1500,
                            'placed' => 600,
                        ),
                    ),
                    7 => array (
                        'levelName' => 'Gold',
                        'altitude' => array(
                            'found' => 1700,
                            'placed' => 700,
                        ),
                    ),
                    8 => array (
                        'levelName' => 'Platinum',
                        'altitude' => array(
                            'found' => 1900,
                            'placed' => 800,
                        ),
                    ),
                    9 => array (
                        'levelName' => 'Perl',
                        'altitude' => array(
                            'found' => 2100,
                            'placed' => 900,
                        ),
                    ),
                    10 => array (
                        'levelName' => 'Crystal',
                        'altitude' => array(
                            'found' => 2450,
                            'placed' => 1000,
                        ),
					),
                ),
            ),
        ),
    );
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
        return self::$medalTypes[$medalType];
    }

    private function allMedals()
    {
        foreach (self::$medalTypes as $type => $medalDetails) {
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
