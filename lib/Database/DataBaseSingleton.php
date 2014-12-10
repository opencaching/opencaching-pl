<?php

namespace lib\Database;

/**
 * Description of DataBaseSingleton
 *
 * @author Łza
 */
final class DataBaseSingleton
{

    private static $dataBase = null;

    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new DataBaseSingleton();
        }
        return self::$dataBase;
    }

    /**
     * Private ctor so nobody else can instance it
     */
    private function __construct($debug = false)
    {
        self::$dataBase = new \dataBase($debug);
    }

}
