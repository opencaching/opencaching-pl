<?php

$rootpath = '../';
require('../lib/common.inc.php');
global $lang;
//prepare the templates and include all neccessary
//  require_once('./lib/common.inc.php');
//Preprocessing
if ($error == false) {
    require("../lib/jpgraph/src/jpgraph.php");
    require("../lib/jpgraph/src/jpgraph_pie.php");
    require("../lib/jpgraph/src/jpgraph_pie3d.php");



    // check for old-style parameters
    if (isset($_REQUEST['cacheid'])) {
        $cache_id = $_REQUEST['cacheid'];
    }

    $y = array();
    $x = array();

    if (checkField('log_types', $lang))
        $lang_db = $lang;
    else
        $lang_db = "en";

// Ustawic sprawdzanie jezyka  w cache_type.pl !!!!
    $rsCSF = sql("SELECT COUNT(`cache_logs`.`type`) `count`, `log_types`.`&2` AS `type` FROM `cache_logs` INNER JOIN `log_types` ON (`cache_logs`.`type`=`log_types`.`id`) WHERE type=1 AND cache_logs.deleted=0 AND cache_logs.cache_id=&1 GROUP BY `cache_logs`.`type` ORDER BY `log_types`.`pl` ASC", $cache_id, $lang_db);

    if ($rsCSF !== false) {
        $xtitle = "";
        $ry = mysql_fetch_array($rsCSF);
        $y[] = $ry['count'];
        $x[] = $ry['type'];
    } else {
        $x[] = tr("found");
    }

    $rsCSNF = sql("SELECT COUNT(`cache_logs`.`type`) `count`, `log_types`.`&2` AS `type` FROM `cache_logs` INNER JOIN `log_types` ON (`cache_logs`.`type`=`log_types`.`id`) WHERE type=2 AND cache_logs.deleted=0 AND cache_logs.cache_id=&1 GROUP BY `cache_logs`.`type` ORDER BY `log_types`.`pl` ASC", $cache_id, $lang_db);

    if ($rsCSNF !== false) {
        $xtitle = "";
        $ry = mysql_fetch_array($rsCSNF);
        $y[] = $ry['count'];
        $x[] = $ry['type'];
    } else {
        $x[] = tr("not_found");
        $y[] = '0';
    }


    $rsCSC = sql("SELECT COUNT(`cache_logs`.`type`) `count`, `log_types`.`&2` AS `type` FROM `cache_logs` INNER JOIN `log_types` ON (`cache_logs`.`type`=`log_types`.`id`) WHERE type=3 AND cache_logs.deleted=0 AND cache_logs.cache_id=&1 GROUP BY `cache_logs`.`type` ORDER BY `log_types`.`pl` ASC", $cache_id, $lang_db);

    if ($rsCSC !== false) {
        $xtitle = "";
        $ry = mysql_fetch_array($rsCSC);
        $y[] = $ry['count'];
        $x[] = $ry['type'];
    } else {
        $x[] = tr("comment");
    }

    mysql_free_result($rsCSF);
    mysql_free_result($rsCSNF);
    mysql_free_result($rsCSC);


/// A new pie graph
    $graph = new PieGraph(400, 200, "auto");
    $graph->SetScale('textint');
    $logtype = tr("by_logtype");
// Title setup
    $graph->title->Set($logtype);
    $graph->title->SetFont(FF_ARIAL, FS_NORMAL);
//$graph ->legend->Pos( 0.25,0.8,"right" ,"bottom");
// Setup the pie plot
    $p1 = new PiePlot($y);
    $p1->SetTheme("earth");
    $p1->value->SetFormat("%d");
    $p1->SetLabelType(PIE_VALUE_ABS);
    $p1->SetSliceColors(array('chartreuse3', 'chocolate2', 'wheat1'));

// Adjust size and position of plot
    $p1->SetSize(0.35);
    $p1->SetCenter(0.25, 0.52);
    $f = tr("found");
    $dnf = tr("not_found");
    $com = tr("comment");
// Setup slice labels and move them into the plot
    $xx = array($f, $dnf, $com);
    $p1->value->SetFont(FF_COURIER, FS_NORMAL);
    $p1->value->SetColor("black");
    $p1->SetLabelPos(0.65);
    $p1->SetLegends($xx);
    $graph->legend->SetFont(FF_ARIAL, FS_NORMAL);

// Explode all slices
//$p1->ExplodeAll(10);
// Finally add the plot
    $graph->Add($p1);

    $graph->SetShadow();

// ... and stroke it
    $graph->Stroke();
}
?>
