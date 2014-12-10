<?php

global $lang, $rootpath;

if (!isset($rootpath))
    $rootpath = './';

//include template handling
require_once($rootpath . 'lib/common.inc.php');

setlocale(LC_TIME, 'pl_PL.UTF-8');

echo '<table width="97%"><tr><td align="center"><center><b>' . tr(Stats_t4_01) . '</b></center></td></tr> </table>';
echo '<table border="1" bgcolor="white" width="97%" style="font-size:11px; line-height:1.6em;">' . "\n";

$linie = sql("SELECT COUNT(*) `count`, `caches`.`name`, `cache_logs`.`cache_id`, `user`.`username` FROM `cache_logs` INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`type`=1 AND `caches`.`type`<>4  AND `caches`.`type`<>5   AND `caches`.`type`<>6 GROUP BY `caches`.`cache_id` ORDER BY `count` DESC, `caches`.`name` ASC");
echo '<tr><td class="bgcolor2" align="right"><b>' . tr(Stats_t4_02) . '</b>&nbsp;&nbsp;</td><td class="bgcolor2" align="center"><b>' . tr(Stats_t4_03) . '</b>&nbsp;&nbsp;</td><td class="bgcolor2" align="center"><b>' . tr(Stats_t4_04) . '</b>&nbsp;&nbsp;</td></tr><tr><td>';
$l2 = "";
$licznik = 0;
while ($linia = sql_fetch_assoc($linie)) {
    $l1 = $linia[count];
    if ($l2 != $l1) {
        $licznik++;
        echo "</td></tr><tr><td class=\"bgcolor2\" align=\"right\">&nbsp;&nbsp;<b>$licznik</b>&nbsp;&nbsp;</td><td class=\"bgcolor2\" align=\"right\">&nbsp;&nbsp;<b>$l1</b>&nbsp;&nbsp;</td>";
        echo "<td class=\"bgcolor2\"><a href=viewcache.php?cacheid=$linia[cache_id]>$linia[name]</a> ($linia[username])";
        $l2 = $l1;
    } else {
        echo ", <a href=viewcache.php?cacheid=$linia[cache_id]>$linia[name]</a> ($linia[username])";
    }
}
echo '</td></tr></table>' . "\n";
?>
