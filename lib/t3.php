<?php

use Utils\Database\XDb;
global $lang, $rootpath;

if (!isset($rootpath))
    $rootpath = './';

//include template handling
require_once($rootpath . 'lib/common.inc.php');

setlocale(LC_TIME, 'pl_PL.UTF-8');
echo '<table width="97%"><tr><td align="center"><center><b>' . tr('Stats_t3_01') . '</b></center></td></tr> </table>';
echo '<table border="1" bgcolor="white" width="97%" style="font-size:11px; line-height:1.6em;">' . "\n";

$linie = XDb::xSql(
    "SELECT COUNT(*) `count`, `user`.`username`
    FROM `cache_logs`
        INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id`
        INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
    WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`type`=1
    GROUP BY `user`.`user_id`
    ORDER BY `count` DESC, `user`.`username` ASC");

echo '<tr><td class="bgcolor2" align="right"><b>' . tr('Stats_t3_02') . '</b>&nbsp;&nbsp;</td><td class="bgcolor2" align="center"><b>' . tr('Stats_t3_03') . '</b>&nbsp;&nbsp;</td><td class="bgcolor2" align="center"><b>' . tr('Stats_t3_04') . '</b>&nbsp;&nbsp;</td></tr><tr><td>';
$l2 = "";
$licznik = 0;
while ($linia = XDb::xFetchArray($linie)) {
    $l1 = $linia['count'];
    if ($l2 != $l1) {
        $licznik++;
        echo "</td></tr><tr><td class=\"bgcolor2\" align=\"right\">&nbsp;&nbsp;<b>$licznik</b>&nbsp;&nbsp;</td><td class=\"bgcolor2\" align=\"right\">&nbsp;&nbsp;<b>$l1</b>&nbsp;&nbsp;</td>";
        echo "<td class=\"bgcolor2\">$linia['username']";
        $l2 = $l1;
    } else {
        echo ", $linia['username']";
    }
}
echo '</td></tr></table>' . "\n";

