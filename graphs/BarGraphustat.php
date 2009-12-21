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
$rsCreateCachesYear= sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` FROM `caches` WHERE user_id=&1 GROUP BY YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC",$user_id);

				if ($rsCreateCachesYear !== false){
				$descibe="Roczna statystyka zalozonych skrzynek";
				$xtitle="";
					while ($ry = mysql_fetch_array($rsCreateCachesYear)){
					$y[] = $ry['count'];
					$x[] = $ry['year'];}
					}
				mysql_free_result($rsCreateCachesYear);
		}


if ($tit == "ccm") {
$rsCreateCachesMonth = sql("SELECT COUNT(*) `count`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `caches` WHERE user_id=&1 AND YEAR(`date_created`)=&2 GROUP BY MONTH(`date_created`), YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC",$user_id,$year);

 				if ($rsCreateCachesMonth !== false) {
				$descibe = "Miesieczna statystyka zalozonych skrzynek";
				$xtitle=$year;
				while ($rm = mysql_fetch_array($rsCreateCachesMonth)){
					$y[] = $rm['count'];
					$x[] = $rm['month'];}
					}

 mysql_free_result($rsCreateCachesMonth);
				
				}

if ($tit == "cfy") {
$rsCachesFindYear = sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` FROM `cache_logs` WHERE type=1 AND user_id=&1 GROUP BY YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC",$user_id);

  				if ($rsCachesFindYear !== false) {
				$descibe="Roczna statystyka znalezionych skrzynek";
				$xtitle="";
				while ($rfy = mysql_fetch_array($rsCachesFindYear)){
					$y[] = $rfy['count'];
					$x[] = $rfy['year'];}
					}
				mysql_free_result($rsCachesFindYear);
}

if ($tit == "cfm") {
$rsCachesFindMonth= sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` , MONTH(`date_created`) `month` FROM `cache_logs` WHERE type=1 AND user_id=&1 AND YEAR(`date_created`)=&2 GROUP BY MONTH(`date_created`) , YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC",$user_id,$year);

 				if ($rsCachesFindMonth !== false){
				$descibe="Miesieczna statystyka znalezionych skrzynek";
				$describe .= $year;
				$xtitle=$year;

				while ($rfm = mysql_fetch_array($rsCachesFindMonth)){
					$y[] = $rfm['count'];
					$x[] = $rfm['month'];}
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

$graph->yaxis->title->Set('Liczba skrzynek');
 
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
