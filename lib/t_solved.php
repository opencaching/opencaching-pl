<?php

global $lang, $rootpath;

if (!isset($rootpath))
    $rootpath = './';

//include template handling
require_once($rootpath . 'lib/common.inc.php');

setlocale(LC_TIME, 'pl_SE.UTF-8');
$rsU = sql('SELECT COUNT(*) `count` FROM (SELECT COUNT(cache_logs.user_id) FROM `cache_logs` WHERE type=9 AND `deleted`=0 AND `hidden`=0 GROUP BY `user_id`) `users_with_founds`');
$fC = sql('SELECT COUNT(*) `count` FROM `cache_logs` WHERE type=9 AND `deleted`=0 AND `hidden`=0');
$rsUs = mysql_fetch_array($rsU);
$fCt = mysql_fetch_array($fC);

echo '<center><table width="97%" border="0"><tr><td align="center"><center><b>' . tr('ranking_by_number_of_solved') . '</b><br />' . tr('number_of_users_who_has_solved') . ':';
echo $rsUs[count];
echo ' .::. ' . tr('number_of_solved') . ':';
echo $fCt[count];
echo '</center></td></tr>';

echo '</table>';
echo '<table border="1" bgcolor="white" width="97%" style="font-size:11px; line-height:1.6em;">' . "\n";

$a = "SELECT COUNT(*) count, username, stat_ban, user.user_id FROM cache_logs, user " .
        "WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`hidden`=0 AND cache_logs.user_id=user.user_id AND cache_logs.type=9 " .
        "GROUP BY user.user_id " .
        "ORDER BY 1 DESC, user.username ASC";

echo "<br />";

$r = mysql_query($a) or die(mysql_error());
echo '<tr class="bgcolor2">' .
 '<td align="center">&nbsp;&nbsp;<b>' . tr('ranking') . '</b>&nbsp;&nbsp;</td>' .
 '<td align="center"><b>' . tr('number_of_solved') . '</b></td>' .
 '<td align="center">&nbsp;&nbsp;<b>' . tr('username') . '</b>&nbsp;&nbsp;</td></tr><tr><td>';

$l2 = ""; // number of users within the same rank
$position = 1; // position ex aequo; incremented by number of users in each rank

while ($line = mysql_fetch_array($r)) {
    $color = "black";
    $banned = "";
    if ($usr['admin'] || $line['stat_ban'] == 0) {
        if ($line['stat_ban']) {
            $color = "gray";
            $banned = " (BAN)";
        }
        $l1 = $line[count];
        if ($l2 != $l1) {
            echo '</td></tr>';
            echo '<tr class="bgcolor2">' .
            '<td align="right">&nbsp;&nbsp;<b>' . $position . '</b>&nbsp;&nbsp;</td>' .
            '<td align="right">&nbsp;&nbsp;<b>' . $l1 . '</b>&nbsp;&nbsp;</td>' .
            '<td>';
            $l2 = $l1;
        } else {
            echo ', ';
        }

        echo '<a style="color:' . $color . '" href="viewprofile.php?userid=' . $line[user_id] . '">' . htmlspecialchars($line[username]) . $banned . '</a>';
        $position++;
    }
}

// end table
//echo "</td></tr>";
echo "</table>\n";
?>
