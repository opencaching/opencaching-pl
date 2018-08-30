<?php

namespace lib\Objects\GeoCache;

use lib\Objects\BaseObject;

/**
 * This class should contains mostly static, READ-ONLY queries
 * used to generates statistics etc. around caches db table
 */
class MultiCacheStats extends BaseObject
{

    /**
     * EVENTS NOT INCLUDED!
     * @param integer $limit
     */
    public static function getLatestCaches($limit, $offset = null)
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
        while($row = $db->dbResultFetch($rs)){
            $row['location'] = CacheLocation::fromDbRowFactory($row);
            $result[] = $row;
        }

        return $result;
    }

    public static function getTopRatedCachesCount($activeOnly = false)
    {
        if ($activeOnly) {
            $countedStatuses = implode(',',[
                GeoCache::STATUS_READY
            ]);
        } else {
            $countedStatuses = implode(',',[
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
            $countedStatuses = implode(',',[
                GeoCache::STATUS_READY
            ]);
        } else {
            $countedStatuses = implode(',',[
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

        $days = (int) $fromLastDays;

        $countedStatuses = implode(',',[
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

    /**
     * Return array of Geocaches based on given cache Ids
     * @param array $cacheIds
     */
    public static function getGeocachesById(array $cacheIds, array $fieldsArr = null)
    {
        if (empty($cacheIds)) {
            return [];
        }

        $db = self::db();

        if (empty($fieldsArr)) {
            $fieldsArr = ['cache_id','status','type','wp_oc','user_id',
                'latitude','longitude','name'];
        }

        $cacheIdsStr = implode(',', $cacheIds);
        $fields = implode(',', $fieldsArr);

        $rs = $db->simpleQuery(
            "SELECT $fields
            FROM caches WHERE cache_id IN ($cacheIdsStr)");

        return $db->dbResultFetchAll($rs);
    }

    /**
     * Returns array of GeoCache objects - newest caches (including events)
     *
     * @param int $limit
     * @param int $offset
     * @return GeoCache[]
     */
    public static function getAllLatestCaches($limit, $offset = null)
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

}