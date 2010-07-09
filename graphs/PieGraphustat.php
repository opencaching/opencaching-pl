<?php
  $rootpath = '../';
  require('../lib/common.inc.php');

  //prepare the templates and include all neccessary
//	require_once('./lib/common.inc.php');
	
	//Preprocessing
	if ($error == false)
	{
require("../lib/jpgraph/src/jpgraph.php");
require("../lib/jpgraph/src/jpgraph_pie.php");
require("../lib/jpgraph/src/jpgraph_pie3d.php");

		$year='';
		// check for old-style parameters
		if (isset($_REQUEST['userid']) && isset($_REQUEST['t']))
		{
			$user_id = $_REQUEST['userid'];
			$tit = $_REQUEST['t'];
		}
		
  $y=array();
  $x=array();
  
  

if ($tit == "cc") {
// Ustawic sprawdzanie jezyka  w cache_type.pl !!!!
$rsCreateCachesYear= sql("SELECT COUNT(`caches`.`type`) `count`, `cache_type`.`pl` `type` FROM `caches` INNER JOIN `cache_type` ON (`caches`.`type`=`cache_type`.`id`) WHERE `user_id`=&1 AND status <> 4 AND status <>5 GROUP BY `caches`.`type` ORDER BY `count` DESC",$user_id);

				if ($rsCreateCachesYear !== false){
				$xtitle="";
					while ($ry = mysql_fetch_array($rsCreateCachesYear)){
					$y[] = $ry['count'];
					$x[] = $ry['type'];}
					}
				mysql_free_result($rsCreateCachesYear);
		}

if ($tit == "cf") {
$rsCachesFindYear = sql("SELECT COUNT(`caches`.`type`) `count`, `cache_type`.`pl` `type` FROM `cache_logs`, caches INNER JOIN `cache_type` ON (`caches`.`type`=`cache_type`.`id`) WHERE cache_logs.`deleted`=0 AND cache_logs.user_id=&1 AND cache_logs.`type`='1' AND cache_logs.`cache_id` = caches.cache_id  GROUP BY `caches`.`type` ORDER BY `count` DESC",$user_id);

  				if ($rsCachesFindYear !== false) {
				$xtitle="";
				while ($rfy = mysql_fetch_array($rsCachesFindYear)){
					$y[] = $rfy['count'];
					$x[] = $rfy['type'];}
					}
				mysql_free_result($rsCachesFindYear);
}


				
/// A new pie graph
$graph = new PieGraph(500,300,"auto");
$graph->SetScale('textint');

// Title setup
$graph->title->Set("Wg typów skrzynek");
$graph->title->SetFont(FF_FONT1,FS_BOLD);
//$graph ->legend->Pos( 0.25,0.8,"right" ,"bottom"); 
 
// Setup the pie plot
$p1 = new PiePlot($y);
$p1->SetTheme("earth");
$p1->value->SetFormat("%d");
$p1->SetLabelType(PIE_VALUE_ABS);

// Adjust size and position of plot
$p1->SetSize(0.35);
$p1->SetCenter(0.25,0.52);
 
// Setup slice labels and move them into the plot

$p1->value->SetFont(FF_FONT1,FS_BOLD);
$p1->value->SetColor("darkred");
$p1->SetLabelPos(0.65);
$p1->SetLegends($x);

 
// Explode all slices
//$p1->ExplodeAll(10);
 
// Finally add the plot
$graph->Add($p1);

$graph->SetShadow();

// ... and stroke it
$graph->Stroke();
 

  }
  
  ?>
