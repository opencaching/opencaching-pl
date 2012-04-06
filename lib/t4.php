<?php
	global $lang, $rootpath;

	if (!isset($rootpath)) $rootpath = './';

	//include template handling
	require_once($rootpath . 'lib/common.inc.php');

  setlocale(LC_TIME, 'pl_PL.UTF-8');

	echo '<table width="97%"><tr><td align="center"><center><b>Ranking skrzynek wg liczby odkryć</b></center></td></tr> </table>';

        echo '<table border="1" bgcolor="white" width="97%" style="font-size:11px; line-height:1.6em;">' . "\n";


$t1="CREATE TEMPORARY TABLE ocpl.tmp (id INT(11) unsigned NOT NULL auto_increment PRIMARY KEY, count INT(11),name VARCHAR(60), cache_id INT(11), username VARCHAR(60)) ENGINE=MEMORY DEFAULT CHARACTER SET=utf8 COLLATE=utf8_polish_ci SELECT COUNT(*) `count`, `caches`.`name`, `cache_logs`.`cache_id`, `user`.`username` FROM `cache_logs` INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`type`=1 AND `caches`.`type`<>4  AND `caches`.`type`<>5   AND `caches`.`type`<>6 GROUP BY `caches`.`cache_id` ORDER BY `count` DESC, `caches`.`name` ASC"; 
//mysql_query("SET NAMES 'utf8'"); 
$r=mysql_query($t1) or die(mysql_error());
//mysql_query("SET NAMES 'utf8'"); 
$a="SELECT count,name, cache_id, username FROM tmp GROUP BY `name` ORDER BY `count` DESC, `name`";

$r=mysql_query($a) or die(mysql_error());
echo '<tr><td class="bgcolor2" align="right">&nbsp;&nbsp;<b>Ranking</b>&nbsp;&nbsp;</td><td class="bgcolor2" align="center">&nbsp;&nbsp;<b>Liczba odkryć</b>&nbsp;&nbsp;</td><td class="bgcolor2" align="center">&nbsp;&nbsp;<b>Nazwa skrzynki (Username)</b>&nbsp;&nbsp;</td></tr><tr><td>';
$l2="";
$licznik=0;
while ($line=mysql_fetch_array($r))
{
$l1=$line[count];
if ($l2!=$l1)
{
$licznik=$licznik+1;
    echo "</td></tr><tr><td class=\"bgcolor2\" align=\"right\">&nbsp;&nbsp;<b>$licznik</b>&nbsp;&nbsp;</td><td class=\"bgcolor2\" align=\"right\">&nbsp;&nbsp;<b>$l1</b>&nbsp;&nbsp;</td>";
    echo  "<td class=\"bgcolor2\"><a href=viewcache.php?cacheid=$line[cache_id]>$line[name]</a> ($line[username])";
    $l2=$l1;
}
else {
    echo ", <a href=viewcache.php?cacheid=$line[cache_id]>$line[name]</a> ($line[username])";
    }

}


	echo '</td></tr></table>' . "\n";

?>
