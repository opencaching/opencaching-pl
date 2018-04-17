<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="">
    {{statistics}}
  </div>

<table class="table" width="760" style="line-height: 1.6em; font-size: 10px;">
    <tr>
        <td>
        <?php
use Utils\Database\XDb;
global $lang, $rootpath;

if (!isset($rootpath))
    $rootpath = './';

//include template handling
require_once($rootpath . 'lib/common.inc.php');

# This page took >60 seconds to render! Added daily caching.

$cache_key = "articles_s8-" . $lang;
$result = apc_fetch($cache_key);

if ($result === false) {
    ob_start();

    $fCt["count"] = XDb::xSimpleQueryValue(
        'SELECT COUNT(*) `count` FROM `cache_logs` WHERE (`type`=1 OR `type`=2 OR `type`=7) AND `deleted`=0', 0);


    $r['users'] = XDb::xSimpleQueryValue(
        'SELECT COUNT(*) AS `users`
        FROM (
                SELECT DISTINCT `user_id` FROM `cache_logs`
                WHERE (`type`=1 OR `type`=2) AND `deleted`=0
            UNION DISTINCT
                SELECT DISTINCT `user_id` FROM `caches`
        ) AS `t`', 0);

    $rsfCR = XDb::xSql(
        "SELECT COUNT(*) `count`, `cache_location`.`adm3` region, `cache_location`.`code3` code_region
        FROM `cache_location`
            INNER JOIN cache_logs ON cache_location.cache_id=cache_logs.cache_id
        WHERE `cache_location`.`code1`='PL'
            AND (cache_logs.type='1' OR cache_logs.type='2')
            AND cache_logs.deleted='0'
        GROUP BY `cache_location`.`code3`
        ORDER BY count DESC");

    echo '<table width="97%"><tr><td align="center"><center><b> ' . tr('Stats_t7_01') . '</b> <br /><br /> ' . tr('Stats_t7_02') . ':<b> ';
    echo $fCt["count"];
    echo ' </b><br />' . tr('Stats_t7_03') . ':<b> ';
    echo $r['users'];
    echo '</b><br /><br />(' . tr('Stats_t7_04') . ')</center></td></tr></table><br><table border="1" bgcolor="white" width="97%">' . "\n";

    echo '
    <tr class="bgcolor2">
        <td width="20%">
            <center><b>' . tr('Stats_t7_05') . '</b></center>
        </td>
        <td align="right">
            &nbsp;&nbsp;<b>' . tr('Stats_t7_06') . '</b>&nbsp;&nbsp;
        </td>
    </tr><tr><td height="2"></td></tr>';

    while ( $line = XDb::xFetchArray($rsfCR) ) {
        echo '<tr class="bgcolor2">
                <td align="right">
                    &nbsp;&nbsp;<b>' . $line["count"] . '</b>&nbsp;&nbsp;
                </td>
                <td align="right">
                    &nbsp;&nbsp;<b><a class=links href=articles.php?page=s10&region=' . $line['code_region'] . '>' . $line['region'] . '</a></b>&nbsp;&nbsp;
                </td>';
    }

    echo '</table>' . "\n";

    XDb::xFreeResults($rsfCR);

    $result = ob_get_clean();
    apc_store($cache_key, $result, 86400);
}
print $result;
        ?>
        </td></tr>
</table>

</div>