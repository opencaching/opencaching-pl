<?php

namespace lib\Objects\GeoCache;

class CacheTitled
{
    
    public static function isTitled($cacheId)
    {
        $queryPt = 'SELECT ratio FROM cache_titled WHERE cache_id=:1';
        $db = \lib\Database\DataBaseSingleton::Instance();
        $db->multiVariableQuery($queryPt, $cacheId);
        
        return $db->rowCount();
    }
    
}

?>