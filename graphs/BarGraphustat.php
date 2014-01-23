<?php
  $rootpath = '../';
  require('../lib/common.inc.php');
  global $lang;
setlocale(LC_TIME, 'pl_PL.utf-8');


    //Preprocessing
    if ($error == false)
    {
require("../lib/jpgraph/src/jpgraph.php");
require('../lib/jpgraph/src/jpgraph_bar.php');
require('../lib/jpgraph/src/jpgraph_date.php');

        $year='';
        // check for old-style parameters
        if (isset($_REQUEST['userid']) && isset($_REQUEST['t']))
        {
            $user_id = $_REQUEST['userid'];
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


if ($tit == "ccy") {
$rsCreateCachesYear= sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` FROM `caches` WHERE status <> 4 AND status <> 5 AND status <> 6 AND user_id=&1 GROUP BY YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC",$user_id);

                if ($rsCreateCachesYear !== false){
                $descibe=tr("annual_stat_created");
                $xtitle="";
                    while ($ry = mysql_fetch_array($rsCreateCachesYear)){
                    $y[] = $ry['count'];
                    $x[] = $ry['year'];}
                    }
                mysql_free_result($rsCreateCachesYear);
        }


if ($tit == "ccm") {
    for ($i = 1; $i < 13; $i++)
            {
            $month= $i;
$rsCreateCachesMonth = sql("SELECT COUNT(*) `count`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `caches` WHERE status <>4 AND status <> 5 AND status <> 6 AND user_id=&1 AND YEAR(`date_created`)=&2 AND MONTH(`date_created`)=&3 GROUP BY MONTH(`date_created`), YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC",$user_id,$year,$month);

                if ($rsCreateCachesMonth !== false) {
                $descibe = tr("monthly_stat_created_user");
                $xtitle=$year;
                $rm = mysql_fetch_array($rsCreateCachesMonth);
                    $y[] = $rm['count'];
                    $x[] = $rm['month'];
                }
                else
                { $y1[] = $i;
                  $x1[] = 0;
                }
            }

 mysql_free_result($rsCreateCachesMonth);

                }

if ($tit == "cfy") {
$rsCachesFindYear = sql("SELECT COUNT(*) `count`,YEAR(`date`) `year` FROM `cache_logs` WHERE type=1 AND cache_logs.deleted='0' AND user_id=&1 GROUP BY YEAR(`date`) ORDER BY YEAR(`date`) ASC",$user_id);

                if ($rsCachesFindYear !== false) {
                $descibe=tr("annual_stat_founds_user");
                $xtitle="";
                while ($rfy = mysql_fetch_array($rsCachesFindYear)){
                    $y[] = $rfy['count'];
                    $x[] = $rfy['year'];}
                    }
                mysql_free_result($rsCachesFindYear);
}

if ($tit == "cfm") {
    for ($i = 1; $i < 13; $i++)
            {
            $month= $i;
$rsCachesFindMonth= sql("SELECT COUNT(*) `count`,YEAR(`date`) `year` , MONTH(`date`) `month` FROM `cache_logs` WHERE type=1 AND cache_logs.deleted='0' AND user_id=&1 AND YEAR(`date`)=&2 AND MONTH(`date`)=&3 GROUP BY MONTH(`date`) , YEAR(`date`) ORDER BY YEAR(`date`) ASC, MONTH(`date`) ASC",$user_id,$year,$month);

                if ($rsCachesFindMonth !== false){
                $descibe=tr("monthly_stat_founds_user");
                $xtitle=$year;

                $rfm = mysql_fetch_array($rsCachesFindMonth);
                    $y[] = $rfm['count'];
                    $x[] = $rfm['month'];
                }
                else
                { $y1[] = $i;
                  $x1[] = 0;
                }
            }

                mysql_free_result($rsCachesFindMonth);
}



// Create the graph. These two calls are always required
$graph = new Graph(500,200,'auto');
$graph->SetScale('textint',0,max($y)+(max($y)*0.2),0,0);
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
 $graph->SetMargin(50,30,30,40);

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
$nc=tr("number_caches");
$graph->yaxis->title->Set($nc);

$graph->title->SetFont(FF_ARIAL,FS_NORMAL);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);


// Setup the values that are displayed on top of each bar
$bplot->value->Show();

// Must use TTF fonts if we want text at an arbitrary angle
$bplot->value->SetFont(FF_FONT1,FS_BOLD);
$bplot->value->SetAngle(0);
$bplot->value->SetFormat('%d');


// Display the graph

  $graph->Stroke();

  }


  ?>
