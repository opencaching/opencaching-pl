<?php

class myninc
{

    public static function checkCacheStatusByUser($record, $userId)
    {
        $cacheTypesIcons = cache::getCacheIconsSet();
        if (isset($record['user_id']) && $record['user_id'] == $userId) {
            return $cacheTypesIcons[$record['cache_type']]['iconSet'][1]['iconSmallOwner'];
        } elseif (isset($record['cache_id']) && self::is_cache_found($record['cache_id'], $userId)) {
            return $cacheTypesIcons[$record['cache_type']]['iconSet'][1]['iconSmallFound'];
        } else {
            return $cacheTypesIcons[$record['cache_type']]['iconSet'][1]['iconSmall'];
        }
    }
}