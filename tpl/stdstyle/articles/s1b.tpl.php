<?php
use Utils\Database\XDb;

global $rootpath;
if (!isset($rootpath))
    $rootpath = './';

    //include template handling
    require_once($rootpath . 'lib/common.inc.php');
    setlocale(LC_TIME, 'pl_PL.UTF-8');

    $userscount = XDb::xSimpleQueryValue(
        'SELECT COUNT(DISTINCT user_id) FROM caches WHERE (status=1 OR `status`=2 OR `status`=3)', 0);

    $cachescount = XDb::xSimpleQueryValue(
        'SELECT COUNT(*) FROM `caches` WHERE (`status`=1 OR `status`=2 OR `status`=3)  AND `caches`.`type`<>6', 0);
?>

<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="">
    {{statistics}}
  </div>

<table width="100%" style="line-height: 1.6em; font-size: 12px;">
  <tr>
    <td colspan="3" style="text-align: center;"><b>{{ranking_by_number_of_created_caches}}</b></td>
  </tr>
  <tr>
    <td colspan="3" style="text-align: center;">{{users_who_created_caches}}: <?=$userscount?><br>{{number_of_caches}}: <?=$cachescount?></td>
  </tr>
  <tr class="bgcolor2">
    <th style="text-align: right; padding: 5px;">{{ranking}}</th>
    <th style="text-align: center; padding: 5px;">{{number_of_caches}}</th>
    <th style="text-align: left; padding: 5px;">{{username}}</th>
  </tr>

<?php 
    $r = XDb::xSql(
        "SELECT COUNT(*) `count`, `user`.`username` `username`, `user`.`user_id` `user_id`
    FROM `caches`
        INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
    WHERE (`caches`.`status`=1 OR `caches`.`status`=2 OR `caches`.`status`=3 )
        AND `caches`.`type`<>6 AND user.stat_ban = 0
    GROUP BY `user`.`user_id`
    ORDER BY `count` DESC, `user`.`username` ASC");
    
    $l2 = "";
    $licznik = 0;
    while ( $line = XDb::xFetchArray($r) ) {
        $l1 = $line['count'];
        $licznik++;
        if ($l2 != $l1) {
            echo '<tr class="bgcolor2"><td style="text-align: right; padding: 5px;">' . $licznik . '</td><td style="text-align: center; padding: 5px;">' . $l1 . '</td><td style="padding: 5px;"><a href="viewprofile.php?userid=' . $line['user_id'] . '">' . htmlspecialchars($line['username']) . '</a>';
            $l2 = $l1;
        } else {
            echo ', <a href="viewprofile.php?userid=' . $line['user_id'] . '">' . htmlspecialchars($line['username']) . '</a>';
        }
    }
?>

  </table>
</div>