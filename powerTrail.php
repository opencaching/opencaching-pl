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
global $lang, $rootpath, $usr, $absolute_server_URI, $cookie;

//prepare the templates and include all neccessary
require_once('lib/common.inc.php');

$_SESSION['powerTrail']['userFounds'] = $usr['userFounds']; 

if(!$powerTrailModuleSwitchOn) {
	header("location: $absolute_server_URI");
}

$firePtMenu = true;
//Preprocessing
if ($error == false)
{
	if(isset($_REQUEST['sortBy']) || isset($_REQUEST['filter']) || isset($_REQUEST['sortDir'])) {
		saveCookie($cookie);
	} else {
		if ($cookie->is_set("ptSrBy")) $_REQUEST['sortBy'] = $cookie->get("ptSrBy"); else $_REQUEST['sortBy'] = 'cacheCount';
		if ($cookie->is_set("ptFltr")) $_REQUEST['filter'] = $cookie->get("ptFltr"); else $_REQUEST['filter'] = 0;
		if ($cookie->is_set("ptSrDr")) $_REQUEST['sortDir'] = $cookie->get("ptSrDr"); else $_REQUEST['sortDir'] = 'desc';
	}
		
		$tplname = 'powerTrail';
		if(!$usr && $hide_coords) {
			$mapControls = 0; 
			tpl_set_var('gpxOptionsTrDisplay', 'none');
		}else {
			$mapControls = 1;
			tpl_set_var('gpxOptionsTrDisplay', 'table-row');
		}
		include_once('powerTrail/powerTrailController.php');
		include_once('powerTrail/powerTrailMenu.php');
		if (isset($_SESSION['user_id'])) tpl_set_var('displayAddCommentSection', 'block'); else  tpl_set_var('displayAddCommentSection', 'none');
		tpl_set_var('nocachess', 'none');
		tpl_set_var('displayCreateNewPowerTrailForm', 'none');
		tpl_set_var('displayUserCaches', 'none');
		tpl_set_var('displayPowerTrails', 'none');
		tpl_set_var('displaySelectedPowerTrail', 'none');
		tpl_set_var('PowerTrailCaches', 'none');
		tpl_set_var('language4js', $lang);
		tpl_set_var('powerTrailName', '');
		tpl_set_var('powerTrailLogo', '');
		tpl_set_var('mainPtInfo', '');
		tpl_set_var('ptTypeSelector', displayPtTypesSelector('type'));
		tpl_set_var('displayToLowUserFound', 'none');
		tpl_set_var('ptMenu', 'block');
		tpl_set_var('mapOuterdiv', 'none');
		tpl_set_var('mapInit', 0);
		tpl_set_var('mapCenterLat', 0);
		tpl_set_var('mapCenterLon', 0);
		tpl_set_var('mapZoom', 15);
		tpl_set_var('zoomControl', $mapControls);
		tpl_set_var('scrollwheel',$mapControls);
		tpl_set_var('scaleControl',$mapControls);
		
		tpl_set_var('ptList4map', '[]');
		tpl_set_var('fullCountryMap', '1');
		tpl_set_var('googleMapApiKey', $googlemap_key);
		tpl_set_var('ocWaypoint', $oc_waypoint);
		tpl_set_var('commentsPaginateCount',powerTrailBase::commentsPaginateCount);	
		
		if(!$usr) tpl_set_var('ptMenu', 'none');
		$ptMenu = new powerTrailMenu($usr);
		tpl_set_var("powerTrailMenu", buildPowerTrailMenu($ptMenu->getPowerTrailsMenu()));

		$pt = new powerTrailController($usr);
		$result = $pt->run();
		$actionPerformed = $pt->getActionPerformed();
		switch ($actionPerformed) {
			case 'createNewSerie':
				if($usr['userFounds'] >= powerTrailBase::userMinimumCacheFoundToSetNewPowerTrail()){
					tpl_set_var('displayCreateNewPowerTrailForm', 'block');
				} else {
					tpl_set_var('displayToLowUserFound', 'block');
					tpl_set_var('CFrequirment', powerTrailBase::userMinimumCacheFoundToSetNewPowerTrail());
				}
				break;
			case 'selectCaches':
				//$userPowerTrails = $pt->getUserPowerTrails();
				tpl_set_var('displayUserCaches', 'block');
				tpl_set_var("keszynki",displayCaches($result, $pt->getUserPowerTrails()));
				break;
			case 'showAllSeries':
				$ptListData = displayPTrails($pt->getpowerTrails(), $pt->getPowerTrailOwn());
				tpl_set_var('filtersTrDisplay', 'table-row');
				tpl_set_var('ptTypeSelector2', displayPtTypesSelector('filter',$_REQUEST['filter'], true));
				tpl_set_var('sortSelector', getSortBySelector($_REQUEST['sortBy']));
				tpl_set_var('sortDirSelector', getSortDirSelector($_REQUEST['sortDir']));
				tpl_set_var('displayedPowerTrailsCount', $pt->getDisplayedPowerTrailsCount());
				tpl_set_var('mapCenterLat', $main_page_map_center_lat);
				tpl_set_var('mapCenterLon', $main_page_map_center_lon);
				tpl_set_var('mapZoom', 6);
				tpl_set_var('PowerTrails',$ptListData[0]);
				tpl_set_var('ptList4map',$ptListData[1]);
				tpl_set_var('displayPowerTrails', 'block');
				if($pt->getPowerTrailOwn() === false) tpl_set_var('statusOrPoints', tr('pt037'));
				else tpl_set_var('statusOrPoints', tr('pt040')); 
				tpl_set_var('mapOuterdiv', 'block');
				tpl_set_var('mapInit', 1);
				tpl_set_var('fullCountryMap', '1');
				break;
			case 'showMySeries':
				$ptListData = displayPTrails($pt->getpowerTrails(), $pt->getPowerTrailOwn());
				// tpl_set_var('ptTypeSelector2', displayPtTypesSelector('filter',$_REQUEST['filter'], true));
				// tpl_set_var('sortSelector', getSortBySelector($_REQUEST['sortBy']));
				// tpl_set_var('sortDirSelector', getSortDirSelector($_REQUEST['sortDir']));
				tpl_set_var('filtersTrDisplay', 'none');
				
				tpl_set_var('displayedPowerTrailsCount', $pt->getDisplayedPowerTrailsCount());
				tpl_set_var('mapCenterLat', $main_page_map_center_lat);
				tpl_set_var('mapCenterLon', $main_page_map_center_lon);
				tpl_set_var('mapZoom', 6);
				tpl_set_var('PowerTrails',$ptListData[0]);
				tpl_set_var('ptList4map',$ptListData[1]);
				tpl_set_var('displayPowerTrails', 'block');
				if($pt->getPowerTrailOwn() === false) tpl_set_var('statusOrPoints', tr('pt037'));
				else tpl_set_var('statusOrPoints', tr('pt040')); 
				tpl_set_var('mapOuterdiv', 'block');
				tpl_set_var('mapInit', 1);
				tpl_set_var('fullCountryMap', '1');
				break;
				
					
			case 'showSerie':
				$ptDbRow = $pt->getPowerTrailDbRow();
				$ptOwners = $pt->getPtOwners();
				$_SESSION['ptName'] = powerTrailBase::clearPtNames($ptDbRow['name']); 
				tpl_set_var('powerTrailId', $ptDbRow['id']);
				tpl_set_var('mapOuterdiv', 'block');
				if ($ptOwners) $userIsOwner = array_key_exists($usr['userid'], $ptOwners);
				else $userIsOwner = false;
				if ($ptDbRow['status'] == 1 || $userIsOwner) {
					$ptTypesArr = powerTrailBase::getPowerTrailTypes();
					$ptStatusArr = powerTrailBase::getPowerTrailStatus();
					$stats = $pt->getCountCachesAndUserFoundInPT();
					$leadingUser = powerTrailBase::getLeadingUser($ptDbRow['id']);
					if($ptDbRow['conquestedCount'] > 0) $removeCacheButtonDisplay = 'none';
					else $removeCacheButtonDisplay = 'inline';
					tpl_set_var('ptStatusSelector',  generateStatusSelector($ptDbRow['status']));
					tpl_set_var('removeCacheButtonDisplay',  $removeCacheButtonDisplay);
					tpl_set_var('leadingUserId',  $leadingUser['user_id']);
					tpl_set_var('leadingUserName',$leadingUser['username']);
					tpl_set_var('fullCountryMap', '0');
					tpl_set_var('ptTypeName', tr($ptTypesArr[$ptDbRow['type']]['translate']));
					tpl_set_var('displaySelectedPowerTrail', 'block');
					tpl_set_var('powerTrailName', $ptDbRow['name']);
					tpl_set_var('powerTrailDescription', stripslashes(htmlspecialchars_decode($ptDbRow['description'])));
					tpl_set_var('displayPtDescriptionUserAction', displayPtDescriptionUserAction($ptDbRow['id']));
					tpl_set_var('powerTrailDateCreated', substr($ptDbRow['dateCreated'], 0, -9));
					tpl_set_var('powerTrailCacheCount', $ptDbRow['cacheCount']);
					tpl_set_var('powerTrailCacheLeft', ($ptDbRow['cacheCount']-$stats['cachesFoundByUser']));
					tpl_set_var('powerTrailOwnerList', displayPtOwnerList($ptOwners));
					tpl_set_var('date', date('Y-m-d'));
					tpl_set_var('powerTrailDemandPercent', $ptDbRow['perccentRequired']);
					tpl_set_var('ptCommentsSelector', displayPtCommentsSelector('commentType', $ptDbRow['perccentRequired'], $pt->getCountCachesAndUserFoundInPT(), $ptDbRow['id'] ));
					tpl_set_var('conquestCount', $ptDbRow['conquestedCount']);
					tpl_set_var('ptPoints', $ptDbRow['points']);
					tpl_set_var('cacheFound', $stats['cachesFoundByUser']);
					tpl_set_var('powerTrailLogo', displayPowerTrailLogo($ptDbRow['id'], $ptDbRow['image']));
					tpl_set_var('powerTrailserStats', displayPowerTrailserStats($stats));
					
					//map
					tpl_set_var('mapInit', 1);
					tpl_set_var('mapCenterLat', $ptDbRow['centerLatitude']);
					tpl_set_var('mapCenterLon', $ptDbRow['centerLongitude']);
					tpl_set_var('mapZoom', 11);
					tpl_set_var('ptList4map',"[".$ptDbRow["centerLatitude"].",".$ptDbRow["centerLongitude"].",'".tr('pt136')."'],");
					
					if ($userIsOwner){
						tpl_set_var('ptStatus', tr($ptStatusArr[$ptDbRow['status']]['translate']));
						tpl_set_var('displayAddCachesButtons', 'block');
						tpl_set_var('percentDemandUserActions', 'block');
						tpl_set_var('ptTypeUserActions', '<a href="javascript:void(0)" class="editPtDataButton" onclick="togglePtTypeEdit();">'.tr('pt046').'</a>');
						tpl_set_var('ptDateUserActions', '<a href="javascript:void(0)" class="editPtDataButton" onclick="togglePtDateEdit();">'.tr('pt045').'</a>');
						tpl_set_var('cacheCountUserActions', '<a href="javascript:void(0)" class="editPtDataButton" onclick="ajaxCountPtCaches('.$ptDbRow['id'].')">'.tr('pt033').'</a>');
						tpl_set_var('ownerListUserActions', '<a id="dddx" href="javascript:void(0)" class="editPtDataButton" onclick="clickShow(\'addUser\', \'dddx\'); ">'.tr('pt030').'</a> <span style="display: none" id="addUser">'.tr('pt028').'<input type="text" id="addNewUser2pt" /><br /><a href="javascript:void(0)" class="editPtDataButton" onclick="cancellAddNewUser2pt()" >'.tr('pt031').'</a><a href="javascript:void(0)" class="editPtDataButton" onclick="ajaxAddNewUser2pt('.$ptDbRow['id'].')" >'.tr('pt032').'</a></span>');
						tpl_set_var('ptTypesSelector', displayPtTypesSelector('ptType1', $ptDbRow['type']));
					} else {
						tpl_set_var('ptStatus', '');
						tpl_set_var('percentDemandUserActions', 'none');
						tpl_set_var('displayAddCachesButtons', 'none');
						tpl_set_var('ptTypeUserActions', '');
						tpl_set_var('ptDateUserActions', '');
						tpl_set_var('cacheCountUserActions', '');
						tpl_set_var('ownerListUserActions', '');
					}
					tpl_set_var('ptList4map', displayAllCachesOfPowerTrail($pt->getAllCachesOfPt(), $pt->getPowerTrailCachesUserLogsByCache()));
					// powerTrailController::debug($pt->getPowerTrailDbRow(), __LINE__);
					// powerTrailController::debug($ptOwners, __LINE__);
				} else {
					
					tpl_set_var('mapOuterdiv', 'none');
					tpl_set_var('mainPtInfo', tr('pt018'));
				}
				break;
			default:
				tpl_set_var('PowerTrails', displayPTrails($pt->getpowerTrails()), false);
				tpl_set_var('displayPowerTrails', 'block');
				break;
		}
		
		// exit;


		tpl_BuildTemplate();
	
}

// budujemy kod html ktory zostaje wsylany do przegladraki
//$Opensprawdzacz->endzik();

function buildPowerTrailMenu($menuArray)
{
	
	// <li class="topmenu"><a href="javascript:void(0)" style="height:16px;line-height:16px;"><span>Item 1</span></a>
	// <ul>
		// <li class="subfirst"><a href="javascript:void(0)">Item 1 0</a></li>
		// <li class="sublast"><a href="javascript:void(0)">Item 1 1</a></li>
	// </ul></li>
	// <li class="topmenu"><a href="javascript:void(0)" style="height:16px;line-height:16px;">Item 3</a></li>
	// <li class="topmenu"><a href="javascript:void(0)" style="height:16px;line-height:16px;">Item 2</a></li>
	
	$menu = '';
	foreach ($menuArray as $key => $menuItem) {
		$menu .= '<li class="topmenu"><a href="'.$menuItem['script'].'?ptAction='.$menuItem['action'].'" style="height:16px;line-height:16px;">'.$menuItem['name'].'</a></li>';
	}
	return $menu;
	
}

function displayCaches($caches, $pTrails)
{
	// powerTrailController::debug($caches);
	// powerTrailController::debug($pTrails);
	if(count($caches) == 0) {
		tpl_set_var('displayUserCaches', 'none');
		tpl_set_var('nocachess', 'block');
		return '';
	}
	$rows = '';
	foreach ($caches as $key => $cache) {
		$ptSelector = '<select onchange="ajaxAddCacheToPT('.$cache['cache_id'].');" id="ptSelectorForCache'.$cache['cache_id'].'"><option value="-1">---</option>';
		$hidden = '<input type="hidden" id="h'.$cache['cache_id'].'" value="-1" >';
		foreach ($pTrails as $ptKey => $pTrail) {
			if($cache['PowerTrailId'] == $pTrail['id']) {
				$ptSelector .= '<option selected value='.$pTrail['id'].'>'.$pTrail['name'].'</option>';
				$hidden = '<input type="hidden" id="h'.$cache['cache_id'].'" value='.$pTrail['id'].' >';
			}
			else {
				$ptSelector .= '<option value='.$pTrail['id'].'>'.$pTrail['name'].'</option>';
			}
		}
		$ptSelector .= '</select>';
		$rows .= '<tr><td><a href="'.$cache['wp_oc'].'">'.$cache['wp_oc'].'</a></td><td>'. $cache['name'].'</td><td>'.$ptSelector.'</td>
		<td width="50"><img style="display: none" id="addCacheLoader'.$cache['cache_id'].'" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
		<span id="cacheInfo'.$cache['cache_id'].'" style="display: none "><img src="tpl/stdstyle/images/free_icons/accept.png" /></span>
		<span id="cacheInfoNOK'.$cache['cache_id'].'" style="display: none "><img src="tpl/stdstyle/images/free_icons/exclamation.png" /></span>'.
		$hidden.		
		'</td></tr>';
	}
	return $rows;
}

function displayPTrails($pTrails, $areOwnSeries)
{
	$poweTrailMarkers = array (
		1 => 'footprintRed.png',
		2 => 'footprintBlue.png',
		3 => 'footprintGreen.png',
		4 => 'footprintYellow.png',
	);
	
	$result = '';
	$dataForMap = '';
	foreach ($pTrails as $pTkey => $pTrail) {
		$pTrail["name"] = str_replace("'", '&#39;', $pTrail["name"]);
		$dataForMap .= "[".	$pTrail["centerLatitude"].",".$pTrail["centerLongitude"].",'<a href=powerTrail.php?ptAction=showSerie&ptrail=".$pTrail["id"].">".$pTrail["name"]."</a>','tpl/stdstyle/images/blue/".$poweTrailMarkers[$pTrail["type"]]."','".$pTrail["name"]."'],";
		$ptTypes = powerTrailBase::getPowerTrailTypes();
		$ptStatus = powerTrailBase::getPowerTrailStatus();
		if(!$areOwnSeries) $ownOrAll = round($pTrail["points"],2);
		else $ownOrAll = tr($ptStatus[$pTrail["status"]]['translate']);
		$ptHrefTitle = $pTrail["name"];
		if(strlen($pTrail["name"]) > 40){
			$pTrail["name"] = substr($pTrail["name"], 0,35).' (...)';
		}
		$result .= '<tr>'.
		'<td align="right" style="padding-right: 5px;"><b><a href="powerTrail.php?ptAction=showSerie&ptrail='.$pTrail["id"].'" title="'.$ptHrefTitle.'">'.$pTrail["name"].'</a></b></td>'.
		'<td ><img src="tpl/stdstyle/images/blue/'.$poweTrailMarkers[$pTrail["type"]].'" /> '.tr($ptTypes[$pTrail["type"]]['translate']).'</td>'.
		'<td class="ptTd">'. $ownOrAll .'</td>'.
		'<td class="ptTd">'.substr($pTrail["dateCreated"] , 0, -9).'</td>'.
		'<td class="ptTd">'.$pTrail["cacheCount"].'</td>'.
		'<td class="ptTd">'.$pTrail["conquestedCount"].'</td>
		</tr>';
		// var_dump($pTrail);
	}
	
	$result= array($result, rtrim($dataForMap, ","));
	
	return $result;
}

function displayAllCachesOfPowerTrail($pTrailCaches, $powerTrailCachesUserLogsByCache) 
{
	$cacheTypesIcons = getCacheTypesIcons();
	$foundCacheTypesIcons = getFoundCacheTypesIcons($cacheTypesIcons);	
		
	$cacheRows = '';
	foreach ($pTrailCaches as $rowNr => $cache) {
		// avoid crash js on quotas (');
		$cacheName = str_replace("'", '&#39;', $cache['name']);
		if (isset($powerTrailCachesUserLogsByCache[$cache['cache_id']])) $image = 'tpl/stdstyle/images/'.$foundCacheTypesIcons[$cache['type']];
		else $image = 'tpl/stdstyle/images/'.$cacheTypesIcons[$cache['type']];
		$cacheRows .= '['.$cache['latitude'].",".$cache['longitude'].",'<a href=".$cache["wp_oc"]." target=_new>".$cacheName."</a>',". "'$image','".$cacheName."',],";
		
	}	
	$cacheRows = rtrim($cacheRows, ",");
	// powerTrailController::debug($pTrailCaches);
	// exit;
	return $cacheRows;
}



function displayPowerTrailserStats($stats)
{
	if ($stats['totalCachesCountInPowerTrail'] != 0) {	
		$stats2display = round($stats['cachesFoundByUser'] * 100 / $stats['totalCachesCountInPowerTrail'], 2);
	} else {
		$stats2display = 0;
	}
	// powerTrailController::debug($stats);
	$stats2display .= '% ('  .tr('pt017') .' <span style="color: #00aa00"><b>' . $stats['cachesFoundByUser'].'</b></span> '.tr('pt016').' <span style="color: #0000aa"><b>'.$stats['totalCachesCountInPowerTrail'].'</b></span> '.tr('pt014').')';
	return $stats2display;
}

function displayPtOwnerList($ptOwners)
{
	$ownerList = '';
	isset($_SESSION['user_id']) ? $userLogged = $_SESSION['user_id'] : $userLogged = -1;
	foreach ($ptOwners as $userId => $user) {
		$ownerList .= '<a href="viewprofile.php?userid='.$userId.'">'.$user['username'].'</a>';
		if($userId != $userLogged) {
			$ownerList .= '<span style="display: none" class="removeUserIcon"><img onclick="ajaxRemoveUserFromPt('.$userId.');" src="tpl/stdstyle/images/free_icons/cross.png" width=10 title="'.tr('pt029').'" /></span>, ';
		} else {
			$ownerList .= ', ';
		}
	}
	$ownerList = substr($ownerList, 0, -2);
	return $ownerList;
}

function displayPtDescriptionUserAction($powerTrailId) {
	$result = '';	
	if (isset($_SESSION['user_id'])){
		if (powerTrailBase::checkIfUserIsPowerTrailOwner($_SESSION['user_id'], $powerTrailId) == 1){
			$result = '<a href="javascript:void(0)" id="toggleEditDescButton" class="editPtDataButton" onclick="toggleEditDesc();">'.tr('pt043').'</a>';
		}
	}
	return $result;
}

function displayPtTypesSelector($htmlid, $selectedId = 0, $witchZeroOption = false){
	$ptTypesArr = powerTrailBase::getPowerTrailTypes();
	$selector = '<select id="'.$htmlid.'" name="'.$htmlid.'">';
	if($witchZeroOption) {
		$selector .= '<option value="0">'.tr('pt165').'</option>';
		
	}
	foreach ($ptTypesArr as $id => $type) {
		if ($selectedId == $id) $selected = 'selected'; else $selected = '';
		$selector .= '<option '.$selected.' value="'.$id.'">'.tr($type['translate']).'</option>';
	}
	$selector .= '</select>';
	return $selector;
}

function displayPtCommentsSelector($htmlid, $percetDemand, $userStats, $ptId, $selectedId = 0){
	
	if($userStats['totalCachesCountInPowerTrail'] != 0){	
		$percentUserFound = round($userStats['cachesFoundByUser'] * 100 / $userStats['totalCachesCountInPowerTrail'], 2);
	} else {
		$percentUserFound = 0;
	}
	$commentsArr = powerTrailBase::getPowerTrailComments();
	
	$selector = '<select id="'.$htmlid.'" name="'.$htmlid.'">';
	foreach ($commentsArr as $id => $type) {
		if ($id == 2) {
			if ($percentUserFound<$percetDemand || powerTrailBase::checkUserConquestedPt($_SESSION['user_id'], $ptId) >0){
				 break;
			}
			$selected = 'selected="selected"'; 
		}
		if(!isset($selected)) $selected = '';
		if ($selectedId == $id) $selected = 'selected'; 
		$selector .= '<option value="'.$id.'" '.$selected.'>'.tr($type['translate']).'</option>';
		unset($selected);
	}
	$selector .= '</select>';
	return $selector;
}

function displayPowerTrailLogo($ptId, $img){
	// global $picurl;	
	if ($img == '') $image = 'tpl/stdstyle/images/blue/powerTrailGenericLogo.png';
	else $image = $img;
	return $image;	
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

function getSortBySelector($sel) {
	$array = array (
			1 => array('val'=>"type", 'tr'=>'pt174'),
			2 => array('val'=>"name", 'tr'=>'pt168'),
			3 => array('val'=>"dateCreated", 'tr'=>'pt169'),
			4 => array('val'=>"cacheCount", 'tr'=>'pt170'),
			5 => array('val'=>"points",'tr'=>'pt171'),
			6 => array('val'=>"conquestedCount",'tr'=>'pt172'),
	);
	return generateSelector($array, $sel, 'sortBy');
}

function getSortDirSelector($sel){
	$arr = array(
		1 => array('val' => 'asc', 'tr' => 'pt176'),
		2 => array('val' => 'desc', 'tr' => 'pt177'),
	);
	return generateSelector($arr, $sel, 'sortDir');
}

function generateSelector($array, $sel, $name){
	$selector = '<select id="'.$name.'" name="'.$name.'">';
	foreach ($array as $opt) {
		if ($opt['val'] == $sel) $selector .= '<option selected="selected" value="'.$opt['val'].'">'.tr($opt['tr']).'</option>';
		else $selector .= '<option value="'.$opt['val'].'">'.tr($opt['tr']).'</option>';
	}
	return $selector;
}

function generateStatusSelector($currStatus){
	$selector = '<select id="ptStatusSelector">';
	if($currStatus == 3) { //permanently closed
		$selector .= '<option value="3">'.tr('pt212').'</option>';
	} else {
		foreach (powerTrailBase::getPowerTrailStatus() as $val => $desc) {
			if ($val == $currStatus) $selected = 'selected="selected"';
			else $selected = '';
			if($val==2 && $currStatus!=2) {} else // (this status is only after new geoPath creation.)
			$selector .= '<option '.$selected.' value="'.$val.'">'.tr($desc['translate']).'</option>';
		}
	}
	$selector .= '</select>';
	return $selector;
}

function saveCookie($cookie) {
	$cookie->set("ptFltr", $_REQUEST['filter']);
	$cookie->set("ptSrBy", $_REQUEST['sortBy']);
	$cookie->set("ptSrDr", $_REQUEST['sortDir']);
}

?>