<?php

use Utils\Database\XDb;
$rootpath = '../';
require('../lib/common.inc.php');
global $lang;

setlocale(LC_TIME, 'pl_PL.utf-8');

require('../lib/jpgraph/src/jpgraph.php');
require('../lib/jpgraph/src/jpgraph_line.php');
require('../lib/jpgraph/src/jpgraph_date.php');

$graph = new Graph(500, 400, "auto", 60 * 24);

$startDate = mktime(0, 0, 0, 5, 1, 2006);

$rsCaches = XDb::xSql(
    'SELECT COUNT(*) `count`, DAY(`date_created`) `day`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `caches`
    WHERE caches.status=1
    GROUP BY DAY(`date_created`), MONTH(`date_created`), YEAR(`date_created`)
    ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC, DAY(`date_created`) ASC');

$rsLogs = XDb::xSql(
    'SELECT COUNT(*) `count`, DAY(`date_created`) `day`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `cache_logs`
    WHERE (`type`=1 OR `type`=7) AND `deleted`=0
    GROUP BY DAY(`date_created`), MONTH(`date_created`), YEAR(`date_created`)
    ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC, DAY(`date_created`) ASC');

$rCaches = XDb::xFetchArray($rsCaches);
$rLogs = XDb::xFetchArray($rsLogs);

$cachesCount = 0;
$logsCount = 0;

$yDataCaches = array();
$yDataLogs = array();
$xDate = array();
$tickPositions = array();
$tickLabels = array();

$days = 0;
$date = $startDate;
while ($date < time()) {
    while (($rCaches !== false) && (strtotime($rCaches['year'] . '-' . $rCaches['month'] . '-' . $rCaches['day']) < $date))
        $rCaches = XDb::xFetchArray($rsCaches);

    if ($rCaches['year'] . '-' . $rCaches['month'] . '-' . $rCaches['day'] == date('Y-n-j', $date))
        $cachesCount += $rCaches['count'];

    while (($rLogs !== false) && (strtotime($rLogs['year'] . '-' . $rLogs['month'] . '-' . $rLogs['day']) < $date))
        $rLogs = XDb::xFetchArray($rsLogs);

    if ($rLogs['year'] . '-' . $rLogs['month'] . '-' . $rLogs['day'] == date('Y-n-j', $date))
        $logsCount += $rLogs['count'];

    $yDataCaches[] = $cachesCount;
    $yDataLogs[] = $logsCount;
    if (date('j', $date) == 1) {
        $tickPositions[] = $date;
        $tickLabels[] = strftime('%G', $date);
    }

    $xDate[] = $date;

    $days++;
    $date = mktime(0, 0, 0, 5, 1 + $days, 2006);
}
XDb::xFreeResults($rsCaches);
XDb::xFreeResults($rsLogs);


//
// Titel, Footer, Legende und Hintergrund
//
$stat = tr('oc_stat');
$graph->title->SetFont(FF_GEORGIA, FS_NORMAL, 14);
$graph->title->Set($stat);
$graph->title->SetMargin(12);

$graph->footer->center->Set(tr('graph_statistics_01') . date('d:m:Y H:i:s'));
$graph->footer->center->SetFont(FF_ARIAL, FS_NORMAL, 7);
$graph->footer->center->SetColor('darkgray');

$graph->legend->SetLayout(LEGEND_HOR);
$graph->legend->Pos(0.5, 0.96, "center", "bottom");
$graph->legend->SetLineWeight(8);

$graph->img->SetMargin(45, 60, 50, 70);
$graph->SetFrame(false);
$graph->SetColor(array(245, 245, 245));
$graph->SetMarginColor(array(238, 238, 238));
$graph->SetBackgroundImage('images/statbg-oc.jpg', BGIMG_CENTER, 'auto');


//
// Skalierung, X- und Y-Achse formatieren
//
$graph->SetScale("intlin", 0, 0, $startDate, time());
$graph->SetY2Scale("lin");

$graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 7);
$graph->xaxis->SetMajTickPositions($tickPositions, $tickLabels);
$graph->xaxis->SetTextLabelInterval(12);


$graph->yaxis->scale->SetGrace(10, 0);
$graph->yaxis->SetColor("blue");
$graph->y2axis->scale->SetGrace(10, 0);
$graph->y2axis->SetColor("darkgreen");


$lineCaches = new LinePlot($yDataCaches, $xDate);
$lineCaches->SetLegend(tr("graph_statistics_02"));
$lineCaches->SetColor("blue");
$lineCaches->SetStyle("solid");
$lineCaches->SetWeight(3);
$lineCaches->SetStepStyle();
$graph->Add($lineCaches);

$lineFound = new LinePlot($yDataLogs, $xDate);
$lineFound->SetLegend(tr("graph_statistics_03"));
$lineFound->SetColor("darkgreen");
$lineFound->SetStyle("solid");
$lineFound->SetWeight(2);
$lineFound->SetStepStyle();
$graph->AddY2($lineFound);


//
// Infotexte einfuegen
//
$txtStat1 = new Text(tr('graph_statistics_04') . strftime('%d-%m-%Y', time()));
$txtStat1->SetPos(55, 55);
$txtStat1->SetFont(FF_ARIAL, FS_NORMAL, 10);

$lineHeight = $txtStat1->GetFontHeight($graph->img);

$hiddenCaches = XDb::xSimpleQueryValue(
    "SELECT COUNT(*) FROM `caches` WHERE (`status`=1 OR `status`=2 OR `status`=3)", 0);

$txtStat2 = new Text(tr('graph_statistics_05') . str_replace(',', '.', number_format($hiddenCaches)));
$txtStat2->SetPos(55, 55 + $lineHeight * 1.5);
$txtStat2->SetFont(FF_ARIAL, FS_NORMAL, 8);
$txtStat2->SetColor('blue');

$activeCaches = XDb::xSimpleQueryValue(
    "SELECT COUNT(*) FROM `caches` WHERE `status`=1", 0);

$txtStat3 = new Text(tr('graph_statistics_06') . str_replace(',', '.', number_format($activeCaches)));
$txtStat3->SetPos(55, 55 + $lineHeight * 2.5);
$txtStat3->SetFont(FF_ARIAL, FS_NORMAL, 8);
$txtStat3->SetColor('blue');

$foundEntries = XDb::xMultiVariableQueryValue(
    "SELECT COUNT(*) FROM `cache_logs` WHERE `type`=1 AND `deleted`=0", 0);

$txtStat5 = new Text(tr('graph_statistics_07') . str_replace(',', '.', number_format($foundEntries)));
$txtStat5->SetPos(55, 55 + $lineHeight * 5.0);
$txtStat5->SetFont(FF_ARIAL, FS_NORMAL, 8);
$txtStat5->SetColor('darkgreen');

$graph->AddText($txtStat1);
$graph->AddText($txtStat2);
$graph->AddText($txtStat3);
$graph->AddText($txtStat5);


//
// Display the graph
//
$graph->Stroke();
