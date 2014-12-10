<?php

global $lang, $rootpath;

if (!isset($rootpath))
    $rootpath = './';

//include template handling
require_once($rootpath . 'lib/common.inc.php');

setlocale(LC_TIME, 'pl_PL.UTF-8');
echo '<table width="97%"><tr><td align="center"><center><b>' . tr(Stats_t5_01) . '</b><br /><br />' . tr(Stats_t5_02) . ': (z% - <b><font color="green">X</font>/<font color="blue">Y</font></b>) X- ' . tr(Stats_t5_03) . ', Y - ' . tr(Stats_t5_04) . ', <br /> z - ' . tr(Stats_t5_05) . ' % ' . tr(Stats_t5_06) . ' (X/Y)*100<br /><br /></center></td></tr> </table>';
echo '<table border="1" bgcolor="white" width="97%" style="font-size:11px; line-height:1.6em;">' . "\n";

$linie = sql("SELECT count(*) count, user.username username, user.user_id user_id FROM caches,cache_rating,user WHERE `cache_rating`.`cache_id`=caches.cache_id AND caches.user_id=user.user_id AND caches.type <> 6 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC");
echo '<tr><td class="bgcolor2" align="right"><b>' . tr(Stats_t5_07) . '</b>&nbsp;&nbsp;</td><td class="bgcolor2" align="center"><img src="images/rating-star.png" border="0" alt="Recommendations" />&nbsp;<b>' . tr(Stats_t5_08) . '</b>&nbsp;&nbsp;</td><td class="bgcolor2" align="center"><b>' . tr(Stats_t5_09) . '</b>&nbsp;&nbsp;</td></tr><tr><td>';
$l2 = "";
$licznik = 0;
while ($linia = sql_fetch_assoc($linie)) {
    $l1 = $linia[count];
    $x = sqlValue("SELECT COUNT(*) FROM caches WHERE `caches`.`topratings` >= 1 AND caches.type <> 6 AND caches.user_id='$linia[user_id]'", 0);
    $y = sqlValue("SELECT COUNT(*) FROM caches WHERE user_id='$linia[user_id]' AND status <> 4 AND status <> 5 AND status <> 6 AND type <> 6", 0);
    $xy = sprintf("%.u", ($x / $y) * 100);
    if ($l2 != $l1) {
        $licznik++;
        echo "</td></tr><tr><td class=\"bgcolor2\" align=\"right\">&nbsp;&nbsp;<b>$licznik</b>&nbsp;&nbsp;</td><td class=\"bgcolor2\" align=\"right\">&nbsp;&nbsp;<b>$l1</b>&nbsp;&nbsp;</td>";
        echo "<td class=\"bgcolor2\"><a class=\"links\" href=\"viewprofile.php?userid=$linia[user_id]\">" . htmlspecialchars($linia[username]) . " (<font color=\"firebrick\">$xy% - </font><font color=\"green\">$x</font>/<font color=\"blue\">$y</font>)</a>";
        $l2 = $l1;
    } else {
        echo ", <a class=\"links\" href=\"viewprofile.php?userid=$linia[user_id]\">" . htmlspecialchars($linia[username]) . " (<font color=\"firebrick\">$xy% - </font><font color=\"green\">$x</font>/<font color=\"blue\">$y</font>)</a>";
    }
}
echo '</td></tr></table>' . "\n";
?>


