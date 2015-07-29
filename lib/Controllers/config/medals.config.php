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
        'name' => _('regionMalopolska'),
        'description' => _('Medal for geocaching in Małopolska'),
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
            'minimumAltitude' => false,
            'cacheCountToAward' => array(
                1 => array(
                    'levelName' => _('paper'),
                    'cacheCount' => array(
                        'found' => 1,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => _('wooden'),
                    'cacheCount' => array(
                        'found' => 10,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => _('iron'),
                    'cacheCount' => array(
                        'found' => 20,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => _('beril'),
                    'cacheCount' => array(
                        'found' => 40,
                        'placed' => 1,
                    ),
                ),
                5 => array(
                    'levelName' => _('bronze'),
                    'cacheCount' => array(
                        'found' => 80,
                        'placed' => 2,
                    ),
                ),
                6 => array(
                    'levelName' => _('silver'),
                    'cacheCount' => array(
                        'found' => 160,
                        'placed' => 5,
                    ),
                ),
                7 => array(
                    'levelName' => _('gold'),
                    'cacheCount' => array(
                        'found' => 320,
                        'placed' => 10,
                    ),
                ),
                8 => array(
                    'levelName' => _('platinum'),
                    'cacheCount' => array(
                        'found' => 640,
                        'placed' => 25,
                    ),
                ),
                9 => array(
                    'levelName' => _('perl'),
                    'cacheCount' => array(
                        'found' => 1000,
                        'placed' => 40,
                    ),
                ),
                10 => array(
                    'levelName' => _('crystal'),
                    'cacheCount' => array(
                        'found' => 1300,
                        'placed' => 64,
                    ),
                ),
            ),
        ),
    ), /* end of medal */
    MedalsContainer::REGION_KRAKOW => array(
        'name' => _('cityKrakow'),
        'description' => _('Medal for geocaching in Kraków'),
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
            'minimumAltitude' => false,
            'cacheCountToAward' => array(
                1 => array(
                    'levelName' => _('paper'),
                    'cacheCount' => array(
                        'found' => 1,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => _('wooden'),
                    'cacheCount' => array(
                        'found' => 2,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => _('iron'),
                    'cacheCount' => array(
                        'found' => 4,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => _('beril'),
                    'cacheCount' => array(
                        'found' => 8,
                        'placed' => 0,
                    ),
                ),
                5 => array(
                    'levelName' => _('bronze'),
                    'cacheCount' => array(
                        'found' => 16,
                        'placed' => 1,
                    ),
                ),
                6 => array(
                    'levelName' => _('silver'),
                    'cacheCount' => array(
                        'found' => 32,
                        'placed' => 2,
                    ),
                ),
                7 => array(
                    'levelName' => _('gold'),
                    'cacheCount' => array(
                        'found' => 64,
                        'placed' => 3,
                    ),
                ),
                8 => array(
                    'levelName' => _('platinum'),
                    'cacheCount' => array(
                        'found' => 128,
                        'placed' => 5,
                    ),
                ),
                9 => array(
                    'levelName' => _('perl'),
                    'cacheCount' => array(
                        'found' => 256,
                        'placed' => 10,
                    ),
                ),
                10 => array(
                    'levelName' => _('crystal'),
                    'cacheCount' => array(
                        'found' => 512,
                        'placed' => 20,
                    ),
                ),
            ),
        ),
    ), /* end of medal */
    MedalsContainer::CACHE_TRADITIONAL => array(
        'name' => _('TraditionalCache'),
        'description' => _('Medal for traditional caches activity'),
        'type' => MedalsController::MEDAL_TYPE_CACHES,
        'dateIntroduced' => '2005-04-09 10:30:00',
        'conditions' => array(
            'ocNodeId' => array(OcConfig::OCNODE_POLAND, OcConfig::OCNODE_BENELUX, OcConfig::OCNODE_ROMANIA),
            'cacheType' => array(
                GeoCache::TYPE_TRADITIONAL,
            ),
            'logType' => \lib\Objects\GeoCache\GeoCacheLog::LOGTYPE_FOUNDIT,
            'placedCacheStatus' => array (GeoCache::STATUS_READY),
            'cacheCountToAward' => array(
                1 => array(
                    'levelName' => _('paper'),
                    'cacheCount' => array(
                        'found' => 5,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => _('wooden'),
                    'cacheCount' => array(
                        'found' => 20,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => _('iron'),
                    'cacheCount' => array(
                        'found' => 50,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => _('beril'),
                    'cacheCount' => array(
                        'found' => 100,
                        'placed' => 1,
                    ),
                ),
                5 => array(
                    'levelName' => _('bronze'),
                    'cacheCount' => array(
                        'found' => 200,
                        'placed' => 2,
                    ),
                ),
                6 => array(
                    'levelName' => _('silver'),
                    'cacheCount' => array(
                        'found' => 500,
                        'placed' => 5,
                    ),
                ),
                7 => array(
                    'levelName' => _('gold'),
                    'cacheCount' => array(
                        'found' => 1000,
                        'placed' => 10,
                    ),
                ),
                8 => array(
                    'levelName' => _('platinum'),
                    'cacheCount' => array(
                        'found' => 2000,
                        'placed' => 20,
                    ),
                ),
                9 => array(
                    'levelName' => _('perl'),
                    'cacheCount' => array(
                        'found' => 5000,
                        'placed' => 50,
                    ),
                ),
                10 => array(
                    'levelName' => _('crystal'),
                    'cacheCount' => array(
                        'found' => 10000,
                        'placed' => 100,
                    ),
                ),
            ),
        ),
    ), /* end of medal */
    MedalsContainer::GEOPATH_KOTLINAJELENIOGORSKA => array(
        'name' => _('PTTK Dookola Kotliny Jeleniogorskiej'),
        'description' => _('Medal for receive PTTK badge "PTTK Dookola Kotliny Jeleniogorskiej"'),
        'type' => MedalsController::MEDAL_TYPE_GEOPATHCOMPLETED,
        'dateIntroduced' => '2014-08-20 10:30:00',
        'conditions' => array(
            'ocNodeId' => array(OcConfig::OCNODE_POLAND),
            'geoPath' => array(
                'geoPathId' => 75
            ),
            'cacheCountToAward' => array(
                0 => array(
                    'levelName' => '',
                ),
            ),
            'userToCacheRelation' => 'found',
        ),
    ), /* end of medal */
    MedalsContainer::REGION_LUBELSKI => array(
        'name' => _('Lubelski Geocaching'),
        'description' => _('Medal for geocaching in Województwo Lubelskie and receive PTTK badge "Lubelski Geocaching"'),
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
            'minimumAltitude' => false,
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
    ), /* end of medal */
    MedalsContainer::MAXALTITUDE_2450 => array(
        'name' => _('AltitudeGeocache'),
        'description' => _('Medal for finding caches at high altitudes'),
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
                    'levelName' => _('meadow'),
                    'altitude' => array(
                        'found' => 500,
                        'placed' => -9000,
                    ),
                ),
                2 => array(
                    'levelName' => _('beech'),
                    'altitude' => array(
                        'found' => 700,
                        'placed' => -9000,
                    ),
                ),
                3 => array(
                    'levelName' => _('fir'),
                    'altitude' => array(
                        'found' => 900,
                        'placed' => -9000,
                    ),
                ),
                4 => array(
                    'levelName' => _('spruce'),
                    'altitude' => array(
                        'found' => 1100,
                        'placed' => -9000,
                    ),
                ),
                5 => array(
                    'levelName' => _('pass'),
                    'altitude' => array(
                        'found' => 1300,
                        'placed' => 500,
                    ),
                ),
                6 => array(
                    'levelName' => _('rowan'),
                    'altitude' => array(
                        'found' => 1500,
                        'placed' => 600,
                    ),
                ),
                7 => array(
                    'levelName' => _('limba'),
                    'altitude' => array(
                        'found' => 1700,
                        'placed' => 700,
                    ),
                ),
                8 => array(
                    'levelName' => _('mountain pine'),
                    'altitude' => array(
                        'found' => 1900,
                        'placed' => 800,
                    ),
                ),
                9 => array(
                    'levelName' => _('scree'),
                    'altitude' => array(
                        'found' => 2100,
                        'placed' => 900,
                    ),
                ),
                10 => array(
                    'levelName' => _('ridge'),
                    'altitude' => array(
                        'found' => 2450,
                        'placed' => 1000,
                    ),
                ),
            ),
        ),
    ), /* end of medal */
     MedalsContainer::HIGHLAND_700 => array(
        'name' => _('HighlandGeocacher'),
        'description' => _('Medal for finding mountain caches'),
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
                    'levelName' => _('paper'),
                    'cacheCount' => array(
                        'found' => 1,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => _('wooden'),
                    'cacheCount' => array(
                        'found' => 3,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => _('iron'),
                    'cacheCount' => array(
                        'found' => 5,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => _('beril'),
                    'cacheCount' => array(
                        'found' => 10,
                        'placed' => 1,
                    ),
                ),
                5 => array(
                    'levelName' => _('bronze'),
                    'cacheCount' => array(
                        'found' => 20,
                        'placed' => 2,
                    ),
                ),
                6 => array(
                    'levelName' => _('silver'),
                    'cacheCount' => array(
                        'found' => 30,
                        'placed' => 3,
                    ),
                ),
                7 => array(
                    'levelName' => _('gold'),
                    'cacheCount' => array(
                        'found' => 50,
                        'placed' => 5,
                    ),
                ),
                8 => array(
                    'levelName' => _('platinum'),
                    'cacheCount' => array(
                        'found' => 100,
                        'placed' => 10,
                    ),
                ),
                9 => array(
                    'levelName' => _('perl'),
                    'cacheCount' => array(
                        'found' => 250,
                        'placed' => 20,
                    ),
                ),
                10 => array(
                    'levelName' => _('crystal'),
                    'cacheCount' => array(
                        'found' => 500,
                        'placed' => 40,
                    ),
                ),
            ),
        ),
    ), /* end of medal */
    MedalsContainer::CACHE_MULTICACHE => array(
        'name' => _('MultiGeocacher'),
        'description' => _('Medal for multicache caches activity'),
        'type' => MedalsController::MEDAL_TYPE_CACHES,
        'dateIntroduced' => '2005-01-01 00:00:01',
        'conditions' => array(
            'ocNodeId' => array(OcConfig::OCNODE_POLAND, OcConfig::OCNODE_BENELUX, OcConfig::OCNODE_ROMANIA),
            'cacheType' => array(
                GeoCache::TYPE_MULTICACHE,
            ),
            'logType' => \lib\Objects\GeoCache\GeoCacheLog::LOGTYPE_FOUNDIT,
            'placedCacheStatus' => array (GeoCache::STATUS_READY, GeoCache::STATUS_UNAVAILABLE),
            'cacheCountToAward' => array(
                1 => array(
                    'levelName' => _('paper'),
                    'cacheCount' => array(
                        'found' => 3,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => _('wooden'),
                    'cacheCount' => array(
                        'found' => 8,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => _('iron'),
                    'cacheCount' => array(
                        'found' => 15,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => _('beril'),
                    'cacheCount' => array(
                        'found' => 30,
                        'placed' => 1,
                    ),
                ),
                5 => array(
                    'levelName' => _('bronze'),
                    'cacheCount' => array(
                        'found' => 75,
                        'placed' => 2,
                    ),
                ),
                6 => array(
                    'levelName' => _('silver'),
                    'cacheCount' => array(
                        'found' => 125,
                        'placed' => 5,
                    ),
                ),
                7 => array(
                    'levelName' => _('gold'),
                    'cacheCount' => array(
                        'found' => 250,
                        'placed' => 10,
                    ),
                ),
                8 => array(
                    'levelName' => _('platinum'),
                    'cacheCount' => array(
                        'found' => 500,
                        'placed' => 20,
                    ),
                ),
                9 => array(
                    'levelName' => _('perl'),
                    'cacheCount' => array(
                        'found' => 1000,
                        'placed' => 30,
                    ),
                ),
                10 => array(
                    'levelName' => _('crystal'),
                    'cacheCount' => array(
                        'found' => 2000,
                        'placed' => 40,
                    ),
                ),
            ),
        ),
    ), /* end of medal */
    MedalsContainer::CACHE_QUIZ => array(
        'name' => _('QuizGeocacher'),
        'description' => _('Medal for quiz caches activity'),
        'type' => MedalsController::MEDAL_TYPE_CACHES,
        'dateIntroduced' => '2005-01-01 00:00:01',
        'conditions' => array(
            'ocNodeId' => array(OcConfig::OCNODE_POLAND, OcConfig::OCNODE_BENELUX, OcConfig::OCNODE_ROMANIA),
            'cacheType' => array(
                GeoCache::TYPE_QUIZ,
            ),
            'logType' => \lib\Objects\GeoCache\GeoCacheLog::LOGTYPE_FOUNDIT,
            'placedCacheStatus' => array (GeoCache::STATUS_READY, GeoCache::STATUS_UNAVAILABLE),
            'cacheCountToAward' => array(
                1 => array(
                    'levelName' => _('paper'),
                    'cacheCount' => array(
                        'found' => 3,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => _('wooden'),
                    'cacheCount' => array(
                        'found' => 8,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => _('iron'),
                    'cacheCount' => array(
                        'found' => 15,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => _('beril'),
                    'cacheCount' => array(
                        'found' => 30,
                        'placed' => 1,
                    ),
                ),
                5 => array(
                    'levelName' => _('bronze'),
                    'cacheCount' => array(
                        'found' => 75,
                        'placed' => 2,
                    ),
                ),
                6 => array(
                    'levelName' => _('silver'),
                    'cacheCount' => array(
                        'found' => 125,
                        'placed' => 5,
                    ),
                ),
                7 => array(
                    'levelName' => _('gold'),
                    'cacheCount' => array(
                        'found' => 250,
                        'placed' => 10,
                    ),
                ),
                8 => array(
                    'levelName' => _('platinum'),
                    'cacheCount' => array(
                        'found' => 500,
                        'placed' => 20,
                    ),
                ),
                9 => array(
                    'levelName' => _('perl'),
                    'cacheCount' => array(
                        'found' => 1000,
                        'placed' => 30,
                    ),
                ),
                10 => array(
                    'levelName' => _('crystal'),
                    'cacheCount' => array(
                        'found' => 2000,
                        'placed' => 40,
                    ),
                ),
            ),
        ),
    ), /* end of medal */
    MedalsContainer::CACHE_OWN => array(
        'name' => _('OwnGeocacher'),
        'description' => _('Medal for own caches activity'),
        'type' => MedalsController::MEDAL_TYPE_CACHES,
        'dateIntroduced' => '2005-01-01 00:00:01',
        'conditions' => array(
            'ocNodeId' => array(OcConfig::OCNODE_POLAND, OcConfig::OCNODE_BENELUX, OcConfig::OCNODE_ROMANIA),
            'cacheType' => array(
                GeoCache::TYPE_OWNCACHE,
            ),
            'logType' => \lib\Objects\GeoCache\GeoCacheLog::LOGTYPE_FOUNDIT,
            'placedCacheStatus' => array (GeoCache::STATUS_READY, GeoCache::STATUS_UNAVAILABLE),
            'cacheCountToAward' => array(
                1 => array(
                    'levelName' => _('paper'),
                    'cacheCount' => array(
                        'found' => 1,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => _('wooden'),
                    'cacheCount' => array(
                        'found' => 3,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => _('iron'),
                    'cacheCount' => array(
                        'found' => 5,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => _('beril'),
                    'cacheCount' => array(
                        'found' => 10,
                        'placed' => 1,
                    ),
                ),
                5 => array(
                    'levelName' => _('bronze'),
                    'cacheCount' => array(
                        'found' => 20,
                        'placed' => 1,
                    ),
                ),
                6 => array(
                    'levelName' => _('silver'),
                    'cacheCount' => array(
                        'found' => 40,
                        'placed' => 1,
                    ),
                ),
                7 => array(
                    'levelName' => _('gold'),
                    'cacheCount' => array(
                        'found' => 60,
                        'placed' => 1,
                    ),
                ),
                8 => array(
                    'levelName' => _('platinum'),
                    'cacheCount' => array(
                        'found' => 80,
                        'placed' => 1,
                    ),
                ),
                9 => array(
                    'levelName' => _('perl'),
                    'cacheCount' => array(
                        'found' => 100,
                        'placed' => 1,
                    ),
                ),
                10 => array(
                    'levelName' => _('crystal'),
                    'cacheCount' => array(
                        'found' => 200,
                        'placed' => 1,
                    ),
                ),
            ),
        ),
    ), /* end of medal */
    MedalsContainer::CACHE_MOVED => array(
        'name' => _('MovingGeocacher'),
        'description' => _('Medal for transport moving caches'),
        'type' => MedalsController::MEDAL_TYPE_CACHES,
        'dateIntroduced' => '2005-01-01 00:00:01',
        'conditions' => array(
            'ocNodeId' => array(OcConfig::OCNODE_POLAND, OcConfig::OCNODE_BENELUX, OcConfig::OCNODE_ROMANIA),
            'cacheType' => array(
                GeoCache::TYPE_MOVING,
            ),
            'logType' => \lib\Objects\GeoCache\GeoCacheLog::LOGTYPE_MOVED,
            'placedCacheStatus' => array (GeoCache::STATUS_READY, GeoCache::STATUS_UNAVAILABLE),
            'cacheCountToAward' => array(
                1 => array(
                    'levelName' => _('paper'),
                    'cacheCount' => array(
                        'found' => 1,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => _('wooden'),
                    'cacheCount' => array(
                        'found' => 3,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => _('iron'),
                    'cacheCount' => array(
                        'found' => 5,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => _('beril'),
                    'cacheCount' => array(
                        'found' => 10,
                        'placed' => 1,
                    ),
                ),
                5 => array(
                    'levelName' => _('bronze'),
                    'cacheCount' => array(
                        'found' => 20,
                        'placed' => 1,
                    ),
                ),
                6 => array(
                    'levelName' => _('silver'),
                    'cacheCount' => array(
                        'found' => 40,
                        'placed' => 1,
                    ),
                ),
                7 => array(
                    'levelName' => _('gold'),
                    'cacheCount' => array(
                        'found' => 60,
                        'placed' => 1,
                    ),
                ),
                8 => array(
                    'levelName' => _('platinum'),
                    'cacheCount' => array(
                        'found' => 80,
                        'placed' => 1,
                    ),
                ),
                9 => array(
                    'levelName' => _('perl'),
                    'cacheCount' => array(
                        'found' => 100,
                        'placed' => 1,
                    ),
                ),
                10 => array(
                    'levelName' => _('crystal'),
                    'cacheCount' => array(
                        'found' => 200,
                        'placed' => 1,
                    ),
                ),
            ),
        ),
    ), /* end of medal */
   MedalsContainer::CACHE_EVENT => array(
        'name' => _('SocialGeocacher'),
       'description' => _('Medal for event attendance'),
        'type' => MedalsController::MEDAL_TYPE_CACHES,
        'dateIntroduced' => '2005-01-01 00:00:01',
        'conditions' => array(
            'ocNodeId' => array(OcConfig::OCNODE_POLAND, OcConfig::OCNODE_BENELUX, OcConfig::OCNODE_ROMANIA),
            'cacheType' => array(
                GeoCache::TYPE_EVENT,
            ),
            'logType' => \lib\Objects\GeoCache\GeoCacheLog::LOGTYPE_ATTENDED,
            'placedCacheStatus' => array (GeoCache::STATUS_READY, GeoCache::STATUS_ARCHIVED, GeoCache::STATUS_UNAVAILABLE),
            'cacheCountToAward' => array(
                1 => array(
                    'levelName' => _('paper'),
                    'cacheCount' => array(
                        'found' => 1,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => _('wooden'),
                    'cacheCount' => array(
                        'found' => 3,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => _('iron'),
                    'cacheCount' => array(
                        'found' => 6,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => _('beril'),
                    'cacheCount' => array(
                        'found' => 6,
                        'placed' => 1,
                    ),
                ),
                5 => array(
                    'levelName' => _('bronze'),
                    'cacheCount' => array(
                        'found' => 9,
                        'placed' => 2,
                    ),
                ),
                6 => array(
                    'levelName' => _('silver'),
                    'cacheCount' => array(
                        'found' => 9,
                        'placed' => 3,
                    ),
                ),
                7 => array(
                    'levelName' => _('gold'),
                    'cacheCount' => array(
                        'found' => 12,
                        'placed' => 4,
                    ),
                ),
                8 => array(
                    'levelName' => _('platinum'),
                    'cacheCount' => array(
                        'found' => 12,
                        'placed' => 5,
                    ),
                ),
                9 => array(
                    'levelName' => _('perl'),
                    'cacheCount' => array(
                        'found' => 15,
                        'placed' => 7,
                    ),
                ),
                10 => array(
                    'levelName' => _('crystal'),
                    'cacheCount' => array(
                        'found' => 20,
                        'placed' => 10,
                    ),
                ),
            ),
        ),
    ), /* end of medal */
    MedalsContainer::OLDGEOCACHER => array(
        'name' => _('OldGeocacher'),
        'description' => _('Medal for long-time relation to opencaching'),
        'type' => MedalsController::MEDAL_TYPE_OLDGEOCACHER,
        'dateIntroduced' => '2005-01-01 00:00:01',
        'conditions' => array(
            //'ocNodeId' => array(OcConfig::OCNODE_POLAND, OcConfig::OCNODE_BENELUX, OcConfig::OCNODE_ROMANIA),
            'cacheCountToAward' => array( /* in this case it is months. but to keep integrity I used  cacheCountToAward key name*/
                1 => array(
                    'levelName' => _('paper'),
                    'months' => 1
                ),
                2 => array(
                    'levelName' => _('wooden'),
                    'months' => 3
                ),
                3 => array(
                    'levelName' => _('iron'),
                    'months' => 6
                ),
                4 => array(
                    'levelName' => _('beril'),
                    'months' => 12
                ),
                5 => array(
                    'levelName' => _('bronze'),
                    'months' => 24
                ),
                6 => array(
                    'levelName' => _('silver'),
                    'months' => 36
                ),
                7 => array(
                    'levelName' => _('gold'),
                    'months' => 48
                ),
                8 => array(
                    'levelName' => _('platinum'),
                    'months' => 60
                ),
                9 => array(
                    'levelName' => _('perl'),
                    'months' => 72
                ),
                10 => array(
                    'levelName' => _('crystal'),
                    'months' => 96
                ),
            ),
        ),
    ), /* end of medal */
    MedalsContainer::REGION_BIESZCZADY => array(
        'name' => _('Bieszczady'),
        'description' => _('Medal for activity in Beskid Niski and Bieszczady'),
        'type' => MedalsController::MEDAL_TYPE_REGION,
        'dateIntroduced' => '2005-01-01 00:00:01',
        'conditions' => array(
            'cacheType' => array(
                GeoCache::TYPE_TRADITIONAL,
                GeoCache::TYPE_MULTICACHE,
                GeoCache::TYPE_QUIZ,
                GeoCache::TYPE_OTHERTYPE,
                GeoCache::TYPE_EVENT
            ),
            'cacheLocation' => array(
                'code3' => 'PL32',
            ),
            'minimumAltitude' => 400,
            'cacheCountToAward' => array(
                1 => array(
                    'levelName' => _('paper'),
                    'cacheCount' => array(
                        'found' => 1,
                        'placed' => 0,
                    ),
                ),
                2 => array(
                    'levelName' => _('wooden'),
                    'cacheCount' => array(
                        'found' => 10,
                        'placed' => 0,
                    ),
                ),
                3 => array(
                    'levelName' => _('iron'),
                    'cacheCount' => array(
                        'found' => 20,
                        'placed' => 0,
                    ),
                ),
                4 => array(
                    'levelName' => _('beril'),
                    'cacheCount' => array(
                        'found' => 40,
                        'placed' => 1,
                    ),
                ),
                5 => array(
                    'levelName' => _('bronze'),
                    'cacheCount' => array(
                        'found' => 80,
                        'placed' => 2,
                    ),
                ),
                6 => array(
                    'levelName' => _('silver'),
                    'cacheCount' => array(
                        'found' => 160,
                        'placed' => 5,
                    ),
                ),
                7 => array(
                    'levelName' => _('gold'),
                    'cacheCount' => array(
                        'found' => 320,
                        'placed' => 10,
                    ),
                ),
                8 => array(
                    'levelName' => _('platinum'),
                    'cacheCount' => array(
                        'found' => 640,
                        'placed' => 25,
                    ),
                ),
                9 => array(
                    'levelName' => _('perl'),
                    'cacheCount' => array(
                        'found' => 1000,
                        'placed' => 40,
                    ),
                ),
                10 => array(
                    'levelName' => _('crystal'),
                    'cacheCount' => array(
                        'found' => 1300,
                        'placed' => 64,
                    ),
                ),
            ),
        ),
    ), /* end of medal */

);

