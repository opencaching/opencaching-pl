<?
$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/common.inc.php';
require_once __DIR__.'/../lib/db.php';
require_once __DIR__.'/powerTrailBase.php';

$ptId = (int) $_REQUEST['ptId'];

$o1 = ' AND `user`.`is_active_flag` = 1 ';
$o2 = ' AND `user`.`last_login` > date_sub(now(), interval 12 month )';
$q = "

SELECT `user`.`username` , `cache_logs`.`user_id` , count( * ) AS `FoundCount`
FROM `cache_logs` , `user`
WHERE `cache_id`
IN (

SELECT `cacheId`
FROM `powerTrail_caches`
WHERE `PowerTrailId` =:1
)
AND `deleted` =0
AND `type` =1
AND `cache_logs`.`user_id` = `user`.`user_id`

$o1 $o2

GROUP BY `user_id`
ORDER BY `FoundCount` DESC

";

$db = new dataBase;
$db->multiVariableQuery($q, $ptId);
$statsArr = $db->dbResultFetchAll();

$ptTotalCacheesCount = powerTrailBase::getPtCacheCount($ptId);
// echo '<pre>'; var_dump($ptTotalCacheesCount, $statsArr);
$stats2display = '<table><tr>
<th>'.tr('pt095').'</th>
<th>'.tr('pt096').'</th>
<th>'.tr('pt097').'</th>
</tr>';
if ($ptTotalCacheesCount != 0) {
	$bgcolor = '#ffffff';	
	foreach ($statsArr as $user) {
		if($bgcolor == '#eeeeff') $bgcolor = '#ffffff'; else $bgcolor = '#eeeeff';
		$stats2display .= '<tr bgcolor="'.$bgcolor.'">
			<td><a href="viewprofile.php?userid='.$user['user_id'].'">'.$user['username'].'</a></td>
			<td align="center">'.$user['FoundCount'].'</td>
			<td align="center">'.round($user['FoundCount'] * 100 / $ptTotalCacheesCount, 2) .'% </td>
		</tr>';
	}	
} else {
		$stats2display .= '';
}
$stats2display .= '</table>';
// powerTrailController::debug($stats);
echo $stats2display;
