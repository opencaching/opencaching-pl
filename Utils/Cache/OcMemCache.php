<?php

namespace Utils\Cache;

use Exception;
use Utils\Debug\Debug;


/**
 * This is just wrapper for apc/apcu.
 *
 * PLEASE NOTE: that PDO object can't be serialized and every "oc-object" h
 * as reference PDO instance. It is handled in BaseObject class as
 * prepare/restore(...)Serialization methods.
 *
 */
class OcMemCache
{
    /**
     * Fetch from cache or create by callback-creator if there is no such entry
     *
     * @param string $key - should be a __CLASS__ of saved object
     * @param integer $ttl - ttl at which this var should be saved in cache
     * @param callable $generator - function used for creation of the value if it is not found in cache
     * @return mixed - requested var
     */
    public static function getOrCreate($key, $ttl, callable $generator)
    {
        // apcu_entry was added in APCu version 5.1
        //if(function_exists('apcu_entry')){
        //    return apcu_entry($key, $generator, $ttl);
        //}

        // this is older installation - there is no apcu_entry function
        // TODO: these lines can be safetly removed when all nodes moved to PHP7 (APCu 5)
        if( ($var = apcu_fetch($key)) === false ){
            $var = call_user_func($generator);
            try{
                apcu_store($key, $var, $ttl);
            }catch(Exception $e){
                Debug::errorLog("Can't serialize object");
            }
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

        try{
            apcu_store($key, $var, $ttl);
        }catch(Exception $e){
            Debug::errorLog("Can't serialize object");
        }

        return $var;
    }

    /**
     * Fetch from cache if given entry is present
     *
     * @param string $key - should be a __CLASS__ of saved object
     * @return mixed - requested entry value or false if there is no such entry
     */
    public static function get($key)
    {
        return apcu_fetch($key);
    }

}

