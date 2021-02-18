<?php

// include composer autoloader
require __DIR__.'/../vendor/autoload.php';

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
    public static function getClassPath($className)
    {
        $classPathArr = explode('\\', $className);

        if (count($classPathArr) > 1) {
            $fileName = array_pop($classPathArr).'.php';
            $classPath = __DIR__.'/../'.implode('/', $classPathArr);

            $fileToInclude = $classPath.'/'.$fileName;

            if (file_exists($fileToInclude)) {
                return $fileToInclude;
            }

            // Okapi has lowercase filenames convention. If there is no such file,
            // try to find file with lowercase filename.
            $fileToInclude = $classPath.'/'.lcfirst($fileName);

            if (file_exists($fileToInclude)) {
                // Check if classname exists
                return $fileToInclude;
            }
        }

        trigger_error(__METHOD__.": ERROR: Trying to load unknown class: {$className}");
        return null;
    }
}

spl_autoload_register(function ($className) {
    $fileName = ClassPathDictionary::getClassPath($className);

    if (! is_null($fileName)) {
        include_once $fileName;
    }
});
