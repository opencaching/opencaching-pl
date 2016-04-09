<?php
use Utils\Database\XDb;

require_once("./lib/common.inc.php");

$wp = XDb::xEscape($_GET['wp']);


$query = "select name,latitude,longitude from caches where wp_oc = '" . $wp . "'";
$wynik = XDb::xSql($query);
$wiersz = XDb::xFetchArray($wynik);

$name = $wiersz['name'];
$lat = $wiersz['latitude'];
$lon = $wiersz['longitude'];

$tpl->assign('name', $name);
$tpl->assign('lat', $lat);
$tpl->assign('lon', $lon);
$tpl->assign('wp', $wp);
$tpl->display('./tpl/osmap.tpl');
?>