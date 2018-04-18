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
            global $rootpath;

            if (!isset($rootpath))
                $rootpath = './';

            //include template handling
            require_once($rootpath . 'lib/common.inc.php');

            if (isset($_REQUEST['region'])) {
                $region = $_REQUEST['region'];
            }
            $woj = XDb::xMultiVariableQueryValue(
                "SELECT nuts_codes.name FROM nuts_codes WHERE code= :1 ", 0, $region);

            echo '<center><table width="97%" border="0"><tr><td align="center"><center><b>{{Stats_s3_01}}<br/><b>';
            echo '<br /><br /><b><font color="blue">';
            echo $woj;
            echo '</font></b></center></td></tr></table>';

            echo '<table border="1" bgcolor="white" width="97%" style="font-size:11px; line-height:1.6em;">' . "\n";
            echo "<br/>";

            $r = XDb::xSql(
                "SELECT COUNT(*) `count`, `caches`.`name`, `cache_logs`.`cache_id`, `user`.`username`
                FROM `cache_logs`
                    INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id`
                    INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`, cache_location
                WHERE (`cache_location`.`code3`= ? AND `cache_location`.`cache_id`=`caches`.`cache_id`)
                    AND `cache_logs`.`deleted`=0 AND `cache_logs`.`type`=1
                    AND `caches`.`status`=1
                GROUP BY `caches`.`cache_id`
                ORDER BY `count` DESC, `caches`.`name` ASC", $region);

            echo '<tr class="bgcolor2">' .
            '<td align="center"><b>{{Stats_s3_02}}</b>&nbsp;&nbsp;</td>' .
            '<td align="center"><b>{{Stats_s3_03}}</b></td>' .
            '<td align="center"><b>{{Stats_s3_04}}</b>&nbsp;&nbsp;</td></tr><tr><td>';

            $l2 = "";
            $licznik = 0;
            while ($line = XDb::xFetchArray($r)) {
                $l1 = $line['count'];
                if ($l2 != $l1) {
                    echo '</td></tr>';
                    $licznik = $licznik + 1;
                    echo "<tr class=\"bgcolor2\"><td align=\"right\">&nbsp;&nbsp;<b>$licznik</b>&nbsp;&nbsp;</td><td align=\"right\">&nbsp;&nbsp;<b>$l1</b>&nbsp;&nbsp;</td>";
                    echo "<td><a href=viewcache.php?cacheid=".$line['cache_id'].">".$line['name']."</a> (".$line['username'].")";
                    $l2 = $l1;
                } else {
                    echo ", <a href=viewcache.php?cacheid=".$line['cache_id'].">".$line['name']."</a> (".$line['username'].")";
                }
            }

            echo '</table>' . "\n";
            ?>
        </td></tr>
</table>
</div>