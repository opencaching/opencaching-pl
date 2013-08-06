<?php 

/**
 *  powerTrail.php
 *  ------------------------------------------------------------------------------------------------
 *  Power Trails in opencaching
 *  this is display file. for API check dir powerTrail
 *  ------------------------------------------------------------------------------------------------
 *  @author: Andrzej 'Łza' Woźniak [wloczynutka@gmail.com]
 *  
 *  
 *  
 */

// variables required by opencaching.pl
global $lang, $rootpath, $usr;

//prepare the templates and include all neccessary
require_once('lib/common.inc.php');

// check if module is swithed on in settings.inc.php
if (!isset($powerTrailModuleSwitchOn) || $powerTrailModuleSwitchOn !== 1) header("location: $absolute_server_URI");

//Preprocessing
if ($error == false)
{
	// czy user zalogowany ?
	if ($usr == false)
	{
		// nie zalogowany wiec przekierowanie na strone z logowaniem
		$target = urlencode(tpl_get_current_page());
		tpl_redirect('login.php?target='.$target);
	}
	else
	{
		// wskazanie pliku z kodem html ktory jest w tpl/stdstyle/
		$tplname = 'powerTrail';
		
		include_once('powerTrail/powerTrailController.php');
		include_once('powerTrail/powerTrailMenu.php');

		tpl_set_var('displayCreateNewPowerTrailForm', 'none');
		tpl_set_var('displayUserCaches', 'none');
		tpl_set_var('displayPowerTrails', 'none');
		tpl_set_var('displaySelectedPowerTrail', 'none');
		tpl_set_var('PowerTrailCaches', 'none');
		tpl_set_var('language4js', $lang);
		tpl_set_var('powerTrailName', '');
		tpl_set_var('powerTrailLogo', '');
		tpl_set_var('mainPtInfo', '');

		$ptMenu = new powerTrailMenu;
		tpl_set_var("powerTrailMenu", buildPowerTrailMenu($ptMenu->getPowerTrailsMenu()));

		$pt = new powerTrailController($usr);
		$result = $pt->run();
		$actionPerformed = $pt->getActionPerformed();

		switch ($actionPerformed) {
			case 'createNewSerie':
				tpl_set_var('displayCreateNewPowerTrailForm', 'block');
				break;
			case 'selectCaches':
				//$userPowerTrails = $pt->getUserPowerTrails();
				tpl_set_var("keszynki",displayCaches($result, $pt->getUserPowerTrails()));
				tpl_set_var('displayUserCaches', 'block');
				break;
			case 'showAllSeries':
				tpl_set_var('PowerTrails', displayPTrails($pt->getpowerTrails()));
				tpl_set_var('displayPowerTrails', 'block');
				break;
			case 'showSerie':
				$ptDbRow = $pt->getPowerTrailDbRow();
				$ptOwners = $pt->getPtOwners();
				if ($ptDbRow['status'] != 0 || array_key_exists($usr['userid'], $ptOwners)) {
					tpl_set_var('displaySelectedPowerTrail', 'block');
					tpl_set_var('powerTrailName', ' - '.$ptDbRow['name']);
					if ($ptDbRow['image'] == '') $image = 'tpl/stdstyle/images/blue/powerTrailGenericLogo.png';
					else $image = $ptDbRow['image'];
					tpl_set_var('powerTrailLogo', $image);
					tpl_set_var('PowerTrailCaches', displayAllCachesOfPowerTrail($pt->getAllCachesOfPt(), $pt->getPowerTrailCachesUserLogsByCache()));
					tpl_set_var('powerTrailserStats', displayPowerTrailserStats($pt->getCountCachesAndUserFoundInPT()));
					tpl_set_var('powerTrailLogo', '');
					powerTrailController::debug($pt->getPowerTrailDbRow(), __LINE__);
				} else {
					tpl_set_var('mainPtInfo', tr('pt018'));
				}
				break;
			default:
				tpl_set_var('PowerTrails', displayPTrails($pt->getpowerTrails()));
				tpl_set_var('displayPowerTrails', 'block');
				break;
		}
		
		// exit;


		tpl_BuildTemplate();
	}
}

// budujemy kod html ktory zostaje wsylany do przegladraki
//$Opensprawdzacz->endzik();

function buildPowerTrailMenu($menuArray)
{
	$menu = '<table bgcolor=#bbbbff><tr>';
	foreach ($menuArray as $key => $menuItem) {
		$menu .= '<td><a href="'.$menuItem['script'].'?ptAction='.$menuItem['action'].'">'.$menuItem['name'].'</a></td>';
	}
	$menu .= '</tr></table>';
	return $menu;
}

function displayCaches($caches, $pTrails)
{
	// powerTrailController::debug($caches);
	$rows = '';
	foreach ($caches as $key => $cache) {
		$ptSelector = '<select onchange="ajaxAddCacheToPT('.$cache['cache_id'].');" id="ptSelectorForCache'.$cache['cache_id'].'"><option value="-1">---</option>';
		foreach ($pTrails as $ptKey => $pTrail) {
			if($cache['PowerTrailId'] == $pTrail['id']) $ptSelector .= '<option selected value='.$pTrail['id'].'>'.$pTrail['name'].'</option>';
			else $ptSelector .= '<option value='.$pTrail['id'].'>'.$pTrail['name'].'</option>';
		}
		$ptSelector .= '</select>';
		// var_dump($cache);
		$rows .= '<tr><td><a href="'.$cache['wp_oc'].'">'.$cache['wp_oc'].'</a></td><td>'. $cache['name'].'</td><td>'.$ptSelector.'</td>
		<span id="cacheInfo'.$cache['cache_id'].'" style="display: none ">zapamiętano!</span></tr>';
	}
	return $rows;
}

function displayPTrails($pTrails)
{
	$result = '';	
	foreach ($pTrails as $pTkey => $pTrail) {
		$result .= '<tr>'.
		'<td><b><a href="powerTrail.php?ptAction=showSerie&ptrail='.$pTrail["id"].'">'.$pTrail["name"]           .'</a></b></td>'.
		'<td>'.$pTrail["centerLatitude"]    .'</td>'.
		'<td>'.$pTrail["centerLongitude"]   .'</td>'.
		'<td>'.$pTrail["type"]              .'</td>'.
		'<td>'.$pTrail["status"]            .'</td>'.
		'<td>'.$pTrail["dateCreated"]       .'</td>'.
		'<td>'.$pTrail["cacheCount"]        .'</td>'.
		'</tr>';
		// var_dump($pTrail);
	}
	return $result;
}

function displayAllCachesOfPowerTrail($pTrailCaches, $powerTrailCachesUserLogsByCache) 
{
	
	$cacheTypesIcons = getCacheTypesIcons();
	$foundCacheTypesIcons = getFoundCacheTypesIcons($cacheTypesIcons);
	$cacheRows = '';
	foreach ($pTrailCaches as $rowNr => $cache) {
		$cacheRows .= '<tr>';
		if (isset($powerTrailCachesUserLogsByCache[$cache['cache_id']])) $cacheRows .= '<td><img src="tpl/stdstyle/images/'.$foundCacheTypesIcons[$cache['type']].'" title="'.$powerTrailCachesUserLogsByCache[$cache['cache_id']]['text'].'"/></td>';
		else $cacheRows .= '<td><img src="tpl/stdstyle/images/'.$cacheTypesIcons[$cache['type']].'" /></td>';
		$cacheRows .= '<td><a href="'.$cache['wp_oc'].'">'.$cache['name'].'</a></td>'.
		'</tr>';
	}	
	
	// powerTrailController::debug($pTrailCaches);
	// exit;
	return $cacheRows;
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
	// powerTrailController::debug($cacheTypesArr);
	// powerTrailController::debug($cacheTypesIcons);
	
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

function displayPowerTrailserStats($stats)
{
	$stats2display = $stats['cachesFoundByUser'] * 100 / $stats['totalCachesCountInPowerTrail'] .'% ('  .tr('pt017') .' ' . $stats['cachesFoundByUser'].' '.tr('pt016').' '.$stats['totalCachesCountInPowerTrail'].' '.tr('pt014');
	// powerTrailController::debug($stats);
	return $stats2display;
}
?>