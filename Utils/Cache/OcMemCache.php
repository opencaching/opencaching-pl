<?php

namespace Utils\Cache;

use Exception;
use Utils\Debug\Debug;
use src\Models\OcConfig\OcConfig;


/**
 * This is just wrapper for apcu.
 *
 * PLEASE NOTE: that PDO object can't be serialized and every "oc-object" h
 * as reference PDO instance. It is handled in BaseObject class as
 * prepare/restore(...)Serialization methods.
 *
 */
class OcMemCache
{
    public static $debug = false;

    /**
     * Fetch from cache or create by callback-creator if there is no such entry
     *
     * @param string $key - should be a __CLASS__ of saved object
     * @param integer $ttl - ttl at which this var should be saved in cache [sec]
     * @param callable $generator - function used for creation of the value if it is not found in cache
     * @return mixed - requested var
     */
    public static function getOrCreate($key, $ttl, callable $generator)
    {
        $var = self::get($key);
        if ($var === false) {
            $var = call_user_func($generator);
            self::store($key, $ttl, $var);
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
        self::store(
            $key,
            $ttl,
            $var = $creatorCallback()
        );

        return $var;
    }

    /**
     * Store value in cache
     *
     * @param string $key - should be a __CLASS__ of saved object
     * @param integer $ttl - ttl at which this var should be saved in cache [sec]
     * @param mixed $var
     */
    public static function store($key, $ttl, $var)
    {
        if ($var === null) {
            // null would be returned as false, see get()
            $var = '[OcMemCache null]';
        }
        if ($var !== false) {
            // false is the same as "not in cache" - no need to store
            try {
                apcu_store(self::getPrefix() . $key, $var, $ttl);
            } catch (Exception $e) {
                Debug::errorLog("Can't serialize object! error: ".$e->getMessage()." ");
            }
        }
    }

    /**
     * Fetch from cache if given entry is present. Includes workaround for
     * some rare apcu bugs(?) where we got a null return value; see
     * https://github.com/opencaching/opencaching-pl/pull/1811
     *
     * @param string $key - should be a __CLASS__ of saved object
     * @return mixed - requested entry value or false if there is no such entry
     */
    public static function get($key)
    {
        $value = apcu_fetch(self::getPrefix() . $key);
        if ($value === null) {
            $value = false;
        } elseif ($value == '[OcMemCache null]') {
            $value = null;
        }
        if (self::$debug) {
            echo "$key: ";
            var_dump($value);
            echo "<br />";
        }
        return $value;
    }

    private static function getPrefix()
    {
        return OcConfig::getShortSiteName() . '_';
    }
}
