<?php
	global $lang, $rootpath;

	if (!isset($rootpath)) $rootpath = './';

	//include template handling
	require_once($rootpath . 'lib/common.inc.php');

  setlocale(LC_TIME, 'pl_PL.UTF-8');
$rsU = sql('SELECT COUNT(*) `count` FROM (SELECT COUNT(cache_logs.user_id) FROM `cache_logs` WHERE `type`=1 AND `deleted`=0 GROUP BY `user_id`) `users_with_founds`');
$fC = sql('SELECT COUNT(*) `count` FROM `cache_logs` WHERE `deleted`=0 AND `type`=1');
  $rsUs = mysql_fetch_array($rsU);
    $fCt = mysql_fetch_array($fC);

	echo '<center><table><tr><td align=center><font size=+0><b>Ranking użytkowników wg liczby odkryć</b></font><br>Użytkowników którzy znalezli:';
	echo $rsUs[count]; 
	echo ' .::. Ile razy odkryto skrzynki:';
	echo $fCt[count]; 
	echo '</td></tr>';
	echo '<tr><td bgcolor="#D5D5D5"><b>Nie licz statystyk dla skrzynek typu:</b><br><form action="articles.php" method="GET">';
	
	
	$sql = "SELECT * FROM cache_type";
	$res_q = mysql_query($sql);
	$no_types = 0;
	$typ = "";
	while( $res = mysql_fetch_array($res_q) )
	{
		$no_types++;
		if( $_GET[$res['id']] == 1)
		{
			$checked = 'checked';
			$typ .= " AND caches.type <> ".$res['id'];
		}
		else
			$checked = '';
			
		echo '<input type="checkbox" value="1" name="'.intval($res['id']).'" id="'.intval($res['id']).'" '.$checked.'><label for="'.intval($res['id']).'">'.strip_tags($res['pl']).'</label>';
		if( $no_types % 5 != 0 )
			echo ' | ';
		if( $no_types == 5 )
			echo '<br>';
	}
	echo '<input type="hidden" name="page" value="s2">';
	echo '<br/><input type="submit" value="Filtruj">';
	
	echo '</form></td></tr>';
	echo '<tr><td> <table bgcolor="white" width=800>' . "\n";

$a = "SELECT COUNT(*) count, username, stat_ban, user.user_id FROM caches, cache_logs, user ".
     "WHERE `cache_logs`.`deleted`=0 AND cache_logs.user_id=user.user_id AND cache_logs.type=1 AND cache_logs.cache_id = caches.cache_id ".$typ." ".
     "GROUP BY user.user_id ".
     "ORDER BY 1 DESC, user.username ASC";

echo "<br>";

$r=mysql_query($a) or die(mysql_error());
echo    "<tr bgcolor=#D5D5D5>".
        "<td align=center><font size=2>&nbsp;&nbsp;<b>Ranking</b>&nbsp;&nbsp;</td>".
        "<td align=center><font size=2><b>Miejsce ex-aequo</b></td>".
	"<td align=center><font size=2><b>Liczba odkryć</b></font></td>".
	"<td align=center>&nbsp;&nbsp;<font size=2><b>Username</b></font>&nbsp;&nbsp;</td>";

$l2=""; // number of users within the same rank
$rank=0; // rank number; increamented by one for each group of users having the same caches discovered
$position=1; // position ex aequo; incremented by number of users in each rank

while ($line=mysql_fetch_array($r))
{
	$color = "black";
	$banned = "";
	if( $usr['admin'] || $line['stat_ban'] == 0)
	{
		if( $line['stat_ban'] )
		{
			$color = "gray";
			$banned = " (BAN)";
		}
    $l1=$line[count];
    if ($l2!=$l1)
    {
        // new rank (finish recent row and start new one)
	echo "</font></td></tr>";
	$rank++;
        echo "<tr bgcolor=#D5D5D5>".
	     "<td align=right><font size=2>&nbsp;&nbsp;<b>$rank</b>&nbsp;&nbsp;</td>".
             "<td align=right><font size=2>&nbsp;&nbsp;$position&nbsp;&nbsp;</td>".
	     "<td align=right>&nbsp;&nbsp;<font size=2><b>$l1</b></font>&nbsp;&nbsp;</td>".
             "<td><font size=2><a style='color:$color' href='viewprofile.php?userid=$line[user_id]'>$line[username]$banned</a>";
        $l2=$l1;
    }
    else 
    {
        // the same rank (continue row)
        echo ", <a style='color:$color' href='viewprofile.php?userid=$line[user_id]'>$line[username] $banned</a>";
    }
    $position++;
	}
}

// end table
echo "</font></td></tr>";
echo "</table></td></tr></table>\n";

?>
