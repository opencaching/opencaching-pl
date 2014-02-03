<?php

class ClassPathDictionary
{
    /**
     * example:
     * 'className' => 'path/to/filename.php',
     *
     * !!! please preserve alphabetical order. !!!
     */
    private static $classDictionary = array(
        'cache' => 'lib/cache.php',
        'dataBase' => 'lib/db.php',
        'GeoKretyApi' => 'GeoKretyAPI.php',
        'GetRegions' => 'GetRegions.php',
        'myninc' => 'lib/myn.inc.php',
        'powerTrailBase' => 'powerTrail/powerTrailBase.php',
        'powerTrailController' => 'powerTrail/powerTrailController.php',
    );

    public static function getClassPath($className)
    {
        return __DIR__.'/../'. self::$classDictionary[$className];
    }
}
