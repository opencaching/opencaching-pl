<?php
/**
 * text cache founded statistics year 2 year.
 * All caches in database.
 */
header('Content-type: text/html; charset=utf-8');

require_once __DIR__.'/lib/ClassPathDictionary.php';
$db = new dataBase;

$query = "SELECT `date_hidden`
FROM `caches` , cache_location
WHERE cache_location.code3 = 'PL21'
AND cache_location.cache_id = caches.cache_id
ORDER BY `date_hidden`
";

$query = "SELECT `date_hidden`, cache_location.code3, cache_location.adm3
FROM `caches`, cache_location
WHERE cache_location.cache_id = caches.cache_id
ORDER BY `date_hidden`
";

$db->simpleQuery($query);
$arr = $db->dbResultFetchAll();

print '<pre>';
// print_r($arr);

foreach ($arr as $value) {
    $data = explode('-', $value['date_hidden']);
    if (!isset($count[$data[0]])) $count[$data[0]]=0;
    $count[$data[0]]++;

    if(!isset($region[$value['adm3']][$data[0]])) $region[$value['adm3']][$data[0]]=0;
    $region[$value['adm3']][$data[0]]++;
}
print 'region <br>';
print '[rok] => ilosc utworzonych keszy w danym roku<br><br>';
print 'caly opencaching.pl:<br>';
print_r($count);
print 'regiony: <br>';
print_r($region);
