<?php
/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    *
    ***************************************************************************/
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
require('../lib/jpgraph/src/jpgraph_mgraph.php');

# Setup begining of stat for OC Team. Start timie 01 June every year
$year = date('Y');
$year_old= $year-1;
$year_new=$year+1;
$count_days= date('z');
if ($count_days < 181) {$start_time = $year_old .'-07-1 00:00:00';
    $title3=tr('number_month').' '. $year_old .'/'. $year;
    } else {
        $start_time= $year .'-06-7 00:00:00';
        $title3=tr('number_month').' '. $year .'/'. $year_new;
        }


  $y=array();
  $x=array();
  $y2=array();
  $x2=array();
  $y3=array();
  $x3=array();
  $y4=array();
  $x4=array();

mysql_query("SET NAMES 'utf8'");
$rsreports= sql("SELECT count(*) count, responsible_id, username from reports,user WHERE submit_date > '&1' and responsible_id <>0 AND responsible_id != 1883 AND user.user_id=responsible_id GROUP BY responsible_id ORDER  BY username",$start_time);

$rsreportsM= sql("SELECT count(*) count, MONTH(`submit_date`) `month` from reports WHERE submit_date > '&1' and responsible_id <>0 AND responsible_id != 1883 GROUP BY MONTH(`submit_date`) , YEAR(`submit_date`) ORDER BY YEAR(`submit_date`) ASC, MONTH(`submit_date`) ASC",$start_time);

mysql_query("SET NAMES 'utf8'");
$rscaches= sql("SELECT count(*) count, username from approval_status,user WHERE user.user_id=approval_status.user_id AND date_approval > '&1' GROUP BY approval_status.user_id ORDER  BY username",$start_time);

$rscachesM= sql("SELECT count(*) count, MONTH(`date_approval`) `month` from approval_status WHERE date_approval > '&1' GROUP BY MONTH(`date_approval`) , YEAR(`date_approval`) ORDER BY YEAR(`date_approval`) ASC, MONTH(`date_approval`) ASC",$start_time);


                $xtitle="";
                    while ($ry = mysql_fetch_array($rsreports))
                    {
                    $y[] = $ry['count'];
                    $x[] = $ry['username'];
                    }
                    while ($ry2 = mysql_fetch_array($rscaches))
                    {
                    $y2[] = $ry2['count'];
                    $x2[] = $ry2['username'];
                    }
                    while ($ry3 = mysql_fetch_array($rsreportsM))
                    {
                    $y3[] = $ry3['count'];
                    $x3[] = $ry3['month'];
                    }
                    while ($ry4 = mysql_fetch_array($rscachesM))
                    {
                    $y4[] = $ry4['count'];
                    $x4[] = $ry4['month'];
                    }

                mysql_free_result($rsreportsM);
                mysql_free_result($rsreports);
                mysql_free_result($rscaches);
                mysql_free_result($rscachesM);


// Create the graph. These two calls are always required
$graph = new Graph(500,200,'auto');
$graph->SetScale('textint',0,max($y)+(max($y)*0.2),0,0);

// Add a drop shadow
$graph->SetShadow();



// Adjust the margin a bit to make more room for titles
 $graph->SetMargin(50,30,30,40);

// Create a bar pot
$bplot = new BarPlot($y);

// Adjust fill color
$bplot->SetFillColor('steelblue2');
$graph->Add($bplot);


// Setup the titles

$descibe="Statystyka COG - prowadzne zg³oszenia";
$graph->title->Set($descibe);
$graph->xaxis->title->Set($xtitle);
$graph->xaxis->SetTickLabels($x);
$noproblems='Liczba zg³oszeñ';
$graph->yaxis->title->Set($noproblems);
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->SetFont(FF_FONT1,FS_BOLD,8);



// Setup the values that are displayed on top of each bar
$bplot->value->Show();

// Must use TTF fonts if we want text at an arbitrary angle
$bplot->value->SetFont(FF_FONT1,FS_BOLD);
$bplot->value->SetAngle(0);
$bplot->value->SetFormat('%d');


// Create the graph. These two calls are always required
$graph2 = new Graph(500,200,'auto');
$graph2->SetScale('textint',0,max($y2)+(max($y2)*0.2),0,0);

// Add a drop shadow
$graph2->SetShadow();


// Adjust the margin a bit to make more room for titles
$graph2->SetMargin(50,30,30,40);

// Create a bar pot
$bplot2 = new BarPlot($y2);

// Adjust fill color
$bplot2->SetFillColor('chartreuse3');
$graph2->Add($bplot2);


// Setup the titles
$descibe2="Statystyka COG - skrzynki weryfikowane";
$graph2->title->Set($descibe2);
$graph2->xaxis->title->Set($xtitle);
$graph2->xaxis->SetTickLabels($x2);

$ncaches=tr('number_caches');
$graph2->yaxis->title->Set($ncaches);

$graph2->title->SetFont(FF_FONT1,FS_BOLD);
$graph2->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph2->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph2->xaxis->SetFont(FF_FONT1,FS_BOLD,8);



// Setup the values that are displayed on top of each bar
$bplot2->value->Show();

// Must use TTF fonts if we want text at an arbitrary angle
$bplot2->value->SetFont(FF_FONT1,FS_BOLD);
$bplot2->value->SetAngle(0);
$bplot2->value->SetFormat('%d');

// Create the graph. These two calls are always required
$graph3 = new Graph(500,200,'auto');
$graph3->SetScale('textint',0,max($y3)+(max($y3)*0.2),0,0);

// Add a drop shadow
$graph3->SetShadow();


// Adjust the margin a bit to make more room for titles
$graph3->SetMargin(50,30,30,40);

// Create a bar pot
$bplot3 = new BarPlot($y3);

// Adjust fill color
$bplot3->SetFillColor('purple1');
$graph3->Add($bplot3);


// Setup the titles
$descibe3="Statystyka COG Miesiêczna - zg³oszenia";
$graph3->title->Set($descibe3);
$graph3->xaxis->title->Set('Numer miesi±ca 2011/2012');
//$graph3->xaxis->title->Set($title3);
$graph3->xaxis->SetTickLabels($x3);


$graph3->yaxis->title->Set($noproblems);

$graph3->title->SetFont(FF_FONT1,FS_BOLD);
$graph3->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph3->xaxis->title->SetFont(FF_FONT1,FS_BOLD);


// Setup the values that are displayed on top of each bar
$bplot3->value->Show();

// Must use TTF fonts if we want text at an arbitrary angle
$bplot3->value->SetFont(FF_FONT1,FS_BOLD);
$bplot3->value->SetAngle(0);
$bplot3->value->SetFormat('%d');

// Create the graph. These two calls are always required
$graph4 = new Graph(500,200,'auto');
$graph4->SetScale('textint',0,max($y4)+(max($y4)*0.2),0,0);

// Add a drop shadow
$graph4->SetShadow();


// Adjust the margin a bit to make more room for titles
$graph4->SetMargin(50,30,30,40);

// Create a bar pot
$bplot4 = new BarPlot($y4);

// Adjust fill color
$bplot4->SetFillColor('chartreuse3');
$graph4->Add($bplot4);


// Setup the titles
$descibe4="Statystyak COG Miesiêczna - skrzynki weryfikowane";
$graph4->title->Set($descibe4);
$graph4->xaxis->title->Set('Numer miesi±ca 2011/2012');
//$graph4->xaxis->title->Set($title3);
$graph4->xaxis->SetTickLabels($x4);


$graph4->yaxis->title->Set($ncaches);

$graph4->title->SetFont(FF_FONT1,FS_BOLD);
$graph4->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph4->xaxis->title->SetFont(FF_FONT1,FS_BOLD);


// Setup the values that are displayed on top of each bar
$bplot4->value->Show();

// Must use TTF fonts if we want text at an arbitrary angle
$bplot4->value->SetFont(FF_FONT1,FS_BOLD);
$bplot4->value->SetAngle(0);
$bplot4->value->SetFormat('%d');

//-----------------------
// Create a multigraph
//----------------------
$mgraph = new MGraph();
$mgraph->SetMargin(10,10,10,10);
$mgraph->SetFrame(true,'darkgray',2);
$mgraph->Add($graph3);
$mgraph->Add($graph4,0,220);
$mgraph->Stroke();

  }

  ?>
