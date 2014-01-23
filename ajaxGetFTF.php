<?php
require_once 'lib/db.php';

$userId = addslashes($_REQUEST['id']);
$database=new dataBase();
//$ftfQuery = 'SELECT `a`.`cache_id`, a.date, caches.name FROM `cache_logs` AS `a`, caches WHERE caches.cache_id = a.cache_id AND a.`user_id` =:1 AND `date` = (SELECT MIN(date) FROM `cache_logs` AS b WHERE a.cache_id = b.cache_id AND deleted=0 and type=1) AND id = (select min(id) FROM `cache_logs` where cache_id = a.cache_id AND deleted=0 and type=1) ORDER BY date';
$ftfQuery = 'SELECT `a`.`cache_id` , a.date, caches.name FROM `cache_logs` AS `a` , caches WHERE caches.cache_id = a.cache_id AND a.`user_id` =:1 AND `date` = ( SELECT MIN( date ) FROM `cache_logs` AS b WHERE a.cache_id = b.cache_id AND deleted =0 AND TYPE =1 ) AND id = ( SELECT id FROM `cache_logs` WHERE `cache_id` = a.cache_id AND `type` =1 AND deleted =0 ORDER BY `cache_logs`.`date` ASC LIMIT 1 ) ORDER BY date';

$database->multiVariableQuery($ftfQuery, $userId);
$ftfResult = $database->dbResultFetchAll();

print json_encode($ftfResult);
exit;
?>