<?php

namespace src\Libs\JpGraph;

use src\Utils\Debug\Debug;

/**
 * This class based on code from:
 * https://raw.githubusercontent.com/ztec/JpGraph/4.x/lib/JpGraph.php
 */
class JpGraphLoader
{
    const JPGRAPH_VERSION = '4.3.1';

    static $loaded = false;
    static $modules = [];

    static function load()
    {
        if (self::$loaded !== true) {
            include_once __DIR__ . '/' . self::JPGRAPH_VERSION . '/jpgraph.php';
            self::$loaded = true;
        }
    }

    static function module($moduleName)
    {
        self::load();
        if (!in_array($moduleName, self::$modules)) {
            $path = __DIR__ . '/' . self::JPGRAPH_VERSION . '/jpgraph_' . $moduleName . '.php';

            if (file_exists($path)) {
                include_once $path;
            } else {
                Debug::errorLog('ERROR: Trying to load unknown jpGraph module: ' . $moduleName);
            }
        }
    }

}
