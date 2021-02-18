<?php

// include composer autoloader
if(file_exists(__DIR__.'/../vendor/autoload.php')){
    require __DIR__.'/../vendor/autoload.php';
}

/**
 * Class autoloading solution.
 *
 * Classes can be loaded automatically without the need to use include statements.
 * When creating a new class, use namespace leading to class php file.
 * The script will extract its path and filename from the namespace and class name.
 *
 * Example:
 *
 *   Place file ClassName.php in directory lib/DirectoryName:
 *
 *     <?php
 *     namespace lib\DirectoryName;
 *
 *     class ClassName { }
 *
 *   Then this class may be used by calling:
 *     $variable = new \lib\DirectoryName\ClassName();
 */
class ClassPathDictionary
{

    /**
     * When creating new class, use namespace leading to class php file.
     * This enables classes to be automatically loaded by getClassPath() method.
     *
     * Use this property if using namespace is not possible for any reason.
     *
     * Please preserve alphabetical order.
     */
    private static $classDictionary = array(
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

            // Okapi has lowercase filenames convention. If there is no such file,
            // try to find file with lowercase filename.
            $fileToInclude = $classPath . '/' . lcfirst($fileName);

            if( file_exists($fileToInclude) ){
                // Check if classname exists
                return $fileToInclude;
            }

            trigger_error(__METHOD__.": ERROR: Trying to load unknown class: $className");
            return null;
        }

        // Try to look for this class in local dictionary.
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
