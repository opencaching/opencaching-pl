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



$sonsOfTheGod = array(9067, 9078, 7699, 7969, 4029, 10737, 1038, 33407, 3,);
if (!in_array($usr['userid'], $sonsOfTheGod)){
	header("location: $absolute_server_URI");
} else {
	$powerTrailModuleSwitchOn = true;
}
if(!$powerTrailModuleSwitchOn) {
	header("location: $absolute_server_URI");
}

$firePtMenu = true;
//Preprocessing
if ($error == false)
{
		$tplname = 'powerTrail';
		
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
		if(!$usr) tpl_set_var('ptMenu', 'none');
		$ptMenu = new powerTrailMenu($usr);
		tpl_set_var("powerTrailMenu", buildPowerTrailMenu($ptMenu->getPowerTrailsMenu()));

		$pt = new powerTrailController($usr);
		$result = $pt->run();
		$actionPerformed = $pt->getActionPerformed();

		switch ($actionPerformed) {
			case 'createNewSerie':
				if($usr['userFounds'] >= powerTrailBase::userMinimumCacheFoundToSetNewPowerTrail){
					tpl_set_var('displayCreateNewPowerTrailForm', 'block');
				} else {
					tpl_set_var('displayToLowUserFound', 'block');
					tpl_set_var('CFrequirment', powerTrailBase::userMinimumCacheFoundToSetNewPowerTrail);
				}
				break;
			case 'selectCaches':
				//$userPowerTrails = $pt->getUserPowerTrails();
				tpl_set_var('displayUserCaches', 'block');
				tpl_set_var("keszynki",displayCaches($result, $pt->getUserPowerTrails()));
				break;
			case 'showAllSeries':
				tpl_set_var('PowerTrails', displayPTrails($pt->getpowerTrails()));
				tpl_set_var('displayPowerTrails', 'block');
				break;
			case 'showSerie':
				$ptDbRow = $pt->getPowerTrailDbRow();
				$ptOwners = $pt->getPtOwners();
				tpl_set_var('powerTrailId', $ptDbRow['id']);
				$userIsOwner = array_key_exists($usr['userid'], $ptOwners);
				if ($ptDbRow['status'] != 0 || $userIsOwner) {
					$ptTypesArr = powerTrailBase::getPowerTrailTypes();
					$ptStatusArr = powerTrailBase::getPowerTrailStatus();
					tpl_set_var('ptTypeName', tr($ptTypesArr[$ptDbRow['type']]['translate']));
					tpl_set_var('displaySelectedPowerTrail', 'block');
					tpl_set_var('powerTrailName', $ptDbRow['name']);
					tpl_set_var('powerTrailDescription', stripslashes(htmlspecialchars_decode($ptDbRow['description'])));
					tpl_set_var('displayPtDescriptionUserAction', displayPtDescriptionUserAction($ptDbRow['id']));
					tpl_set_var('powerTrailDateCreated', substr($ptDbRow['dateCreated'], 0, -9));
					tpl_set_var('powerTrailCacheCount', $ptDbRow['cacheCount']);
					tpl_set_var('powerTrailOwnerList', displayPtOwnerList($ptOwners));
					tpl_set_var('date', date('Y-m-d'));
					tpl_set_var('powerTrailDemandPercent', $ptDbRow['perccentRequired']);
					tpl_set_var('ptCommentsSelector', displayPtCommentsSelector('commentType', $ptDbRow['perccentRequired'], $pt->getCountCachesAndUserFoundInPT(), $ptDbRow['id'] ));
					tpl_set_var('conquestCount', $ptDbRow['conquestedCount']);
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
					

					tpl_set_var('powerTrailLogo', displayPowerTrailLogo($ptDbRow['id'], $ptDbRow['image']));
					// tpl_set_var('PowerTrailCaches', displayAllCachesOfPowerTrail($pt->getAllCachesOfPt(), $pt->getPowerTrailCachesUserLogsByCache()));
					tpl_set_var('powerTrailserStats', displayPowerTrailserStats($pt->getCountCachesAndUserFoundInPT()));
					// powerTrailController::debug($pt->getPowerTrailDbRow(), __LINE__);
					// powerTrailController::debug($ptOwners, __LINE__);
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
		foreach ($pTrails as $ptKey => $pTrail) {
			if($cache['PowerTrailId'] == $pTrail['id']) $ptSelector .= '<option selected value='.$pTrail['id'].'>'.$pTrail['name'].'</option>';
			else $ptSelector .= '<option value='.$pTrail['id'].'>'.$pTrail['name'].'</option>';
		}
		$ptSelector .= '</select>';
		$rows .= '<tr><td><a href="'.$cache['wp_oc'].'">'.$cache['wp_oc'].'</a></td><td>'. $cache['name'].'</td><td>'.$ptSelector.'</td>
		<td width="50"><span id="cacheInfo'.$cache['cache_id'].'" style="display: none "><img src="tpl/stdstyle/images/free_icons/accept.png" /></span></td></tr>';
	}
	return $rows;
}

function displayPTrails($pTrails)
{
	$result = '';	
	foreach ($pTrails as $pTkey => $pTrail) {
		$ptTypes = powerTrailBase::getPowerTrailTypes();
		$ptStatus = powerTrailBase::getPowerTrailStatus();
		$result .= '<tr>'.
		'<td class="ptTd"><b><a href="powerTrail.php?ptAction=showSerie&ptrail='.$pTrail["id"].'">'.$pTrail["name"]           .'</a></b></td>'.
		'<td class="ptTd">'.$pTrail["centerLatitude"]    .'</td>'.
		'<td class="ptTd">'.$pTrail["centerLongitude"]   .'</td>'.
		'<td class="ptTd">'.tr($ptTypes[$pTrail["type"]]['translate'])   .'</td>'.
		'<td class="ptTd">'.tr($ptStatus[$pTrail["status"]]['translate']) .'</td>'.
		'<td class="ptTd">'.substr($pTrail["dateCreated"] , 0, -9).'</td>'.
		'<td class="ptTd">'.$pTrail["cacheCount"].'</td>'.
		'<td class="ptTd">'.$pTrail["conquestedCount"].'</td>
		</tr>';
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
		// powerTrailController::debug($cache);
		$cacheRows .= '<tr>';
		//display icon found/not found depend on current user
		if (isset($powerTrailCachesUserLogsByCache[$cache['cache_id']])) $cacheRows .= '<td><img src="tpl/stdstyle/images/'.$foundCacheTypesIcons[$cache['type']].'" title="'.$powerTrailCachesUserLogsByCache[$cache['cache_id']]['text'].'"/></td>';
		else $cacheRows .= '<td><img src="tpl/stdstyle/images/'.$cacheTypesIcons[$cache['type']].'" /></td>';
		
		$cacheRows .= '<td><a href="'.$cache['wp_oc'].'">'.$cache['name'].'</a>()</td>'.
		'</tr>';
	}	
	
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
	$stats2display .= '% ('  .tr('pt017') .' ' . $stats['cachesFoundByUser'].' '.tr('pt016').' '.$stats['totalCachesCountInPowerTrail'].' '.tr('pt014');
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

function displayPtTypesSelector($htmlid, $selectedId = 0){
	$ptTypesArr = powerTrailBase::getPowerTrailTypes();
	$selector = '<select id="'.$htmlid.'" name="'.$htmlid.'">';
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
			if ($percentUserFound<$percetDemand || powerTrailBase::checkUserConquestedPt($_SESSION['user_id'], $ptId) >0) break;
		}
		if ($selectedId == $id) $selected = 'selected'; else $selected = '';
		$selector .= '<option '.$selected.' value="'.$id.'">'.tr($type['translate']).'</option>';
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
?>