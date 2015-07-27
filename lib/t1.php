<?php
	global $lang, $rootpath;

	if (!isset($rootpath)) $rootpath = './';

	//include template handling
	require_once($rootpath . 'lib/common.inc.php');

  setlocale(LC_TIME, 'pl_PL.UTF-8');
  $rsU = sql('SELECT COUNT(*) `count` FROM (SELECT COUNT(caches.user_id) FROM `caches` WHERE `status`=1 GROUP BY `user_id`) `users_with_founds`');
  $fC = sql('SELECT COUNT(*) `count` FROM `caches` WHERE `status`=1');
    $rsUs = mysql_fetch_array($rsU);
    $fCt = mysql_fetch_array($fC);
	echo '<center><table><tr><td align=center><font size=+0><b>Ranking użytkowników wg liczby założonych skrzynek</b></font><br>tylko aktywne skrzynki<br>Użytkowników którzy założyli skrzynki:';
	echo $rsUs[count]; 
	echo ' .::. Liczba aktywnych skrzynek:';
	echo $fCt[count]; 
	echo '</td></tr></table><table bgcolor="white" width=800>' . "\n";

//  mysql_query("SET NAMES 'utf8'"); 
// $rsUser = sql('SELECT COUNT(*) `count`, `user`.`username` FROM `caches` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `caches`.`status`=1 AND `caches`.`type`!=4 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC LIMIT 20'); 
// $rsUser = sql('SELECT COUNT(*) `count`, `user`.`username` FROM `caches` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `caches`.`status`=1 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC LIMIT 20');


  
//mysql_query("SET NAMES 'utf8'"); 
$t1="CREATE TEMPORARY TABLE ocpl.tmp (id INT(11) unsigned NOT NULL auto_increment PRIMARY KEY, count INT(11), username VARCHAR(60), user_id INT(11)) ENGINE=MEMORY SELECT COUNT(*) `count`, `user`.`username`, `user`.`user_id` FROM `caches` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE (`caches`.`status`=1 OR `caches`.`status`=2) AND user.stat_ban = 0 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC"; 
//mysql_query("SET NAMES 'utf8'"); 
$r=mysql_query($t1) or die(mysql_error());
//mysql_query("SET NAMES 'utf8'"); 
$a="SELECT count, username, user_id FROM tmp GROUP BY `username` ORDER BY `count` DESC, `username`";
echo "<br>";

$r=mysql_query($a) or die(mysql_error());
echo "
<tr bgcolor=#D5D5D5>
	<td align=right>
		<font size=2>&nbsp;&nbsp;<b>Ranking</b>&nbsp;&nbsp;
	</td>
	<td align=center>
		&nbsp;&nbsp;<font size=2><b>Liczba skrzynek</b></font>&nbsp;&nbsp;
	</td>
	<td align=center>
		&nbsp;&nbsp;<font size=2><b>Username</b></font>&nbsp;&nbsp;
	</td>
</tr>
<tr><td height='2'><font size='1'>
";
$l2="";
$licznik=0;
while ($line=mysql_fetch_array($r))
{
$l1=$line[count];
$licznik++;

if ($l2!=$l1)
{
    echo "
			</font>
			</td>
		</tr>
		<tr bgcolor='#D5D5D5'>
			<td align='right'>
				<font size='2'>&nbsp;&nbsp;<b>$licznik</b>&nbsp;&nbsp;
			</td>
			<td align='right'>
				&nbsp;&nbsp;<font size='2'><b>$l1</b></font>&nbsp;&nbsp;
			</td>
			<td>
				<font size='2'><a href='viewprofile.php?userid=$line[user_id]'>$line[username]</a>";
    $l2=$l1;
}
else {
    echo ", <a href='viewprofile.php?userid=$line[user_id]'>$line[username]</a>";
    }
}
echo "</font>
	</td>
</tr>";


	echo '</table>' . "\n";

?>
