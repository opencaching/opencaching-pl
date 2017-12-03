<?php

namespace Utils\Cache;


/**
 * This is just wrapper fpr apc/apcu now,
 * but maybe we change to something else later...
 */
class OcMemCache
{
    /**
     * Fetch from cache or create by callback-creator if there is no such entry
     *
     * @param string $key - should be a __CLASS__ of saved object
     * @param integer $ttl - ttl at which this var should be saved in cache
     * @param callable $creatorCallback
     * @return mixed - requested var
     */
    public static function getOrCreate($key, $ttl, callable $creatorCallback)
    {
        //TODO: it should be just wrapper for apc_entry
        //      but there is no such func. in APCu 4.*
        //return apc_entry($key, $creatorCalback, $ttl);

        if( ($var = apcu_fetch($key)) === FALSE ){
            $var = call_user_func($creatorCallback);
            apcu_store($key, $var, $ttl);
        }

        return $var;
    }

    /**
     * Create object by callback-creator and save it to the cache
     * with given $key on given ttl; Return created value.
     *
     * @param string $key - shoud be a __CLASS__ of object
     * @param integer $ttl - ttl at which this var should be saved in cache
     * @param callable $creatorCallback
     * @return mixed - requested var
     */
    public static function refreshAndReturn($key, $ttl, callable $creatorCallback)
    {
        $var = $creatorCallback();
        apcu_store($key, $var, $ttl);
        return $var;
    }
}

