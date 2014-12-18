<?php

require_once("./lib/common.inc.php");

$wp = mysql_real_escape_string($_GET['wp']);

db_connect();
$query = "select name,latitude,longitude from caches where wp_oc = '" . $wp . "'";
$wynik = db_query($query);
$wiersz = mysql_fetch_assoc($wynik);

$name = $wiersz['name'];
$lat = $wiersz['latitude'];
$lon = $wiersz['longitude'];

$tpl->assign('name', $name);
$tpl->assign('lat', $lat);
$tpl->assign('lon', $lon);
$tpl->assign('wp', $wp);
$tpl->display('./tpl/osmap.tpl');
?>