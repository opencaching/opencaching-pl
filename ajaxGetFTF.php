<?php
require_once 'lib/db.php';

$userId = addslashes($_REQUEST['id']);
$database=new dataBase();
$ftfQuery = 'SELECT `a`.`cache_id`, a.date, caches.name FROM `cache_logs` AS `a`,  caches WHERE caches.cache_id = a.cache_id AND a.`user_id` =:1 AND `date` IN (SELECT MIN(date) FROM `cache_logs` AS b WHERE a.cache_id = b.cache_id AND deleted=0 and type=1) ORDER BY date';

$database->multiVariableQuery($ftfQuery, $userId);
$ftfResult = $database->dbResultFetchAll();

$html = '<br>FTF x '.$database->rowCount().'<br><br><table class="ptCacheTable">';
$bgColor='#eeeeff';
foreach ($ftfResult as $ftfCache) {
	if($bgColor == '#eeeeff') $bgColor = '#ffffff'; else $bgColor ='#eeeeff';
	$data = explode(' ', $ftfCache['date']);
	$html .= '<tr bgcolor="'.$bgColor.'"><td style="width: 60px;">'.$data[0].'</td><td style="width: 60px;">'.$data[1].'</td><td><a href=viewcache.php?cacheid='.$ftfCache['cache_id'].'>'.$ftfCache['name'].'</a></td></tr>';
}
$html .= '</table>';

print $html;
//var_dump($ftfResult);
