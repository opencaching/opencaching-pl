<?php

  //prepare the templates and include all neccessary
//	require_once('./lib/common.inc.php');
	
	//Preprocessing
	if ($error == false)
	{
require("../lib/jpgraph/src/jpgraph.php");
require("../lib/jpgraph/src/jpgraph_pie.php");
require("../lib/jpgraph/src/jpgraph_pie3d.php");

  require('../lib/web.inc.php');
  sql('USE `ocpl`');

		// check for old-style parameters
		if (isset($_REQUEST['cacheid']))
		{
			$cache_id = $_REQUEST['cacheid'];
		}
		
  $y=array();
  $x=array();
  
// Ustawic sprawdzanie jezyka  w cache_type.pl !!!!
$rsCSF= sql("SELECT COUNT(`cache_logs`.`type`) `count`, `log_types`.`pl` `type` FROM `cache_logs` INNER JOIN `log_types` ON (`cache_logs`.`type`=`log_types`.`id`) WHERE type=1 AND cache_logs.deleted=0 AND cache_logs.cache_id=&1 GROUP BY `cache_logs`.`type` ORDER BY `log_types`.`pl` ASC",$cache_id);

		if (mysql_num_rows($rsCSF) != 0){
				$xtitle="";
					$ry = mysql_fetch_array($rsCSF);
					$y[] = $ry['count'];
					$x[] = $ry['type'];
					} else {
					$x[] = "znaleziona";
							}

$rsCSNF= sql("SELECT COUNT(`cache_logs`.`type`) `count`, `log_types`.`pl` `type` FROM `cache_logs` INNER JOIN `log_types` ON (`cache_logs`.`type`=`log_types`.`id`) WHERE type=2 AND cache_logs.deleted=0 AND cache_logs.cache_id=&1 GROUP BY `cache_logs`.`type` ORDER BY `log_types`.`pl` ASC",$cache_id);

		if (mysql_num_rows($rsCSNF) != 0){
				$xtitle="";
					$ry = mysql_fetch_array($rsCSNF);
					$y[] = $ry['count'];
					$x[] = $ry['type'];
					} else {
					$x[] = "nieznaleziona";
							}	
				

$rsCSC= sql("SELECT COUNT(`cache_logs`.`type`) `count`, `log_types`.`pl` `type` FROM `cache_logs` INNER JOIN `log_types` ON (`cache_logs`.`type`=`log_types`.`id`) WHERE type=3 AND cache_logs.deleted=0 AND cache_logs.cache_id=&1 GROUP BY `cache_logs`.`type` ORDER BY `log_types`.`pl` ASC",$cache_id);

		if (mysql_num_rows($rsCSC) != 0){
				$xtitle="";
					$ry = mysql_fetch_array($rsCSC);
					$y[] = $ry['count'];
					$x[] = $ry['type'];
					} else {
					$x[] = "komentarze";
					}
					
				mysql_free_result($rsCSF);
				mysql_free_result($rsCSNF);
				mysql_free_result($rsCSC);

				
/// A new pie graph
$graph = new PieGraph(400,200,"auto");
$graph->SetScale('textint');

// Title setup
$graph->title->Set("Wg typów logów");
$graph->title->SetFont(FF_FONT1,FS_BOLD);
//$graph ->legend->Pos( 0.25,0.8,"right" ,"bottom"); 


// Setup the pie plot
$p1 = new PiePlot($y);
$p1->SetTheme("earth");
$p1->value->SetFormat("%d");
$p1->SetLabelType(PIE_VALUE_ABS);
$p1->SetSliceColors(array('chartreuse3','chocolate2','wheat1')); 

// Adjust size and position of plot
$p1->SetSize(0.35);
$p1->SetCenter(0.25,0.52);
 
// Setup slice labels and move them into the plot

$p1->value->SetFont(FF_FONT1,FS_BOLD);
$p1->value->SetColor("black");
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
