<?php
require_once 'lib/db.php';

$userId = addslashes($_REQUEST['id']);
$database=new dataBase();
//$ftfQuery = 'SELECT * FROM caches WHERE cache_id IN (SELECT `a`.`cache_id` FROM `cache_logs` AS `a` WHERE `user_id` =:1 AND `date` IN (SELECT MIN(date) FROM `cache_logs` AS b WHERE a.cache_id = b.cache_id ))';


$ftfQuery = 'SELECT `a`.`cache_id`, a.date, caches.name FROM `cache_logs` AS `a`,  caches WHERE caches.cache_id = a.cache_id AND a.`user_id` =:1 AND `date` IN (SELECT MIN(date) FROM `cache_logs` AS b WHERE a.cache_id = b.cache_id AND deleted=0 and type=1) ORDER BY date';

$database->multiVariableQuery($ftfQuery, $userId);
$ftfResult = $database->dbResultFetchAll();
$c=0;
foreach ($ftfResult as $ftfCache) {
	$c++;
	print '<a href=viewcache.php?cacheid='.$ftfCache['cache_id'].'>'.$ftfCache['name'].'</a> - '.$ftfCache['date'].'<br/>';
}
print '<br><br>tot: '.$c;
var_dump($ftfResult);
