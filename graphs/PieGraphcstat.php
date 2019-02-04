<?php

use Libs\JpGraph\JpGraphLoader;
use Utils\Database\XDb;
use Utils\I18n\I18n;

require(__DIR__.'/../lib/common.inc.php');

// jpgraph package doesn't contains fonts
define('TTF_DIR',__DIR__.'/../lib/fonts/');

JpGraphLoader::load();
JpGraphLoader::module('pie');
JpGraphLoader::module('pie3d');

 // check for old-style parameters
if (isset($_REQUEST['cacheid'])) {
    $cache_id = $_REQUEST['cacheid'];
}

$y = array();
$x = array();

$lang_db = I18n::getLangForDbTranslations('log_types');

// Ustawic sprawdzanie jezyka w cache_type.pl !!!!
$rsCSF = XDb::xSql(
    "SELECT COUNT(`cache_logs`.`type`) `count`, `log_types`.`$lang_db` AS `type`
    FROM `cache_logs` INNER JOIN `log_types` ON (`cache_logs`.`type`=`log_types`.`id`)
    WHERE type=1 AND cache_logs.deleted=0 AND cache_logs.cache_id= ?
    GROUP BY `cache_logs`.`type`
    ORDER BY `log_types`.`pl` ASC", $cache_id);

if ($rsCSF !== false) {
    $xtitle = "";
    $ry = XDb::xFetchArray($rsCSF);
    $y[] = $ry['count'];
    $x[] = $ry['type'];
} else {
    $x[] = tr("found");
}

$rsCSNF = XDb::xSql(
    "SELECT COUNT(`cache_logs`.`type`) `count`, `log_types`.`$lang_db` AS `type`
    FROM `cache_logs` INNER JOIN `log_types` ON (`cache_logs`.`type`=`log_types`.`id`)
    WHERE type=2 AND cache_logs.deleted=0 AND cache_logs.cache_id= ?
    GROUP BY `cache_logs`.`type`
    ORDER BY `log_types`.`pl` ASC", $cache_id );

if ($rsCSNF !== false) {
    $xtitle = "";
    $ry = XDb::xFetchArray($rsCSNF);
    $y[] = $ry['count'];
    $x[] = $ry['type'];
} else {
    $x[] = tr("not_found");
    $y[] = '0';
}


$rsCSC = XDb::xSql(
    "SELECT COUNT(`cache_logs`.`type`) `count`, `log_types`.`$lang_db` AS `type`
    FROM `cache_logs` INNER JOIN `log_types` ON (`cache_logs`.`type`=`log_types`.`id`)
    WHERE type=3 AND cache_logs.deleted=0 AND cache_logs.cache_id= ?
    GROUP BY `cache_logs`.`type`
    ORDER BY `log_types`.`pl` ASC", $cache_id );

if ($rsCSC !== false) {
    $xtitle = "";
    $ry = XDb::xFetchArray($rsCSC);
    $y[] = $ry['count'];
    $x[] = $ry['type'];
} else {
    $x[] = tr("log_note");
}

XDb::xFreeResults($rsCSF);
XDb::xFreeResults($rsCSNF);
XDb::xFreeResults($rsCSC);


// A new pie graph
$graph = new PieGraph(400, 200, "auto");
$graph->SetScale('textint');
$logtype = tr("by_logtype");

// Title setup
$graph->title->Set($logtype);
$graph->title->SetFont(FF_ARIAL, FS_NORMAL);
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
$com = tr("log_note");

// Setup slice labels and move them into the plot
$xx = array($f, $dnf, $com);
$p1->value->SetFont(FF_COURIER, FS_NORMAL);
$p1->value->SetColor("black");
$p1->SetLabelPos(0.65);
$p1->SetLegends($xx);
$graph->legend->SetFont(FF_ARIAL, FS_NORMAL);

// Finally add the plot
$graph->Add($p1);

$graph->SetShadow();

// ... and stroke it
$graph->Stroke();
