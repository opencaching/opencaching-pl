<?php
	global $lang, $rootpath;

	if (!isset($rootpath)) $rootpath = './';

	//include template handling
	require_once($rootpath . 'lib/common.inc.php');

  setlocale(LC_TIME, 'pl_PL.UTF-8');
	echo '<table width="97%"><tr><td align="center"><center><b>Ranking użytkowników wg liczby odkryć skrzynek danego użytkownika</b></center></td></tr> </table>';
        echo '<table border="1" bgcolor="white" width="97%" style="font-size:11px; line-height:1.6em;">' . "\n";

$t1="CREATE TEMPORARY TABLE ocpl.tmp (id INT(11) unsigned NOT NULL auto_increment PRIMARY KEY, count INT(11), username VARCHAR(60)) ENGINE=MEMORY SELECT COUNT(*) `count`, `user`.`username` FROM `cache_logs` INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`type`=1 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC"; 
//mysql_query("SET NAMES 'utf8'"); 
$r=mysql_query($t1) or die(mysql_error());
//mysql_query("SET NAMES 'utf8'"); 
$a="SELECT count, username FROM tmp GROUP BY `username` ORDER BY `count` DESC, `username`";

$r=mysql_query($a) or die(mysql_error());
echo '<tr><td class="bgcolor2" align="right">&nbsp;&nbsp;<b>Ranking</b>&nbsp;&nbsp;</td><td class="bgcolor2" align="center">&nbsp;&nbsp;<b>Liczba odkryć</b>&nbsp;&nbsp;</td><td class="bgcolor2" align="center">&nbsp;&nbsp;<b>Username</b>&nbsp;&nbsp;</td></tr><tr><td>';
$l2="";
$licznik=0;
while ($line=mysql_fetch_array($r))
{
$l1=$line[count];
if ($l2!=$l1)
{
$licznik=$licznik+1;
    echo "</td></tr><tr><td class=\"bgcolor2\" align=\"right\">&nbsp;&nbsp;<b>$licznik</b>&nbsp;&nbsp;</td><td class=\"bgcolor2\" align=\"right\">&nbsp;&nbsp;<b>$l1</b>&nbsp;&nbsp;</td>";
    echo  "<td class=\"bgcolor2\">$line[username]";
    $l2=$l1;
}
else {
    echo ", $line[username]";
    }

}


	echo '</td></tr></table>' . "\n";

?>
