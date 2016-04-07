<?php

use Utils\Database\XDb;
$rootpath = '../';
require('../lib/common.inc.php');
global $lang;

//Preprocessing
if ($error == false) {
    require("../lib/jpgraph/src/jpgraph.php");
    require('../lib/jpgraph/src/jpgraph_bar.php');
    require('../lib/jpgraph/src/jpgraph_date.php');

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
        $rsCachesFindYear1 = XDb::xSql(
            "SELECT COUNT(*) `count`, YEAR(`date`) `year` FROM `cache_logs`
            WHERE type=1 AND cache_logs.deleted='0' AND cache_id= ?
            GROUP BY YEAR(`date`)
            ORDER BY YEAR(`date`) ASC", $cache_id);

        if ($rsCachesFindYear1 !== false) {
            $describe = tr("annual_stat");
            $xtitle = "";
            while ($rfy1 = XDb::xFetchArray($rsCachesFindYear1)) {
                $y1[] = $rfy1['count'];
                $x1[] = $rfy1['year'];
            }
        }
        XDb::xFreeResults($rsCachesFindYear1);

        $rsCachesFindYear2 = XDb::xSql(
            "SELECT COUNT(*) `count`, YEAR(`date`) `year` FROM `cache_logs`
            WHERE type=2 AND cache_logs.deleted='0' AND cache_id=?
            GROUP BY YEAR(`date`)
            ORDER BY YEAR(`date`) ASC", $cache_id);

        if ($rsCachesFindYear2 !== false) {
            $describe = tr("annual_stat");
            $xtitle = "";
            while ($rfy2 = XDb::xFetchArray($rsCachesFindYear2)) {
                $y2[] = $rfy2['count'];
                $x2[] = $rfy2['year'];
            }
        }
        XDb::xFreeResults($rsCachesFindYear2);
    }

    if ($tit == "csm") {
        $describe = tr("monthly_stat");
        $describe .= $year;
        $xtitle = $year;
        for ($i = 1; $i < 13; $i++) {
            $month = $i;
            $rsCachesFindMonth1 = XDb::xSql(
                "SELECT COUNT(*) `count`, YEAR(`date`) `year`, MONTH(`date`) `month` FROM `cache_logs`
                WHERE type=1 AND cache_logs.deleted='0' AND cache_id=? AND YEAR(`date`)=? AND MONTH(`date`)=?
                GROUP BY MONTH(`date`) , YEAR(`date`)
                ORDER BY YEAR(`date`) ASC, MONTH(`date`) ASC",
                $cache_id, $year, $month);

            if ($rsCachesFindMonth1 !== false) {
                $rfm = XDb::xFetchArray($rsCachesFindMonth1);
                $y1[] = $rfm['count'];
                $x1[] = $rfm['month'];
            } else {
                $y1[] = $i;
                $x1[] = 0;
            }
        }
        XDb::xFreeResults($rsCachesFindMonth1);

        for ($i = 1; $i < 13; $i++) {
            $month = $i;
            $rsCachesFindMonth2 = XDb::xSql(
                "SELECT COUNT(*) `count`, YEAR(`date`) `year`, MONTH(`date`) `month` FROM `cache_logs`
                WHERE type=2 AND cache_logs.deleted='0' AND cache_id=? AND YEAR(`date`)=? AND MONTH(`date`)=?
                GROUP BY MONTH(`date`) , YEAR(`date`)
                ORDER BY YEAR(`date`) ASC, MONTH(`date`) ASC",
                $cache_id, $year, $month);

            if ($rsCachesFindMonth2 !== false) {

                $rfm = XDb::xFetchArray($rsCachesFindMonth2);
                $y2[] = $rfm['count'];
                $x2[] = $rfm['month'];
            } else {
                $y2[] = $i;
                $x2[] = 0;
            }
        }
        XDb::xFreeResults($rsCachesFindMonth2);
    }

    setlocale(LC_ALL, 'pl_PL.utf8');
    $dateLocale = new DateLocale();

    // Create the graph. These two calls are always required
    $graph = new Graph(400, 200);
    $graph->SetScale("textlin");

    $graph->SetShadow();
    $graph->img->SetMargin(50, 30, 30, 55);

    // Create the bar plots
    $b1plot = new BarPlot($y1);
    $b1plot->SetFillColor("chartreuse3");
    $b2plot = new BarPlot($y2);
    $b2plot->SetFillColor("chocolate2");
    // Set the legends for the plots

    $fn = tr('found');
    $dnf = tr('not_found');
    $b1plot->SetLegend($fn);
    $b2plot->SetLegend($dnf);

    // Adjust the legend position
    $graph->legend->SetLayout(LEGEND_HOR);
    $graph->legend->Pos(0.5, 0.94, "center", "bottom");
    $graph->legend->SetLineWeight(8);

    // Create the grouped bar plot
    $gbplot = new GroupBarPlot(array($b1plot, $b2plot));
    // ...and add it to the graPH
    $graph->Add($gbplot);

    $le = "";
    $graph->title->Set($describe);
    $graph->xaxis->title->Set($xtitle);
    $graph->yaxis->title->Set($le);

    $graph->title->SetFont(FF_ARIAL, FS_NORMAL);
    $graph->yaxis->title->SetFont(FF_COURIER, FS_BOLD);
    $graph->xaxis->title->SetFont(FF_COURIER, FS_BOLD);

    $graph->legend->SetFont(FF_ARIAL, FS_NORMAL);


    // Display the graph
    $graph->Stroke();
}

