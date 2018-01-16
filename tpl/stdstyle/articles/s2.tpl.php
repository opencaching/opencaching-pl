<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="">
    {{statistics}}
  </div>


<table width="760" class="table" style="line-height: 1.6em; font-size: 10px;">
    <tr>
        <td>
<?php

use Utils\Database\XDb;
global $lang, $rootpath;

if (!isset($rootpath))
    $rootpath = './';

    //include template handling
    require_once($rootpath . 'lib/common.inc.php');
    setlocale(LC_TIME, 'pl_PL.UTF-8');

    $userscount = XDb::xSimpleQueryValue(
        'SELECT COUNT( DISTINCT user_id) FROM cache_logs WHERE type=1 AND `deleted`=0', 0);

    $cachelogscount = XDb::xSimpleQueryValue(
        'SELECT COUNT(*) FROM `cache_logs` WHERE type=1 AND `deleted`=0', 0);

    echo '<center><table width="97%" border="0"><tr><td align="center"><center><b>' . tr('ranking_by_number_of_finds') . '</b><br />' . tr('total_amount_loggers');
    echo $userscount;
    echo ' .::. ' . tr('total_amount_logs');
    echo $cachelogscount;
    echo '</center></td></tr>';
    echo '<tr><td class="bgcolor2"><b>' . tr('filter_out_caches') . '</b><br /><form action="articles.php" method="GET">';

    $res_q = XDb::xSql('SELECT id, pl FROM cache_type WHERE id != 6');

    $no_types = 0;
    $typ = "";
    while ( $res = XDb::xFetchArray($res_q) ) {
        $no_types++;
        if (isset($_GET[$res['id']]) && $_GET[$res['id']] == 1) {
            $checked = 'checked';
            if ($typ == ''){
                $typ = ' AND caches.type NOT IN (' . $res['id'];
            } else {
                $typ .= ', ' . $res['id'];
            }
        } else
            $checked = '';

            echo '<input type="checkbox" value="1" name="' . intval($res['id']) . '" id="' . intval($res['id']) . '" ' . $checked . ' /><label for="' . intval($res['id']) . '">' . strip_tags($res['pl']) . '</label>';
            if ($no_types % 5 != 0)
                echo ' | ';
                if ($no_types == 5)
                    echo '<br />';
    }

    if ($typ != ''){
        $typ .= ')';
    }

    echo '<input type="hidden" name="page" value="s2">';
    echo '<br/><input type="submit" value=' . tr('filter') . '>';

    echo '</form></td></tr></table>';
    echo '<table border="1" bgcolor="white" width="97%" style="font-size:11px; line-height:1.6em;">' . "\n";


    if( $typ == '' ) { //without cache-type filter
        $a = "SELECT COUNT(*) count, username, stat_ban, user.user_id
          FROM cache_logs, user
          WHERE `cache_logs`.`deleted`=0
            AND cache_logs.user_id=user.user_id
            AND cache_logs.type=1
          GROUP BY user.user_id
          ORDER BY 1 DESC, user.username ASC";

    } else { //with cache-type filter : very expensive!!!
        $a = "SELECT COUNT(*) count, username, stat_ban, user.user_id
        FROM caches, cache_logs, user
        WHERE `cache_logs`.`deleted`=0
        AND cache_logs.user_id=user.user_id
        AND cache_logs.type=1
        AND cache_logs.cache_id = caches.cache_id
        $typ
        GROUP BY user.user_id
        ORDER BY 1 DESC, user.username ASC";
    }

    $cache_key = 'articles_s2'.md5($a);
    $lines = apc_fetch($cache_key);

    if ($lines === false) {
        $r = XDb::xSql( $a );

        $lines = [];
        while ( $line = XDb::xFetchArray($r) ){
            $lines[] = $line;
        }

        unset($r);
        apc_store($cache_key, $lines, 3600);
    }

    echo "<br />";


    echo '<tr class="bgcolor2">' .
        '<td align="center">&nbsp;&nbsp;<b>' . tr('ranking') . '</b>&nbsp;&nbsp;</td>' .
        '<td align="center"><b>' . tr('shared_place') . '</b></td>' .
        '<td align="center"><b>' . tr('number_found_caches') . '</b></td>' .
        '<td align="center">&nbsp;&nbsp;<b>' . tr('username') . '</b>&nbsp;&nbsp;</td></tr><tr><td>';

    $l2 = ""; // number of users within the same rank
    $rank = 0; // rank number; increamented by one for each group of users having the same caches discovered
    $position = 1; // position ex aequo; incremented by number of users in each rank

    foreach ($lines as $line) {
        $color = "black";
        $banned = "";
        if ($usr['admin'] || $line['stat_ban'] == 0) {
            if ($line['stat_ban']) {
                $color = "gray";
                $banned = " (BAN)";
            }
            $l1 = $line["count"];
            if ($l2 != $l1) {
                // new rank (finish recent row and start new one)
                echo '</td></tr>';
                $rank++;
                echo '<tr class="bgcolor2">' .
                    '<td align="right">&nbsp;&nbsp;<b>' . $rank . '</b>&nbsp;&nbsp;</td>' .
                    '<td align="right">&nbsp;&nbsp;' . $position . '&nbsp;&nbsp;</td>' .
                    '<td align="right">&nbsp;&nbsp;<b>' . $l1 . '</b>&nbsp;&nbsp;</td>' .
                    '<td><a style="color:' . $color . '" href="viewprofile.php?userid=' . $line["user_id"] . '">' . htmlspecialchars($line["username"]) . $banned . '</a>';
                $l2 = $l1;
            } else {
                // the same rank (continue row)
                echo ', <a style="color:' . $color . '" href="viewprofile.php?userid=' . $line["user_id"] . '">' . htmlspecialchars($line["username"]) . $banned . '</a>';
            }
            $position++;
        }
    }
?>
</table>
        </td></tr>
</table>

</div>