<?php
use Utils\Database\XDb;
use Utils\I18n\I18n;
require_once("./lib/common.inc.php");



$query = "select date_hidden, name,  latitude, longitude, wp_oc, user_id, type from caches where status='1' and date_hidden<now() order by date_hidden desc limit 10";
$wynik = XDb::xSql($query);
$ile = XDb::xNumRows($wynik);
$tpl->assign("ile", $ile);

$znalezione = array();
$lista = array();
$tpl->assign("address", "viewcache");

while ($rekord = XDb::xFetchArray($wynik)) {

    $query = "select username from user where user_id = " . $rekord['user_id'] . ";";
    $wynik2 = XDb::xSql($query);
    $wiersz = XDb::xFetchArray($wynik2);

    $query = "select " . I18n::getCurrentLang() . " from cache_type where id = " . $rekord['type'] . ";";
    $wynik2 = XDb::xSql($query);
    $wiersz2 = XDb::xFetchArray($wynik2);

    $rekord['username'] = $wiersz['username'];
    $rekord['date_hidden'] = date("d-m-Y", strtotime($rekord['date_hidden']));
    $rekord['N'] = cords($rekord['latitude']);
    $rekord['E'] = cords($rekord['longitude']);
    $rekord['typetext'] = $wiersz2[0];

    $lista[] = $rekord['wp_oc'];
    $znalezione [] = $rekord;
}

$tpl->assign('lista', $lista);
$tpl->assign("max", 1);
$tpl->assign("znalezione", $znalezione);
$tpl->display('./tpl/find2.tpl');
?>