<?
require("common.inc.php");

if (isset($_COOKIE['ocplm'])) 
{
    foreach ($_COOKIE['ocplm'] as $name => $value)
      {
       $login[$name]=base64_decode($value);
       }

 $logino = $login['USER'];
$loginh = $login['HASLO'];
setcookie("ocplm[USER]", base64_encode($logino), time()+36000);

setcookie("ocplm[HASLO]", base64_encode($loginh), time()+36000);

  mysql_connect("localhost","ocpl","n1uch@cz");
  mysql_select_db("ocpl");

  $result1 = mysql_query("SELECT * FROM `user` WHERE `username` = '$logino' LIMIT 1; ");

  $result = mysql_fetch_array($result1);
  
  echo('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>LOG</title></head><body>');

  if (($result['password'] == hash('sha512', md5($login['HASLO']))) && ($result['username'] == $login['USER']) && ($result['is_active_flag'] == '1') && ($result['rules_confirmed'] == '1'))
    { 
     //zalogowany





       
            
            echo('Zalogowany, <b>'. $logino . '</b><br>');
            echo('<font size="+2">Ostatnie logi</font><br /><a href="index.php">&raquo; Strona Glowna</a><br /><hr />');
   
   
   
   
   
   
   
	$LOGS_PER_PAGE = 20;
	$PAGES_LISTED = 5;
		
	$rs = mysql_query("SELECT count(id) FROM cache_logs");
	$total_logs = mysql_result($rs,0);
	mysql_free_result($rs);
	
	$pages = "";
	$total_pages = ceil($total_logs/$LOGS_PER_PAGE);
	
	if( !isset($_GET['start']) || intval($_GET['start'])<0 || intval($_GET['start']) > $total_logs)
		$start = 0;
	else
		$start = intval($_GET['start']);
	
	$startat = max(0,floor((($start/$LOGS_PER_PAGE)+1)/$PAGES_LISTED)*$PAGES_LISTED);
	$pages .='<br>';
	
	for( $i=max(1,$startat);$i<$startat+$PAGES_LISTED;$i++ )
	{
		$page_number = ($i-1)*$LOGS_PER_PAGE;
		if( $page_number == $start )
			$pages .= '<b> ';
		$pages .= ' <a href="ostatnie_logi.php?start='.$page_number.'">'.$i.' </a> '; 
		if( $page_number == $start )
			$pages .= ' </b>';
		
	}
	$pages .='<br>';
	
	if( ($start/$LOGS_PER_PAGE)+1 >= $PAGES_LISTED )
		$pages .= '<br><a href="ostatnie_logi.php?start='.max(0,($startat-$PAGES_LISTED-1)*$LOGS_PER_PAGE).'"><<<</a>'; 
		$pages .=' | ';
	
	if( $total_pages > $PAGES_LISTED )
		$pages .= '<a href="ostatnie_logi.php?start='.(($i-1)*$LOGS_PER_PAGE).'">>>></a> <br>'; 
	
	$rs = mysql_query("SELECT `cache_logs`.`id`
			FROM `cache_logs`, `caches`
			WHERE `cache_logs`.`cache_id`=`caches`.`cache_id`
			  AND `caches`.`status` != 5 
				AND `caches`.`status` != 6
			ORDER BY  `cache_logs`.`last_modified` DESC
			LIMIT ".intval($start).", ".intval($LOGS_PER_PAGE));
	$log_ids = '';
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = mysql_fetch_array($rs);
		if ($i > 0)
		{
			$log_ids .= ', ' . $record['id'];
		}
		else
		{
			$log_ids = $record['id'];
		}
	}
	mysql_free_result($rs);

	$rs = mysql_query("SELECT cache_logs.cache_id AS cache_id,
	                          cache_logs.type AS log_type,
	                          cache_logs.date AS log_date,
	                          caches.name AS cache_name,
	                          countries.pl AS country_name,
	                          user.username AS user_name,
                                  log_types.icon_small AS icon_small
	                  FROM ((cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id)) INNER JOIN countries ON (caches.country = countries.short)) INNER JOIN user ON (cache_logs.user_id = user.user_id) INNER JOIN log_types ON (cache_logs.type = log_types.id)
	                   WHERE cache_logs.id IN (" . $log_ids . ")
	                   ORDER BY cache_logs.last_modified DESC");
	//$rs = mysql_query($sql);

	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		//group by country
		$record = mysql_fetch_array($rs);

		$newlogs[$record['country_name']][] = array(
			'cache_id'   => $record['cache_id'],
			'log_type'   => $record['log_type'],
			'log_date'   => $record['log_date'],
			'cache_name' => $record['cache_name'],
			'user_name'  => $record['user_name'],
                        'icon_small' => $record['icon_small']
		);
	}

	//sort by country name
//	uksort($newlogs, 'cmp');

	$file_content = '';

	if (isset($newlogs))
	{
		foreach ($newlogs AS $countryname => $country_record)
		{
			$file_content .= '<tr><td ><b>' . htmlspecialchars($countryname, ENT_COMPAT, 'UTF-8') . '</b><br></td></tr>';

			foreach ($country_record AS $log_record)
			{

				$file_content .= "<tr><td><br><br>";
				$file_content .= htmlspecialchars(date("d.m.Y", strtotime($log_record['log_date'])), ENT_COMPAT, 'UTF-8');
				$file_content .= ' <img src="/tpl/stdstyle/images/' . $log_record['icon_small'] . '" width="10" height="10" align="middle" border="0" align="left" alt="" title="">';
				$file_content .= ' <br> <a href="zobacz_cache.php?cacheid=' . htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($log_record['cache_name'], ENT_COMPAT, 'UTF-8') . '</a>';

				switch( $log_record['log_type'] )
				{
					case 1:
						$file_content .= '<br> znalazl ';
					break;
					case 2:
						$file_content .= '<br> nie znalazl ';
					break;
					case 3:
						$file_content .= '<br> komentarz ';
					break;
					case 7:
						$file_content .= '<br> uczestniczyl ';
					break;
					case 8:
						$file_content .= '<br> bedzie uczestniczyl ';
					break;
				}
				$file_content .= htmlspecialchars($log_record['user_name'], ENT_COMPAT, 'UTF-8').'';

				$file_content .= "</td></tr>";
				$file_content .= "\n";
			}
		}
	}
	//$n_file = fopen("/tpl/stdstyle/html/newlogs.tpl.php", 'w');
	//fwrite($n_file, $file_content);
	//fclose($n_file);
	 echo $file_content;
	 echo ('<center><br><br><font size="+2">');
	echo $pages;
	echo ('</font></center></br>');
	unset($newcaches);
//	unset($newcaches);

	//user definied sort function
	

function cmp($a, $b)
	{
		if ($a == $b)
		{
			return 0;
		}
		return ($a > $b) ? 1 : -1;
	}

   
   
   
   
            
                  

    }
   else 
   {
     echo('Blad cookie<br>');
     echo('<font size="+2">Zaloguj sie</font><br /><a href="index.php">&raquo; Strona Glowna</a><br /><hr /><form action=login.php method=post>Uzytkownik: <br><input name="UZYTKOWNIK" type=text size="10"><br><br>Haslo:<br> <input name="HASELO" type=password size="10"><br><br><input type=submit value="Wejdz"></form>');
   }
	
} 
else
{
echo('Nie zalogowany. Jesli nie mozesz sie zalogowac, sprawdz obsluge coockies w telefonie.<br>');
echo('<font size="+2">Zaloguj sie</font><br /><a href="index.php">&raquo; Strona Glowna</a><br /><hr /><form action=login.php method=post>Uzytkownik: <br><input name="UZYTKOWNIK" type=text size="10"><br><br>Haslo:<br> <input name="HASELO" type=password size="10"><br><br><input type=submit value="Wejdz"></form>');


}



?>


<hr /><center>

</center>
</body>
</html>
