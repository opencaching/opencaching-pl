<?php

namespace lib\Objects\GeoCache;

use Utils\Database\OcDb;

class CacheTitled
{

    public static function isTitled($cacheId)
    {
        $queryPt = 'SELECT ratio FROM cache_titled WHERE cache_id=:1';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($queryPt, $cacheId);
        return $db->rowCount($s);
    }

}

