<?php

/* * *************************************************************************
  ./util.sec/geokrety/geokrety.new.php
  --------------------
  date                 : 06.09.2011r
  copyright            : (C) 2011 Opencaching.pl
  author               : Kamil "Limak" Karczmarczyk
  contact              : kkarczmarczyk@gmail.com

  description          : It's the new version of geokrety.org synchronization
  for opencaching nodes. This code uses a dedicated method
  export_oc.php - see: http://geokrety.org/api.php for more
  information. The old method that is used in
  geokrety.class.php is deprecated.

 * ************************************************************************* */

$rootpath = '../../';
require_once($rootpath . 'lib/clicompatbase.inc.php');
require_once($rootpath . 'okapi/facade.php');
\okapi\Facade::disable_error_handling();

/* database connection */
db_connect();
if ($dblink === false) {
    echo 'Unable to connect to database';
    exit;
}

/* last synchro check */
$sql = "SELECT value FROM sysconfig WHERE name='geokrety_lastupdate'";
$last_updated = mysql_result(mysql_query($sql), 0);
$modifiedsince = strtotime($last_updated);

/* new OC dedicated geokrety XML export */
$url = 'http://geokrety.org/export_oc.php?modifiedsince=' . date('YmdHis', $modifiedsince - 1);


$xmlString = file_get_contents($url);
$gkxml = @simplexml_load_string($xmlString);


//    $gkxml=@simplexml_load_file($url);
if (!$gkxml) {
    print $xmlString;
    die("Geokrety export error! Failed to load XML file [simplexml_load_file()]: " . $url);
}

/* read geokrety data */
foreach ($gkxml->geokret as $geokret) {
    /* for safety */
    $id = sql_escape($geokret['id']);
    $name = sql_escape($geokret->name);
    $dist = sql_escape($geokret->distancetravelled);
    $state = sql_escape($geokret->state);
    $lat = sql_escape($geokret->position['latitude']);
    $lon = sql_escape($geokret->position['longitude']);

    /* geokrety info update */
    $sql = "INSERT INTO gk_item (`id`, `name`, `distancetravelled`, `latitude`, `longitude`, `stateid`) VALUES ('" . $id . "', '" . $name . "', '" . $dist . "', '" . $lat . "', '" . $lon . "','" . $state . "')
        ON DUPLICATE KEY UPDATE `name`='" . $name . "', `distancetravelled`='" . $dist . "', `latitude`='" . $lat . "', `longitude`='" . $lon . "', `stateid`='" . $state . "'";
    $query = mysql_query($sql);

    /* Notify OKAPI. https://github.com/opencaching/okapi/issues/179 */
    $rs = mysql_query("SELECT distinct wp FROM gk_item_waypoint WHERE id='" . mysql_real_escape_string($id) . "'");
    $cache_codes = array();
    while ($row = mysql_fetch_array($rs))
        $cache_codes[] = $row[0];
    \okapi\Facade::schedule_geocache_check($cache_codes);

    /* waypoints update */
    sql("DELETE FROM gk_item_waypoint WHERE id='&1'", $id);
    foreach ($geokret->waypoints as $waypoint) {
        $wp = sql_escape($waypoint->waypoint);
        if ($wp != '') {
            $sql = "INSERT INTO gk_item_waypoint (id, wp) VALUES ('" . $id . "', '" . $wp . "') ON DUPLICATE KEY UPDATE wp='" . $wp . "'";
            mysql_query($sql);
        }
    }
}

/* cleaning... */

/* Notify OKAPI. https://github.com/opencaching/okapi/issues/179 */
$rs = mysql_query("SELECT distinct wp FROM gk_item_waypoint WHERE id NOT IN (SELECT id FROM gk_item)");
$cache_codes = array();
while ($row = mysql_fetch_array($rs))
    $cache_codes[] = $row[0];
\okapi\Facade::schedule_geocache_check($cache_codes);

sql("DELETE FROM gk_item_waypoint WHERE id NOT IN (SELECT id FROM gk_item)");

/* last synchro update */
sql("UPDATE sysconfig SET value = '" . sql_escape($gkxml['date']) . "' WHERE name='geokrety_lastupdate'");
?>