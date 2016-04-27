<?php

namespace Config;

class XExampleConfig extends CommonConfig
{
    protected static $loadedClassName = null;
    protected static $configInstance = null;

    protected static function getModuleName(){
        return 'XExample';
    }

    /**
     * Here we define default value of simple var exampleVar1
     */
    public static function getXExampleVar1(){
        return static::getConfigInstance()->getXExampleVar1();
    }

    /**
     * Here we define default value of simple var exampleVar2
     */
    public static function getXExampleVar2(){
        return static::getConfigInstance()->getXExampleVar2();
    }

    /**
     * Here we define default value of array var exampleVar2
     */
    public static function getXExampleVar3(){
        return static::getConfigInstance()->getXExampleVar3();
    }


}