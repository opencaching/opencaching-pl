<?php

global $lang, $rootpath;

if (!isset($rootpath))
    $rootpath = './';

//include template handling
require_once($rootpath . 'lib/common.inc.php');

setlocale(LC_TIME, 'pl_PL.UTF-8');
$rsU = sql('SELECT COUNT(*) `count` FROM (SELECT COUNT(caches.user_id) FROM `caches` WHERE `status`=1 GROUP BY `user_id`) `users_with_founds`');
$fC = sql('SELECT COUNT(*) `count` FROM `caches` WHERE `status`=1');
$rsUs = mysql_fetch_array($rsU);
$fCt = mysql_fetch_array($fC);
echo '<center><table><tr><td align=center><font size=+0><b>Ranking użytkowników wg liczby założonych skrzynek</b></font><br />tylko aktywne skrzynki<br />Użytkowników którzy założyli skrzynki:';
echo $rsUs[count];
echo ' .::. Liczba aktywnych skrzynek:';
echo $fCt[count];
echo '</td></tr><tr><td><center><table bgcolor=white>';

mysql_query("SET NAMES 'utf8'");
// $rsUser = sql('SELECT COUNT(*) `count`, `user`.`username` FROM `caches` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `caches`.`status`=1 AND `caches`.`type`!=4 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC LIMIT 20');
$rsUser = sql('SELECT COUNT(*) `count`, `user`.`username` FROM `caches` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `caches`.`status`=1 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC LIMIT 20');
$rmax = sql('SELECT COUNT(*) `count`, `user`.`username` FROM `caches` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `caches`.`status`=1 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC LIMIT 1');
$rsmax = mysql_fetch_array($rmax);
while ($rs = mysql_fetch_array($rsUser)) {
    $widthB = round(200 * ($rs[count] / $rsmax[count]) / 1, 0);

    $line = '<tr><td align="left"><b>{name}</b></td> <td>&nbsp;&nbsp;(<b>{count1}</b>)&nbsp;&nbsp;</td><td><img src=/graphs/images/leftbar.gif /><img src=/graphs/images/mainbar.gif height=14 width={widthB} /><img src=/graphs/images/rightbar.gif /> </td></tr>';
    $line = str_replace('{widthB}', $widthB, $line);
    $line = str_replace('{name}', $rs[username], $line);
    $line = str_replace('{count1}', $rs[count], $line);
    echo $line;
}

echo '</table></td></tr></table></center><br />';


$rsU = sql('SELECT COUNT(*) `count` FROM (SELECT COUNT(cache_logs.user_id) FROM `cache_logs` WHERE `type`=1 AND `deleted`=0 GROUP BY `user_id`) `users_with_founds`');
$fC = sql('SELECT COUNT(*) `count` FROM `cache_logs` WHERE `type`=1 AND `deleted`=0');
$rsUs = mysql_fetch_array($rsU);
$fCt = mysql_fetch_array($fC);

echo '<center><table><tr><td align=center><font size=+0><b>Ranking użytkowników wg liczby odkryć</b></font><br />Użytkowników którzy znalezli:';
echo $rsUs[count];
echo ' .::. Ile razy odkryto skrzynki:';
echo $fCt[count];
echo '</td></tr><tr><td><center><table bgcolor=white>';


mysql_query("SET NAMES 'utf8'");
$rsUser = sql('SELECT COUNT(*) `count`, `user`.`username` FROM `cache_logs` INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` INNER JOIN `user` ON `cache_logs`.`user_id`=`user`.`user_id` WHERE `cache_logs`.`type`=1 AND `cache_logs`.`deleted`=0 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC LIMIT 20');
$rmax = sql('SELECT COUNT(*) `count`, `user`.`username` FROM `cache_logs` INNER JOIN `user` ON `cache_logs`.`user_id`=`user`.`user_id` WHERE `cache_logs`.`type`=1 AND `cache_logs`.`deleted`=0 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC LIMIT 1');
$rsmax = mysql_fetch_array($rmax);
while ($rs = mysql_fetch_array($rsUser)) {
    $widthB = round(200 * ($rs[count] / $rsmax[count]) / 1, 0);

    $line = '<tr><td align="left"><b>{name}</b></td> <td>&nbsp;&nbsp;(<b>{count1}</b>)&nbsp;&nbsp;</td><td><img src=/graphs/images/leftbar.gif /><img src=/graphs/images/mainbar.gif height="14" width="{widthB}" /><img src=/graphs/images/rightbar.gif /> </td></tr>';
    $line = str_replace('{widthB}', $widthB, $line);
    $line = str_replace('{name}', $rs[username], $line);
    $line = str_replace('{count1}', $rs[count], $line);
    echo $line;
}

echo '</table></td></tr></table></center><br />';

setlocale(LC_TIME, 'pl_PL.UTF-8');


echo '<center><table><tr><td align=center><font size=+0><b>Ranking użytkowników wg liczby odkryć skrzynek danego użytkownika</b></font></td></tr>';
echo '<tr><td><center><table bgcolor=white>';

mysql_query("SET NAMES 'utf8'");

$r = sql('SELECT COUNT(*) `count`, `user`.`username` FROM `cache_logs` INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `cache_logs`.`type`=1 AND `cache_logs`.`deleted`=0 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC LIMIT 20');
$rmax = sql('SELECT COUNT(*) `count`, `user`.`username` FROM `cache_logs` INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `cache_logs`.`type`=1 AND `cache_logs`.`deleted`=0 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC LIMIT 1');
$rsmax = mysql_fetch_array($rmax);

while ($rs = mysql_fetch_array($r)) {
    $widthB = round(200 * ($rs[count] / $rsmax[count]) / 1, 0);

    $line = '<tr><td align="left"><b>{name}</b></td> <td>&nbsp;&nbsp;(<b>{count1}</b>)&nbsp;&nbsp;</td><td><img src=/graphs/images/leftbar.gif /><img src=/graphs/images/mainbar.gif height="14" width="{widthB}" /><img src=/graphs/images/rightbar.gif /> </td></tr>';
    $line = str_replace('{widthB}', $widthB, $line);
    $line = str_replace('{name}', $rs[username], $line);
    $line = str_replace('{count1}', $rs[count], $line);
    echo $line;
}


echo '</table></td></tr></table></center><br />';

echo '<center><table><tr><td align=center><font size=+0><b>Ranking skrzynek wg liczby odkryć</b></font></td></tr>';
echo '<tr><td><center><table bgcolor=white><tr><td><font color=blue><b>Nazwa skrzynki</b></font></td><td></td><td><font color=blue><b>Liczba odkryć</b></font></td></tr>';

mysql_query("SET NAMES 'utf8'");
$r = sql('SELECT COUNT(*) `count`, `caches`.`name`, `cache_logs`.`cache_id`, `user`.`username` FROM `cache_logs` INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `cache_logs`.`type`=1 AND `cache_logs`.`deleted`=0 GROUP BY `caches`.`cache_id` ORDER BY `count` DESC, `caches`.`name` ASC LIMIT 20');
$rmax = sql('SELECT COUNT(*) `count`, `caches`.`name`, `cache_logs`.`cache_id` FROM `cache_logs` INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` WHERE `cache_logs`.`type`=1 AND `cache_logs`.`deleted`=0 GROUP BY `caches`.`cache_id` ORDER BY `count` DESC LIMIT 1');
$rsmax = mysql_fetch_array($rmax);

while ($rs = mysql_fetch_array($r)) {
    $widthB = round(100 * ($rs[count] / $rsmax[count]) / 1, 0);

    $line = '<tr><td><a href=http://www.opencaching.pl/viewcache.php?cacheid={cacheid} target=_blank>{name}</a> (<b>{username}</b>)</td><td align=right>&nbsp;(<b>{count1}</b>)&nbsp;</td><td><img src=/graphs/images/leftbar.gif /><img src=/graphs/images/mainbar.gif height=14 width={widthB} /><img src=/graphs/images/rightbar.gif /> </td> </tr>';
    $line = str_replace('{count1}', $rs[count], $line);
    $line = str_replace('{username}', $rs[username], $line);
    $line = str_replace('{widthB}', $widthB, $line);
    $line = str_replace('{cacheid}', $rs[cache_id], $line);
    $line = str_replace('{name}', $rs[name], $line);
    echo $line;
}


echo '</table></td></tr></table></center><br />';
?>
