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
	echo '<table width="97%"><tr><td align="center"><center><b> '.tr('ranking_by_number_of_created_caches').'</b><br><br /> '.tr('users_who_created_caches').': ';
	echo $rsUs[count]; 
	echo ' .::. '.tr('number_of_active_caches').': ';
	echo $fCt[count]; 
	echo '</center><br /></td></tr></table><table border="1" bgcolor="white" width="97%">' . "\n";

//  mysql_query("SET NAMES 'utf8'"); 
// $rsUser = sql('SELECT COUNT(*) `count`, `user`.`username` FROM `caches` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `caches`.`status`=1 AND `caches`.`type`!=4 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC LIMIT 20'); 
// $rsUser = sql('SELECT COUNT(*) `count`, `user`.`username` FROM `caches` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `caches`.`status`=1 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC LIMIT 20');


  
//mysql_query("SET NAMES 'utf8'"); 
$t1="CREATE TEMPORARY TABLE ocpl.tmp (id INT(11) unsigned NOT NULL auto_increment PRIMARY KEY, count INT(11), username VARCHAR(60), user_id INT(11)) ENGINE=MEMORY SELECT COUNT(*) `count`, `user`.`username`, `user`.`user_id` FROM `caches` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` WHERE `caches`.`status`=1 AND `caches`.`type`<>6 AND user.stat_ban = 0 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC"; 
//mysql_query("SET NAMES 'utf8'"); 
$r=mysql_query($t1) or die(mysql_error());
//mysql_query("SET NAMES 'utf8'"); 
$a="SELECT count, username, user_id FROM tmp GROUP BY `username` ORDER BY `count` DESC, `username`";
//echo "<br />";

$r=mysql_query($a) or die(mysql_error());
echo '
<tr class="bgcolor2">
	<td align="right">
		&nbsp;&nbsp;<b>'.tr('ranking').'</b>&nbsp;&nbsp;
	</td>
	<td align="center">
		&nbsp;&nbsp;<b>'.tr('number_of_caches').'</b>&nbsp;&nbsp;
	</td>
	<td align="center">
		&nbsp;&nbsp;<b>'.tr('username').'</b>&nbsp;&nbsp;
	</td>
</tr>
<tr><td height="2">';
$l2="";
$licznik=0;

while ($line=mysql_fetch_array($r))
{
$l1=$line[count];

$licznik++;
//			if( $row_num % 2 )
//				$bgcolor = "bgcolor1";
//			else
//				$bgcolor = "bgcolor2";

if ($l2!=$l1)
{
    echo '
			</td>
		</tr>
		<tr class="bgcolor2">
			<td align="right">
				&nbsp;&nbsp;<b>'.$licznik.'</b>&nbsp;&nbsp;
			</td>
			<td align="right">
				&nbsp;&nbsp;<b>'.$l1.'</b>&nbsp;&nbsp;
			</td>
			<td>
				<a href="viewprofile.php?userid='.$line[user_id].'">'.htmlspecialchars($line[username]).'</a>';
    $l2=$l1;
}
else {
    echo ', <a href="viewprofile.php?userid='.$line[user_id].'">'.htmlspecialchars($line[username]).'</a>';
    }
	$row_num++;
}
//echo "
//	</td>
//</tr>";


	echo '</td></tr></table>' . "\n";

?>
