<?php

/*
Zalozenia:

zmienna weyjsciowa user_id wiec zapytanie typu user_stat.php?user_id=xx
$startdate = user.date_created (data rejstracji)
$endtime = time(); biezaca data
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

Statystyki roczne - liczba skrzynek 
Pierwszy rok brany z $startdate ostatni rok z $endtime
Zalozonych
 0    14    10
2006 2007 2008

zalozone
dane z SELECT count() ....  FROM caches  WHERE user_id=user.user_id
-----------------------------
znalezione
SELECT   FROM cache_logs count() ......   WHERE type=1 AND user_id= user.user_id
----------------------

Statystyki miesieczne - aktywnosc
Pierwszy rok brany z $startdate ostatni rok z $endtime
          10            4
2007  1 2 3 4 5 6 7 8 9 10 11 12   dla kazdego miesiaca

zalozone
SELECT  count() ..... FROM caches WHERE user_id=user.user_id
znalezione
SELECT  count() ..... FROM cache_logs WHERE user_id=user.user_id



*/
//  setlocale(LC_TIME, 'pl_PL.UTF-8');
// setlocale(LC_TIME, 'pl_PL.utf-8');
// setlocale(LC_TIME, 'en_EN.UTF-8');
require("../lib/jpgraph/src/jpgraph.php");
require('../lib/jpgraph/src/jpgraph_bar.php');
require('../lib/jpgraph/src/jpgraph_date.php');
require("../lib/jpgraph/src/jpgraph_pie.php");
require("../lib/jpgraph/src/jpgraph_pie3d.php");



  require('../lib/web.inc.php');
  sql('USE `ocpl`');

  // Start von Opencaching
  $startDate = mktime(0, 0, 0, 5, 1, 2006);



  //
  // Daten abfragen
  //
 // $rsCaches = sql('SELECT COUNT(*) `count`, DAY(`date_created`) `day`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `caches` WHERE caches.status=1 GROUP BY DAY(`date_created`), MONTH(`date_created`), YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC, DAY(`date_created`) ASC');
 $rsCaches = sql('SELECT COUNT(*) `count`, DAY(`date_created`) `day`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `caches` WHERE caches.status=1 AND user_id=49 GROUP BY DAY(`date_created`), MONTH(`date_created`), YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC, DAY(`date_created`) ASC');

 // $rsCaches = sql('SELECT COUNT(*) `count`, DAY(`date_created`) `day`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `caches` WHERE caches.status=1 GROUP BY DAY(`date_created`), MONTH(`date_created`), YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC, DAY(`date_created`) ASC');

 $rsLogs = sql('SELECT COUNT(*) `count`, DAY(`date_created`) `day`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `cache_logs` WHERE `type`=1 AND `deleted`=0 GROUP BY DAY(`date_created`), MONTH(`date_created`), YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC, DAY(`date_created`) ASC');
// $rsLogs_found = sql('SELECT COUNT(*) `count`, DAY(`date_created`) `day`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `cache_logs` WHERE `type`=1 AND `cache_id`=1 AND `deleted`=0 GROUP BY DAY(`date_created`), MONTH(`date_created`), YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC, DAY(`date_created`) ASC');
 // $rsLogs_notfound = sql('SELECT COUNT(*) `count`, DAY(`date_created`) `day`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `cache_logs` WHERE `type`=2 AND `cache_id`=1 AND `deleted`=0 GROUP BY DAY(`date_created`), MONTH(`date_created`), YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC, DAY(`date_created`) ASC');

  $rCaches = mysql_fetch_array($rsCaches);
//  $rLogs = mysql_fetch_array($rsLogs);
 

 
  $cachesCount = 0;
  $logsCount = 0;

  $yDataCaches = array();
  $yDataLogs = array();
  $xDate = array();
  $tickPositions = array();
  $tickLabels = array();

  // alle tage von 01-08-2005 bis heute durchgehen
  $days = 0;
  $date = $startDate;
  while ($date < time())
  {
    while (($rCaches !== false) && (strtotime($rCaches['year'] . '-' . $rCaches['month'] . '-' . $rCaches['day']) < $date))
      $rCaches = mysql_fetch_array($rsCaches);

    if ($rCaches['year'] . '-' . $rCaches['month'] . '-' . $rCaches['day'] == date('Y-n-j', $date))
      $cachesCount += $rCaches['count'];

    while (($rLogs !== false) && (strtotime($rLogs['year'] . '-' . $rLogs['month'] . '-' . $rLogs['day']) < $date))
      $rLogs = mysql_fetch_array($rsLogs);

    if ($rLogs['year'] . '-' . $rLogs['month'] . '-' . $rLogs['day'] == date('Y-n-j', $date))
      $logsCount += $rLogs['count'];

    $yDataCaches[] = $cachesCount;
    $yDataLogs[] = $logsCount;
    if (date('j', $date) == 1)
    {
      $tickPositions[]= $date;
      $tickLabels[] = strftime('%b', $date);
    }

    $xDate[] = $date;

    $days++;
    $date = mktime(0, 0, 0, 5, 1 + $days, 2006);
  }
  mysql_free_result($rsCaches);
  mysql_free_result($rsLogs);

$datay=array(12,8,19,3,10,5);

// Create the graph. These two calls are always required
$graph = new Graph(300,200);
$graph->SetScale('intlin');
 
// Add a drop shadow
$graph->SetShadow();
 
// Adjust the margin a bit to make more room for titles
$graph->SetMargin(40,30,20,40);
 
// Create a bar pot
$bplot = new BarPlot($rCaches['month']);
 
// Adjust fill color
$bplot->SetFillColor('orange');
$graph->Add($bplot);
 
// Setup the titles
$graph->title->Set('Statystyka usera');
$graph->xaxis->title->Set('X-title');
$graph->yaxis->title->Set('Liczba skrzynek');
 
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
 
// Display the graph

  $graph->Stroke();

  ?>
