<?php
$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/db.php';
require_once __DIR__.'/../lib/common.inc.php';
require_once __DIR__.'/powerTrailBase.php';
require_once __DIR__.'/powerTrailController.php';

$pt = new powerTrailController($usr);
$pt->run();
// $ptDbRow = $pt->getPowerTrailDbRow();
// $ptOwners = $pt->getPtOwners();		


print displayAllCachesOfPowerTrail($pt->getAllCachesOfPt(), $pt->getPowerTrailCachesUserLogsByCache());



function displayAllCachesOfPowerTrail($pTrailCaches, $powerTrailCachesUserLogsByCache) 
{
	if(count($pTrailCaches) == 0) return '<br /><br />'.tr('pt082');	
		
	$statusIcons = array (
		1 => '/tpl/stdstyle/images/log/16x16-published.png',
		2 => '/tpl/stdstyle/images/log/16x16-temporary.png',
		3 => '/tpl/stdstyle/images/log/16x16-trash.png',
		5 => '/tpl/stdstyle/images/log/16x16-need-maintenance.png',
	);	

	$cacheTypesIcons = getCacheTypesIcons();
	// var_dump($cacheTypesIcons);
	$foundCacheTypesIcons = getFoundCacheTypesIcons($cacheTypesIcons);
	$cacheRows = '<table border=0><tr>
		<th>'.tr('pt075').'</th>
		<th>'.tr('pt076').'</th>
		<th>'.tr('pt077').'</th>
		<th><img src="tpl/stdstyle/images/log/16x16-found.png" /></th>
		<th>'.tr('pt078').'</th>
		<th><img src="images/rating-star.png" /></th>
	</tr>';
	foreach ($pTrailCaches as $rowNr => $cache) {
		 // powerTrailController::debug($cache); exit;
		$cacheRows .= '<tr>';
		//display icon found/not found depend on current user
		if (isset($powerTrailCachesUserLogsByCache[$cache['cache_id']])) $cacheRows .= '<td><img src="tpl/stdstyle/images/'.$foundCacheTypesIcons[$cache['type']].'" /></td>';
		else $cacheRows .= '<td><img src="tpl/stdstyle/images/'.$cacheTypesIcons[$cache['type']].'" /></td>';
		//cachename, username
		$cacheRows .= '<td><b><a href="'.$cache['wp_oc'].'">'.$cache['name'].'</b></a> ('.$cache['username'].')</td>';
		//status
		$cacheRows .= '<td><img src="'.$statusIcons[$cache['status']].'" /></td>';
		//FoundCount
		$cacheRows .= '<td>'.$cache['founds'].'</td>';
		//score, toprating
		$cacheRows .= '<td>'.ratings($cache['score'], $cache['votes']).'</td>';
		$cacheRows .= '<td>'.$cache['topratings'].'</td>';
		
		'</tr>';
	}	
	$cacheRows .= '</table>';
	// powerTrailController::debug($pTrailCaches);
	// exit;
	return $cacheRows;
}

function ratings($score, $votes){
	if ($votes < 3) return '<span style="color: gray">'.tr('pt083').'</span>';
	if ($score > 2)                return '<span style="color: green">'.tr('pt070').'</span>';
	if ($score > 1 && $score<=2)   return '<span style="color: green">'.tr('pt071').'</span>';
	if ($score > 0 && $score<=1)   return '<span style="color: green">'.tr('pt072').'</span>';
	if ($score > -1 && $score<=0)  return '<span style="color: red">'.tr('pt073').'</span>';
	if ($score < -1)               return '<span style="color: red">'.tr('pt074').'</span>';
}

/**
 * prepare array contain small icons for diffrent cachetypes
 */
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
?>