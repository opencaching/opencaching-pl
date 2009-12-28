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
				$descibe="Roczna statystyka skrzynki";
				$xtitle="";
				while ($rfy1 = mysql_fetch_array($rsCachesFindYear1)){
					$y1[] = $rfy1['count'];
					$x1[] = $rfy1['year'];}
					}
//					echo $y1[];
				mysql_free_result($rsCachesFindYear1);
$rsCachesFindYear2 = sql("SELECT COUNT(*) `count`,YEAR(`date`) `year` FROM `cache_logs` WHERE type=2 AND cache_logs.deleted='0' AND cache_id=&1 GROUP BY YEAR(`date`) ORDER BY YEAR(`date`) ASC",$cache_id);

//$rsCachesFindYear = sql("SELECT COUNT(*) `count`,YEAR(`date`) `year`, type type FROM `cache_logs` WHERE (type=1 OR type=2) AND cache_logs.deleted='0' AND cache_id=&1 GROUP BY YEAR(`date`), type ORDER BY YEAR(`date`) ASC",$cache_id);
  				if ($rsCachesFindYear2 !== false) {
				$descibe="Roczna statystyka skrzynki";
				$xtitle="";
				while ($rfy2 = mysql_fetch_array($rsCachesFindYear2)){
					$y2[] = $rfy2['count'];
					$x2[] = $rfy2['year'];}
					}
				mysql_free_result($rsCachesFindYear2);
				
				}

if ($tit == "csm") {
$rsCachesFindMonth= sql("SELECT COUNT(*) `count`,YEAR(`date`) `year` , MONTH(`date`) `month` FROM `cache_logs` WHERE type=1 AND cache_logs.deleted='0' AND cache_id=&1 AND YEAR(`date`)=&2 GROUP BY MONTH(`date`) , YEAR(`date`) ORDER BY YEAR(`date`) ASC, MONTH(`date`) ASC",$cache_id,$year);

 				if ($rsCachesFindMonth !== false){
				$descibe="Miesiêczna statystyka skrzynki (ostatni rok)";
				$describe .= $year;
				$xtitle=$year;

				while ($rfm = mysql_fetch_array($rsCachesFindMonth)){
					$y[] = $rfm['count'];
					$x[] = $rfm['month'];}
				}
				mysql_free_result($rsCachesFindMonth);
}


				
// Create the graph. These two calls are always required
$graph = new Graph(310,200);    
$graph->SetScale("textlin");
 
$graph->SetShadow();
$graph->img->SetMargin(40,30,20,40);
 
// Create the bar plots
$b1plot = new BarPlot($y1);
$b1plot->SetFillColor("orange");
$b2plot = new BarPlot($y2);
$b2plot->SetFillColor("blue");
 
// Create the grouped bar plot
$gbplot = new GroupBarPlot(array($b1plot,$b2plot));
 
// ...and add it to the graPH
$graph->Add($gbplot);
 
$graph->title->Set("Example 21");
$graph->xaxis->title->Set("X-title");
$graph->yaxis->title->Set("Y-title");
 
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
 
// Display the graph
$graph->Stroke();

   
  }
 
  
  ?>
