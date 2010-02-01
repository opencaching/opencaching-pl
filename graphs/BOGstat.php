<?php
setlocale(LC_TIME, 'pl_PL.utf-8');
  
	
	//Preprocessing
	if ($error == false)
	{
require("../lib/jpgraph/src/jpgraph.php");
require('../lib/jpgraph/src/jpgraph_bar.php');
require('../lib/jpgraph/src/jpgraph_date.php');


  require('../lib/web.inc.php');
  sql('USE `ocpl`');

	
  $y=array();
  $x=array();
  
  
$rsreports= sql("SELECT count(*) count, responsible_id, username from reports,user WHERE submit_date > '2009-05-31 00:00:00' and responsible_id <>0 AND responsible_id != 1883 AND user.user_id=responsible_id GROUP BY responsible_id ORDER  BY username");

$rscaches= sql("SELECT count(*) count, username from approval_status,user WHERE user.user_id=approval_status.user_id  GROUP BY approval_status.user_id");

				$descibe="Statystyka RR2 liczona od 31-05-2009";
				$xtitle="";
					while ($ry = mysql_fetch_array($rsreports))
					{
					$y[] = $ry['count'];
					$x[] = $ry['username'];
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

  $graph->Stroke();
   
  }
 
  
  ?>
