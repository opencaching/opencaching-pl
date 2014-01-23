<?php
require_once  __DIR__.'/db.php';
/* =====================================================================================
* Funkcja sprawdzająca czy skrzynka jest znaleziona przez użytkownika
*
* dane wejściowe:
* id skrzynki
* id zalogowanego użytkownika
*
* zwraca true lub false
*
 ===================================================================================== */

function is_cache_found ($cache_id, $user_id) {
    $q = 'SELECT user_id FROM cache_logs WHERE cache_id = '. $cache_id . ' AND user_id = '. $user_id . ' AND type = 1';
    $db = new dataBase;
    $db->simpleQuery($q);
    $rec = $db->dbResultFetch();

    //$sql     = ( sql('SELECT user_id FROM cache_logs WHERE cache_id = '. $cache_id . ' AND user_id = '. $user_id . ' AND type = 1') );
    //$rec     = sql_fetch_array($sql);
    if (isset ($rec['user_id'])) {
        return true;
    }
    else {
        return false;
        #return $rec['user_id'];
    }
}
/* =====================================================================================
* Funkcja sprawdzająca czy użytkownik uczestniczył w wydarzeniu
*
* dane wejściowe:
* id skrzynki
* id zalogowanego użytkownika
*
* zwraca true lub false
*
 ===================================================================================== */

function is_event_attended ($cache_id, $user_id) {
    $q = 'SELECT user_id FROM cache_logs WHERE cache_id = '. $cache_id . ' AND user_id = '. $user_id . ' AND type = 7';
    $db = new dataBase;
    $db->simpleQuery($q);
    $rec = $db->dbResultFetch();

  //  $sql     = ( sql('SELECT user_id FROM cache_logs WHERE cache_id = '. $cache_id . ' AND user_id = '. $user_id . ' AND type = 7') );
  //  $rec     = sql_fetch_array($sql);
    if (isset ($rec['user_id'])) {
        return true;
    }
    else {
        return false;
        #return $rec['user_id'];
    }
}



?>
