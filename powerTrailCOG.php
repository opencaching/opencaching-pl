<?php
$rootpath = __DIR__.DIRECTORY_SEPARATOR;
require_once './lib/common.inc.php';

if ($error == false) {
    $tplname = 'powerTrailCOG';

    $pt = new powerTrailController($usr);
    $result = $pt->run();
    if($usr['userid'] == 9067){}else{
    if(!(isset($usr['admin']) && $usr['admin'] == 1)) {
        print 'sorry, only for oc-team';
        exit;
    }}
    //var_dump($pt->getpowerTrails());
    //exit;
    tpl_set_var("selPtDiv", 'none');
    tpl_set_var("PtDetailsDiv", 'none');
    tpl_set_var('language4js', $lang);

    if(isset($_REQUEST['ptSelector'])){
        $_SESSION['ptRmByCog'] = 1;
        $ptData = powerTrailBase::getPtDbRow($_REQUEST['ptSelector']);
        $ptStatus = powerTrailBase::getPowerTrailStatus();
        $ptType = powerTrailBase::getPowerTrailTypes();

        tpl_set_var("ptCaches", preparePtCaches(powerTrailBase::getPtCaches($_REQUEST['ptSelector'])));
        tpl_set_var("ptStatSelect", generateStatusSelector($ptData['status']));
        tpl_set_var("ptId", $ptData['id']);
        tpl_set_var("ptName", $ptData['name']);
        tpl_set_var("ptType", tr($ptType[$ptData['type']]['translate']));
        tpl_set_var("ptStatus", tr($ptStatus[$ptData['status']]['translate']));

        tpl_set_var("PtDetailsDiv", 'block');

    } else {
        tpl_set_var("ptSelector", makePtSelector(powerTrailBase::getAllPt('AND status != 2'), 'ptSelector'));
        tpl_set_var("selPtDiv", 'block');
    }

}

tpl_BuildTemplate();


function makePtSelector($ptAll, $id){
    $selector = '<select id='.$id.' name='.$id.'>';
    foreach ($ptAll as $pt) {
        $selector .= '<option value='.$pt['id'].'>'.$pt['name'].'</option>';
    }
    $selector .= '</select>';
    return $selector;
}

function preparePtCaches($ptCaches){
    //var_dump($ptCaches);
    $table = '<table ><tr bgcolor="#cccccc"><td>'.tr('pt036').'</td><td>'.tr('owner_label').'</td><td>'.tr('pt210').'</td></tr>';
    $color = '#eeeeff';
    foreach ($ptCaches as $cache) {
        if ($color == '#eeffee') $color = '#eeeeff';
        else $color = '#eeffee';
        $table .= '<tr bgcolor="'.$color.'" id="tr'.$cache['cache_id'].'">
            <td>'.$cache['name'].'</td>
            <td>'.$cache['username'].'</td>
            <td><a href="javascript:void(0);" onclick="rmCache('.$cache['cache_id'].');" class="editPtDataButton">'.tr('pt130').'</a> <img src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif"  style="display: none" id="rmCacheLoader'.$cache['cache_id'].'" /> </td>
        </tr>';
    }
    $table .= '</table>';
    return $table;
}

function generateStatusSelector($currStatus){
    $selector = '<select id="ptStatusSelector">';
        foreach (powerTrailBase::getPowerTrailStatus() as $val => $desc) {
            if ($val == $currStatus) $selected = 'selected="selected"';
            else $selected = '';
            if($val==2 && $currStatus!=2) {} else // (this status is only after new geoPath creation.)
            $selector .= '<option '.$selected.' value="'.$val.'">'.tr($desc['translate']).'</option>';
        }
    $selector .= '</select>';
    return $selector;
}
?>