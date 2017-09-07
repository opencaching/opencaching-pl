<?php

use Utils\Database\XDb;
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
require_once($rootpath . 'lib/ClassPathDictionary.php');
require_once($rootpath . 'okapi/Facade.php');
\okapi\Facade::disable_error_handling();

/* database connection */

/* last synchro check */
$last_updated = XDb::xSimpleQueryValue(
    "SELECT value FROM sysconfig WHERE name='geokrety_lastupdate'", 0);
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
    $id = XDb::xEscape($geokret['id']);
    $name = XDb::xEscape($geokret->name);
    $dist = XDb::xEscape($geokret->distancetravelled);
    $state = XDb::xEscape($geokret->state);
    $lat = XDb::xEscape($geokret->position['latitude']);
    $lon = XDb::xEscape($geokret->position['longitude']);

    /* geokrety info update */
    $query = XDb::xSql(
        "INSERT INTO gk_item (`id`, `name`, `distancetravelled`, `latitude`, `longitude`, `stateid`)
        VALUES ('" . $id . "', '" . $name . "', '" . $dist . "', '" . $lat . "', '" . $lon . "','" . $state . "')
        ON DUPLICATE KEY UPDATE `name`='" . $name . "', `distancetravelled`='" . $dist . "',
                                `latitude`='" . $lat . "', `longitude`='" . $lon . "',
                                `stateid`='" . $state . "'");

    /* Notify OKAPI. https://github.com/opencaching/okapi/issues/179 */
    $rs = XDb::xSql(
        "SELECT distinct wp FROM gk_item_waypoint
        WHERE id='" . XDb::xEscape($id) . "'");
    $cache_codes = array();
    while ($row = XDb::xFetchArray($rs))
        $cache_codes[] = $row[0];
    \okapi\Facade::schedule_geocache_check($cache_codes);

    /* waypoints update */
    XDb::xSql("DELETE FROM gk_item_waypoint WHERE id= ?", $id);
    foreach ($geokret->waypoints as $waypoint) {
        $wp = XDb::xEscape($waypoint->waypoint);
        if ($wp != '') {

            XDb::xSql(
                "INSERT INTO gk_item_waypoint (id, wp)
                VALUES ('" . $id . "', '" . $wp . "')
                ON DUPLICATE KEY UPDATE wp='" . $wp . "'");
        }
    }
}

/* cleaning... */

/* Notify OKAPI. https://github.com/opencaching/okapi/issues/179 */
$rs = XDb::xSql("SELECT distinct wp FROM gk_item_waypoint WHERE id NOT IN (SELECT id FROM gk_item)");
$cache_codes = array();
while ($row = XDb::xFetchArray($rs))
    $cache_codes[] = $row[0];
\okapi\Facade::schedule_geocache_check($cache_codes);

XDb::xSql("DELETE FROM gk_item_waypoint WHERE id NOT IN (SELECT id FROM gk_item)");

/* last synchro update */
XDb::xSql(
    "UPDATE sysconfig SET value = '" . XDb::xEscape($gkxml['date']) . "'
    WHERE name='geokrety_lastupdate'");
