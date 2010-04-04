<table class="content" width="97%">
	<tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="{{stats}}" title="{{stats}}" align="middle" /><font size="4">  <b>{{stats}}</b></font></td></tr>
	<tr><td class="spacer"></td></tr>
</table>

<table class="table" width="760" style="line-height: 1.6em; font-size: 10px;">
<tr>
<td>
<?php
	global $lang, $rootpath;

	if (!isset($rootpath)) $rootpath = './';

	//include template handling
	require_once($rootpath . 'lib/common.inc.php');

	if (isset($_REQUEST['region']))
		{
			$region= $_REQUEST['region'];
		}
//	$region="PL61";
    $woj=sqlValue("SELECT nuts_codes.name FROM nuts_codes WHERE code='$region'", 0);
  setlocale(LC_TIME, 'pl_PL.UTF-8');
  $rsU = sql('SELECT COUNT(*) `count` FROM (SELECT COUNT(caches.user_id) FROM `caches` WHERE `status`=1 GROUP BY `user_id`) `users_with_founds`');
  $fC = sql('SELECT COUNT(*) `count` FROM `caches` WHERE `status`=1');
    $rsUs = mysql_fetch_array($rsU);
    $fCt = mysql_fetch_array($fC);
	echo '<table width="97%"><tr><td align="center"><center><b> '.tr('ranking_by_number_of_created_caches').' </b><br />tylko aktywne skrzynki<br />';
//	echo $rsUs[count]; 
	echo '<br /><b>Województwo ';
	echo $woj;
//	echo ' .::. '.tr('number_of_active_caches').': ';
//	echo $fCt[count]; 
	echo '</b></center><br /></td></tr></table><table border="1" bgcolor="white" width="97%">' . "\n";

	// cleanup (old gpxcontent lingers if gpx-download is cancelled by user)		
	mysql_query('DROP TEMPORARY TABLE IF EXISTS `ocpl.tmps9`');

$t1="CREATE TEMPORARY TABLE ocpl.tmps9 (id INT(11) unsigned NOT NULL auto_increment PRIMARY KEY, count INT(11), username VARCHAR(60), user_id INT(11)) ENGINE=MEMORY SELECT COUNT(*) `count`, `user`.`username`, `user`.`user_id` FROM `caches` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`,  cache_location  WHERE (`cache_location`.`code3`='$region' AND `cache_location`.`cache_id`=`caches`.`cache_id`) AND `caches`.`status`=1 AND `caches`.`type`<>6 GROUP BY `user`.`user_id` ORDER BY `count` DESC, `user`.`username` ASC"; 

$r=mysql_query($t1) or die(mysql_error());

$a="SELECT count, username, user_id FROM tmps9 GROUP BY `username` ORDER BY `count` DESC, `username`";

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


//			if( $row_num % 2 )
//				$bgcolor = "bgcolor1";
//			else
//				$bgcolor = "bgcolor2";

if ($l2!=$l1)
{$licznik++;
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
</td></tr>
</table>

