<?php

namespace lib\Objects\GeoCache;


use lib\Objects\BaseObject;

class MultiCacheQueries extends BaseObject
{

    /**
     * EVENTS NOT INCLUDED!
     * @param unknown $limit
     */
    public static function getLatestCaches($limit)
    {

        $db = self::db();
        list($limit, $offset) = $db->quoteLimitOffset($limit, 0);

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

        return $db->dbResultFetchAll($rs);
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
                AND c.date_hidden >= NOW()
            ORDER BY
                c.date_hidden ASC
            LIMIT $offset, $limit", GeoCache::TYPE_EVENT, GeoCache::STATUS_READY);

        return $db->dbResultFetchAll($rs);
    }


}

