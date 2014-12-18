<?php

setlocale(LC_TIME, 'pl_PL.utf-8');


//Preprocessing
if ($error == false) {
    require("../lib/jpgraph/src/jpgraph.php");
    require('../lib/jpgraph/src/jpgraph_bar.php');
    require('../lib/jpgraph/src/jpgraph_date.php');


    require('../lib/web.inc.php');
    sql('USE `ocpl`');
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
        $rsCachesFindYear = sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` FROM `cache_logs` WHERE cache_id=&1 GROUP BY YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC", $cache_id);

        if ($rsCachesFindYear !== false) {
            $descibe = "Roczna statystyka znalezie� skrzynki";
            $xtitle = "";
            while ($rfy = mysql_fetch_array($rsCachesFindYear)) {
                $y[] = $rfy['count'];
                $x[] = $rfy['year'];
            }
        }
        mysql_free_result($rsCachesFindYear);
    }

    if ($tit == "csm") {
        $rsCachesFindMonth = sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` , MONTH(`date_created`) `month` FROM `cache_logs` WHERE  cache_id=&1 AND YEAR(`date_created`)=&2 GROUP BY MONTH(`date_created`) , YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC", $cache_id, $year);

        if ($rsCachesFindMonth !== false) {
            $descibe = "Miesieczna statystyka znalezie� skrzynki (ostatni rok)";
            $describe .= $year;
            $xtitle = $year;

            while ($rfm = mysql_fetch_array($rsCachesFindMonth)) {
                $y[] = $rfm['count'];
                $x[] = $rfm['month'];
            }
        }
        mysql_free_result($rsCachesFindMonth);
    }



// Create the graph. These two calls are always required
    $graph = new Graph(500, 200, 'auto');
    $graph->SetScale('textint', 0, max($y) + (max($y) * 0.2), 0, 0);
// ,0,0,0,max($y)-min($y)+5);
// Add a drop shadow
    $graph->SetShadow();


// Label callback
//function year_callback($aLabel) {
//    return 1700+(int)$aLabel;
//}
//$graph->xaxis->SetLabelFormatCallback('year_callback');
// $graph->SetScale('intint',0,0,0,max($year)-min($year)+1);
// Adjust the margin a bit to make more room for titles
    $graph->SetMargin(50, 30, 30, 40);

// Create a bar pot
    $bplot = new BarPlot($y);

// Adjust fill color
    $bplot->SetFillColor('steelblue2');
    $graph->Add($bplot);


// Setup the titles
    $graph->title->Set($descibe);
    $graph->xaxis->title->Set($xtitle);
    $graph->xaxis->SetTickLabels($x);


// Some extra margin looks nicer
//$graph->xaxis->SetLabelMargin(10);

    $graph->yaxis->title->Set('Liczba znalezie�');

    $graph->title->SetFont(FF_FONT1, FS_BOLD);
    $graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
    $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);


// Setup the values that are displayed on top of each bar
    $bplot->value->Show();

// Must use TTF fonts if we want text at an arbitrary angle
    $bplot->value->SetFont(FF_FONT1, FS_BOLD);
    $bplot->value->SetAngle(0);
    $bplot->value->SetFormat('%d');


// Display the graph

    $graph->Stroke();
}
?>
