<?php

use Libs\JpGraph\JpGraphLoader;
use Utils\Database\XDb;

require(__DIR__.'/../lib/common.inc.php');

// jpgraph package doesn't contains fonts
define('TTF_DIR',__DIR__.'/../lib/fonts/');

JpGraphLoader::load();
JpGraphLoader::module('bar');
JpGraphLoader::module('date');

$year = '';
if (isset($_REQUEST['cacheid']) && isset($_REQUEST['t'])) {
    $cache_id = $_REQUEST['cacheid'];
    $titles = $_REQUEST['t'];
    if (strlen($titles) > 3) {
        $year = substr($titles, -4);
        $tit = substr($titles, 0, -4);
    } else {
        $tit = $titles;
    }
}


$y = array();
$x = array();


if ($tit == "csy") {
    $rsCachesFindYear = XDb::xSql(
        "SELECT COUNT(*) `count`, YEAR(`date`) `year` FROM `cache_logs`
        WHERE type=1 AND cache_logs.deleted='0' AND cache_id= ?
        GROUP BY YEAR(`date`)
        ORDER BY YEAR(`date`) ASC", $cache_id);

    if ($rsCachesFindYear !== false) {
        $descibe = tr("annual_stat_founds");
        $xtitle = "";
        while ($rfy = XDb::xFetchArray($rsCachesFindYear)) {
            $y[] = $rfy['count'];
            $x[] = $rfy['year'];
        }
    }
    XDb::xFreeResults($rsCachesFindYear);
}

if ($tit == "csm") {
    $rsCachesFindMonth = XDb::xSql(
        "SELECT COUNT(*) `count`, YEAR(`date`) `year`, MONTH(`date`) `month` FROM `cache_logs`
        WHERE type=1 AND cache_logs.deleted='0' AND cache_id= ? AND YEAR(`date`)= ?
        GROUP BY MONTH(`date`) , YEAR(`date`)
        ORDER BY YEAR(`date`) ASC, MONTH(`date`) ASC", $cache_id, $year);

    if ($rsCachesFindMonth !== false) {
        $descibe = tr("monthly_stat_founds");
        $describe .= $year;
        $xtitle = $year;

        while ($rfm = XDb::xFetchArray($rsCachesFindMonth)) {
            $y[] = $rfm['count'];
            $x[] = $rfm['month'];
        }
    }
    XDb::xFreeResults($rsCachesFindMonth);
}



// Create the graph. These two calls are always required
$graph = new Graph(400, 200, 'auto');
$graph->SetScale('textint', 0, max($y) + (max($y) * 0.2), 0, 0);
// Add a drop shadow
$graph->SetShadow();


// Label callback

// Adjust the margin a bit to make more room for titles
$graph->SetMargin(50, 30, 30, 40);

// Create a bar pot
$bplot = new BarPlot($y);

// Adjust fill color
$bplot->SetFillColor('chartreuse3');
$graph->Add($bplot);


// Setup the titles
$graph->title->Set($descibe);
$graph->xaxis->title->Set($xtitle);
$graph->xaxis->SetTickLabels($x);


// Some extra margin looks nicer
//$graph->xaxis->SetLabelMargin(10);
$nf = "";
$graph->yaxis->title->Set($nf);

$graph->title->SetFont(FF_ARIAL, FS_NORMAL);
$graph->yaxis->title->SetFont(FF_COURIER, FS_BOLD);
$graph->xaxis->title->SetFont(FF_COURIER, FS_BOLD);


// Setup the values that are displayed on top of each bar
$bplot->value->Show();

// Must use TTF fonts if we want text at an arbitrary angle
$bplot->value->SetFont(FF_COURIER, FS_BOLD);
$bplot->value->SetAngle(0);
$bplot->value->SetFormat('%d');


// Display the graph
$graph->Stroke();


