<?php

  //prepare the templates and include all neccessary
//	require_once('./lib/common.inc.php');
	
	//Preprocessing
	if ($error == false)
	{
require("../lib/jpgraph/src/jpgraph.php");
require('../lib/jpgraph/src/jpgraph_date.php');
require("../lib/jpgraph/src/jpgraph_pie.php");
require("../lib/jpgraph/src/jpgraph_pie3d.php");

  require('../lib/web.inc.php');
  sql('USE `ocpl`');
		$year='';
		// check for old-style parameters
		if (isset($_REQUEST['userid']) && isset($_REQUEST['title']))
		{
			$user_id = $_REQUEST['userid'];
			$titles = $_REQUEST['title'];
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
				$descibe="Roczna statystyka";
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
				$descibe="Miesiêczna statystyka za rok";
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
				$descibe="Roczna statystyka";
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
				$descibe="Miesiêczna statystyka za rok ";
				$describe .= $year;
				$xtitle=$year;
				while ($rfm = mysql_fetch_array($rsCachesFindMonth)){
					$y[] = $rfm['count'];
					$x[] = $rfm['month'];}
				}
				mysql_free_result($rsCachesFindMonth);
}


				
/// A new pie graph
$graph = new PieGraph(250,200,"auto");
$graph->SetShadow();
 
// Title setup
$graph->title->Set("Exploding all slices");
$graph->title->SetFont(FF_FONT1,FS_BOLD);
//$graph ->legend->Pos( 0.25,0.8,"right" ,"bottom"); 
 
// Setup the pie plot
$p1 = new PiePlot($data);
$p1->SetLabelType(PIE_VALUE_ABS);

// Adjust size and position of plot
$p1->SetSize(0.35);
$p1->SetCenter(0.5,0.52);
 
// Setup slice labels and move them into the plot
$p1->value->SetFont(FF_FONT1,FS_BOLD);
$p1->value->SetColor("darkred");
$p1->SetLabelPos(0.65);
 
// Explode all slices
$p1->ExplodeAll(10);
 
// Add drop shadow
$p1->SetShadow();
 
// Finally add the plot
$graph->Add($p1);
 
// ... and stroke it
$graph->Stroke();
 

  }
  
  ?>
