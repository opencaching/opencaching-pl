<?php

namespace lib\Objects\Stats;


use lib\Objects\BaseObject;
use lib\Objects\Stats\TotalStats\BasicStats;
use lib\Objects\User\MultiUserStats;
use Utils\Cache\OcMemCache;
use Utils\Text\Formatter;
use lib\Objects\CacheSet\CacheSet;
use lib\Objects\GeoCache\MultiCacheStats;
use lib\Objects\GeoCache\MultiLogStats;

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
        $periodDays = 130;
        $basicStats = new BasicStats();

        $basicStats->totalCaches = Formatter::number(MultiCacheStats::getAllCachesCount());
        $basicStats->activeCaches = Formatter::number(MultiCacheStats::getAllCachesCount(true));
        $basicStats->topRatedCaches = Formatter::number(MultiCacheStats::getTopRatedCachesCount());

        $basicStats->latestCaches = MultiCacheStats::getNewCachesCount($periodDays);

        $basicStats->activeCacheSets =Formatter::number(CacheSet::getActiveCacheSetsCount());

        $basicStats->totalUsers = Formatter::number(MultiUserStats::getActiveUsersCount());

        $basicStats->newUsers = MultiUserStats::getUsersRegistratedCount($periodDays);

        $basicStats->totalSearches = Formatter::number(MultiLogStats::getTotalSearchesNumber());

        $basicStats->latestSearches = Formatter::number(
            MultiLogStats::getLastSearchesCount($periodDays));

        $basicStats->latestRecomendations = Formatter::number(
            MultiLogStats::getLastRecomendationsCount($periodDays));

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
    public $topRatedCaches;
    public $latestCaches;
    public $activeCacheSets;
    public $totalUsers;
    public $newUsers;
    public $totalSearches;
    public $latestSearches;
    public $latestRecomendations;
}