<?php

use lib\Objects\PowerTrail\PowerTrail;
use lib\Objects\GeoCache\GeoCache;
use Utils\Uri\Uri;
use Utils\I18n\I18n;

require_once (__DIR__.'/lib/common.inc.php');

if ($error == false) {
    $tplname = 'powerTrailCOG';

    $pt = new powerTrailController($usr);
    $result = $pt->run();
    if ($usr['userid'] == 9067) {

    } else {
        if (!(isset($usr['admin']) && $usr['admin'])) {
            print tr('pt236');
            exit;
        }
    }
    tpl_set_var("selPtDiv", 'none');
    tpl_set_var("PtDetailsDiv", 'none');
    tpl_set_var('language4js', I18n::getCurrentLang());

    $view->loadJQuery();
    $view->addLocalCss(Uri::getLinkWithModificationTime('tpl/stdstyle/css/powerTrail.css'));

    if (isset($_REQUEST['ptSelector'])) {
        $powerTrail = new PowerTrail(array('id' => $_REQUEST['ptSelector']));
        $_SESSION['ptRmByCog'] = 1;
        $ptData = powerTrailBase::getPtDbRow($_REQUEST['ptSelector']);
        $ptStatus = \lib\Controllers\PowerTrailController::getPowerTrailStatus();
        $ptType = powerTrailBase::getPowerTrailTypes();

        tpl_set_var("ptCaches", preparePtCaches($powerTrail));
        tpl_set_var("ptStatSelect", generateStatusSelector($powerTrail->getStatus()));
        tpl_set_var("ptId", $powerTrail->getId());
        tpl_set_var("ptName", $powerTrail->getName());
        tpl_set_var("ptType", tr($ptType[$ptData['type']]['translate']));
        tpl_set_var("ptStatus", tr($ptStatus[$ptData['status']]['translate']));

        tpl_set_var("PtDetailsDiv", 'block');
    } else {
        tpl_set_var("ptSelector", makePtSelector(powerTrailBase::getAllPt('AND status != 2'), 'ptSelector'));
        tpl_set_var("selPtDiv", 'block');
    }
}

tpl_BuildTemplate();

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
            . ' <th>'. tr('waypoint') . '</th>'
            . ' <th style="text-align: center;">' . tr('number_founds') . '</th>'
            . ' <th>&nbsp;</th>'
            . '</tr>';
    $color = '#eeeeff';
    /* @var $geocache GeoCache */
    foreach ($powerTrail->getGeocaches() as $geocache) {
        if ($color == '#eeffee') {
            $color = '#eeeeff';
        } else {
            $color = '#eeffee';
        }
        if($geocache->getFounds() > 0){
            $color = 'ffbbbb';
        }
        $table .= '<tr style="background-color: ' . $color . ';" id="tr' . $geocache->getCacheId() . '">
            <td>' . $geocache->getCacheName() . '</td>
            <td>' . $geocache->getOwner()->getUserName() . '</td>
            <td><a href="'.$geocache->getWaypointId().'">' . $geocache->getWaypointId() . '</a></td>
            <td style="text-align: center;">' . $geocache->getFounds() . '</td>
            <td style="text-align: center;"><a href="javascript:void(0);" onclick="rmCache(' . $geocache->getCacheId() . ');" class="editPtDataButton">' . tr('pt130') . '</a> <img src="tpl/stdstyle/images/misc/ptPreloader.gif"  style="display: none" id="rmCacheLoader' . $geocache->getCacheId() . '" alt=""> </td>
        </tr>';
    }
    $table .= '</table>';
    return $table;
}

function generateStatusSelector($currStatus)
{
    $selector = '<select id="ptStatusSelector">';
    foreach (\lib\Controllers\PowerTrailController::getPowerTrailStatus() as $val => $desc) {
        if ($val == $currStatus)
            $selected = 'selected="selected"';
        else
            $selected = '';
        if ($val == 2 && $currStatus != 2) {

        } else // (this status is only after new geoPath creation.)
            $selector .= '<option ' . $selected . ' value="' . $val . '">' . tr($desc['translate']) . '</option>';
    }
    $selector .= '</select>';
    return $selector;
}
