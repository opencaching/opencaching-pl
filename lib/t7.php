<?php
    global $lang, $rootpath;

    if (!isset($rootpath)) $rootpath = './';

    //include template handling
    require_once($rootpath . 'lib/common.inc.php');

  setlocale(LC_TIME, 'pl_PL.UTF-8');

# This page took >60 seconds to render! Added daily caching.

$cache_key = "t7.php-result".$lang;
$result = apc_fetch($cache_key);
if ($result === false)
{
    ob_start();
    //  $rsU = sql('SELECT COUNT(*) `count` FROM (SELECT COUNT(cache_logs.user_id) FROM `cache_logs` WHERE (`type`=1 OR `type`=2 OR `type`=7) AND `deleted`=0 GROUP BY `user_id`) `users_with_founds`');
    $fC = sql('SELECT COUNT(*) `count` FROM `cache_logs` WHERE (`type`=1 OR `type`=2 OR `type`=7) AND `deleted`=0');
    //   $rsUs = mysql_fetch_array($rsU);
    $fCt = mysql_fetch_array($fC);
    $rs = sql('SELECT COUNT(*) AS `users` FROM (SELECT DISTINCT `user_id` FROM `cache_logs` WHERE (`type`=1 OR `type`=2) AND `deleted`=0 UNION DISTINCT SELECT DISTINCT `user_id` FROM `caches`) AS `t`');
    $r = sql_fetch_array($rs);

    $rsfCR = sql("SELECT COUNT(*) `count`, `cache_location`.`adm3` region, `cache_location`.`code3` code_region FROM `cache_location` INNER JOIN cache_logs ON cache_location.cache_id=cache_logs.cache_id WHERE `cache_location`.`code1`='PL' AND (cache_logs.type='1' OR cache_logs.type='2') AND cache_logs.deleted='0' GROUP BY `cache_location`.`code3` ORDER BY count DESC");
    echo '<table width="97%"><tr><td align="center"><center><b> '.tr('activity_by_region').'</b> <br /><br /> '.tr('number_of_visit_caches').' (znalezione + nieznalezione): <b>';
    echo $fCt["count"];
    echo ' </b><br />'.tr('users_active').':<b> ';
    echo  $r['users'];
    echo '</b><br /><br />(Kliknij na nazwe województwa aby zobaczyć statytykę użytkowników w danym województwie)</center></td></tr></table><br><table border="1" bgcolor="white" width="97%">' . "\n";
    mysql_free_result($rs);
    echo '
    <tr class="bgcolor2">
        <td width="20%">
            <center><b>'.tr('number_of_visit_caches').'</b></center>
        </td>
        <td align="right">
            &nbsp;&nbsp;<b>'.tr('region').'</b>&nbsp;&nbsp;
        </td>
    </tr><tr><td height="2"></td></tr>';

    while ($line=mysql_fetch_array($rsfCR))
    {
        echo '<tr class="bgcolor2">
                <td align="right">
                    &nbsp;&nbsp;<b>'.$line["count"].'</b>&nbsp;&nbsp;
                </td>
                <td align="right">
                    &nbsp;&nbsp;<b><a class=links href=articles.php?page=s10&region='.$line['code_region'].'>'.$line['region'].'</a></b>&nbsp;&nbsp;
                </td>';
    }

    echo '</table>' . "\n";

    mysql_free_result($rsfCR);

    $result = ob_get_clean();
    apc_store($cache_key, $result, 86400);
}
print $result;

?>
