<?php
use Utils\Database\XDb;
?>
<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="">
    {{statistics}}
  </div>

<table class="table" width="760" style="line-height: 1.6em; font-size: 10px;">
    <tr>
        <td>
            <?php
            if (isset($_REQUEST['region'])) {
                $region = $_REQUEST['region'];
            }
            $woj = XDb::xMultiVariableQueryValue(
                "SELECT nuts_codes.name FROM nuts_codes WHERE code= :1 ", 0, $region);

            echo '<center><table width="97%" border="0"><tr><td align="center"><center><b>' . tr('Stats_s10_01') . '  <b>';
            echo '<br /><br /><b><font color="blue">';
            echo $woj;
            echo '</font></b></center></td></tr></table>';

            echo '<table border="1" bgcolor="white" width="97%" style="font-size:11px; line-height:1.6em;">' . "\n";

            echo "<br />";

            $r = XDb::xSql(
                "SELECT COUNT(*) count, username, stat_ban, user.user_id FROM cache_location, caches, cache_logs, user
                WHERE (`cache_location`.`code3`= ? AND `cache_location`.`cache_id`=`caches`.`cache_id`)
                    AND `cache_logs`.`deleted`=0 AND cache_logs.user_id=user.user_id
                    AND (cache_logs.type=1 OR cache_logs.type=2)
                    AND cache_logs.cache_id = caches.cache_id
                GROUP BY user.user_id
                ORDER BY 1 DESC, user.username ASC", $region);

            echo '<tr class="bgcolor2">' .
            '<td align="center">&nbsp;&nbsp;<b>' . tr('Stats_s10_02') . '</b>&nbsp;&nbsp;</td>' .
            '<td align="center"><b>' . tr('Stats_s10_03') . '</b></td>' .
            '<td align="center">&nbsp;&nbsp;<b>' . tr('Stats_s10_04') . '</b>&nbsp;&nbsp;</td></tr><tr><td>';

            $l2 = ""; // number of users within the same rank
            $rank = 0; // rank number; increamented by one for each group of users having the same caches discovered
            $position = 1; // position ex aequo; incremented by number of users in each rank

            while( $line = XDb::xFetchArray($r) ) {
                $color = "black";
                $banned = "";
                if ($usr['admin'] || $line['stat_ban'] == 0) {
                    if ($line['stat_ban']) {
                        $color = "gray";
                        $banned = " (BAN)";
                    }
                    $l1 = $line['count'];
                    if ($l2 != $l1) {
                        // new rank (finish recent row and start new one)
                        echo '</td></tr>';
                        $rank++;
                        echo '<tr class="bgcolor2">' .
                        '<td align="right">&nbsp;&nbsp;<b>' . $rank . '</b>&nbsp;&nbsp;</td>' .
                        '<td align="right">&nbsp;&nbsp;<b>' . $l1 . '</b>&nbsp;&nbsp;</td>' .
                        '<td><a style="color:' . $color . '" href="viewprofile.php?userid=' . $line['user_id'] . '">' . htmlspecialchars($line['username']) . $banned . '</a>';
                        $l2 = $l1;
                    } else {
                        // the same rank (continue row)
                        echo ', <a style="color:' . $color . '" href="viewprofile.php?userid=' . $line['user_id'] . '">' . htmlspecialchars($line['username']) . $banned . '</a>';
                    }
                    $position++;
                }
            }

// end table
            echo "</table>\n";
            ?>
        </td></tr>
</table>
</div>
