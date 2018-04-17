<?php

use lib\Objects\OcConfig\OcConfig;
use lib\Objects\ApplicationContainer;
use lib\Objects\PowerTrail\PowerTrail;
use lib\Objects\GeoCache\GeoCache;
use Utils\Uri\Uri;
use lib\Objects\OcConfig\OcDynamicMapConfig;
use Utils\View\View;
use Utils\Uri\OcCookie;

/**
 *  powerTrail.php
 *  ------------------------------------------------------------------------------------------------
 *  Power Trails in opencaching
 *  this is display file. for API check dir powerTrail
 *  ------------------------------------------------------------------------------------------------
 *  @author: Andrzej 'Łza' Woźniak [wloczynutka[on]gmail.com]
 *
 *
 *
 */
// variables required by opencaching.pl
global $lang, $rootpath, $usr, $absolute_server_URI, $googlemap_key;

//prepare the templates and include all neccessary
require_once('lib/common.inc.php');
$ocConfig = OcConfig::instance();
$appContainer = ApplicationContainer::Instance();

require_once('lib/cache.php');

$_SESSION['powerTrail']['userFounds'] = $usr['userFounds'];

if ($ocConfig->isPowerTrailModuleSwitchOn() === false) {
    header("location: $absolute_server_URI");
}

$firePtMenu = true;
//Preprocessing
if ($error == false) {

    if (isset($_REQUEST['sortBy']) || isset($_REQUEST['filter']) || isset($_REQUEST['sortDir'])
        || isset($_REQUEST['myPowerTrailsBool']) || isset($_REQUEST['gainedPowerTrailsBool'])
        || isset($_REQUEST['historicLimitBool'])){

            saveCookie();

    } else {

        $_REQUEST['sortBy'] = OcCookie::getOrDefault("ptSrBy", 'cacheCount');
        $_REQUEST['filter'] = OcCookie::getOrDefault("ptFltr", 0);
        $_REQUEST['sortDir'] = OcCookie::getOrDefault("ptSrDr", 'desc');
        $_REQUEST['myPowerTrailsBool'] = OcCookie::getOrDefault("ptMyBool", 'no');
        $_REQUEST['gainedPowerTrailsBool'] = OcCookie::getOrDefault("ptGaBool", 'no');
        $_REQUEST['historicLimitBool'] = OcCookie::getOrDefault("ptMiniBool", 'no');

    }

    $tplname = 'powerTrail';
    $view->addLocalCss(Uri::getLinkWithModificationTime('tpl/stdstyle/css/powerTrail.css'));
    $view->addLocalCss(Uri::getLinkWithModificationTime('tpl/stdstyle/css/ptMenuCss/style.css'));
    $view->loadJQuery();
    $view->loadJQueryUI();
    $view->loadTimepicker();
    $view->loadGMapApi();

    if (!$usr && $hide_coords) {
        $mapControls = 0;
        tpl_set_var('gpxOptionsTrDisplay', 'none');
    } else {
        $mapControls = 1;
        tpl_set_var('gpxOptionsTrDisplay', 'table-row');
    }
    if (!$usr) {
        tpl_set_var('statsOptionsDisplay', 'display: none;');
    } else {
        tpl_set_var('statsOptionsDisplay', '');
    }
    include_once('powerTrail/powerTrailController.php');
    include_once('powerTrail/powerTrailMenu.php');
    if (isset($_SESSION['user_id']))
        tpl_set_var('displayAddCommentSection', 'block');
    else
        tpl_set_var('displayAddCommentSection', 'none');
    if (isset($_REQUEST['historicLimit']) && $_REQUEST['historicLimit'] == 1) {
        tpl_set_var('historicLimitHref', 'powerTrail.php');
        tpl_set_var('switchMiniPT', tr('pt233'));
        tpl_set_var('historicLimit', 1);
    } else {
        tpl_set_var('historicLimitHref', 'powerTrail.php?historicLimit=1');
        tpl_set_var('switchMiniPT', tr('pt232'));
        tpl_set_var('historicLimit', 0);
    }
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
    tpl_set_var('scrollwheel', $mapControls);
    tpl_set_var('scaleControl', $mapControls);

    tpl_set_var('ptList4map', '[]');
    tpl_set_var('fullCountryMap', '1');
    tpl_set_var('googleMapApiKey', $googlemap_key);
    tpl_set_var('ocWaypoint', $oc_waypoint);
    tpl_set_var('commentsPaginateCount', powerTrailBase::commentsPaginateCount);

    tpl_set_var('attributionMap', OcDynamicMapConfig::getJsAttributionMap());
    tpl_set_var('mapItems', OcDynamicMapConfig::getJsMapItems());
    tpl_set_var('mapWMSConfigLoader', OcDynamicMapConfig::getWMSImageMapTypeOptions());


    tpl_set_var('googlemap_key', $googlemap_key);
    tpl_set_var('powerTrailId', '');
    tpl_set_var('keszynki', '');
    tpl_set_var('cacheFound', '');
    tpl_set_var('powerTrailCacheLeft', '');
    tpl_set_var('PowerTrails', '');
    tpl_set_var('demandPercentMinimum', lib\Controllers\PowerTrailController::MINIMUM_PERCENT_REQUIRED);
    tpl_set_var('powerTrailDemandPercent', '100');
    tpl_set_var('leadingUserId', '');

    if (!$usr)
        tpl_set_var('ptMenu', 'none');
    $ptMenu = new powerTrailMenu($usr);
    tpl_set_var("powerTrailMenu", buildPowerTrailMenu($ptMenu->getPowerTrailsMenu()));

    $view->setVar('csWikiLink', OcConfig::getWikiLink('geoPaths'));

    $pt = new powerTrailController($usr);
    $result = $pt->run();
    $actionPerformed = $pt->getActionPerformed();
    switch ($actionPerformed) {
        case 'createNewSerie':
            if ($usr['userFounds'] >= powerTrailBase::userMinimumCacheFoundToSetNewPowerTrail()) {
                tpl_set_var('displayCreateNewPowerTrailForm', 'block');
            } else {
                tpl_set_var('displayToLowUserFound', 'block');
                tpl_set_var('CFrequirment', powerTrailBase::userMinimumCacheFoundToSetNewPowerTrail());
            }
            break;
        case 'selectCaches':
            //$userPowerTrails = $pt->getUserPowerTrails();
            tpl_set_var('displayUserCaches', 'block');
            tpl_set_var("keszynki", displayCaches($result, $pt->getUserPowerTrails()));
            break;
        case 'showAllSeries':
            $ptListData = displayPTrails($pt->getpowerTrails(), $pt->getPowerTrailOwn());
            tpl_set_var('filtersTrDisplay', 'table-row');
            tpl_set_var('ptTypeSelector2', displayPtTypesSelector('filter', isset($_REQUEST['filter'])?$_REQUEST['filter']:0, true));
            tpl_set_var('sortSelector', getSortBySelector($_REQUEST['sortBy']));
            tpl_set_var('sortDirSelector', getSortDirSelector($_REQUEST['sortDir']));
            tpl_set_var('myPowerTrailsBool', getMyPowerTrailsSelector($_REQUEST['myPowerTrailsBool']));
            tpl_set_var('gainedPowerTrailsBool', getGainedPowerTrailsSelector(
                isset($_REQUEST['gainedPowerTrailsBool'])?$_REQUEST['gainedPowerTrailsBool']:0));
            tpl_set_var('historicLimitBool', getMiniPowerTrailSelector($_REQUEST['historicLimitBool']));
            tpl_set_var('displayedPowerTrailsCount', $pt->getDisplayedPowerTrailsCount());
            tpl_set_var('mapCenterLat', $main_page_map_center_lat);
            tpl_set_var('mapCenterLon', $main_page_map_center_lon);
            tpl_set_var('mapZoom', 6);
            tpl_set_var('PowerTrails', $ptListData[0]);
            tpl_set_var('ptList4map', $ptListData[1]);
            tpl_set_var('displayPowerTrails', 'block');
            if ($pt->getPowerTrailOwn() === false)
                tpl_set_var('statusOrPoints', tr('pt037'));
            else
                tpl_set_var('statusOrPoints', tr('cs_status'));
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
            tpl_set_var('PowerTrails', $ptListData[0]);
            tpl_set_var('ptList4map', $ptListData[1]);
            tpl_set_var('displayPowerTrails', 'block');
            if ($pt->getPowerTrailOwn() === false)
                tpl_set_var('statusOrPoints', tr('pt037'));
            else
                tpl_set_var('statusOrPoints', tr('cs_status'));
            tpl_set_var('mapOuterdiv', 'block');
            tpl_set_var('mapInit', 1);
            tpl_set_var('fullCountryMap', '1');
            break;


        case 'showSerie':
            if(!isset($_GET['ptrail'])){
                // just redirect to all powertrails
                header("Location: " . "//" . $_SERVER['HTTP_HOST'] . '/powerTrail.php');
                exit;
            }
            $powerTrail = new PowerTrail(array('id' => (int) $_GET['ptrail']));
            $ptOwners = $pt->getPtOwners();
            $_SESSION['ptName'] = powerTrailBase::clearPtNames($powerTrail->getName());
            tpl_set_var('powerTrailId', $powerTrail->getId());
            if (!$usr && $hide_coords) {
                tpl_set_var('mapOuterdiv', 'none');
            } else {
                tpl_set_var('mapOuterdiv', 'block');
            }

            $userIsOwner = $powerTrail->isUserOwner($usr['userid']);
            if ($powerTrail->getStatus() == 1 || $userIsOwner || ($appContainer->getLoggedUser() !== null && $appContainer->getLoggedUser()->getIsAdmin())) {
                $ptTypesArr = powerTrailBase::getPowerTrailTypes();
                $ptStatusArr = \lib\Controllers\PowerTrailController::getPowerTrailStatus();
                $foundCachsByUser = $powerTrail->getFoundCachsByUser($usr['userid']);
                $leadingUser = powerTrailBase::getLeadingUser($powerTrail->getId());
                if ($powerTrail->getConquestedCount() > 0){
                    $removeCacheButtonDisplay = 'none';
                } else {
                    $removeCacheButtonDisplay = 'inline';
                }
                tpl_set_var('ptStatusSelector', generateStatusSelector($powerTrail->getStatus()));
                tpl_set_var('removeCacheButtonDisplay', $removeCacheButtonDisplay);
                tpl_set_var('leadingUserId', $leadingUser['user_id']);
                tpl_set_var('leadingUserName', $leadingUser['username']);
                tpl_set_var('fullCountryMap', '0');
                tpl_set_var('ptTypeName', tr($ptTypesArr[$powerTrail->getType()]['translate']));
                tpl_set_var('displaySelectedPowerTrail', 'block');
                tpl_set_var('powerTrailName', htmlspecialchars($powerTrail->getName(), ENT_COMPAT | ENT_HTML5));
                tpl_set_var('powerTrailDescription', stripslashes(htmlspecialchars_decode($powerTrail->getDescription())));
                tpl_set_var('displayPtDescriptionUserAction', displayPtDescriptionUserAction($powerTrail));
                tpl_set_var('powerTrailDateCreated', $powerTrail->getDateCreated()->format($dateFormat));
                tpl_set_var('powerTrailCacheCount', $powerTrail->getCacheCount());

                tpl_set_var('powerTrailActiveCacheCount', $powerTrail->getActiveGeocacheCount());
                tpl_set_var('powerTrailUnavailableCacheCount', $powerTrail->getUnavailableGeocacheCount());
                tpl_set_var('powerTrailArchivedCacheCount', $powerTrail->getArchivedGeocacheCount());

                tpl_set_var('powerTrailCacheLeft', ($powerTrail->getCacheCount() - count($foundCachsByUser)));
                tpl_set_var('powerTrailOwnerList', displayPtOwnerList($powerTrail));
                tpl_set_var('date', date($dateFormat));
                tpl_set_var('powerTrailDemandPercent', $powerTrail->getPerccentRequired());
                tpl_set_var('ptCommentsSelector', displayPtCommentsSelector('commentType', $powerTrail, null, $usr));
                tpl_set_var('conquestCount', $powerTrail->getConquestedCount());
                tpl_set_var('ptPoints', $powerTrail->getPoints());
                tpl_set_var('cacheFound', count($foundCachsByUser));
                tpl_set_var('powerTrailLogo', displayPowerTrailLogo($powerTrail->getId(), $powerTrail->getImage()));
                tpl_set_var('powerTrailserStats', displayPowerTrailserStats($powerTrail, $foundCachsByUser));

                //map
                tpl_set_var('mapInit', 1);
                tpl_set_var('mapCenterLat', $powerTrail->getCenterCoordinates()->getLatitude());
                tpl_set_var('mapCenterLon', $powerTrail->getCenterCoordinates()->getLongitude());
                tpl_set_var('mapZoom', 11);
                tpl_set_var('ptList4map', "[" . $powerTrail->getCenterCoordinates()->getLatitude() . "," . $powerTrail->getCenterCoordinates()->getLongitude() . ",'" . tr('pt136') . "'],");

                if ($userIsOwner) {
                    tpl_set_var('ptStatus', tr($ptStatusArr[$powerTrail->getStatus()]['translate']));
                    tpl_set_var('displayAddCachesButtons', 'block');
                    tpl_set_var('percentDemandUserActions', 'block');
                    tpl_set_var('ptTypeUserActions', '<a href="javascript:void(0)" class="editPtDataButton" onclick="togglePtTypeEdit();">' . tr('pt046') . '</a>');
                    tpl_set_var('ptDateUserActions', '<a href="javascript:void(0)" class="editPtDataButton" onclick="togglePtDateEdit();">' . tr('pt045') . '</a>');
                    tpl_set_var('cacheCountUserActions', '<a href="javascript:void(0)" class="editPtDataButton" onclick="ajaxCountPtCaches(' . $powerTrail->getId() . ')">' . tr('pt033') . '</a>');
                    tpl_set_var('ownerListUserActions', '<a id="dddx" href="javascript:void(0)" class="editPtDataButton" onclick="clickShow(\'addUser\', \'dddx\'); ">' . tr('pt030') . '</a> <span style="display: none" id="addUser">' . tr('pt028') . '<input type="text" id="addNewUser2pt" /><br /><a href="javascript:void(0)" class="editPtDataButton" onclick="cancellAddNewUser2pt()" >' . tr('pt031') . '</a><a href="javascript:void(0)" class="editPtDataButton" onclick="ajaxAddNewUser2pt(' . $powerTrail->getId() . ')" >' . tr('pt032') . '</a></span>');
                    tpl_set_var('ptTypesSelector', displayPtTypesSelector('ptType1', $powerTrail->getType()));
                } else {
                    tpl_set_var('ptStatus', '');
                    tpl_set_var('percentDemandUserActions', 'none');
                    tpl_set_var('displayAddCachesButtons', 'none');
                    tpl_set_var('ptTypeUserActions', '');
                    tpl_set_var('ptDateUserActions', '');
                    tpl_set_var('cacheCountUserActions', '');
                    tpl_set_var('ownerListUserActions', '');
                }
                if ($usr || !$hide_coords) {
                    tpl_set_var('ptList4map', displayAllCachesOfPowerTrail($powerTrail));
                }
            } else {
                tpl_set_var('mapOuterdiv', 'none');
                tpl_set_var('mainPtInfo', tr('pt018'));
            }
            break;
        default:
            tpl_set_var('PowerTrails', displayPTrails($pt->getpowerTrails(), false));
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
        $menu .= '<li class="topmenu"><a href="' . $menuItem['script'] . '?ptAction=' . $menuItem['action'] . '" style="height:16px;line-height:16px;">' . $menuItem['name'] . '</a></li>';
    }
    return $menu;
}

function displayCaches($caches, $pTrails)
{
    // powerTrailController::debug($caches);
    // powerTrailController::debug($pTrails);
    if (count($caches) == 0) {
        tpl_set_var('displayUserCaches', 'none');
        tpl_set_var('nocachess', 'block');
        return '';
    }
    $rows = '';
    foreach ($caches as $key => $cache) {
        $ptSelector = '<select onchange="ajaxAddCacheToPT(' . $cache['cache_id'] . ');" id="ptSelectorForCache' . $cache['cache_id'] . '"><option value="-1">---</option>';
        $hidden = '<input type="hidden" id="h' . $cache['cache_id'] . '" value="-1" >';
        foreach ($pTrails as $ptKey => $pTrail) {
            if ($cache['PowerTrailId'] == $pTrail['id']) {
                $ptSelector .= '<option selected value=' . $pTrail['id'] . '>' . $pTrail['name'] . '</option>';
                $hidden = '<input type="hidden" id="h' . $cache['cache_id'] . '" value=' . $pTrail['id'] . ' >';
            } else {
                $ptSelector .= '<option value=' . $pTrail['id'] . '>' . $pTrail['name'] . '</option>';
            }
        }
        $ptSelector .= '</select>';
        $rows .= '<tr><td><a href="' . $cache['wp_oc'] . '">' . $cache['wp_oc'] . '</a></td><td>' . $cache['name'] . '</td><td>' . $ptSelector . '</td>
        <td width="50"><img style="display: none" id="addCacheLoader' . $cache['cache_id'] . '" src="tpl/stdstyle/images/misc/ptPreloader.gif" alt="">
        <span id="cacheInfo' . $cache['cache_id'] . '" style="display: none "><img src="tpl/stdstyle/images/free_icons/accept.png" alt=""></span>
        <span id="cacheInfoNOK' . $cache['cache_id'] . '" style="display: none "><img src="tpl/stdstyle/images/free_icons/exclamation.png" alt=""></span>' .
                $hidden .
                '</td></tr>';
    }
    return $rows;
}

function displayPTrails($pTrails, $areOwnSeries)
{


    $ptTypes = powerTrailBase::getPowerTrailTypes();
    $ptStatus = \lib\Controllers\PowerTrailController::getPowerTrailStatus();

    $result = '';
    $dataForMap = '';

    foreach ($pTrails as $pTkey => $pTrail) {
        $pTrail["name"] = str_replace("'", '&#39;', $pTrail["name"]);
        $dataForMap .= "[" . $pTrail["centerLatitude"] . "," . $pTrail["centerLongitude"] . ",'<a href=powerTrail.php?ptAction=showSerie&ptrail=" . $pTrail["id"] . ">" . $pTrail["name"] . "</a>','" . $ptTypes[$pTrail['type']]['icon'] . "','" . $pTrail["name"] . "'],";
        if (!$areOwnSeries)
            $ownOrAll = round($pTrail["points"], 2);
        else
            $ownOrAll = tr($ptStatus[$pTrail["status"]]['translate']);
        if (strlen($pTrail["name"]) > 40) {
            $pTrail["name"] = mb_substr($pTrail["name"], 0, 35) . ' (...)';
        }
        $result .= '<tr>' .
                '<td style="text-align: right; padding-right: 5px;"><b><a href="powerTrail.php?ptAction=showSerie&ptrail=' . $pTrail["id"] . '">' . $pTrail["name"] . '</a></b></td>' .
                '<td><img src="' . $ptTypes[$pTrail["type"]]['icon'] . '" alt=""> ' . tr($ptTypes[$pTrail["type"]]['translate']) . '</td>' .
                '<td class="ptTd">' . $ownOrAll . '</td>' .
                '<td class="ptTd">' . substr($pTrail["dateCreated"], 0, -9) . '</td>' .
                '<td class="ptTd">' . $pTrail["cacheCount"] . '</td>' .
                '<td class="ptTd">' . $pTrail["conquestedCount"] . '</td>
        </tr>';
    }

    $result = array($result, rtrim($dataForMap, ","));

    return $result;
}

function displayAllCachesOfPowerTrail(PowerTrail $powerTrail)
{
    isset($_SESSION['user_id']) ? $userId = $_SESSION['user_id'] : $userId = -9999;

    $powerTrailCachesUserLogsByCache = $powerTrail->getPowerTrailCachesLogsForCurrentUser();

    $cacheTypesIcons = cache::getCacheIconsSet();
    $pTrailCaches = $powerTrail->getGeocaches();
    $cacheRows = '';
    /* @var $geocache Geocache */
    foreach ($pTrailCaches as $geocache) {
        // avoid crash js on quotas (');
        $cacheName = str_replace("'", '&#39;', $geocache->getCacheName());
        if (isset($powerTrailCachesUserLogsByCache[$geocache->getCacheId()])) {
            $image = $cacheTypesIcons[$geocache->getCacheType()]['iconSet'][1]['iconSmallFound'];
        } elseif ($geocache->getOwner()->getUserId() == $userId) {
            $image = $cacheTypesIcons[$geocache->getCacheType()]['iconSet'][1]['iconSmallOwner'];
        } else {
            $image = $cacheTypesIcons[$geocache->getCacheType()]['iconSet'][1]['iconSmall'];
        }
        $cacheRows .= '[' . $geocache->getCoordinates()->getLatitude() . "," . $geocache->getCoordinates()->getLongitude() . ",'<a href=" . $geocache->getWaypointId() . " target=_new>" . $cacheName . "</a>'," . "'$image','" . $cacheName . "',],";
    }
    $cacheRows = rtrim($cacheRows, ",");
    return $cacheRows;
}

function displayPowerTrailserStats(PowerTrail $powerTrail, $cachesFoundByUser)
{
    if ($powerTrail->getCacheCount() != 0) {
        $stats2display = round(count($cachesFoundByUser) * 100 / $powerTrail->getCacheCount(), 2);
    } else {
        $stats2display = 0;
    }
    $stats2display .= '% (' . tr('pt017') . ' <span style="color: #00aa00"><b>' . count($cachesFoundByUser) . '</b></span> ' . tr('pt016') . ' <span style="color: #0000aa"><b>' . $powerTrail->getCacheCount() . '</b></span> ' . tr('pt014') . ')';
    return $stats2display;
}

function displayPtOwnerList(PowerTrail $powerTrail)
{
    $ptOwners = $powerTrail->getOwners();
    $ownerList = '';
    isset($_SESSION['user_id']) ? $userLogged = $_SESSION['user_id'] : $userLogged = -1;
    /* @var $owner lib\Objects\PowerTrail\Owner*/
    foreach ($ptOwners as $owner) {
        $ownerList .= '<a href="viewprofile.php?userid=' . $owner->getUserId() . '">' . $owner->getUserName() . '</a>';
        if ($owner->getUserId() != $userLogged) {
            $ownerList .= '<span style="display: none" class="removeUserIcon"><img onclick="ajaxRemoveUserFromPt(' . $owner->getUserId() . ');" src="tpl/stdstyle/images/free_icons/cross.png" width=10 title="' . tr('pt029') . '" /></span>, ';
        } else {
            $ownerList .= ', ';
        }
    }
    $ownerList = substr($ownerList, 0, -2);
    return $ownerList;
}

function displayPtDescriptionUserAction(PowerTrail $powerTrail)
{
    $result = '';
    if (isset($_SESSION['user_id'])) {
        if ( $powerTrail->isUserOwner($_SESSION['user_id']  )) {
            $result = '<a href="javascript:void(0)" id="toggleEditDescButton" class="editPtDataButton" onclick="toggleEditDesc();">' . tr('pt043') . '</a>';
        }
    }
    return $result;
}

function displayPtTypesSelector($htmlid, $selectedId = 0, $witchZeroOption = false)
{
    $ptTypesArr = powerTrailBase::getPowerTrailTypes();
    $selector = '<select id="' . $htmlid . '" name="' . $htmlid . '">';
    if ($witchZeroOption) {
        $selector .= '<option value="0">' . tr('pt165') . '</option>';
    }
    foreach ($ptTypesArr as $id => $type) {
        if ($selectedId == $id)
            $selected = 'selected';
        else
            $selected = '';
        $selector .= '<option ' . $selected . ' value="' . $id . '">' . tr($type['translate']) . '</option>';
    }
    $selector .= '</select>';
    return $selector;
}

function displayPtCommentsSelector($htmlid, PowerTrail $powerTrail, $selectedId = 0, $usr = null)
{
    $appContainer = ApplicationContainer::Instance();
    if($appContainer->getLoggedUser() === null){
        return '';
    }
    $cachesFoundByUser = $powerTrail->getFoundCachsByUser($appContainer->getLoggedUser()->getUserId());
    $percetDemand = $powerTrail->getPerccentRequired();
    $ptId = $powerTrail->getId();
    if ($powerTrail->getCacheCount() != 0) {
        $percentUserFound = round(count($cachesFoundByUser) * 100 / $powerTrail->getCacheCount(), 2);
    } else {
        $percentUserFound = 0;
    }
    $commentsArr = lib\Controllers\PowerTrailController::getEntryTypes();

    $ptOwners = powerTrailBase::getPtOwners($ptId);
    $selector = '<select id="' . $htmlid . '" name="' . $htmlid . '">';


    foreach ($commentsArr as $id => $type) {
        if ($id == 2) {
            if ($percentUserFound < $percetDemand || powerTrailBase::checkUserConquestedPt($appContainer->getLoggedUser()->getUserId(), $ptId) > 0) {
                continue;
            }
            $selected = 'selected="selected"';
        }

        if (!isset($ptOwners[$appContainer->getLoggedUser()->getUserId()]) && ($id == 3 || $id == 4 || $id == 5)) {
            continue;
        }

        if($id == 3 && $powerTrail->canBeOpened() === false && $powerTrail->getStatus() != PowerTrail::STATUS_OPEN){ /* this PT cannot be opened */
            continue;
        }

        if($id === \lib\Objects\PowerTrail\Log::TYPE_ADD_WARNING && $appContainer->getLoggedUser()->getIsAdmin() === false){
            continue;
        }

        if (!isset($selected)){
            $selected = '';
        }
        if ($selectedId == $id){
            $selected = 'selected';
        }


        $selector .= '<option value="' . $id . '" ' . $selected . '>' . tr($type['translate']) . '</option>';
        unset($selected);
    }
    $selector .= '</select>';

    return $selector;
}

function displayPowerTrailLogo($ptId, $img)
{
    // global $picurl;
    if (empty($img)){
        return '/tpl/stdstyle/images/blue/powerTrailGenericLogo.png';
    }else {
        return $img;
    }
}

function getSortBySelector($sel)
{
    $array = array(
        1 => array('val' => "type", 'tr' => 'pt174'),
        2 => array('val' => "name", 'tr' => 'pt168'),
        3 => array('val' => "dateCreated", 'tr' => 'pt169'),
        4 => array('val' => "cacheCount", 'tr' => 'pt170'),
        5 => array('val' => "points", 'tr' => 'pt171'),
        6 => array('val' => "conquestedCount", 'tr' => 'pt172'),
    );
    return generateSelector($array, $sel, 'sortBy');
}

function getSortDirSelector($sel)
{
    $arr = array(
        1 => array('val' => 'asc', 'tr' => 'pt176'),
        2 => array('val' => 'desc', 'tr' => 'pt177'),
    );
    return generateSelector($arr, $sel, 'sortDir');
}

function getGainedPowerTrailsSelector($sel)
{
    $arr = array(
        1 => array('val' => 'no', 'tr' => 'no'),
        2 => array('val' => 'yes', 'tr' => 'yes'),
    );
    return generateSelector($arr, $sel, 'gainedPowerTrailsBool');
}

function getMyPowerTrailsSelector($sel)
{
    $arr = array(
        1 => array('val' => 'no', 'tr' => 'no'),
        2 => array('val' => 'yes', 'tr' => 'yes'),
    );
    return generateSelector($arr, $sel, 'myPowerTrailsBool');
}

function getMiniPowerTrailSelector($sel)
{
    $arr = array(
        1 => array('val' => 'no', 'tr' => 'no'),
        2 => array('val' => 'yes', 'tr' => 'yes'),
    );
    return generateSelector($arr, $sel, 'historicLimitBool');
}

function generateSelector($array, $sel, $name)
{
    $selector = '<select id="' . $name . '" name="' . $name . '">';
    foreach ($array as $opt) {
        if ($opt['val'] == $sel)
            $selector .= '<option selected="selected" value="' . $opt['val'] . '">' . tr($opt['tr']) . '</option>';
        else
            $selector .= '<option value="' . $opt['val'] . '">' . tr($opt['tr']) . '</option>';
    }
    $selector .= '</select>';
    return $selector;
}

function generateStatusSelector($currStatus)
{
    $selector = '<select id="ptStatusSelector">';
    if ($currStatus == 3) { //permanently closed
        $selector .= '<option value="3">' . tr('cs_statusClosed') . '</option>';
    } else {
        foreach (\lib\Controllers\PowerTrailController::getPowerTrailStatus() as $val => $desc) {
            if ($val == $currStatus)
                $selected = 'selected="selected"';
            else
                $selected = '';
            if ($val == 2 && $currStatus != 2) {

            } else // (this status is only after new geoPath creation.)
                $selector .= '<option ' . $selected . ' value="' . $val . '">' . tr($desc['translate']) . '</option>';
        }
    }
    $selector .= '</select>';
    return $selector;
}

function saveCookie()
{
    OcCookie::set("ptFltr", $_REQUEST['filter']);
    OcCookie::set("ptSrBy", $_REQUEST['sortBy']);
    OcCookie::set("ptSrDr", $_REQUEST['sortDir']);
    OcCookie::set("ptGaBool", $_REQUEST['gainedPowerTrailsBool']);
    OcCookie::set("ptMyBool", $_REQUEST['myPowerTrailsBool']);
    OcCookie::set("ptMiniBool", $_REQUEST['historicLimitBool']);
}

