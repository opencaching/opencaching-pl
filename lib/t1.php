<?php

use Utils\Database\XDb;
global $lang, $rootpath;
if (!isset($rootpath))
    $rootpath = './';

//include template handling
require_once($rootpath . 'lib/common.inc.php');
setlocale(LC_TIME, 'pl_PL.UTF-8');

$userscount = XDb::xSimpleQueryValue('SELECT COUNT(DISTINCT user_id) FROM caches WHERE status=1', 0);
$cachescount = XDb::xSimpleQueryValue('SELECT COUNT(*) FROM `caches` WHERE `status`=1', 0);

echo '<table width="97%"><tr><td align="center"><center><b> ' . tr('ranking_by_number_of_created_active_caches') . '</b><br><br /> ' . tr('users_who_created_caches_active') . ':';
echo $userscount;
echo ' .::. ' . tr('number_of_active_caches') . ': ';
echo $cachescount;
echo '</center><br /></td></tr></table><table border="1" bgcolor="white" width="97%">' . "\n";

$r = XDb::xSql(
    "SELECT COUNT(*) `count`, `user`.`username` `username`, `user`.`user_id` `user_id`
    FROM `caches`
        INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
    WHERE `caches`.`status`=1
        AND `caches`.`type`<>6
        AND user.stat_ban = 0
    GROUP BY `user`.`user_id`
    ORDER BY `count` DESC, `user`.`username` ASC");

echo '<tr class="bgcolor2"><td align="right">&nbsp;&nbsp;<b>' . tr('ranking') . '</b>&nbsp;&nbsp;</td><td align="center">&nbsp;&nbsp;<b>' . tr('number_of_caches') . '</b>&nbsp;&nbsp;</td><td align="center">&nbsp;&nbsp;<b>' . tr('username') . '</b>&nbsp;&nbsp;</td></tr>';
echo '<tr><td height="2">';
$l2 = "";
$licznik = 0;
while ( $line = XDb::xFetchArray($r) ) {
    $l1 = $line['count'];
    $licznik++;
    if ($l2 != $l1) {
        echo '</td></tr>';
        echo '<tr class="bgcolor2"><td align="right">&nbsp;&nbsp;<b>' . $licznik . '</b>&nbsp;&nbsp;</td><td align="right">&nbsp;&nbsp;<b>' . $l1 . '</b>&nbsp;&nbsp;</td><td><a href="viewprofile.php?userid=' . $line['user_id'] . '">' . htmlspecialchars($line['username']) . '</a>';
        $l2 = $l1;
    } else {
        echo ', <a href="viewprofile.php?userid=' . $line['user_id'] . '">' . htmlspecialchars($line['username']) . '</a>';
    }
}
echo '</table>' . "\n";

