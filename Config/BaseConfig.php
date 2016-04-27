<?php
/**
 * This file contains configuration base for OC configuration ( meta connfiguration :) )
 *
 * This file can't have node-specific version (like BaseDefaultsPL or something like that)!
 *
 * Please note:
 *      DON'T EDIT THIS FILE to override default values for your server.
 *
 *      Instead of that locate ExampleLocal class in file ExampleLocal.php
 *      and there add your overrides.
 */

namespace Config;

class BaseConfig extends CommonConfig
{
    protected static $loadedClassName = null;
    protected static $configInstance = null;

    protected static function getModuleName(){
        return 'Base';
    }

    /**
     * This method is overloaded because this is the only *Config class
     * which not load node-specific configs
     */
    protected static function getUsedConfigNodeId(){
        return '';
    }

    public static function getConfigNodeId(){
        return static::getConfigInstance()->getConfigNodeId();
    }

    public static function useNewConfig(){
        return static::getConfigInstance()->useNewConfig();
    }

}