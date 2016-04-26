<?php

namespace Config;

use Exception;
use ReflectionClass;

abstract class CommonConfig
{
    protected abstract static function getModuleName();

    protected static function getUsedConfigNodeId(){
        return BaseConfig::getConfigNodeId();
    }

    public static function getLoadedConfigName(){
        return static::$loadedClassName;
    }

    /**
     * returns the highest existing config file:
     *
     * @param string nodeId - node id as country code for example PL for OCPL
     * @return
     *      - Module-Local class name if exists
     *      - Module-Node-Defaults class name if exists
     *      - Module-Defaults otherwise
     */
    protected static function getModuleConfigClass(){

        $nodeId = static::getUsedConfigNodeId();
        $moduleName = static::getModuleName();

        $localClass      = $moduleName.'Local';
        $nodeClass       = $moduleName.'Defaults'.$nodeId;
        $defaultsClass   = $moduleName.'Defaults';


        //look for <$moduleName>Local file
        if(file_exists(__DIR__.'/'.$localClass.'.php')){
            //Local file exists - assume local class is present
            return $localClass;
        }

        if( ! empty( $nodeId) ){
            if(file_exists(__DIR__.'/'.$nodeClass.'.php')){
                //node-specific defaults file exists - assume node-specific defaults class is present
                return $nodeClass;
            }
        }

        if(file_exists(__DIR__.'/'.$defaultsClass.'.php')){
            //Defaults file exists - assume defaults class is present
            return $defaultsClass;
        }else{
            //no such defaults! Throw Error!
            static::error(new Exception("No defaults file for $defaultsClass found!"));
        }
    }

    protected static function getConfigInstance(){

        if (static::$configInstance === null) {

            $className = static::getModuleConfigClass();
            $className = 'Config\\'.$className;
            static::$loadedClassName = $className;

            static::$configInstance = new $className();
        }
        return static::$configInstance;
    }

    protected static function error(Exception $e){

        print_r($e); //TODO: create production solution!
        d($e);
    }

}
