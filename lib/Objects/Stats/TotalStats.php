<?php

namespace lib\Objects\Stats;


use lib\Objects\BaseObject;
use lib\Objects\Stats\TotalStats\BasicStats;
use Utils\Cache\OcMemCache;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\GeoCache\GeoCacheLog;
use Utils\Text\Formatter;

/**
 * This class provides general statsistics
 *
 */

class TotalStats extends BaseObject
{

    public function __construct()
    {
        parent::__construct();
    }

    public static function getBasicTotalStats($dropCache=null)
    {

        if(!$dropCache){
            return OcMemCache::getOrCreate(
                BasicStats::KEY, BasicStats::TTL, function(){
                    return self::contructBasicTotalStats();
            });

        }else{
            return OcMemCache::refreshAndReturn(
                BasicStats::KEY, BasicStats::TTL, function(){
                    return self::contructBasicTotalStats();
            });
        }
    }

    private static function contructBasicTotalStats()
    {
        $basicStats = new BasicStats();

        $basicStats->totalCaches = Formatter::number(GeoCache::getAllCachesCount());
        $basicStats->activeCaches = Formatter::number(GeoCache::getAllCachesCount(true));
        $basicStats->founds = Formatter::number(GeoCacheLog::getTotalFoundsNumber());
        $basicStats->activeUsers = Formatter::number(UserStats::getActiveUsersCount());


        return $basicStats;
    }

}


namespace lib\Objects\Stats\TotalStats;

/**
 * Simple object which provides basic total stats
 */
class BasicStats
{
    const KEY = __CLASS__;
    const TTL = 6*60*60; //sec.

    public $totalCaches;
    public $activeCaches;
    public $founds;
    public $activeUsers;

}