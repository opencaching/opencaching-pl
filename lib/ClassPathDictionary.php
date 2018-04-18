<?php

/* include composer autoloader */
if(file_exists(__DIR__ . '/../vendor/autoload.php')){
    require __DIR__ . '/../vendor/autoload.php';
}

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
     * can be loaded automatically with function getClassPath().
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
        'myninc' => 'lib/myn.inc.php',
        'powerTrailBase' => 'powerTrail/powerTrailBase.php',
        'powerTrailController' => 'powerTrail/powerTrailController.php',
        'sendEmail' => 'powerTrail/sendEmail.php',
    );

    public static function getClassPath($className)
    {
        $classPathArr = explode('\\', $className);

        if ( count($classPathArr) > 1) { /* namespace solution */

            $fileName = array_pop($classPathArr) . '.php';
            $classPath = __DIR__ . '/../' . implode('/', $classPathArr);

            $fileToInclude = $classPath . '/' . $fileName;
            if( file_exists($fileToInclude) ){
                return $fileToInclude;
            }

            // there is no such file - okapi has lowercase filenames convension
            // try to find file with lowercase filename
            $fileToInclude = $classPath . '/' . lcfirst($fileName);

            if( file_exists($fileToInclude) ){
                // check if classname exists
                return $fileToInclude;
            }

            trigger_error(__METHOD__.": ERROR: Trying to load unknown class: $className");
            return null;
        }

        // try to look for this class in local dictionary
        if( isset(self::$classDictionary[$className] ) ){
            $fileToInclude = __DIR__ . '/../' . self::$classDictionary[$className];
            if( file_exists($fileToInclude) ){
                return $fileToInclude;
            }else{
                trigger_error(__METHOD__.": ERROR: Class $className found in dictionary, but file is missing!");
            }
        }

        trigger_error(__METHOD__.": ERROR: Trying to load unknown class: $className");
        return null;
    }
}

spl_autoload_register(function ($className) {

    $classFile = ClassPathDictionary::getClassPath($className);
    if(!is_null($classFile))
        include_once $classFile;
});
