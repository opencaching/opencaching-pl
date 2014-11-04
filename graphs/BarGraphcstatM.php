<?php
  $rootpath = '../';
  require('../lib/common.inc.php');
      global $lang;

    //Preprocessing
    if ($error == false)
    {
require("../lib/jpgraph/src/jpgraph.php");
require('../lib/jpgraph/src/jpgraph_bar.php');
require('../lib/jpgraph/src/jpgraph_date.php');


        $year='';
        if (isset($_REQUEST['cacheid']) && isset($_REQUEST['t']))
        {
            $cache_id = $_REQUEST['cacheid'];
            $titles = $_REQUEST['t'];
            if (strlen($titles) >3) {
            $year = substr ($titles,-4);
            $tit= substr($titles,0,-4);
            }
            else
            { $tit=$titles;}
        }


  $y=array();
  $x=array();


if ($tit == "csy") {
$rsCachesFindYear1 = sql("SELECT COUNT(*) `count`,YEAR(`date`) `year` FROM `cache_logs` WHERE type=1 AND cache_logs.deleted='0' AND cache_id=&1 GROUP BY YEAR(`date`) ORDER BY YEAR(`date`) ASC",$cache_id);

//$rsCachesFindYear = sql("SELECT COUNT(*) `count`,YEAR(`date`) `year`, type type FROM `cache_logs` WHERE (type=1 OR type=2) AND cache_logs.deleted='0' AND cache_id=&1 GROUP BY YEAR(`date`), type ORDER BY YEAR(`date`) ASC",$cache_id);
                if ($rsCachesFindYear1 !== false) {
                $descibe=tr("annual_stat");
                $xtitle="";
                while ($rfy1 = mysql_fetch_array($rsCachesFindYear1)){
                    $y1[] = $rfy1['count'];
                    $x1[] = $rfy1['year'];}
                    }
//                  echo $y1[];
                mysql_free_result($rsCachesFindYear1);
$rsCachesFindYear2 = sql("SELECT COUNT(*) `count`,YEAR(`date`) `year` FROM `cache_logs` WHERE type=2 AND cache_logs.deleted='0' AND cache_id=&1 GROUP BY YEAR(`date`) ORDER BY YEAR(`date`) ASC",$cache_id);

//$rsCachesFindYear = sql("SELECT COUNT(*) `count`,YEAR(`date`) `year`, type type FROM `cache_logs` WHERE (type=1 OR type=2) AND cache_logs.deleted='0' AND cache_id=&1 GROUP BY YEAR(`date`), type ORDER BY YEAR(`date`) ASC",$cache_id);
                if ($rsCachesFindYear2 !== false) {
                $descibe=tr("annual_stat");
                $xtitle="";
                while ($rfy2 = mysql_fetch_array($rsCachesFindYear2)){
                    $y2[] = $rfy2['count'];
                    $x2[] = $rfy2['year'];}
                    }
                mysql_free_result($rsCachesFindYear2);

                }

if ($tit == "csm") {
                $descibe=tr("monthly_stat");
                $describe .= $year;
                $xtitle=$year;
    for ($i = 1; $i < 13; $i++) {
            $month= $i;
$rsCachesFindMonth1= sql("SELECT COUNT(*) `count`,YEAR(`date`) `year` , MONTH(`date`) `month` FROM `cache_logs` WHERE type=1 AND cache_logs.deleted='0' AND cache_id=&1 AND YEAR(`date`)=&2 AND MONTH(`date`)=&3 GROUP BY MONTH(`date`) , YEAR(`date`) ORDER BY YEAR(`date`) ASC, MONTH(`date`) ASC",$cache_id,$year, $month);

 //             if (mysql_num_rows($rsCachesFindMonth1) != 0){
                if ($rsCachesFindMonth1 !== false){
                $rfm = mysql_fetch_array($rsCachesFindMonth1);
                    $y1[] = $rfm['count'];
                    $x1[] = $rfm['month'];
                }
                else
                { $y1[] = $i;
                  $x1[] = 0;
                }
                }
                mysql_free_result($rsCachesFindMonth1);

    for ($i = 1; $i < 13; $i++) {
            $month= $i;
$rsCachesFindMonth2= sql("SELECT COUNT(*) `count`,YEAR(`date`) `year` , MONTH(`date`) `month` FROM `cache_logs` WHERE type=2 AND cache_logs.deleted='0' AND cache_id=&1 AND YEAR(`date`)=&2 AND MONTH(`date`)=&3 GROUP BY MONTH(`date`) , YEAR(`date`) ORDER BY YEAR(`date`) ASC, MONTH(`date`) ASC",$cache_id,$year,$month);

                if ($rsCachesFindMonth2 !== false){

                $rfm = mysql_fetch_array($rsCachesFindMonth2);
                    $y2[] = $rfm['count'];
                    $x2[] = $rfm['month'];
                }
                else
                { $y2[] = $i;
                  $x2[] = 0;
                }
                }
                mysql_free_result($rsCachesFindMonth2);
}


setlocale(LC_ALL, 'pl_PL.utf8');
$dateLocale = new DateLocale();
// Use Swedish locale
//$dateLocale->Set('pl_PL.utf8');
// Create the graph. These two calls are always required
$graph = new Graph(400,200);
$graph->SetScale("textlin");

$graph->SetShadow();
$graph->img->SetMargin(50,30,30,55);



// Create the bar plots
$b1plot = new BarPlot($y1);
$b1plot->SetFillColor("chartreuse3");
$b2plot = new BarPlot($y2);
$b2plot->SetFillColor("chocolate2");
 // Set the legends for the plots

 $fn=tr('found');
 $dnf=tr('not_found');
$b1plot->SetLegend($fn);
$b2plot->SetLegend($dnf);
// Adjust the legend position
//$graph->legend->Pos(0.5,0.8,'right','center');
//$graph->legend->SetPos(0.2,0.2,'center','bottom');
$graph->legend->SetLayout(LEGEND_HOR);
$graph->legend->Pos(0.5, 0.94, "center", "bottom");
$graph->legend->SetLineWeight(8);

// Create the grouped bar plot
$gbplot = new GroupBarPlot(array($b1plot,$b2plot));
//$gbplot->SetLegends($x);

// ...and add it to the graPH
$graph->Add($gbplot);
//$le=tr("number_logentry");
$le="";
$graph->title->Set($descibe);
$graph->xaxis->title->Set($xtitle);
$graph->yaxis->title->Set($le);

//$year = $gDateLocale->GetShortMonth();
//$graph->xaxis->SetTickLabels($year);

$graph->title->SetFont(FF_ARIAL,FS_NORMAL);
$graph->yaxis->title->SetFont(FF_COURIER,FS_BOLD);
$graph->xaxis->title->SetFont(FF_COURIER,FS_BOLD);

$graph->legend->SetFont(FF_ARIAL,FS_NORMAL);


// Display the graph
$graph->Stroke();


  }


  ?>
