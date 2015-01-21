<?php

use lib\Controllers\MedalsController;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\OcConfig\OcConfig;
use lib\Objects\Medals\MedalsContainer;

/**
 * configuration table for medals
 */
return array(
    MedalsContainer::REGION_MALOPOLSKA => array(
        'name' => 'regionMalopolska',
        'type' => MedalsController::MEDAL_TYPE_REGION,
        'dateIntroduced' => '2006-04-09 10:30:00',
        'conditions' => array(
            'cacheType' => array(
                GeoCache::TYPE_TRADITIONAL,
                GeoCache::TYPE_MULTICACHE,
                GeoCache::TYPE_QUIZ,
                GeoCache::TYPE_OTHERTYPE,
            ),
            'cacheLocation' => array(
                'code3' => 'PL21',
            ),
            'cacheCountToAward' => array(
                1 => array(
                    'levelName' => 'paper',
                    'cacheCount' => array(
                        'found' => 1,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => 'wooden',
                    'cacheCount' => array(
                        'found' => 10,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => 'iron',
                    'cacheCount' => array(
                        'found' => 20,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => 'Beril',
                    'cacheCount' => array(
                        'found' => 40,
                        'placed' => 1,
                    ),
                ),
                5 => array(
                    'levelName' => 'Bronze',
                    'cacheCount' => array(
                        'found' => 80,
                        'placed' => 2,
                    ),
                ),
                6 => array(
                    'levelName' => 'Silver',
                    'cacheCount' => array(
                        'found' => 160,
                        'placed' => 5,
                    ),
                ),
                7 => array(
                    'levelName' => 'Gold',
                    'cacheCount' => array(
                        'found' => 320,
                        'placed' => 10,
                    ),
                ),
                8 => array(
                    'levelName' => 'Platinum',
                    'cacheCount' => array(
                        'found' => 640,
                        'placed' => 25,
                    ),
                ),
                9 => array(
                    'levelName' => 'Perl',
                    'cacheCount' => array(
                        'found' => 1000,
                        'placed' => 40,
                    ),
                ),
                10 => array(
                    'levelName' => 'Crystal',
                    'cacheCount' => array(
                        'found' => 1300,
                        'placed' => 64,
                    ),
                ),
            ),
        ),
    ),
    MedalsContainer::REGION_KRAKOW => array(
        'name' => 'cityKrakow',
        'type' => MedalsController::MEDAL_TYPE_REGION,
        'dateIntroduced' => '2006-04-09 10:30:00',
        'conditions' => array(
            'cacheType' => array(
                GeoCache::TYPE_TRADITIONAL,
                GeoCache::TYPE_MULTICACHE,
                GeoCache::TYPE_QUIZ,
            ),
            'cacheLocation' => array(
                'code3' => 'PL21',
                'code4' => 'PL213',
            ),
            'cacheCountToAward' => array(
                1 => array(
                    'levelName' => 'Paper',
                    'cacheCount' => array(
                        'found' => 1,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => 'Wooden',
                    'cacheCount' => array(
                        'found' => 2,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => 'Iron',
                    'cacheCount' => array(
                        'found' => 4,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => 'Beril',
                    'cacheCount' => array(
                        'found' => 8,
                        'placed' => 0,
                    ),
                ),
                5 => array(
                    'levelName' => 'Bronze',
                    'cacheCount' => array(
                        'found' => 16,
                        'placed' => 1,
                    ),
                ),
                6 => array(
                    'levelName' => 'Silver',
                    'cacheCount' => array(
                        'found' => 32,
                        'placed' => 2,
                    ),
                ),
                7 => array(
                    'levelName' => 'Gold',
                    'cacheCount' => array(
                        'found' => 64,
                        'placed' => 3,
                    ),
                ),
                8 => array(
                    'levelName' => 'Platinum',
                    'cacheCount' => array(
                        'found' => 128,
                        'placed' => 5,
                    ),
                ),
                9 => array(
                    'levelName' => 'Perl',
                    'cacheCount' => array(
                        'found' => 256,
                        'placed' => 10,
                    ),
                ),
                10 => array(
                    'levelName' => 'Crystal',
                    'cacheCount' => array(
                        'found' => 512,
                        'placed' => 20,
                    ),
                ),
            ),
        ),
    ),
    MedalsContainer::CACHE_TRADITIONAL => array(
        'name' => 'TraditionalCache',
        'type' => MedalsController::MEDAL_TYPE_CACHES,
        'dateIntroduced' => '2005-04-09 10:30:00',
        'conditions' => array(
            'cacheType' => array(
                GeoCache::TYPE_TRADITIONAL,
            ),
            'cacheCountToAward' => array(
                1 => array(
                    'levelName' => 'Paper',
                    'cacheCount' => array(
                        'found' => 5,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => 'Wooden',
                    'cacheCount' => array(
                        'found' => 20,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => 'Iron',
                    'cacheCount' => array(
                        'found' => 50,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => 'Beril',
                    'cacheCount' => array(
                        'found' => 100,
                        'placed' => 1,
                    ),
                ),
                5 => array(
                    'levelName' => 'Bronze',
                    'cacheCount' => array(
                        'found' => 200,
                        'placed' => 2,
                    ),
                ),
                6 => array(
                    'levelName' => 'Silver',
                    'cacheCount' => array(
                        'found' => 500,
                        'placed' => 5,
                    ),
                ),
                7 => array(
                    'levelName' => 'Gold',
                    'cacheCount' => array(
                        'found' => 1000,
                        'placed' => 10,
                    ),
                ),
                8 => array(
                    'levelName' => 'Platinum',
                    'cacheCount' => array(
                        'found' => 2000,
                        'placed' => 20,
                    ),
                ),
                9 => array(
                    'levelName' => 'Perl',
                    'cacheCount' => array(
                        'found' => 5000,
                        'placed' => 50,
                    ),
                ),
                10 => array(
                    'levelName' => 'Crystal',
                    'cacheCount' => array(
                        'found' => 10000,
                        'placed' => 100,
                    ),
                ),
            ),
        ),
    ),
    MedalsContainer::GEOPATH_KOTLINAJELENIOGORSKA => array(
        'name' => 'PTTKDookolaKotlinyJeleniogorskiej',
        'type' => MedalsController::MEDAL_TYPE_GEOPATHCOMPLETED,
        'dateIntroduced' => '2014-08-20 10:30:00',
        'conditions' => array(
            'ocNodeId' => array(OcConfig::OCNODE_POLAND),
            'geoPath' => array(
                'geoPathId' => 75
            ),
            'cacheCountToAward' => array(),
            'userToCacheRelation' => 'found',
        ),
    ),
    MedalsContainer::REGION_LUBELSKI => array(
        'name' => 'Lubelski Geocaching',
        'type' => MedalsController::MEDAL_TYPE_REGION,
        'dateIntroduced' => '2014-01-23 00:01:00',
        'conditions' => array(
            'ocNodeId' => array(OcConfig::OCNODE_POLAND),
            'cacheType' => array(
                GeoCache::TYPE_TRADITIONAL,
                GeoCache::TYPE_MULTICACHE,
                GeoCache::TYPE_QUIZ,
                GeoCache::TYPE_GEOPATHFINAL,
                GeoCache::TYPE_OTHERTYPE,
            ),
            'cacheLocation' => array(
                'code3' => 'PL31',
            ),
            'cacheCountToAward' => array(
                1 => array(
                    'levelName' => 'Popularny',
                    'cacheCount' => array(
                        'found' => 50,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => 'Brązowy',
                    'cacheCount' => array(
                        'found' => 100,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => 'Srebrny',
                    'cacheCount' => array(
                        'found' => 150,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => 'Złoty',
                    'cacheCount' => array(
                        'found' => 200,
                        'placed' => 10,
                    ),
                ),
                5 => array(
                    'levelName' => 'Honorowy',
                    'cacheCount' => array(
                        'found' => 300,
                        'placed' => 20,
                    ),
                ),
            ),
        ),
    ),
    MedalsContainer::MAXALTITUDE_2450 => array(
        'name' => 'AltitudeGeocache',
        'type' => MedalsController::MEDAL_TYPE_MAXALTITUDE,
        'dateIntroduced' => '2005-01-01 00:01:00',
        'conditions' => array(
            'ocNodeId' => array(
                OcConfig::OCNODE_POLAND,
                OcConfig::OCNODE_ROMANIA
            ),
            'cacheType' => array(
                GeoCache::TYPE_TRADITIONAL,
                GeoCache::TYPE_MULTICACHE,
                GeoCache::TYPE_QUIZ,
            ),
            'altitudeToAward' => array(
                1 => array(
                    'levelName' => 'Paper',
                    'altitude' => array(
                        'found' => 500,
                        'placed' => -9000,
                    ),
                ),
                2 => array(
                    'levelName' => 'Wooden',
                    'altitude' => array(
                        'found' => 700,
                        'placed' => -9000,
                    ),
                ),
                3 => array(
                    'levelName' => 'Iron',
                    'altitude' => array(
                        'found' => 900,
                        'placed' => -9000,
                    ),
                ),
                4 => array(
                    'levelName' => 'Beril',
                    'altitude' => array(
                        'found' => 1100,
                        'placed' => -9000,
                    ),
                ),
                5 => array(
                    'levelName' => 'Bronze',
                    'altitude' => array(
                        'found' => 1300,
                        'placed' => 500,
                    ),
                ),
                6 => array(
                    'levelName' => 'Silver',
                    'altitude' => array(
                        'found' => 1500,
                        'placed' => 600,
                    ),
                ),
                7 => array(
                    'levelName' => 'Gold',
                    'altitude' => array(
                        'found' => 1700,
                        'placed' => 700,
                    ),
                ),
                8 => array(
                    'levelName' => 'Platinum',
                    'altitude' => array(
                        'found' => 1900,
                        'placed' => 800,
                    ),
                ),
                9 => array(
                    'levelName' => 'Perl',
                    'altitude' => array(
                        'found' => 2100,
                        'placed' => 900,
                    ),
                ),
                10 => array(
                    'levelName' => 'Crystal',
                    'altitude' => array(
                        'found' => 2450,
                        'placed' => 1000,
                    ),
                ),
            ),
        ),
    ),
     MedalsContainer::HIGHLAND_9000 => array(
        'name' => 'HighlandGeocacher',
        'type' => MedalsController::MEDAL_TYPE_HIGHLAND,
        'dateIntroduced' => '2005-01-01 00:01:00',
        'conditions' => array(
            'ocNodeId' => array(
                OcConfig::OCNODE_POLAND,
                OcConfig::OCNODE_ROMANIA
            ),
            'cacheType' => array(
                GeoCache::TYPE_TRADITIONAL,
                GeoCache::TYPE_MULTICACHE,
            ),
            'minimumAltitude' => 700,
            'cacheCountToAward' => array(
                1 => array(
                    'levelName' => 'Paper',
                    'cacheCount' => array(
                        'found' => 1,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => 'Wooden',
                    'cacheCount' => array(
                        'found' => 5,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => 'Iron',
                    'cacheCount' => array(
                        'found' => 10,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => 'Beril',
                    'cacheCount' => array(
                        'found' => 20,
                        'placed' => 1,
                    ),
                ),
                5 => array(
                    'levelName' => 'Bronze',
                    'cacheCount' => array(
                        'found' => 40,
                        'placed' => 2,
                    ),
                ),
                6 => array(
                    'levelName' => 'Silver',
                    'cacheCount' => array(
                        'found' => 50,
                        'placed' => 3,
                    ),
                ),
                7 => array(
                    'levelName' => 'Gold',
                    'cacheCount' => array(
                        'found' => 100,
                        'placed' => 5,
                    ),
                ),
                8 => array(
                    'levelName' => 'Platinum',
                    'cacheCount' => array(
                        'found' => 200,
                        'placed' => 10,
                    ),
                ),
                9 => array(
                    'levelName' => 'Perl',
                    'cacheCount' => array(
                        'found' => 400,
                        'placed' => 20,
                    ),
                ),
                10 => array(
                    'levelName' => 'Crystal',
                    'cacheCount' => array(
                        'found' => 800,
                        'placed' => 40,
                    ),
                ),
            ),
        ),
    ),


);

