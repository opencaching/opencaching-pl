<?php
namespace src\Models\Stats;

use src\Utils\Cache\OcMemCache;
use src\Utils\Text\Formatter;
use src\Models\BaseObject;
use src\Models\CacheSet\CacheSet;
use src\Models\GeoCache\MultiCacheStats;
use src\Models\GeoCache\MultiLogStats;
use src\Models\Stats\TotalStats\BasicStats;
use src\Models\User\MultiUserQueries;

/**
 * This class provides general statsistics of service
 */
class TotalStats extends BaseObject
{

    public function __construct()
    {
        parent::__construct();
    }

    public static function getBasicTotalStats($dropCache = null)
    {
        if (! $dropCache) {
            return OcMemCache::getOrCreate(BasicStats::KEY, BasicStats::TTL, function () {
                return self::contructBasicTotalStats();
            });
        } else {
            return OcMemCache::refreshAndReturn(BasicStats::KEY, BasicStats::TTL, function () {
                return self::contructBasicTotalStats();
            });
        }
    }

    private static function contructBasicTotalStats()
    {
        $periodDays = 30;
        $basicStats = new BasicStats();

        $basicStats->totalCaches = Formatter::number(MultiCacheStats::getAllCachesCount());
        $basicStats->activeCaches = Formatter::number(MultiCacheStats::getAllCachesCount(true));
        $basicStats->topRatedCaches = Formatter::number(MultiCacheStats::getTopRatedCachesCount());

        $basicStats->latestCaches = MultiCacheStats::getNewCachesCount($periodDays);

        $basicStats->activeCacheSets = Formatter::number(CacheSet::getActiveCacheSetsCount());

        $basicStats->totalUsers = Formatter::number(MultiUserQueries::getActiveUsersCount());

        $basicStats->newUsers = MultiUserQueries::getUsersRegistratedCount($periodDays);

        $basicStats->totalSearches = Formatter::number(MultiLogStats::getTotalSearchesNumber());

        $basicStats->latestSearches = Formatter::number(MultiLogStats::getLastSearchesCount($periodDays));

        $basicStats->latestRecomendations = Formatter::number(MultiLogStats::getLastRecommendationsCount($periodDays));

        $basicStats->lastYearActiveUsers = Formatter::number(MultiLogStats::getUsersCountWithAtLeastOneLog(365));

        $basicStats->createdAt = time();
        return $basicStats;
    }
}

namespace src\Models\Stats\TotalStats;

/**
 * Simple object which provides basic total stats
 */
class BasicStats
{

    const KEY = __CLASS__."_rand1"; // update key each time class is changed to invalidate cache

    const TTL = 6 * 60 * 60;       // sec.
    public $createdAt;

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
    public $lastYearActiveUsers;
}
