<?php

// include composer autoloader
require __DIR__.'/../vendor/autoload.php';

/**
 * All classes should be automatically loaded by Composer autoloader.
 * This autoloader is left as a fallback for classes which do not follow
 * standard conventions and might have been overlooked during migration.
 */
class ClassPathDictionary
{
    public static function getClassPath($className): ?string
    {
        $classPathArr = explode('\\', $className);

        if (count($classPathArr) === 1) {
            return null;
        }

        $fileName = array_pop($classPathArr).'.php';
        $classPath = __DIR__.'/../'.implode('/', $classPathArr);

        $fileToInclude = $classPath.'/'.$fileName;

        if (file_exists($fileToInclude)) {
            return $fileToInclude;
        }

        // If there is no such a file, try to find a file with lowercase filename.
        $fileToInclude = $classPath.'/'.lcfirst($fileName);

        if (file_exists($fileToInclude)) {
            return $fileToInclude;
        }

        return null;
    }
}

spl_autoload_register(function ($className) {
    $fileName = ClassPathDictionary::getClassPath($className);

    if (! is_null($fileName)) {
        include_once $fileName;
    }
});
