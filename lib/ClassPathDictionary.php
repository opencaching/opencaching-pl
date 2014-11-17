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
        'sendEmail' => 'powerTrail/sendEmail.php',
        'Kint' => 'lib/kint/Kint.class.php',
        'userInputFilter' => 'lib/userInputFilters/userInputFilter.php'
    );

    public static function getClassPath($className)
    {
        return __DIR__.'/../'. self::$classDictionary[$className];
    }
}

spl_autoload_register(function ($className) {
    include_once ClassPathDictionary::getClassPath($className);
});
