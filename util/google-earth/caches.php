<?php

ob_start();

use Utils\Database\XDb;

/* Bounding Box:
  BBOX=2.38443,48.9322,27.7053,55.0289
 */

$rootpath = '../../';
header('Content-type: text/html; charset=utf-8');
require($rootpath . 'lib/common.inc.php');
require($rootpath . 'lib/export.inc.php');
require_once($rootpath . 'lib/format.kml.inc.php');

$bbox = isset($_REQUEST['BBOX']) ? $_REQUEST['BBOX'] : '0,0,0,0';
$abox = mb_split(',', $bbox);

if (count($abox) != 4)
    exit;

if (!is_numeric($abox[0]))
    exit;
if (!is_numeric($abox[1]))
    exit;
if (!is_numeric($abox[2]))
    exit;
if (!is_numeric($abox[3]))
    exit;

$lat_from = $abox[1];
$lon_from = $abox[0];
$lat_to = $abox[3];
$lon_to = $abox[2];

// restrict area for which we actually perform queries.
if ((abs($lon_from - $lon_to) > 2) || (abs($lat_from - $lat_to) > 2)) {
    $lon_from = $lon_to;
    $lat_from = $lat_to;
}

$rs = XDb::xSql(
    "SELECT `caches`.`cache_id` `cacheid`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`,
            `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`date_hidden` `date_hidden`,
            `caches`.`name` `name`, `caches`.`wp_oc` `cache_wp`,
            `cache_type`.`" . $lang . "` `typedesc`, `cache_size`.`" . $lang . "` `sizedesc`,
            `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`,
            `user`.`username` `username`
    FROM `caches`, `cache_type`, `cache_size`, `user`
    WHERE `caches`.`type`=`cache_type`.`id`
        AND `caches`.`size`=`cache_size`.`id`
        AND `caches`.`user_id`=`user`.`user_id`
        AND `caches`.`status` IN (1,2)
        AND `caches`.`longitude` >= ?
        AND `caches`.`longitude` <= ?
        AND `caches`.`latitude` >= ?
        AND `caches`.`latitude` <= ? ",
    $lon_from, $lon_to, $lat_from, $lat_to);

echo $kmlHead;

while ($r = XDb::xFetchArray($rs)) {
    $thisline = $kmlLine;
    $thiskmlTypeIMG = $kmlTypeIMG;

    if (isset($kmlType[$r['type']])) {
        $icon = $kmlType[$r['type']];
        $thiskmlTypeIMG = str_replace('{type}', $kmlType[$r['type']], $thiskmlTypeIMG);
        $thiskmlTypeIMG = str_replace('{type_text}', $kmlGeocacheTypeText[$r['type']], $thiskmlTypeIMG);
    } else {
        // unknown
        $icon = $kmlType[1];
        $thiskmlTypeIMG = str_replace('{type}', $kmlType[1], $thiskmlTypeIMG);
        $thiskmlTypeIMG = str_replace('{type_text}', $kmlGeocacheTypeText[1], $thiskmlTypeIMG);
    }

    $statusStyle = 'color: green';
    if ($kmlArchived[$r['status']] == 'True') {
        $icon .= '-archived';
        $statusStyle = 'color: #900; text-decoration: line-through';
    } else {
        if ($kmlAvailable[$r['status']] == 'False') {
            $icon .= '-disabled';
            $statusStyle = 'color: rgb(240,100,100);';
        }
    }

    $thisline = str_replace('{icon}', $icon, $thisline);
        $thisline = str_replace('{typeimgurl}', $thiskmlTypeIMG, $thisline);
    $thisline = str_replace('{status}', tr('cacheStatus_' . $r['status']), $thisline);
    $thisline = str_replace('{status-style}', $statusStyle, $thisline);

    $lat = sprintf('%01.5f', $r['latitude']);
    $thisline = str_replace('{lat}', $lat, $thisline);

    $lon = sprintf('%01.5f', $r['longitude']);
    $thisline = str_replace('{lon}', $lon, $thisline);

    $thisline = str_replace('{name}', xmlentities(convert_string($r['name'])), $thisline);

    // no user modified coords
    $thisline = str_replace('{mod_suffix}', '', $thisline);

    $thisline = str_replace('{type}', xmlentities(convert_string($r['typedesc'])), $thisline);
    $thisline = str_replace('{size}', xmlentities(convert_string($r['sizedesc'])), $thisline);

    $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
    $thisline = str_replace('{difficulty}', $difficulty, $thisline);

    $terrain = sprintf('%01.1f', $r['terrain'] / 2);
    $thisline = str_replace('{terrain}', $terrain, $thisline);

    $thisline = str_replace('{username}', xmlentities(convert_string($r['username'])), $thisline);
    $thisline = str_replace('{cache_wp}', xmlentities($r['cache_wp']), $thisline);

    echo $thisline;
    // DO NOT USE HERE:
    // ob_flush();
}
XDb::xFreeResults($rs);

echo $kmlFoot;

header('Content-Type: application/vnd.google-earth.kml; charset=utf8');
header('Content-Disposition: attachment; filename="ge.kml"');
ob_end_flush();

exit();
