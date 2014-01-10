<?php
$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/db.php';
require_once __DIR__.'/../lib/common.inc.php';
require_once __DIR__.'/powerTrailBase.php';
require_once __DIR__.'/powerTrailController.php';
db_disconnect();

$pt = new powerTrailController($usr);
$pt->run();
// $ptDbRow = $pt->getPowerTrailDbRow();
// $ptOwners = $pt->getPtOwners();		

if(isset($_REQUEST['choseFinalCaches'])) $choseFinalCaches = true; else $choseFinalCaches = false;
print displayAllCachesOfPowerTrail($pt->getAllCachesOfPt(), $pt->getPowerTrailCachesUserLogsByCache(), $choseFinalCaches);



function displayAllCachesOfPowerTrail($pTrailCaches, $powerTrailCachesUserLogsByCache, $choseFinalCaches) 
{
	if(count($pTrailCaches) == 0) return '<br /><br />'.tr('pt082');	
		
	$statusIcons = array (
		1 => '/tpl/stdstyle/images/log/16x16-published.png',
		2 => '/tpl/stdstyle/images/log/16x16-temporary.png',
		3 => '/tpl/stdstyle/images/log/16x16-trash.png',
		5 => '/tpl/stdstyle/images/log/16x16-need-maintenance.png',
	);	

	$statusDesc = array(
		1 => tr('pt141'),
		2 => tr('pt142'),
		3 => tr('pt143'),
		5 => tr('pt144'),
	);

	$cacheTypesIcons = getCacheTypesIcons();
	$foundCacheTypesIcons = getFoundCacheTypesIcons($cacheTypesIcons);
	
	$cacheRows = '<table class="ptCacheTable" align="center" width="90%"><tr>
		<th>'.tr('pt075').'</th>
		<th>'.tr('pt076').'</th>';
	if($choseFinalCaches) $cacheRows .= '<th></th>';
	$cacheRows .= 
	'	<th>'.tr('pt077').'</th>
		<th><img src="tpl/stdstyle/images/log/16x16-found.png" /></th>
		<th>'.tr('pt078').'</th>
		<th><img src="images/rating-star.png" /></th>
	</tr>';
	$totalFounds = 0;
	$totalTopRatings = 0;
	$bgcolor = '#ffffff';
	$cachetypes = array (1 => 0,2 => 0,3 => 0,4 => 0,5 => 0,6 => 0,7 => 0,8 => 0,9 => 0,10 => 0,);
	$cacheSize = array (2 => 0,3 => 0,4 => 0,5 => 0,6 => 0,7 => 0,);
	unset($_SESSION['geoPathCacheList']);
	foreach ($pTrailCaches as $rowNr => $cache) {
		$_SESSION['geoPathCacheList'][] = $cache['cache_id'];
		$totalFounds += $cache['founds'];
		$totalTopRatings += $cache['topratings'];
		$cachetypes[$cache['type']]++;
		$cacheSize[$cache['size']]++;
		 // powerTrailController::debug($cache); exit;
		if($bgcolor == '#eeeeff') $bgcolor = '#ffffff'; else $bgcolor = '#eeeeff'; 
		if($cache['isFinal']) {
			$bgcolor = '#000000';
			$fontColor = '<font color ="#ffffff">';
		} else {
			$fontColor = '';
		}
		$cacheRows .= '<tr bgcolor="'.$bgcolor.'">';
		//display icon found/not found depend on current user
		if (isset($powerTrailCachesUserLogsByCache[$cache['cache_id']])) $cacheRows .= '<td align="center"><img src="tpl/stdstyle/images/'.$foundCacheTypesIcons[$cache['type']].'" /></td>';
		else $cacheRows .= '<td align="center"><img src="tpl/stdstyle/images/'.$cacheTypesIcons[$cache['type']].'" /></td>';
		//cachename, username
		$cacheRows .= '<td><b><a href="'.$cache['wp_oc'].'">'.$fontColor.$cache['name'].'</b></a> ('.$cache['username'].') ';
		if($cache['isFinal']) $cacheRows .= '<span class="finalCache">'.tr('pt148').'</span>';
		
		$cacheRows .= '</td>';
		//chose final caches
		if($choseFinalCaches){
			if($cache['isFinal']) $checked = 'checked = "checked"';
			else $checked = '';
			$cacheRows .= '<td><span class="ownerFinalChoice"><input type="checkbox" id="fcCheckbox'.$cache['cache_id'].'" onclick="setFinalCache('.$cache['cache_id'].')" '.$checked.' /></span></td>';
		}
		//status
		$cacheRows .= '<td align="center"><img src="'.$statusIcons[$cache['status']].'" title="'.$statusDesc[$cache['status']].'"/></td>';
		//FoundCount
		$cacheRows .= '<td align="center">'.$fontColor.$cache['founds'].'</td>';
		//score, toprating
		$cacheRows .= '<td align="center">'.ratings($cache['score'], $cache['votes']).'</td>';
		$cacheRows .= '<td align="center">'.$fontColor.$cache['topratings'].'</td>';
		
		'</tr>';
	}	
	$cacheRows .= '
	<tr bgcolor="#efefff">
		<td></td>
		<td style="font-size: 9px;">'.tr('pt085').'</td>
		<td></td>
		<td align="center" style="font-size: 9px;">'.$totalFounds.'</td>
		<td></td>
		<td align="center" style="font-size: 9px;">'.$totalTopRatings.'</td>
	</tr>
	</table>';
	$restCaches = $cachetypes[4] + $cachetypes[5] + $cachetypes[6] + $cachetypes[8] + $cachetypes[9] + $cachetypes[10];
	$countCaches = count($pTrailCaches);
	$restCachesPercent = round(($restCaches * 100)/$countCaches);
	foreach ($cachetypes as $key => $value) {
		$cachePercent[$key] = round(($value * 100)/$countCaches);
	}
	foreach ($cacheSize as $key => $value) {
		$cacheSizePercent[$key] = round(($value * 100)/$countCaches);
	}
	$img = '<table align="center"><tr><td align=center width="50%">'.tr('pt107').'<br /><img src="http://chart.apis.google.com/chart?chs=350x100&chd=t:'.$cachetypes[2].','.$cachetypes[3].','.$cachetypes[7].','.$cachetypes[1].','.$restCaches.'&cht=p3&chl='.$cachetypes[2].'|'.$cachetypes[3].'|'.$cachetypes[7].'|'.$cachetypes[1].'|'.$restCaches.'&chco=00aa00|FFEB0D|0000cc|cccccc|eeeeee&&chdl=%20'.tr('pt108').'%20('.$cachePercent[2].'%)|'.tr('pt109').'%20('.$cachePercent[3].'%)|'.tr('pt110').'%20('.$cachePercent[7].'%)|'.urlencode(tr('pt111')).'%20('.$cachePercent[1].'%)|'.urlencode(tr('pt112')).'%20('.$restCachesPercent.'%)" /></td>';
	$img .= '<td align=center width="50%">'.tr('pt106').'<br /><img src="http://chart.apis.google.com/chart?chs=350x100&chd=t:'.$cacheSize[2].','.$cacheSize[3].','.$cacheSize[4].','.$cacheSize[5].','.$cacheSize[6].'&cht=p3&chl=%20'.$cacheSize[2].'|'.$cacheSize[3].'|'.$cacheSize[4].'|'.$cacheSize[5].'|'.$cacheSize[6].'&chco=0000aa|00aa00|aa0000|aaaa00|00aaaa&&chdl='.urlencode(tr('pt113')).'%20('.$cacheSizePercent[2].'%)|'.urlencode(tr('pt114')).'%20('.$cacheSizePercent[3].'%)|'.urlencode(tr('pt115')).'%20('.$cacheSizePercent[4].'%)|'.urlencode(tr('pt116')).'%20('.$cacheSizePercent[5].'%)|'.urlencode(tr('pt117')).'%20('.$cacheSizePercent[6].'%)" /></td></tr></table><br /><br />';
	// powerTrailController::debug($pTrailCaches);
	// exit;
	return $img.$cacheRows;
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