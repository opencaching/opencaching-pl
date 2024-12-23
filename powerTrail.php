<?php

use src\Controllers\MainMapController;
use src\Models\ApplicationContainer;
use src\Models\CacheSet\CacheSetCommon;
use src\Models\CacheSet\GeopathLogoUploadModel;
use src\Models\ChunkModels\DynamicMap\CacheMarkerModel;
use src\Models\ChunkModels\DynamicMap\CacheSetMarkerModel;
use src\Models\ChunkModels\DynamicMap\DynamicMapModel;
use src\Models\OcConfig\OcConfig;
use src\Models\PowerTrail\Log;
use src\Models\PowerTrail\PowerTrail;
use src\Models\User\User;
use src\Utils\Debug\Debug;
use src\Utils\I18n\I18n;
use src\Utils\Text\Formatter;
use src\Utils\Uri\OcCookie;
use src\Utils\Uri\SimpleRouter;
use src\Utils\Uri\Uri;

/**
 *  Power Trails in opencaching
 *  this is display file. for API check dir powerTrail
 */

global $absolute_server_URI;

require_once __DIR__ . '/lib/common.inc.php';

$ocConfig = OcConfig::instance();

if (!OcConfig::areGeopathsSupported()) {
    header("location: {$absolute_server_URI}");
}

$loggedUser = ApplicationContainer::GetAuthorizedUser();
$view = tpl_getView();

$_SESSION['powerTrail']['userFounds'] = (!$loggedUser) ? 0 : $loggedUser->getFoundGeocachesCount();

$firePtMenu = true;

if (isset($_REQUEST['sortBy']) || isset($_REQUEST['filter']) || isset($_REQUEST['sortDir'])
    || isset($_REQUEST['myPowerTrailsBool']) || isset($_REQUEST['gainedPowerTrailsBool'])
    || isset($_REQUEST['historicLimitBool'])) {
    saveCookie();
} else {
    $_REQUEST['sortBy'] = OcCookie::getOrDefault('ptSrBy', 'cacheCount');
    $_REQUEST['filter'] = OcCookie::getOrDefault('ptFltr', 0);
    $_REQUEST['sortDir'] = OcCookie::getOrDefault('ptSrDr', 'desc');
    $_REQUEST['myPowerTrailsBool'] = OcCookie::getOrDefault('ptMyBool', 'no');
    $_REQUEST['gainedPowerTrailsBool'] = OcCookie::getOrDefault('ptGaBool', 'no');
    $_REQUEST['historicLimitBool'] = OcCookie::getOrDefault('ptMiniBool', 'no');
}

$view->setTemplate('powerTrail');
$view->addLocalCss(Uri::getLinkWithModificationTime('/css/powerTrail.css'));
$view->addLocalCss(Uri::getLinkWithModificationTime('/css/ptMenuCss/style.css'));
$view->loadJQuery();
$view->loadJQueryUI();
$view->loadTimepicker();
$view->addHeaderChunk('openLayers5');

if (!$loggedUser && OcConfig::coordsHiddenForNonLogged()) {
    $mapControls = 0;
    tpl_set_var('gpxOptionsTrDisplay', 'none');
} else {
    $mapControls = 1;
    tpl_set_var('gpxOptionsTrDisplay', 'table-row');
}

if (!$loggedUser) {
    tpl_set_var('statsOptionsDisplay', 'display: none;');
} else {
    tpl_set_var('statsOptionsDisplay', '');
}

include_once 'powerTrail/powerTrailController.php';

include_once 'powerTrail/powerTrailMenu.php';

if (isset($_SESSION['user_id'])) {
    tpl_set_var('displayAddCommentSection', 'block');
} else {
    tpl_set_var('displayAddCommentSection', 'none');
}

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
tpl_set_var('language4js', I18n::getCurrentLang());
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
tpl_set_var('zoomControl', $mapControls);
tpl_set_var('scrollwheel', $mapControls);
tpl_set_var('scaleControl', $mapControls);

tpl_set_var('fullCountryMap', '1');
tpl_set_var('ocWaypoint', $GLOBALS['oc_waypoint']);
tpl_set_var('commentsPaginateCount', powerTrailBase::commentsPaginateCount);

tpl_set_var('powerTrailId', '');
tpl_set_var('keszynki', '');
tpl_set_var('cacheFound', '');
tpl_set_var('powerTrailCacheLeft', '');
tpl_set_var('PowerTrails', '');
tpl_set_var('demandPercentMinimum', src\Controllers\PowerTrailController::MINIMUM_PERCENT_REQUIRED);
tpl_set_var('powerTrailDemandPercent', '100');
tpl_set_var('leadingUserId', '');

if (!$loggedUser) {
    tpl_set_var('ptMenu', 'none');
}
$ptMenu = new powerTrailMenu($loggedUser);
tpl_set_var('powerTrailMenu', buildPowerTrailMenu($ptMenu->getPowerTrailsMenu()));

$view->setVar('csWikiLink', OcConfig::getWikiLink('geoPaths'));

$pt = new powerTrailController($loggedUser);
$result = $pt->run();
$actionPerformed = $pt->getActionPerformed();

switch ($actionPerformed) {
    case 'createNewSerie':
        if ($loggedUser && $loggedUser->getFoundGeocachesCount() >= OcConfig::geopathOwnerMinFounds()) {
            tpl_set_var('displayCreateNewPowerTrailForm', 'block');
        } else {
            tpl_set_var('displayToLowUserFound', 'block');
            tpl_set_var('CFrequirment', OcConfig::geopathOwnerMinFounds());
        }
        break;
    case 'selectCaches':
        //$userPowerTrails = $pt->getUserPowerTrails();
        tpl_set_var('displayUserCaches', 'block');
        tpl_set_var('keszynki', displayCaches($result, $pt->getUserPowerTrails()));
        break;
    case 'showAllSeries':
        $ptListData = displayPTrails($pt->getpowerTrails(), $pt->getPowerTrailOwn());
        tpl_set_var('PowerTrails', $ptListData[0]);

        $mapModel = new DynamicMapModel();
        $mapModel->addMarkersWithExtractor(CacheSetMarkerModel::class, $pt->getpowerTrails(), function ($pt) {
            $marker = new CacheSetMarkerModel();
            $marker->icon = CacheSetCommon::GetTypeIcon($pt['type']);
            $marker->lat = $pt['centerLatitude'];
            $marker->lon = $pt['centerLongitude'];
            $marker->name = $pt['name'];
            $marker->link = CacheSetCommon::getCacheSetUrlById($pt['id']);

            return $marker;
        });

        tpl_set_var('mapCenterLat', OcConfig::getMapDefaultCenter()->getLatitude());
        tpl_set_var('mapCenterLon', OcConfig::getMapDefaultCenter()->getLongitude());

        $mapModel->forceDefaultZoom();

        $view->setVar('dynamicMapModel', $mapModel);

        tpl_set_var('filtersTrDisplay', 'table-row');
        tpl_set_var('ptTypeSelector2', displayPtTypesSelector('filter', $_REQUEST['filter'] ?? 0, true));
        tpl_set_var('sortSelector', getSortBySelector($_REQUEST['sortBy']));
        tpl_set_var('sortDirSelector', getSortDirSelector($_REQUEST['sortDir']));
        tpl_set_var('myPowerTrailsBool', getMyPowerTrailsSelector(
            $_REQUEST['myPowerTrailsBool'] ?? 'no'
        ));
        tpl_set_var('gainedPowerTrailsBool', getGainedPowerTrailsSelector(
            $_REQUEST['gainedPowerTrailsBool'] ?? 0
        ));
        tpl_set_var('historicLimitBool', getMiniPowerTrailSelector(
            $_REQUEST['historicLimitBool'] ?? 'no'
        ));
        tpl_set_var('displayedPowerTrailsCount', $pt->getDisplayedPowerTrailsCount());

        tpl_set_var('displayPowerTrails', 'block');

        if ($pt->getPowerTrailOwn() === false) {
            tpl_set_var('statusOrPoints', tr('pt037'));
        } else {
            tpl_set_var('statusOrPoints', tr('cs_status'));
        }
        tpl_set_var('mapOuterdiv', 'block');
        tpl_set_var('mapInit', 1);
        tpl_set_var('fullCountryMap', '1');
        break;
    case 'showMySeries':
        $ptListData = displayPTrails($pt->getpowerTrails(), $pt->getPowerTrailOwn());
        tpl_set_var('PowerTrails', $ptListData[0]);

        $mapModel = new DynamicMapModel();
        $mapModel->addMarkersWithExtractor(CacheSetMarkerModel::class, $pt->getpowerTrails(), function ($pt) {
            $marker = new CacheSetMarkerModel();
            $marker->icon = CacheSetCommon::GetTypeIcon($pt['type']);
            $marker->lat = $pt['centerLatitude'];
            $marker->lon = $pt['centerLongitude'];
            $marker->name = $pt['name'];
            $marker->link = CacheSetCommon::getCacheSetUrlById($pt['id']);

            return $marker;
        });

        tpl_set_var('mapCenterLat', OcConfig::getMapDefaultCenter()->getLatitude());
        tpl_set_var('mapCenterLon', OcConfig::getMapDefaultCenter()->getLongitude());
        // no need to set coords in map-model - defaults are the same

        $view->setVar('dynamicMapModel', $mapModel);

        // tpl_set_var('ptTypeSelector2', displayPtTypesSelector('filter',$_REQUEST['filter'], true));
        // tpl_set_var('sortSelector', getSortBySelector($_REQUEST['sortBy']));
        // tpl_set_var('sortDirSelector', getSortDirSelector($_REQUEST['sortDir']));
        tpl_set_var('filtersTrDisplay', 'none');

        tpl_set_var('displayedPowerTrailsCount', $pt->getDisplayedPowerTrailsCount());

        tpl_set_var('displayPowerTrails', 'block');

        if ($pt->getPowerTrailOwn() === false) {
            tpl_set_var('statusOrPoints', tr('pt037'));
        } else {
            tpl_set_var('statusOrPoints', tr('cs_status'));
        }
        tpl_set_var('mapOuterdiv', 'block');
        tpl_set_var('mapInit', 1);
        tpl_set_var('fullCountryMap', '1');
        break;
    case 'showSerie':
        if (!isset($_GET['ptrail']) || empty($_GET['ptrail']) || (is_numeric($_GET['ptrail']) && intval($_GET['ptrail']) > intval(PowerTrail::getMaxPowerTrailId()))) {
            // just redirect to all powertrails
            header('Location: ' . '//' . $_SERVER['HTTP_HOST'] . '/powerTrail.php');

            exit;
        }
        $powerTrail = new PowerTrail(['id' => (int)$_GET['ptrail']]);

        if (empty($powerTrail->getName()) && empty($powerTrail->getType())) {
            Debug::errorLog(sprintf(
                "Power Trail does not exist or is incomplete. ID: %s",
                $_GET['ptrail'] ?? '',
            ));
            header('Location: ' . '//' . $_SERVER['HTTP_HOST'] . '/powerTrail.php');
            exit;
        }

        $ptOwners = $pt->getPtOwners();
        $_SESSION['ptName'] = powerTrailBase::clearPtNames($powerTrail->getName());
        tpl_set_var('powerTrailId', $powerTrail->getId());

        if (!$loggedUser && OcConfig::coordsHiddenForNonLogged()) {
            tpl_set_var('mapOuterdiv', 'none');
        } else {
            tpl_set_var('mapOuterdiv', 'block');
        }

        $userIsOwner = $loggedUser && $powerTrail->isUserOwner($loggedUser->getUserId());

        if ($powerTrail->getStatus() == 1 || $userIsOwner
            || ($loggedUser && $loggedUser->hasOcTeamRole())) {
            $ptTypesArr = powerTrailBase::getPowerTrailTypes();
            $ptStatusArr = \src\Controllers\PowerTrailController::getPowerTrailStatus();
            $foundCachsByUser = (!$loggedUser) ? [] : $powerTrail->getFoundCachsByUser($loggedUser->getUserId());
            $leadingUser = powerTrailBase::getLeadingUser($powerTrail->getId());

            if ($powerTrail->getConquestedCount() > 0) {
                $removeCacheButtonDisplay = 'none';
            } else {
                $removeCacheButtonDisplay = 'inline';
            }
            tpl_set_var('ptStatusSelector', generateStatusSelector($powerTrail->getStatus()));
            tpl_set_var('removeCacheButtonDisplay', $removeCacheButtonDisplay);
            tpl_set_var('leadingUserId', $leadingUser['user_id'] ?? '');
            tpl_set_var('leadingUserName', htmlspecialchars($leadingUser['username'] ?? ''));
            tpl_set_var('fullCountryMap', '0');
            tpl_set_var('ptTypeName', tr($ptTypesArr[$powerTrail->getType()]['translate']));
            tpl_set_var('displaySelectedPowerTrail', 'block');
            tpl_set_var('powerTrailName', htmlspecialchars($powerTrail->getName(), ENT_COMPAT | ENT_HTML5));
            tpl_set_var('powerTrailDescription', stripslashes(htmlspecialchars_decode($powerTrail->getDescription())));
            tpl_set_var('displayPtDescriptionUserAction', displayPtDescriptionUserAction($powerTrail));
            tpl_set_var('powerTrailDateCreated', Formatter::date($powerTrail->getDateCreated()));
            tpl_set_var('powerTrailCacheCount', $powerTrail->getCacheCount());

            tpl_set_var('powerTrailActiveCacheCount', $powerTrail->getActiveGeocacheCount());
            tpl_set_var('powerTrailUnavailableCacheCount', $powerTrail->getUnavailableGeocacheCount());
            tpl_set_var('powerTrailArchivedCacheCount', $powerTrail->getArchivedGeocacheCount());

            tpl_set_var('powerTrailCacheLeft', ($powerTrail->getCacheCount() - count($foundCachsByUser)));
            tpl_set_var('powerTrailOwnerList', displayPtOwnerList($powerTrail));
            tpl_set_var('date', Formatter::date('now'));
            tpl_set_var('powerTrailDemandPercent', $powerTrail->getPerccentRequired());
            tpl_set_var('ptCommentsSelector', displayPtCommentsSelector('commentType', $powerTrail, null, $loggedUser));
            tpl_set_var('conquestCount', $powerTrail->getConquestedCount());
            tpl_set_var('ptPoints', $powerTrail->getPoints());
            tpl_set_var('cacheFound', count($foundCachsByUser));
            tpl_set_var('powerTrailLogo', displayPowerTrailLogo($powerTrail->getImage()));
            tpl_set_var('powerTrailserStats', $powerTrail->displayPowerTrailserStats($powerTrail, $foundCachsByUser));

            if ($userIsOwner) {
                tpl_set_var('ptStatus', tr($ptStatusArr[$powerTrail->getStatus()]['translate']));
                tpl_set_var('displayAddCachesButtons', 'block');
                tpl_set_var('percentDemandUserActions', 'block');
                tpl_set_var('ptTypeUserActions', '<a href="javascript:void(0)" class="editPtDataButton" onclick="togglePtTypeEdit();">' . tr('pt046') . '</a>');
                tpl_set_var('ptDateUserActions', '<a href="javascript:void(0)" class="editPtDataButton" onclick="togglePtDateEdit();">' . tr('pt045') . '</a>');
                tpl_set_var('cacheCountUserActions', '<a href="javascript:void(0)" class="editPtDataButton" onclick="ajaxCountPtCaches(' . $powerTrail->getId() . ')">' . tr('pt033') . '</a>');
                tpl_set_var('ownerListUserActions', '<a id="dddx" href="javascript:void(0)" class="editPtDataButton" onclick="clickShow(\'addUser\', \'dddx\'); ">' . tr('pt030') . '</a> <span style="display: none" id="addUser">' . tr('pt028') . '<input type="text" id="addNewUser2pt" /><br /><a href="javascript:void(0)" class="editPtDataButton" onclick="cancellAddNewUser2pt()" >' . tr('pt031') . '</a><a href="javascript:void(0)" class="editPtDataButton" onclick="ajaxAddNewUser2pt(' . $powerTrail->getId() . ')" >' . tr('pt032') . '</a></span>');
                tpl_set_var('ptTypesSelector', displayPtTypesSelector('ptType1', $powerTrail->getType()));

                $view->addHeaderChunk('upload/upload');
                $view->addHeaderChunk('handlebarsJs');
                $uploadModel = GeopathLogoUploadModel::forGeopath($powerTrail->getId());
                $view->setVar('logoUploadModelJson', $uploadModel->getJsonParams());
            } else {
                tpl_set_var('ptStatus', '');
                tpl_set_var('percentDemandUserActions', 'none');
                tpl_set_var('displayAddCachesButtons', 'none');
                tpl_set_var('ptTypeUserActions', '');
                tpl_set_var('ptDateUserActions', '');
                tpl_set_var('cacheCountUserActions', '');
                tpl_set_var('ownerListUserActions', '');
            }

            tpl_set_var('mapInit', 1);

            $ptId = $powerTrail->getId();
            $view->setVar(
                'fullScreenMapPtLink',
                SimpleRouter::getLink(MainMapController::class, 'fullScreen') . "?cs={$ptId}"
            );

            $mapModel = new DynamicMapModel();

            tpl_set_var('mapCenterLat', $powerTrail->getCenterCoordinates()->getLatitude());
            tpl_set_var('mapCenterLon', $powerTrail->getCenterCoordinates()->getLongitude());

            if ($loggedUser || !OcConfig::coordsHiddenForNonLogged()) {
                $mapModel->addMarkersWithExtractor(CacheMarkerModel::class, $powerTrail->getGeocaches()->getArrayCopy(), function ($geocache) use ($loggedUser) {
                    return CacheMarkerModel::fromGeocacheFactory($geocache, $loggedUser);
                });
            }

            $view->setVar('dynamicMapModel', $mapModel);
        } else {
            tpl_set_var('mapOuterdiv', 'none');
            tpl_set_var('mainPtInfo', tr('pt018'));
        }
        break;
    default:
        tpl_redirect('powerTrail.php');
        break;
}

// exit;

$view->buildView();

// budujemy kod html ktory zostaje wsylany do przegladraki
//$Opensprawdzacz->endzik();

function buildPowerTrailMenu($menuArray): string
{
    // <li class="topmenu"><a href="javascript:void(0)" style="height:16px;line-height:16px;"><span>Item 1</span></a>
    // <ul>
    // <li class="subfirst"><a href="javascript:void(0)">Item 1 0</a></li>
    // <li class="sublast"><a href="javascript:void(0)">Item 1 1</a></li>
    // </ul></li>
    // <li class="topmenu"><a href="javascript:void(0)" style="height:16px;line-height:16px;">Item 3</a></li>
    // <li class="topmenu"><a href="javascript:void(0)" style="height:16px;line-height:16px;">Item 2</a></li>

    $menu = '';

    foreach ($menuArray as $menuItem) {
        $menu .= '<li class="topmenu"><a href="' . $menuItem['script'] . '?ptAction=' . $menuItem['action'] . '" style="height:16px;line-height:16px;">' . $menuItem['name'] . '</a></li>';
    }

    return $menu;
}

function displayCaches($caches, $pTrails): string
{
    // powerTrailController::debug($caches);
    // powerTrailController::debug($pTrails);
    if (count($caches) == 0) {
        tpl_set_var('displayUserCaches', 'none');
        tpl_set_var('nocachess', 'block');

        return '';
    }
    $rows = '';

    foreach ($caches as $cache) {
        $ptSelector = '<select onchange="ajaxAddCacheToPT(' . $cache['cache_id'] . ')" id="ptSelectorForCache' . $cache['cache_id'] . '"><option value="-1">---</option>';
        $hidden = '<input type="hidden" id="h' . $cache['cache_id'] . '" value="-1" >';

        foreach ($pTrails as $pTrail) {
            if ($cache['PowerTrailId'] == $pTrail['id']) {
                $ptSelector .= '<option selected value=' . $pTrail['id'] . '>' . $pTrail['name'] . '</option>';
                $hidden = '<input type="hidden" id="h' . $cache['cache_id'] . '" value=' . $pTrail['id'] . ' >';
            } else {
                $ptSelector .= '<option value=' . $pTrail['id'] . '>' . $pTrail['name'] . '</option>';
            }
        }
        $ptSelector .= '</select>';
        $rows .= '<tr><td><a href="' . $cache['wp_oc'] . '">' . $cache['wp_oc'] . '</a></td><td>' . $cache['name'] . '</td><td>' . $ptSelector . '</td>
        <td width="50"><img style="display: none" id="addCacheLoader' . $cache['cache_id'] . '" src="images/misc/ptPreloader.gif" alt="">
        <span id="cacheInfo' . $cache['cache_id'] . '" style="display: none "><img src="images/free_icons/accept.png" alt=""></span>
        <span id="cacheInfoNOK' . $cache['cache_id'] . '" style="display: none "><img src="images/free_icons/exclamation.png" alt=""></span>'
            . $hidden
            . '</td></tr>';
    }

    return $rows;
}

function displayPTrails($pTrails, $areOwnSeries): array
{
    $ptTypes = powerTrailBase::getPowerTrailTypes();
    $ptStatus = \src\Controllers\PowerTrailController::getPowerTrailStatus();

    $dataForList = '';
    $dataForMap = '';

    if (!is_array($pTrails)) {
        return ['', ''];
    }

    foreach ($pTrails as $pTrail) {
        $pTrail['name'] = str_replace("'", '&#39;', $pTrail['name']);

        $dataForMap .= '[' . $pTrail['centerLatitude'] . ',' . $pTrail['centerLongitude']
            . ",'<a href=powerTrail.php?ptAction=showSerie&ptrail=" . $pTrail['id'] . '>' . $pTrail['name']
            . "</a>','" . $ptTypes[$pTrail['type']]['icon'] . "','" . $pTrail['name'] . "'],";

        if (!$areOwnSeries) {
            $ownOrAll = round($pTrail['points'], 2);
        } else {
            $ownOrAll = tr($ptStatus[$pTrail['status']]['translate']);
        }

        if (strlen($pTrail['name']) > 40) {
            $pTrail['name'] = mb_substr($pTrail['name'], 0, 35) . ' (...)';
        }
        $dataForList .= '<tr>'
            . '<td style="text-align: right; padding-right: 5px;"><b><a href="powerTrail.php?ptAction=showSerie&ptrail=' . $pTrail['id'] . '">' . $pTrail['name'] . '</a></b></td>'
            . '<td class="ptType"><img src="' . $ptTypes[$pTrail['type']]['icon'] . '" alt=""><span>' . tr($ptTypes[$pTrail['type']]['translate']) . '</span></td>'
            . '<td class="ptTd ptStatusOrPoints">' . $ownOrAll . '</td>'
            . '<td class="ptTd">' . Formatter::date($pTrail['dateCreated']) . '</td>'
            . '<td class="ptTd">' . $pTrail['cacheCount'] . '</td>'
            . '<td class="ptTd">' . $pTrail['conquestedCount'] . '</td>
        </tr>';
    }

    return [$dataForList, rtrim($dataForMap, ',')];
}

function displayPtOwnerList(PowerTrail $powerTrail)
{
    $ptOwners = $powerTrail->getOwners();
    $ownerList = '';
    isset($_SESSION['user_id']) ? $userLogged = $_SESSION['user_id'] : $userLogged = -1;
    // @var $owner src\Models\PowerTrail\Owner
    if($ptOwners){
        foreach ($ptOwners as $owner) {
            $ownerList .= '<a href="viewprofile.php?userid=' . $owner->getUserId() . '">' . $owner->getUserName() . '</a>';

            if ($owner->getUserId() != $userLogged) {
                $ownerList .= '<span style="display: none" class="removeUserIcon"><img onclick="ajaxRemoveUserFromPt(' . $owner->getUserId() . ')" src="images/free_icons/cross.png" width=10 title="' . tr('pt029') . '" alt="' . tr('pt029') . '"></span>, ';
            } else {
                $ownerList .= ', ';
            }
        }
    }
    return substr($ownerList, 0, -2);
}

function displayPtDescriptionUserAction(PowerTrail $powerTrail): string
{
    $result = '';

    if (isset($_SESSION['user_id'])) {
        if ($powerTrail->isUserOwner($_SESSION['user_id'])) {
            $result = '<a href="javascript:void(0)" id="toggleEditDescButton" class="editPtDataButton" onclick="toggleEditDesc();">' . tr('pt043') . '</a>';
        }
    }

    return $result;
}

function displayPtTypesSelector($htmlid, $selectedId = 0, $witchZeroOption = false): string
{
    $ptTypesArr = powerTrailBase::getPowerTrailTypes();
    $selector = '<select id="' . $htmlid . '" name="' . $htmlid . '">';

    if ($witchZeroOption) {
        $selector .= '<option value="0">' . tr('pt165') . '</option>';
    }

    foreach ($ptTypesArr as $id => $type) {
        if ($selectedId == $id) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        $selector .= '<option ' . $selected . ' value="' . $id . '">' . tr($type['translate']) . '</option>';
    }
    $selector .= '</select>';

    return $selector;
}

function displayPtCommentsSelector($htmlid, PowerTrail $powerTrail, $selectedId = 0, User $loggedUser = null): string
{
    if (!$loggedUser) {
        return '';
    }
    $cachesFoundByUser = $powerTrail->getFoundCachsByUser($loggedUser->getUserId());
    $percetDemand = $powerTrail->getPerccentRequired();
    $ptId = $powerTrail->getId();

    if ($powerTrail->getCacheCount() != 0) {
        $percentUserFound = round(count($cachesFoundByUser) * 100 / $powerTrail->getCacheCount(), 2);
    } else {
        $percentUserFound = 0;
    }
    $commentsArr = src\Controllers\PowerTrailController::getEntryTypes();

    $ptOwners = powerTrailBase::getPtOwners($ptId);
    $selector = '<select id="' . $htmlid . '" name="' . $htmlid . '">';

    foreach ($commentsArr as $id => $type) {
        if ($id == 2) {
            if ($percentUserFound < $percetDemand || powerTrailBase::checkUserConquestedPt($loggedUser->getUserId(), $ptId) > 0) {
                continue;
            }
            $selected = 'selected="selected"';
        }

        if (!isset($ptOwners[$loggedUser->getUserId()]) && ($id == 3 || $id == 4 || $id == 5)) {
            continue;
        }

        if ($id == 3 && $powerTrail->canBeOpened() === false && $powerTrail->getStatus() != PowerTrail::STATUS_OPEN) { // this PT cannot be opened
            continue;
        }

        if ($id === Log::TYPE_ADD_WARNING && !$loggedUser->hasOcTeamRole()) {
            continue;
        }

        if (!isset($selected)) {
            $selected = '';
        }

        if ($selectedId == $id) {
            $selected = 'selected';
        }

        $selector .= '<option value="' . $id . '" ' . $selected . '>' . tr($type['translate']) . '</option>';
        unset($selected);
    }
    $selector .= '</select>';

    return $selector;
}

function displayPowerTrailLogo($img)
{
    if (empty($img)) {
        return '/images/blue/powerTrailGenericLogo.png';
    }

    return $img;
}

function getSortBySelector($sel): string
{
    $array = [
        1 => ['val' => 'type', 'tr' => 'pt174'],
        2 => ['val' => 'name', 'tr' => 'pt168'],
        3 => ['val' => 'dateCreated', 'tr' => 'pt169'],
        4 => ['val' => 'cacheCount', 'tr' => 'pt170'],
        5 => ['val' => 'points', 'tr' => 'pt171'],
        6 => ['val' => 'conquestedCount', 'tr' => 'pt172'],
    ];

    return generateSelector($array, $sel, 'sortBy');
}

function getSortDirSelector($sel): string
{
    $arr = [
        1 => ['val' => 'asc', 'tr' => 'pt176'],
        2 => ['val' => 'desc', 'tr' => 'pt177'],
    ];

    return generateSelector($arr, $sel, 'sortDir');
}

function getGainedPowerTrailsSelector($sel): string
{
    $arr = [
        1 => ['val' => 'no', 'tr' => 'no'],
        2 => ['val' => 'yes', 'tr' => 'yes'],
    ];

    return generateSelector($arr, $sel, 'gainedPowerTrailsBool');
}

function getMyPowerTrailsSelector($sel): string
{
    $arr = [
        1 => ['val' => 'no', 'tr' => 'no'],
        2 => ['val' => 'yes', 'tr' => 'yes'],
    ];

    return generateSelector($arr, $sel, 'myPowerTrailsBool');
}

function getMiniPowerTrailSelector($sel): string
{
    $arr = [
        1 => ['val' => 'no', 'tr' => 'no'],
        2 => ['val' => 'yes', 'tr' => 'yes'],
    ];

    return generateSelector($arr, $sel, 'historicLimitBool');
}

function generateSelector($array, $sel, $name): string
{
    $selector = '<select id="' . $name . '" name="' . $name . '">';

    foreach ($array as $opt) {
        if ($opt['val'] == $sel) {
            $selector .= '<option selected="selected" value="' . $opt['val'] . '">' . tr($opt['tr']) . '</option>';
        } else {
            $selector .= '<option value="' . $opt['val'] . '">' . tr($opt['tr']) . '</option>';
        }
    }
    $selector .= '</select>';

    return $selector;
}

function generateStatusSelector($currStatus): string
{
    $selector = '<select id="ptStatusSelector">';

    if ($currStatus == 3) { //permanently closed
        $selector .= '<option value="3">' . tr('cs_statusClosed') . '</option>';
    } else {
        foreach (\src\Controllers\PowerTrailController::getPowerTrailStatus() as $val => $desc) {
            if ($val == $currStatus) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }

            if ($val == 2 && $currStatus != 2) {
            } else { // (this status is only after new geoPath creation.)
                $selector .= '<option ' . $selected . ' value="' . $val . '">' . tr($desc['translate']) . '</option>';
            }
        }
    }
    $selector .= '</select>';

    return $selector;
}

function saveCookie()
{
    if (isset($_REQUEST['filter'])) {
        OcCookie::set('ptFltr', $_REQUEST['filter']);
    }

    if (isset($_REQUEST['sortBy'])) {
        OcCookie::set('ptSrBy', $_REQUEST['sortBy']);
    }

    if (isset($_REQUEST['sortDir'])) {
        OcCookie::set('ptSrDr', $_REQUEST['sortDir']);
    }

    if (isset($_REQUEST['gainedPowerTrailsBool'])) {
        OcCookie::set('ptGaBool', $_REQUEST['gainedPowerTrailsBool']);
    }

    if (isset($_REQUEST['myPowerTrailsBool'])) {
        OcCookie::set('ptMyBool', $_REQUEST['myPowerTrailsBool']);
    }

    if (isset($_REQUEST['historicLimitBool'])) {
        OcCookie::set('ptMiniBool', $_REQUEST['historicLimitBool']);
    }
}
