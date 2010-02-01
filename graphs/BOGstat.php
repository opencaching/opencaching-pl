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

//prepare the templates and include all neccessary
//	require('../lib/common.inc.php');	
//	if( $usr['admin'] )
//	{ 	
  $y=array();
  $x=array();
  $y2=array();
  $x2=array();

$rsreports= sql("SELECT count(*) count, responsible_id, username from reports,user WHERE submit_date > '2009-05-31 00:00:00' and responsible_id <>0 AND responsible_id != 1883 AND user.user_id=responsible_id GROUP BY responsible_id ORDER  BY username");

$rscaches= sql("SELECT count(*) count, username from approval_status,user WHERE user.user_id=approval_status.user_id  GROUP BY approval_status.user_id");


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
				mysql_free_result($rsreports);
				mysql_free_result($rscaches);


				
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
$descibe="Statystyka RR2 - zg³oszenia";
$graph->title->Set($descibe);
$graph->xaxis->title->Set($xtitle);
$graph->xaxis->SetTickLabels($x);


// Some extra margin looks nicer
//$graph->xaxis->SetLabelMargin(10);

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


// Display the graph

//  $graph->Stroke();
   

// Create the graph. These two calls are always required
$graph2 = new Graph(500,200,'auto');
$graph2->SetScale('textint',0,max($y2)+(max($y2)*0.2),0,0);
// ,0,0,0,max($y)-min($y)+5);
// Add a drop shadow
$graph2->SetShadow();


// Label callback
//function year_callback($aLabel) {
//    return 1700+(int)$aLabel;
//}
//$graph->xaxis->SetLabelFormatCallback('year_callback');
// $graph->SetScale('intint',0,0,0,max($year)-min($year)+1);

 
// Adjust the margin a bit to make more room for titles
 $graph2->SetMargin(50,30,30,40);
 
// Create a bar pot
$bplot2 = new BarPlot($y2);
 
// Adjust fill color
$bplot2->SetFillColor('chartreuse3');
$graph2->Add($bplot2);
 
 
// Setup the titles
$descibe2="Statystyka RR2 - skrzynki zatwierdzone";
$graph2->title->Set($descibe2);
$graph2->xaxis->title->Set($xtitle);
$graph2->xaxis->SetTickLabels($x2);


// Some extra margin looks nicer
//$graph->xaxis->SetLabelMargin(10);

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


// Display the graph

//  $graph->Stroke();


//-----------------------
// Create a multigraph
//----------------------
$mgraph = new MGraph();
$mgraph->SetMargin(10,10,10,10);
$mgraph->SetFrame(true,'darkgray',2);
$mgraph->Add($graph);
$mgraph->Add($graph2,0,220);
$mgraph->Stroke();

//}
   
  }
 
  
  ?>
