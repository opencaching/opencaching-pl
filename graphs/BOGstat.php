<?php

setlocale(LC_TIME, 'pl_PL.utf-8');

	//Preprocessing
	if ($error == false)
	{

require("../lib/jpgraph/src/jpgraph.php");
require('../lib/jpgraph/src/jpgraph_bar.php');
require('../lib/jpgraph/src/jpgraph_date.php');
require('../lib/jpgraph/src/jpgraph_mgraph.php');

  require('../lib/web.inc.php');
  sql('USE `ocpl`');

		
  $y=array();
  $x=array();
  $y2=array();
  $x2=array();
  $y3=array();
  $x3=array();
$rsreports= sql("SELECT count(*) count, responsible_id, username from reports,user WHERE submit_date > '2009-06-1 00:00:00' and responsible_id <>0 AND responsible_id != 1883 AND user.user_id=responsible_id GROUP BY responsible_id ORDER  BY username");

$rsreportsM= sql("SELECT count(*) count, MONTH(`submit_date`) `month` from reports WHERE submit_date > '2009-06-1 00:00:00' and responsible_id <>0 AND responsible_id != 1883 GROUP BY MONTH(`submit_date`) , YEAR(`submit_date`) ORDER BY YEAR(`submit_date`) ASC, MONTH(`submit_date`) ASC");

$rscaches= sql("SELECT count(*) count, username from approval_status,user WHERE user.user_id=approval_status.user_id  GROUP BY approval_status.user_id ORDER  BY username");


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
				mysql_free_result($rsreportsM);
				mysql_free_result($rsreports);
				mysql_free_result($rscaches);


				
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
$descibe="Statystyka RR2 - zg³oszenia";
$graph->title->Set($descibe);
$graph->xaxis->title->Set($xtitle);
$graph->xaxis->SetTickLabels($x);

$graph->yaxis->title->Set('Liczba zg³oszeñ');
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
 
  
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
$descibe2="Statystyka RR2 - skrzynki weryfikowane";
$graph2->title->Set($descibe2);
$graph2->xaxis->title->Set($xtitle);
$graph2->xaxis->SetTickLabels($x2);


$graph2->yaxis->title->Set('Liczba skrzynek');
 
$graph2->title->SetFont(FF_FONT1,FS_BOLD);
$graph2->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph2->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
 
  
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
$descibe3="Statystyka RR2 Miesiêczna - zg³oszenia";
$graph3->title->Set($descibe3);
$graph3->xaxis->title->Set('Numer miesi±ca 2009/2010');
$graph3->xaxis->SetTickLabels($x3);


$graph3->yaxis->title->Set('Liczba zg³oszeñ');
 
$graph3->title->SetFont(FF_FONT1,FS_BOLD);
$graph3->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph3->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
 
  
// Setup the values that are displayed on top of each bar
$bplot3->value->Show();
 
// Must use TTF fonts if we want text at an arbitrary angle
$bplot3->value->SetFont(FF_FONT1,FS_BOLD);
$bplot3->value->SetAngle(0);
$bplot3->value->SetFormat('%d');

//-----------------------
// Create a multigraph
//----------------------
$mgraph = new MGraph();
$mgraph->SetMargin(10,10,10,10);
$mgraph->SetFrame(true,'darkgray',2);
$mgraph->Add($graph);
$mgraph->Add($graph3,0,220);
$mgraph->Add($graph2,0,440);
$mgraph->Stroke();
   
  }
 
  
  ?>
