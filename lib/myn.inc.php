<?php
require_once  __DIR__.'/db.php';

/*
set of functions used in myneighborhood and myn files
*/



function is_cache_found ($cache_id, $user_id) {
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
	$q = 'SELECT user_id FROM cache_logs WHERE cache_id =:v1 AND user_id =:v2 AND type = 1 AND Deleted=0';
	$db = new dataBase;
	$params['v1']['value'] = (integer) $cache_id;
    $params['v1']['data_type'] = 'integer';
    $params['v2']['value'] =(integer) $user_id;
    $params['v2']['data_type'] = 'integer';
    $db->paramQuery($q,$params);
	$rec = $db->dbResultFetch();
	unset($db);
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


function is_event_attended ($cache_id, $user_id) {
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
	$q = 'SELECT user_id FROM cache_logs WHERE cache_id =:v1 AND user_id =:v2 AND type = 7 AND Deleted=0'; 
	$db = new dataBase;
	$params['v1']['value'] = (integer) $cache_id;
    $params['v1']['data_type'] = 'integer';
    $params['v2']['value'] =(integer) $user_id;
    $params['v2']['data_type'] = 'integer';
    $db->paramQuery($q,$params);
	$rec = $db->dbResultFetch();
	unset($db);
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


// 2 functions copied from powerTrail.php - START:
		function getCacheTypesIcons() 
{
	$q = 'SELECT `id`, `icon_small` FROM `cache_type` WHERE 1';
	$db = new dataBase;
	$db->simpleQuery($q);
	$cacheTypesArr = $db->dbResultFetchAll();
	foreach ($cacheTypesArr as $cacheType) {
		$cacheTypesIcons[$cacheType['id']] = $cacheType['icon_small'];
	}
	return $cacheTypesIcons;
}

function getFoundCacheTypesIcons($cacheTypesIcons)
{
	foreach ($cacheTypesIcons as $id => $cacheIcon) {
		$tmp = explode('.', $cacheIcon);
		$tmp[0] = $tmp[0].'-found';
		$foundCacheTypesIcons[$id] = implode('.', $tmp);
	}
	// powerTrailController::debug($foundCacheTypesIcons);
	return $foundCacheTypesIcons;
}
		$CacheTypesIcons = getCacheTypesIcons();
		$foundCacheTypesIcons = getFoundCacheTypesIcons($CacheTypesIcons);	
		$cache_icon_folder = 'tpl/stdstyle/images/';
// 2 functions copied from powerTrail.php - END	


?>
