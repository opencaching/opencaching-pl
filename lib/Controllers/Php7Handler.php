<?php

namespace lib\Controllers;

/**
 * Class php7Handler
 * This clas contain some functions to be diffrent handlig under php 7 vs older versions.
 *
 * To be removed when everyone move to php7
 * @author Łza
 */
class Php7Handler
{
    private static $isPhp7;

    /**
     * apcu library 5.x for php 7 have no more functions apc_*
     * Uses apcu_* instead
     */
    public static function apc_store($key, $var, $ttl)
    {
        if(self::isPhp7()){
            return apcu_store($key, $var, $ttl);
        } else {
            return apc_store($key, $var, $ttl);
        }
    }

    /**
     * apcu library 5.x for php 7 have no more functions apc_*
     * Uses apcu_* instead
     */
    public static function apc_fetch($cacheKey)
    {
        if(self::isPhp7()){
            return apcu_fetch($cacheKey);
        } else {
            return apc_fetch($cacheKey);
        }
    }
    /**
     * boolval is present in php > 5.5
     * @param unknown $val
     */
    public static function Boolval($val)
    {
        if(self::isPhp7()){
            return boolval($val);
        } else {
            return (bool) $val;
        }
    }

    /**
     * Throwable interface is a new feature of PHP7
     *
     * @param object $e
     * @return boolean - true if given object is implements Throwable intercae
     */
    public static function isThrowableInstance($e)
    {
        if(self::isPhp7()){
            return $e instanceof \Throwable;
        }else{
            return false;
        }
    }


    private static function isPhp7()
    {
        if(self::$isPhp7 === null){
            if(substr(PHP_VERSION, 0, 1) >= 7){
                self::$isPhp7 = true;
            } else {
                self::$isPhp7 = false;
            }
        }
        return self::$isPhp7;
    }
}
