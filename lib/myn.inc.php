<?php

use Utils\Database\OcDb;
class myninc
{

    /** =====================================================================================
     * Funkcja sprawdzająca czy skrzynka jest znaleziona przez użytkownika
     *
     * dane wejściowe:
     * id skrzynki
     * id zalogowanego użytkownika
     *
     * zwraca true lub false
     *
      ===================================================================================== */
    private static function is_cache_found($cache_id, $user_id)
    {
        $q = 'SELECT user_id FROM cache_logs WHERE cache_id =:v1 AND user_id =:v2 AND type IN(1,7) AND Deleted=0';
        $db = OcDb::instance();
        $params['v1']['value'] = (integer) $cache_id;
        $params['v1']['data_type'] = 'integer';
        $params['v2']['value'] = (integer) $user_id;
        $params['v2']['data_type'] = 'integer';

        $s = $db->paramQuery($q, $params);
        $rec = $db->dbResultFetch($s);

        if (isset($rec['user_id'])) {
            return true;
        } else {
            return false;
        }
    }

    /**  =====================================================================================
     * Funkcja sprawdzająca czy użytkownik uczestniczył w wydarzeniu
     *
     * dane wejściowe:
     * id skrzynki
     * id zalogowanego użytkownika
     *
     * zwraca true lub false
     *
      ===================================================================================== */
    private static function is_event_attended($cache_id, $user_id)
    {
        $q = 'SELECT user_id FROM cache_logs WHERE cache_id =:v1 AND user_id =:v2 AND type = 7 AND Deleted=0';

        $db = OcDb::instance();

        $params['v1']['value'] = (integer) $cache_id;
        $params['v1']['data_type'] = 'integer';
        $params['v2']['value'] = (integer) $user_id;
        $params['v2']['data_type'] = 'integer';
        $s = $db->paramQuery($q, $params);
        $rec = $db->dbResultFetch($s);

        if (isset($rec['user_id'])) {
            return true;
        } else {
            return false;
        }
    }

    public static function checkCacheStatusByUser($record, $userId)
    {
        $cacheTypesIcons = cache::getCacheIconsSet();
        if ( isset($record['user_id']) && $record['user_id'] == $userId ) {
            return $cacheTypesIcons[$record['cache_type']]['iconSet'][1]['iconSmallOwner'];
        } elseif (isset($record['cache_id']) && self::is_cache_found($record['cache_id'], $userId)) {
            return $cacheTypesIcons[$record['cache_type']]['iconSet'][1]['iconSmallFound'];
        } else {
            return $cacheTypesIcons[$record['cache_type']]['iconSet'][1]['iconSmall'];
        }
    }

}

?>
