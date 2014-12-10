<?php

/**
 *  Autoload class solution.
 *
 * Classes can be loaded automaticly without need of use include statements.
 * While new class creating, try use namespace, leading to class php file. Then script can extract
 * path and class filename by namespace.
 *
 * Example:
 *
 * place file Classname.php in directory lib/Directoryname:
 * <?php
 * namespace lib\Directoryname;
 * class Classname
 * {
 *    ... (class body)
 * }
 *
 * then to use this class it is enough to call:
 * $variable = new \lib\Directoryname\Classname();
 * and file is included automaticly.
 *
 */
class ClassPathDictionary
{

    /**

     * While new class creating, try use namespace, leading to class php file. Thanks to that class 
     * can be loaded automaticly witch function getClassPath().
     * 
     * use this method if using namespace is not possible any reason
     *
     * example:
     * 'className' => 'path/to/filename.php',
     *
     * !!! please preserve alphabetical order. !!!
     */
    private static $classDictionary = array(
        'cache' => 'lib/cache.php',
        'dataBase' => 'lib/Database/Db.php',
        'MyDB' => 'lib/Database/MyDB.php',
        'PasswordManager' => 'lib/passwordManager.php',
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
        $classPathArr = explode('\\', $className);
        if (isset($classPathArr[1])) { /* namespace solution */
            $classPath = __DIR__ . '/../';
            foreach ($classPathArr as $pathPiece) {
                $classPath .= $pathPiece . '/';
            }
            $classPath = substr($classPath, 0, -1) . '.php';
            return $classPath;
        }
        return __DIR__ . '/../' . self::$classDictionary[$className];
    }

}

spl_autoload_register(function ($className) {
    include_once ClassPathDictionary::getClassPath($className);
});
