<table class="content" width="97%">
	<tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="{{stats}}" title="{{stats}}" align="middle" /><font size="4">  <b>{{statistics}}</b></font></td></tr>
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

  setlocale(LC_TIME, 'pl_PL.UTF-8');
	if (isset($_REQUEST['region']))
		{
			$region= $_REQUEST['region'];
		}
//	$region="PL61";
    $woj=sqlValue("SELECT nuts_codes.name FROM nuts_codes WHERE code='$region'", 0);
	
	echo '<center><table width="97%" border="0"><tr><td align="center"><center><b>Ranking skrzynek wg liczby odkryć w regionie<br/><b>';
	echo '<br /><br /><b><font color="blue">';
	echo $woj;
	echo '</font></b></center></td></tr></table>';

	echo '<table border="1" bgcolor="white" width="97%" style="font-size:11px; line-height:1.6em;">' . "\n";
	echo "<br/>";
	
	// cleanup (old gpxcontent lingers if gpx-download is cancelled by user)		
	mysql_query('DROP TEMPORARY TABLE IF EXISTS `ocpl.tmps11`');
	$t1="CREATE TEMPORARY TABLE ocpl.tmps11 (id INT(11) unsigned NOT NULL auto_increment PRIMARY KEY, count INT(11),name VARCHAR(60), cache_id INT(11), username VARCHAR(60)) ENGINE=MEMORY DEFAULT CHARACTER SET=utf8 COLLATE=utf8_polish_ci SELECT COUNT(*) `count`, `caches`.`name`, `cache_logs`.`cache_id`, `user`.`username` FROM `cache_logs` INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`, cache_location  WHERE (`cache_location`.`code3`='$region' AND `cache_location`.`cache_id`=`caches`.`cache_id`) AND `cache_logs`.`deleted`=0 AND `cache_logs`.`type`=1 AND `caches`.`status`=1 GROUP BY `caches`.`cache_id` ORDER BY `count` DESC, `caches`.`name` ASC"; 
//mysql_query("SET NAMES 'utf8'"); 
$r=mysql_query($t1) or die(mysql_error());
//mysql_query("SET NAMES 'utf8'"); 
$a="SELECT count,name, cache_id, username FROM tmps11 GROUP BY `name` ORDER BY `count` DESC, `name`";

$r=mysql_query($a) or die(mysql_error());
echo    '<tr class="bgcolor2">'.
        '<td align="center">&nbsp;&nbsp;<b>Ranking</b>&nbsp;&nbsp;</td>'.
	'<td align="center"><b>Liczba odkryć</b></td>'.
	'<td align="center">&nbsp;&nbsp;<b>Geocache (User)</b>&nbsp;&nbsp;</td></tr><tr><td>';
	
$l2="";
$licznik=0;
while ($line=mysql_fetch_array($r))
{
$l1=$line[count];
if ($l2!=$l1)
{
echo '</td></tr>';
$licznik=$licznik+1;
    echo "<tr class=\"bgcolor2\"><td align=\"right\">&nbsp;&nbsp;<b>$licznik</b>&nbsp;&nbsp;</td><td align=\"right\">&nbsp;&nbsp;<b>$l1</b>&nbsp;&nbsp;</td>";
    echo  "<td><a href=viewcache.php?cacheid=$line[cache_id]>$line[name]</a> ($line[username])";
    $l2=$l1;
}
else {
    echo ", <a href=viewcache.php?cacheid=$line[cache_id]>$line[name]</a> ($line[username])";
    }

}

	echo '</table>' . "\n";
?>
</td></tr>
</table><br/><br/>
