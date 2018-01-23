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
    public static function getLatestCaches($limit, $offset=null)
    {

        $db = self::db();
        list($limit, $offset) = $db->quoteLimitOffset($limit, $offset);

        $rs = $db->multiVariableQuery(
            "SELECT
                u.user_id, u.username,
                c.name, c.longitude, c.latitude, c.wp_oc,
                c.country, c.type, c.status,
                IF((c.date_hidden > c.date_created), c.date_hidden,c.date_created) AS date,
                cl.*
            FROM caches AS c
                LEFT JOIN cache_location AS cl USING (cache_id)
                LEFT JOIN user AS u USING (user_id)
            WHERE c.type <> :1
                AND c.status = :2
                AND c.date_hidden   <= NOW()
                AND c.date_created  <= NOW()
            ORDER BY
                IF( (c.date_hidden > c.date_created), c.date_hidden, c.date_created) DESC,
                c.cache_id DESC
            LIMIT $offset, $limit", GeoCache::TYPE_EVENT, GeoCache::STATUS_READY);

        $result = [];
        while($row = $db->dbResultFetch($rs)){
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

    public static function getTopRatedCachesCount($activeOnly=false)
    {
        if($activeOnly){
            $countedStatuses = implode(',',[
                GeoCache::STATUS_READY
            ]);
        }else{
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

    public static function getAllCachesCount($activeOnly=false)
    {

        if($activeOnly){
            $countedStatuses = implode(',',[
                GeoCache::STATUS_READY
            ]);
        }else{
            $countedStatuses = implode(',',[
                GeoCache::STATUS_ARCHIVED,
                GeoCache::STATUS_UNAVAILABLE,
                GeoCache::STATUS_READY
            ]);
        }

        return self::db()->simpleQueryValue(
            "SELECT COUNT(*) FROM caches WHERE status IN ($countedStatuses)", 0);
    }

    public static function getNewCachesCount($fromLastDays){

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
                date_hidden > DATE_SUB(NOW(), INTERVAL $days day)
                OR date_hidden > DATE_SUB(NOW(), INTERVAL $days day)
            )", 0);
    }

    /**
     * Return array of Geocaches based on given cache Ids
     * @param array $cacheIds
     */
    public static function getGeocachesById(array $cacheIds, array $fieldsArr)
    {
        $db = self::db();

        $cacheIdsStr = implode(',', $cacheIds);
        $fields = implode(',', $fieldsArr);

        $rs = $db->simpleQuery(
            "SELECT $fields
            FROM caches WHERE cache_id IN ($cacheIdsStr)");

        return $db->dbResultFetchAll($rs);
    }

}

