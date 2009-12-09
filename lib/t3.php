<?php
	global $lang, $rootpath;

	if (!isset($rootpath)) $rootpath = './';

	//include template handling
	require_once($rootpath . 'lib/common.inc.php');

  setlocale(LC_TIME, 'pl_PL.UTF-8');
	echo '<center><table width="600"><tr><td align=center><font size=+0><b>Ranking użytkowników wg liczby odkryć skrzynek danego użytkownika</b></font></td></tr>';
        echo '</td></tr> <table bgcolor="white" width=600>' . "\n";

$t1="CREATE TEMPORARY TABLE ocpl.tmp (id INT(11) unsigned NOT NULL auto_increment PRIMARY KEY, count INT(11), username VARCHAR(60)) ENGINE=MEMORY SELECT COUNT(*) `count`, `user`.`username` FROM `cache_logs` INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`type`=1 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC"; 
//mysql_query("SET NAMES 'utf8'"); 
$r=mysql_query($t1) or die(mysql_error());
//mysql_query("SET NAMES 'utf8'"); 
$a="SELECT count, username FROM tmp GROUP BY `username` ORDER BY `count` DESC, `username`";
echo "<br>";

$r=mysql_query($a) or die(mysql_error());
echo "<tr bgcolor=#D5D5D5><td align=right><font size=2>&nbsp;&nbsp;<b>Ranking</b>&nbsp;&nbsp;</td><td align=center>&nbsp;&nbsp;<font size=2><b>Liczba odkryć</b></font>&nbsp;&nbsp;</td><td align=center>&nbsp;&nbsp;<font size=2><b>Username</b></font>&nbsp;&nbsp;</td>";
$l2="";
$licznik=0;
while ($line=mysql_fetch_array($r))
{
$l1=$line[count];
if ($l2!=$l1)
{
$licznik=$licznik+1;
    echo "</font></td></tr><tr bgcolor=#D5D5D5><td align=right><font size=2>&nbsp;&nbsp;<b>$licznik</b>&nbsp;&nbsp;</td><td align=right>&nbsp;&nbsp;<font size=2><b>$l1</b></font>&nbsp;&nbsp;</td>";
    echo  "<td><font size=2>$line[username]";
    $l2=$l1;
}
else {
    echo ", $line[username]";
    }

}


	echo '</table></table>' . "\n";

?>
