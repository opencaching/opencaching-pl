<?php

namespace src\Models\GeoCache;

use src\Models\BaseObject;
use src\Models\OcConfig\OcConfig;
use src\Models\User\User;
use src\Utils\Cache\OcMemCache;

/**
 * This class should contains mostly static, READ-ONLY queries
 * used to generates statistics etc. around caches db table
 */
class MultiCacheStats extends BaseObject
{

    /**
     * EVENTS NOT INCLUDED!
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getLatestCaches($limit, $offset = 0)
    {

        $db = self::db();
        list($limit, $offset) = $db->quoteLimitOffset($limit, $offset);

        $rs = $db->multiVariableQuery(
            "SELECT
                u.user_id, u.username,
                c.name, c.longitude, c.latitude, c.wp_oc,
                c.country, c.type, c.status,
                c.date_published AS date,
                cl.*
            FROM caches AS c
                LEFT JOIN cache_location AS cl USING (cache_id)
                LEFT JOIN user AS u USING (user_id)
            WHERE c.type <> :1
                AND c.status = :2
                AND c.date_published <= NOW()
            ORDER BY
                date_published DESC,
                c.cache_id DESC
            LIMIT $offset, $limit", GeoCache::TYPE_EVENT, GeoCache::STATUS_READY);

        $result = [];
        while ($row = $db->dbResultFetch($rs)) {
            $row['location'] = CacheLocation::fromDbRowFactory($row);
            $result[] = $row;
        }

        return $result;
    }

    public static function getIncomingEvents($limit)
    {
        $db = self::db();
        list($limit, $offset) = $db->quoteLimitOffset($limit, 0);

        $rs = $db->multiVariableQuery(
            "SELECT
                u.user_id, u.username,
                c.name, c.longitude, c.latitude, c.wp_oc,
                c.country, c.type, c.status,
                c.date_hidden AS date,
                cl.*
            FROM caches AS c
                LEFT JOIN cache_location AS cl USING (cache_id)
                LEFT JOIN user AS u USING (user_id)
            WHERE c.type = :1
                AND c.status = :2
                AND c.date_hidden >= DATE(NOW())
            ORDER BY
                c.date_hidden ASC
            LIMIT $offset, $limit", GeoCache::TYPE_EVENT, GeoCache::STATUS_READY);


        $result = [];
        while ($row = $db->dbResultFetch($rs)) {
            $row['location'] = CacheLocation::fromDbRowFactory($row);
            $result[] = $row;
        }

        return $result;
    }

    public static function getTopRatedCachesCount($activeOnly = false)
    {
        if ($activeOnly) {
            $countedStatuses = implode(',', [
                GeoCache::STATUS_READY
            ]);
        } else {
            $countedStatuses = implode(',', [
                GeoCache::STATUS_ARCHIVED,
                GeoCache::STATUS_UNAVAILABLE,
                GeoCache::STATUS_READY
            ]);
        }

        return self::db()->multiVariableQueryValue(
            "SELECT COUNT(*) FROM caches
            WHERE status IN ($countedStatuses)
            AND score >= :1", 0, GeoCache::MIN_SCORE_OF_RATING_5);
    }

    public static function getAllCachesCount($activeOnly = false)
    {

        if ($activeOnly) {
            $countedStatuses = implode(',', [
                GeoCache::STATUS_READY
            ]);
        } else {
            $countedStatuses = implode(',', [
                GeoCache::STATUS_ARCHIVED,
                GeoCache::STATUS_UNAVAILABLE,
                GeoCache::STATUS_READY
            ]);
        }

        return self::db()->simpleQueryValue(
            "SELECT COUNT(*) FROM caches WHERE status IN ($countedStatuses)", 0);
    }

    public static function getNewCachesCount($fromLastDays)
    {

        $days = (int)$fromLastDays;

        $countedStatuses = implode(',', [
            GeoCache::STATUS_ARCHIVED,
            GeoCache::STATUS_UNAVAILABLE,
            GeoCache::STATUS_READY
        ]);

        return self::db()->simpleQueryValue(
            "SELECT COUNT(*) FROM caches
            WHERE status IN ($countedStatuses)
            AND (
                date_published > DATE_SUB(NOW(), INTERVAL $days day)
            )", 0);
    }

    public static function getNewCachesCountMonthly($year)
    {
        $db = self::db();
        $year = (int)$year;

        $countedStatuses = implode(',', [
            GeoCache::STATUS_ARCHIVED,
            GeoCache::STATUS_UNAVAILABLE,
            GeoCache::STATUS_READY
        ]);

        $rs = $db->multiVariableQuery(
            "SELECT COUNT(*) AS newCaches, adm3 AS region
            FROM caches JOIN cache_location USING (cache_id)
            WHERE status IN ($countedStatuses)
            AND YEAR(date_published) = :1
            GROUP BY adm3
            ORDER BY newCaches DESC", $year);

        return $db->dbFetchAsKeyValArray($rs, "region", "newCaches");

    }

    /**
     * Return array of Geocaches based on given cache Ids
     * @param array $cacheIds
     * @param array $fieldsArr
     * @return array
     */
    public static function getGeocachesDataById(array $cacheIds, array $fieldsArr = [])
    {
        if (empty($cacheIds)) {
            return [];
        }

        $db = self::db();

        if (empty($fieldsArr)) {
            $fieldsArr = ['cache_id', 'status', 'type', 'wp_oc', 'user_id',
                'latitude', 'longitude', 'name'];
        }

        $cacheIdsStr = implode(',', $cacheIds);
        $fields = implode(',', $fieldsArr);

        $rs = $db->simpleQuery(
            "SELECT $fields
            FROM caches WHERE cache_id IN ($cacheIdsStr)");

        return $db->dbResultFetchAll($rs);
    }

    public static function getGeocachesById(array $cacheIds)
    {
        if (empty($cacheIds)) {
            return [];
        }

        $db = self::db();

        $cacheIdsStr = implode(',', $cacheIds);
        $limit = count($cacheIds);

        $rs = $db->simpleQuery(
            "SELECT * FROM caches WHERE cache_id IN ($cacheIdsStr) LIMIT $limit");

        $result = [];
        while ($data = $db->dbResultFetch($rs)) {
            $cache = new GeoCache();
            $cache->loadFromRow($data);
            $result[] = $cache;
        }
        return $result;
    }

    /**
     * Returns array of GeoCache objects - latest caches (including events)
     *
     * @param int $limit
     * @param int $offset
     * @return GeoCache[]
     */
    public static function getAllLatestCaches($limit, $offset = 0)
    {
        list ($limit, $offset) = self::db()->quoteLimitOffset($limit, $offset);

        $stmt = self::db()->multiVariableQuery("
            SELECT `cache_id`
            FROM `caches`
            WHERE `status` = :1
                AND `date_published` IS NOT NULL
            ORDER BY
                `date_published` DESC,
                `cache_id` DESC
            LIMIT $offset, $limit", GeoCache::STATUS_READY);

        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return GeoCache::fromCacheIdFactory($row['cache_id']);
        });
    }

    /**
     * Returns cacheId's array of all latest ready for search caches in OC country.
     * Include only caches published in last 365 days
     * Events are not included
     *
     * @return array
     */
    private static function getLatestNationalCachesId()
    {
        return OcMemCache::getOrCreate(__CLASS__ . ':getLatestNationalCaches', 60 * 60, function () {
            $countriesStr = '';
            foreach (OcConfig::getSitePrimaryCountriesList() as $item) {
                $countriesStr .= "'". self::db()->quoteString($item) ."', ";
            }
            $countriesStr = rtrim($countriesStr, ', ');
            $stmt = self::db()->multiVariableQuery("
            SELECT `cache_id`
            FROM `caches`
            WHERE `status` = :1
                AND `date_published` IS NOT NULL
                AND `date_published` > NOW() - INTERVAL 365 DAY
                AND `type` != :2
                AND `country` IN (". $countriesStr.")
            ORDER BY
                `date_published` DESC,
                `cache_id` DESC",
                GeoCache::STATUS_READY,
                GeoCache::TYPE_EVENT);
            return self::db()->dbFetchOneColumnArray($stmt, 'cache_id');
        });
    }

    /**
     * Returns count of latest caches in OC country
     * Include only caches published in last 365 days
     *
     * @return int
     */
    public static function getLatestNationalCachesCount()
    {
        return count(self::getLatestNationalCachesId());
    }

    /**
     * Returns GeoCache[] of latest caches in OC country if site supports only one country
     * Include only caches published in last 365 days
     * Exclude caches ignored by User
     *
     * @param User|null $user
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getLatestNationalCachesForUserOneCountry($user, $limit, $offset = 0)
    {
        $cachesId = self::getLatestNationalCachesId();
        $cachesCount = self::getLatestNationalCachesCount();
        $result = [];

        $index = $offset;
        while ($index < $cachesCount && $index < $offset + $limit) {
            $cache = GeoCache::fromCacheIdFactory($cachesId[$index]);
            // Remove ignored caches
            if (!is_null($user) && $cache->isIgnoredByUser($user)) {
                unset($cache);
                $index++;
                continue;
            }
            $result[] = $cache;
            unset($cache);
            $index++;
        }

        return $result;
    }

    /**
     * Returns array of all latest ready for search GeoCaches in all supported by site OC countries.
     * Include only caches published in last 365 days
     * Exclude caches ignored by User
     *
     * Structure of returned array (example):
     * ['NL' => [GeoCacheObj, GeoCacheObj, GeoCacheObj],  'LU' => [GeoCacheObj]]
     *
     * @param User|null $user
     * @return array
     */
    public static function getLatestNationalCachesForUserMultiCountries($user = null)
    {
        $cachesList = self::getLatestNationalCachesId();
        $result = [];
        foreach ($cachesList as $cache) {
            $cacheObj = GeoCache::fromCacheIdFactory($cache);
            // Remove ignored caches
            if (!is_null($user) && $cacheObj->isIgnoredByUser($user)) {
                unset($cacheObj);
                continue;
            }
            $result[$cacheObj->getCountry()][] = $cacheObj;
            unset($cacheObj);
        }

        if (!empty($cachesList)) {
            ksort($cachesList);
        }

        return $result;
    }

    /**
     * Returns array of all latest ready for search GeoCaches outside of OC country.
     * Include only caches published in last 365 days, limit - max 300 caches
     * Exclude caches ignored by User
     *
     * Structure of returned array (example):
     * ['ES' => [GeoCacheObj, GeoCacheObj, GeoCacheObj],  'ZA' => [GeoCacheObj]]
     *
     * @param User|null $user
     * @return array
     */
    public static function getLatestForeignCachesForUser($user = null)
    {
        $cachesList = OcMemCache::getOrCreate(__CLASS__ . ':getLatestForeignCaches', 60 * 60, function () {
            $countriesStr = '';
            foreach (OcConfig::getSitePrimaryCountriesList() as $item) {
                $countriesStr .= "'". self::db()->quoteString($item) ."', ";
            }
            $countriesStr = rtrim($countriesStr, ', ');
            $stmt = self::db()->multiVariableQuery("
            SELECT `cache_id`, `country`
            FROM `caches`
            WHERE `status` = :1
                AND `date_published` IS NOT NULL
                AND `date_published` > NOW() - INTERVAL 365 DAY
                AND `country` NOT IN (".$countriesStr.")
            ORDER BY
                `date_published` DESC,
                `cache_id` DESC
            LIMIT 300",
                GeoCache::STATUS_READY);
            return self::db()->dbResultFetchAll($stmt);
        });

        $result = [];
        foreach ($cachesList as $cache) {
            $cacheObj = GeoCache::fromCacheIdFactory($cache['cache_id']);
            // Remove ignored caches
            if (!is_null($user) && $cacheObj->isIgnoredByUser($user)) {
                unset($cacheObj);
                continue;
            }
            $result[$cache['country']][] = $cacheObj;
            unset($cacheObj);
        }

        if (!empty($cachesList)) {
            ksort($cachesList);
        }

        return $result;
    }

    /**
     * Returns array of incoming events (max 300)
     * Exclude events ignored by User
     *
     * Structure of returned array (example):
     * ['Poland > mazowieckie' => [GeoCacheObj, GeoCacheObj, GeoCacheObj],  'Great Britain > Devon' => [GeoCacheObj]]
     *
     * @param User|null $user
     * @return array
     */
    public static function getLatestEventsForUser($user = null)
    {
        $eventList = OcMemCache::getOrCreate(__CLASS__ . ':getLatestEvents', 60 * 60, function () {
            $stmt = self::db()->multiVariableQuery("
            SELECT `cache_id`
            FROM `caches`
            WHERE `status` = :1
                AND `type` = :2
                AND `date_hidden` > NOW() - INTERVAL 2 DAY
            ORDER BY
                `date_hidden` DESC
            LIMIT 500",
                GeoCache::STATUS_READY,
                GeoCache::TYPE_EVENT);
            return self::db()->dbResultFetchAll($stmt);
        });

        $result = [];
        foreach ($eventList as $event) {
            $eventObj = GeoCache::fromCacheIdFactory($event['cache_id']);
            // Remove ignored events
            if (!is_null($user) && $eventObj->isIgnoredByUser($user)) {
                unset($eventObj);
                continue;
            }
            $result[$eventObj->getCacheLocationObj()->getLocationDesc(' &gt; ')][] = $eventObj;
            unset($eventObj);
        }

        if (!empty($eventList)) {
            ksort($eventList);
        }

        return $result;

    }

    /**
     * Returns cacheId's array of all titled caches in OC country.
     *
     * @return array
     */
    private static function getTitledCachesId()
    {
        return OcMemCache::getOrCreate(__CLASS__ . ':getTitledCachesId', 24 * 60 * 60, function () {
            $stmt = self::db()->multiVariableQuery("
            SELECT `cache_titled`.`cache_id` AS `cache_id`, `cache_titled`.`date_alg` AS `date`
            FROM `cache_titled`
            JOIN `caches` ON `caches`.`cache_id` = `cache_titled`.`cache_id`
            WHERE `status` = :1
            ORDER BY
                `cache_titled`.`date_alg` DESC
            LIMIT 500",
                GeoCache::STATUS_READY);
            return self::db()->dbResultFetchAll($stmt);
        });
    }

    /**
     * Returns count of titled caches
     *
     * @return int
     */
    public static function getTitledCount()
    {
        return count(self::getTitledCachesId());
    }

    /**
     * Returns array of titled caches
     * Array is sorted by titled date in reverse order
     *
     * Format of returned array:
     * [0 => ['date' => '2019-12-01', 'cache' => GeoCache], 1 => ['date' => '2019-11-01', 'cache' => GeoCache]]
     *
     * @param User|null $user
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getTitledCachesForUser($user, $limit, $offset = 0)
    {
        $cachesId = self::getTitledCachesId();
        $cachesCount = self::getTitledCount();
        $result = [];

        $index = $offset;
        while ($index < $cachesCount && $index < $offset + $limit) {
            $result[] = [
                'date' => $cachesId[$index]['date'],
                'cache' => GeoCache::fromCacheIdFactory($cachesId[$index]['cache_id'])
            ];
            $index++;
        }

        return $result;
    }

    /**
     * Returns array of the most recommended cachesId
     * Array is sorted by recommendation count in reverse order
     * Max 500 items
     *
     * @return array
     */
    private static function getRecommendedCachesId()
    {
        return OcMemCache::getOrCreate(__CLASS__ . ':getRecommendedCachesId', 24 * 60 * 60, function () {
            $stmt = self::db()->multiVariableQuery("
            SELECT `cache_id`
            FROM `caches`
            WHERE `status` = :1
                AND `topratings` > 0
            ORDER BY
                `topratings` DESC,
                `date_published` DESC
            LIMIT 500",
                GeoCache::STATUS_READY);
            return self::db()->dbFetchOneColumnArray($stmt, 'cache_id');
        });
    }

    /**
     * Returns count of recommended caches (max 500 - see LIMIT in getRecommendedCachesId() )
     *
     * @return int
     */
    public static function getRecommendedCount()
    {
        return count(self::getRecommendedCachesId());
    }

    /**
     * Returns GeoCache[] of the most recommended caches
     * Array is sorted by recommendation count in reverse order
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getRecommendedCaches($limit, $offset = 0)
    {
        $cachesId = self::getRecommendedCachesId();
        $cachesCount = self::getRecommendedCount();
        $result = [];

        $index = $offset;
        while ($index < $cachesCount && $index < $offset + $limit) {
            $result[] = GeoCache::fromCacheIdFactory($cachesId[$index]);
            $index++;
        }

        return $result;
    }

}
