<?php

//ini_set ('display_errors', On); 
  //
  // Basic setup
  //
  setlocale(LC_TIME, 'pl_PL.UTF-8');
  require('../lib/jpgraph/src/jpgraph.php');
  require('../lib/jpgraph/src/jpgraph_pie.php');

  // for productive use
  $graph = new PieGraph(700, 600, "auto", 60 * 24);

  // for development
  //$graph = new PieGraph(550, 350);

  require('../lib/web.inc.php');
  sql('USE `ocpl`');

  // Start von Opencaching
  $startDate = mktime(0, 0, 0, 1, 1, 2006);



  //
  // Daten abfragen
  //
  $rsTypes = sql('SELECT COUNT(`caches`.`type`) `count`, `cache_type`.`pl` `type`, `cache_type`.`color` FROM `caches` INNER JOIN `cache_type` ON (`caches`.`type`=`cache_type`.`id`) WHERE `status`=1 GROUP BY `caches`.`type` ORDER BY `count` DESC');

  $yData = array();
  $xData = array();
  $colors = array();
  while ($rTypes = mysql_fetch_array($rsTypes))
  {
    $yData[] = ' (' . $rTypes['count'] . ') ' . $rTypes['type'];
	$xData[] = $rTypes['count'];
    $colors[] = $rTypes['color'];
  }

  mysql_free_result($rsTypes);


  //
  // Titel, Footer, Legende und Hintergrund
  //
  $graph->title->SetFont(FF_GEORGIA, FS_NORMAL, 14);
  $graph->title->Set("Statystyka rodzajów skrzynek na OC PL");
  $graph->title->SetMargin(5);

  $graph->subtitle->SetFont(FF_GEORGIA, FS_NORMAL, 9);
  $graph->subtitle->SetColor(array(0, 0, 255));
  $graph->subtitle->Set("(tylko aktywne skrzynki)");
  $graph->subtitle->SetMargin(0);


  $graph->footer->center->Set("Dane dla www.opencaching.pl :: Data " . date('d:m:Y H:i:s'));
  $graph->footer->center->SetFont(FF_ARIAL, FS_NORMAL, 7);
  $graph->footer->center->SetColor('darkgray');

  $graph->legend->SetFont(FF_ARIAL, FS_NORMAL, 7);
  $graph->legend->SetLayout(LEGEND_VERT);
  $graph->legend->Pos(0.01, 0.3, "right", "center");

  $graph->SetColor(array(238, 238, 238));
  $graph->SetFrame(true, array(238, 238, 238), 0);

  //
  // Skalierung, X- und Y-Achse formatieren
  //


  //
  // Linien hinzufuegen
  //

  $pieTypes = new PiePlot($xData);
  $pieTypes->SetLegends($yData);
  $pieTypes->SetCenter(0.3);
  $pieTypes->value->SetFont(FF_ARIAL, FS_NORMAL, 9);
/*
  $pieTypes->SetSliceColors(array(
                                  array(255, 0, 255),
                                  array(255, 128, 255),
                                  array(255, 128, 0),
                                  array(255, 255, 0),
                                  array(0, 255, 255),
                                  array(0, 213, 255),
                                  array(0, 123, 255),
                                  array(0, 0, 255),
                                  array(0, 255, 0),
                                  array(0, 212, 0)
                                ));
*/
  $pieTypes->SetSliceColors(array_reverse($colors));
  $graph->Add($pieTypes);


  //
  // Infotexte einfuegen
  //

  //
  // Display the graph
  //
  $graph->Stroke();
?>
