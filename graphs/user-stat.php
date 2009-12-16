<?php
require("../lib/jpgraph/src/jpgraph.php");
require('../lib/jpgraph/src/jpgraph_bar.php');
require('../lib/jpgraph/src/jpgraph_date.php');
require("../lib/jpgraph/src/jpgraph_pie.php");
require("../lib/jpgraph/src/jpgraph_pie3d.php");



  require('../lib/web.inc.php');
  sql('USE `ocpl`');

/*
Zalozenia:

zmienna weyjsciowa user_id wiec zapytanie typu user_stat.php?user_id=xx
$startdate = user.date_created (data rejstracji)
$endtime = date("Y-m-d H:i:s"); biezaca data
$tartdate-$endtime= $difftime   ilosc dni jakie upynelo od rejstracji na OC PL
--------------------------------

Ogolny stat:
Liczba ukrytych: user.hidden_count
Liczba znalezionych: user.founds_count
Liczba nieznalezionych: user.notfounds_count
Liczba komentarzy: user.log_notes_count
W ilu uczestniczyl wydarzeniach: search cache_logs count() WHERE type=7 AND user_id=user.user_id
------------------------------

Podzial statystyk - czy na jednym wykresie roznymi kolorami bary znalezione /zalozone ? czy osobno dzialy

==== Aktywnosc zalozonych skrzynek ======


==== Aktywnosc znalezionych skrzynek ====
$rsCacheFindYear = mysql('SELECT COUNT(*) `count`,YEAR(`date_created`) `year` FROM `cache_logs` WHERE type=1 AND user_id=449 GROUP BY YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC');
$rsCacheFindMonth=mysql('SELECT COUNT(*) `count`,YEAR(`date_created`) `year` , MONTH(`date_created`) `month` FROM `cache_logs` WHERE type=1 AND user_id=449 GROUP BY MONTH(`date_created`) , YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`)  ASC');

Statystyki roczne - liczba skrzynek 
Pierwszy rok brany z $startdate ostatni rok z $endtime
Zalozonych
 0    14    10
2006 2007 2008

zalozone
dane z SELECT count() ....  FROM caches  WHERE user_id=user.user_id GROUP BY
*/
$rsCreateCachesYear= sql('SELECT COUNT(*) `count`,YEAR(`date_created`) `year` FROM `caches` WHERE user_id=449 GROUP BY YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC');
//  $rsccy = mysql_fetch_array($rsCreateCachesYear);
  $y=array();
  $x=array();
				while ($rccy = mysql_fetch_array($rsCreateCachesYear)){
					$y[] = $rccy['count'];
					$x[] = $rccy['year'];}
				mysql_free_result($rsCreateCachesYear);
// Create the graph. These two calls are always required
$graph2 = new Graph(600,200,'auto');
$graph2->SetScale('textint');
 
// Add a drop shadow
$graph2->SetShadow();
 
// Adjust the margin a bit to make more room for titles
 $graph2->SetMargin(40,30,20,40);
 
// Create a bar pot
$bplot = new BarPlot($y);
 
// Adjust fill color
$bplot->SetFillColor('steelblue2');
$graph2->Add($bplot);
 
// Setup the titles
$graph2->title->Set('Statystyka usera');
$graph2->xaxis->title->Set('Rok');
$graph2->xaxis->SetTickLabels($x);
$graph2->yaxis->title->Set('Liczba skrzynek');
 
$graph2->title->SetFont(FF_FONT1,FS_BOLD);
$graph2->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph2->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
 
  
// Setup the values that are displayed on top of each bar
$bplot->value->Show();
 
// Must use TTF fonts if we want text at an arbitrary angle
$bplot->value->SetFont(FF_FONT1,FS_BOLD);
$bplot->value->SetAngle(0);

// Display the graph

 $graph2->Stroke();

/*
-----------------------------
znalezione
SELECT   FROM cache_logs count() ......   WHERE type=1 AND user_id= user.user_id GROUP BY
----------------------

Statystyki miesieczne - aktywnosc
Pierwszy rok brany z $startdate ostatni rok z $endtime
          10            4
2007  1 2 3 4 5 6 7 8 9 10 11 12   dla kazdego miesiaca

zalozone */

$rsCreateCachesMonth = sql('SELECT COUNT(*) `count`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `caches` WHERE user_id=449 GROUP BY MONTH(`date_created`), YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC');
 $ym=array();
 $xm=array();
//				for ($i = 0; $i < mysql_num_rows($rsCreateCachesMonth); $i++)
 //{
				while ($rccm = mysql_fetch_array($rsCreateCachesMonth)){
					$ym[] = $rccm['count'];
					$xm[] = $rccm['month'];}
// Create the graph. These two calls are always required
$graph = new Graph(600,200,'auto');
$graph->SetScale('textint');
 
// Add a drop shadow
$graph->SetShadow();
 
// Adjust the margin a bit to make more room for titles
 $graph->SetMargin(40,30,20,40);
 
// Create a bar pot
$bplot2 = new BarPlot($ym);
 
// Adjust fill color
$bplot2->SetFillColor('steelblue2');
$graph->Add($bplot2);
 
// Setup the titles
$graph->title->Set('Statystyka usera');
$graph->xaxis->title->Set('Rok');
$graph->xaxis->SetTickLabels($xm);
$graph->yaxis->title->Set('Liczba skrzynek');
 
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
 
  
// Setup the values that are displayed on top of each bar
$bplot2->value->Show();
 
// Must use TTF fonts if we want text at an arbitrary angle
$bplot2->value->SetFont(FF_FONT1,FS_BOLD);
$bplot2->value->SetAngle(0);

// Display the graph

  $graph->Stroke();
//}
  mysql_free_result($rsCreateCachesMonth);
				
				
/*
znalezione
SELECT  count() ..... FROM cache_logs WHERE user_id=user.user_id GROUP BY



*/
  ?>
