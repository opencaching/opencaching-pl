<?php
$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/common.inc.php';
db_disconnect();

$ptId = (int) $_REQUEST['ptId'];
$ptTotalCacheesCount = powerTrailBase::getPtCacheCount($ptId);
if($ptTotalCacheesCount == 0){ // power Trail has no caches!
    print tr('pt105');
    exit;
}

$o1 = ' AND `user`.`is_active_flag` = 1 ';
$o2 = ' AND `user`.`last_login` > date_sub(now(), interval 12 month )';
$q = "

SELECT `user`.`username` , `cache_logs`.`user_id`, count( * ) AS `FoundCount`, `cache_logs`.`date`
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

GROUP BY `user_id`, `date`
ORDER BY `FoundCount` DESC, `username` ASC

";

$db = \lib\Database\DataBaseSingleton::Instance();
$db->multiVariableQuery($q, $ptId);
$statsArr = $db->dbResultFetchAll();

// echo '<pre>'; var_dump($ptTotalCacheesCount, $statsArr);
if(count($statsArr) == 0){ // no result!
    print tr('pt105');
    exit;
}


foreach ($statsArr as $user) {
    $tmpDate = substr($user['date'], 0, -9);
    if(!isset($sorted[$user["user_id"]])) {
        $sorted[$user["user_id"]] = array(
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'FoundCount' => $user['FoundCount'],
        );
        $tmp[$user["user_id"]]['dates'][] = $tmpDate;
    } else {
        $sorted[$user["user_id"]]['FoundCount'] += $user['FoundCount'];
        if(!in_array($tmpDate, $tmp[$user["user_id"]]['dates'])){
            $tmp[$user["user_id"]]['dates'][] = $tmpDate;
        }
    }
}

foreach ($tmp as $userId => $value) {
    $sorted[$userId]['daysSpent'] = count($value['dates']);
}

$sort = array();
foreach($sorted as $k=>$v) {
    $sort['username'][$k] = $v['username'];
    $sort['FoundCount'][$k] = $v['FoundCount'];
}
array_multisort($sort['FoundCount'], SORT_DESC, $sort['username'], SORT_ASC, $sorted);
$ptDbRow = powerTrailBase::getPtDbRow($ptId);

// print_r($sorted);
// print_r($tmp);
$stats2display = '<table class="statsTable" style="border-collapse:collapse;" align="center"><tr>
<th>'.tr('pt095').'</th>
<th>'.tr('pt096').'</th>
<th>'.tr('pt097').'</th>
<th>'.tr('pt101').'</th>
<th>'.tr('pt102').'</th>
</tr>';
if ($ptTotalCacheesCount != 0) {
    $bgcolor = '#ffffff';

    $fullPtFoundCount = 0;
    $totCacheDays = 0;
    foreach ($sorted as $user) {
        $totCacheDays += $user['daysSpent'];
        $ptPercent = round($user['FoundCount'] * 100 / $ptTotalCacheesCount, 2);
        if ($ptPercent >= $ptDbRow['perccentRequired']) {
            $fullPtFoundCount++;
            if(isset($averageDaysSpent)) {
                $averageDaysSpent = (($averageDaysSpent * $totalNumber) + $user['daysSpent']) / ($totalNumber + 1);
                $totalNumber++;
            }
            else {
                $averageDaysSpent = $user['daysSpent'];
                $totalNumber = 1;
            }
        }
        if($bgcolor == '#eeeeff') $bgcolor = '#ffffff'; else $bgcolor = '#eeeeff';
        $stats2display .= '<tr bgcolor="'.$bgcolor.'">
            <td ><a href="viewprofile.php?userid='.$user['user_id'].'">'.$user['username'].'</a></td>
            <td align="center">'.$user['FoundCount'].'</td>
            <td align="center">'.$ptPercent.'% </td>
            <td align="center">'.$user['daysSpent'].'</td>
            <td align="center">'.round($user['FoundCount']/$user['daysSpent'], 2).'</td>
        </tr>';
    }
} else {
    $stats2display .= '';
}
$stats2display .= '</table>';

isset($averageDaysSpent) ? $ads = round($averageDaysSpent,1) : $ads = '&#8734;';
$stats2display .=

'<hr>
<table class="statsTable" style="border-collapse:collapse;" align="center">
    <tr>
        <th>'.tr('pt122').'</th>
        <th>'.tr('pt123').'</th>
    </tr>
    <tr>
        <td>'.tr('pt119').'</td>
        <td>'.count($sorted).'</td>
    </tr>
    <tr>
        <td>'.tr('pt135').'</td>
        <td>'.$totCacheDays.'</td>
    </tr>
    <tr>
        <td>'.tr('pt120').'<a class="tooltip" href="javascript:void(0);">'.tr('pt125').'<span class="custom help"><img src="tpl/stdstyle/images/toltipsImages/Info.png" alt="Help" height="48" width="48" /><em>'.tr('pt126').'</em>'.tr('pt124').'</span></a></td>
        <td>'.$fullPtFoundCount.'</td>
    </tr>
    <tr>
        <td>'.tr('pt121').'<a class="tooltip" href="javascript:void(0);">'.tr('pt125').'<span class="custom help"><img src="tpl/stdstyle/images/toltipsImages/Info.png" alt="Help" height="48" width="48" /><em>'.tr('pt126').'</em>'.tr('pt124').'</span></a></td>
        <td>'.$ads.'</td>
    </tr>
</table>
';
// powerTrailController::debug($stats);
echo $stats2display;
