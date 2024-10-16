<?php

use src\Models\ApplicationContainer;
use src\Models\GeoCache\GeoCache;
use src\Models\PowerTrail\PowerTrail;
use src\Utils\I18n\I18n;
use src\Utils\Uri\Uri;
use src\Utils\View\View;

require_once __DIR__ . '/lib/common.inc.php';

/** @var View $view */
$view = tpl_getView();

$loggedUser = ApplicationContainer::GetAuthorizedUser();

if (empty($loggedUser) || ! $loggedUser->hasOcTeamRole()) {
    $view->redirect('/');
}

$view->setTemplate('powerTrailCOG');

$pt = new powerTrailController($loggedUser);
$pt->run();

tpl_set_var('selPtDiv', 'none');
tpl_set_var('PtDetailsDiv', 'none');
tpl_set_var('language4js', I18n::getCurrentLang());

$view->loadJQuery();
$view->addLocalCss(Uri::getLinkWithModificationTime('/css/powerTrail.css'));

if (isset($_REQUEST['ptSelector'])) {
    $powerTrail = new PowerTrail(['id' => $_REQUEST['ptSelector']]);
    $_SESSION['ptRmByCog'] = 1;
    $ptData = powerTrailBase::getPtDbRow($_REQUEST['ptSelector']);
    $ptStatus = src\Controllers\PowerTrailController::getPowerTrailStatus();
    $ptType = powerTrailBase::getPowerTrailTypes();

    tpl_set_var('ptCaches', preparePtCaches($powerTrail));
    tpl_set_var('ptStatSelect', generateStatusSelector($powerTrail->getStatus()));
    tpl_set_var('ptId', $powerTrail->getId());
    tpl_set_var('ptName', $powerTrail->getName());
    tpl_set_var('ptUrl', $powerTrail->getPowerTrailUrl());
    tpl_set_var('ptType', tr($ptType[$ptData['type']]['translate']));
    tpl_set_var('ptStatus', tr($ptStatus[$ptData['status']]['translate']));

    tpl_set_var('PtDetailsDiv', 'block');

    $view->setVar('allPtsUrl', '');
    $view->setVar('allPtsText', '');
} else {
    if (isset($_GET['allPts'])) {
        // display all powertrails - even these not published
        $pts = powerTrailBase::getAllPt('');
        $view->setVar('allPtsUrl', Uri::removeParam('allPts', Uri::getCurrentUri(true)));
        $view->setVar('allPtsText', 'Display only published geopaths');
    } else {
        $pts = powerTrailBase::getAllPt('AND status != 2');
        $view->setVar('allPtsUrl', Uri::addParamsToUri(Uri::getCurrentUri(true), ['allPts' => null]));
        $view->setVar('allPtsText', 'Display all geopaths (also not published)');
    }
    tpl_set_var('ptSelector', makePtSelector($pts, 'ptSelector'));
    tpl_set_var('selPtDiv', 'block');
}

$view->buildView();

function makePtSelector($ptAll, $id)
{
    $selector = '<select class="form-control input400" id=' . $id . ' name=' . $id . '>';

    foreach ($ptAll as $pt) {
        $selector .= '<option value=' . $pt['id'] . '>' . $pt['name'] . '</option>';
    }
    $selector .= '</select>';

    return $selector;
}

function preparePtCaches(PowerTrail $powerTrail)
{
    $table = '<table class="table" style="width: 100%"><tr>'
        . ' <th>' . tr('name_label') . '</th>'
        . ' <th>' . tr('owner') . '</th>'
        . ' <th>' . tr('waypoint') . '</th>'
        . ' <th style="text-align: center;">' . tr('number_founds') . '</th>'
        . ' <th>&nbsp;</th>'
        . '</tr>';
    $color = '#eeeeff';

    // @var $geocache GeoCache
    foreach ($powerTrail->getGeocaches() as $geocache) {
        if ($color == '#eeffee') {
            $color = '#eeeeff';
        } else {
            $color = '#eeffee';
        }

        if ($geocache->getFounds() > 0) {
            $color = 'ffbbbb';
        }
        $table .= '<tr style="background-color: ' . $color . ';" id="tr' . $geocache->getCacheId() . '">
            <td>' . $geocache->getCacheName() . '</td>
            <td>' . $geocache->getOwner()->getUserName() . '</td>
            <td><a href="' . $geocache->getWaypointId() . '">' . $geocache->getWaypointId() . '</a></td>
            <td style="text-align: center;">' . $geocache->getFounds() . '</td>
            <td style="text-align: center;"><a href="javascript:void(0);" onclick="rmCache(' . $geocache->getCacheId() . ');" class="editPtDataButton">' . tr('pt130') . '</a> <img src="images/misc/ptPreloader.gif"  style="display: none" id="rmCacheLoader' . $geocache->getCacheId() . '" alt=""> </td>
        </tr>';
    }
    $table .= '</table>';

    return $table;
}

function generateStatusSelector($currStatus)
{
    $selector = '<select id="ptStatusSelector">';

    foreach (src\Controllers\PowerTrailController::getPowerTrailStatus() as $val => $desc) {
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
    $selector .= '</select>';

    return $selector;
}
