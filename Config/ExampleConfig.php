<?php

namespace Config;

class ExampleConfig extends CommonConfig
{
    protected static $loadedClassName = null;
    protected static $configInstance = null;

    protected static function getModuleName(){
        return 'Example';
    }

    /**
     * Here we define default value of simple var exampleVar1
     */
    public static function getExampleVar1(){
        return static::getConfigInstance()->getExampleVar1();
    }

    /**
     * Here we define default value of simple var exampleVar2
     */
    public static function getExampleVar2(){
        return static::getConfigInstance()->getExampleVar2();
    }

    /**
     * Here we define default value of array var exampleVar2
     */
    public static function getExampleVar3(){
        return static::getConfigInstance()->getExampleVar3();
    }


}