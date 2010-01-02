<?php
global $bgcolor1, $bgcolor2;
	
	function colorCacheStatus($text, $id )
	{
		switch( $id )
		{
			case '1':
				return "<font color='green'>$text</font>";
			case '2':
				return "<font color='orange'>$text</font>";
			case '3':
				return "<font color='red'>$text</font>";
			default:
				return "<font color='gray'>$text</font>";
		}
	}
	
	function nonEmptyCacheName($cacheName)
	{
		if( str_replace(" ", "", $cacheName) == "" )
			return "[bez nazwy]";
		return $cacheName;
	}
	
	function getUsername($userid)
	{
		$sql = "SELECT username FROM user WHERE user_id='".sql_escape(intval($userid))."'";
		$query = mysql_query($sql) or die();
		if( mysql_num_rows($query) > 0)
			return mysql_result($query,0);
		return null;
	}
	
	function getCachename($cacheid)
	{
		$sql = "SELECT name FROM caches WHERE cache_id='".sql_escape(intval($cacheid))."'";
		$query = mysql_query($sql) or die();
		if( mysql_num_rows($query) > 0)
			return mysql_result($query,0);
		return null;
	}
	
	function getCacheOwnername($cacheid)
	{
		$sql = "SELECT user_id FROM caches WHERE cache_id='".sql_escape(intval($cacheid))."'";
		$query = mysql_query($sql) or die();
		if( mysql_num_rows($query) > 0)
			return getUsername(mysql_result($query,0));
		return null;
	}
	
	function actionRequired($cacheid)
	{
		// check if cache requires activation
		$sql = "SELECT status FROM caches WHERE cache_id='".sql_escape(intval($cacheid))."' AND status = 4";
		$query = mysql_query($sql) or die();
		if( mysql_num_rows($query) > 0)
			return true;
		return false;
	}
	
	function activateCache($cacheid)
	{
		// activate the cache by changing its status to temporarly unavailable
		if( actionRequired($cacheid) )
		{
			$sql = "UPDATE caches SET status = 5 WHERE cache_id='".sql_escape(intval($cacheid))."'";
			if( mysql_query($sql) )
			{
				sql("UPDATE sysconfig SET value = value - 1 WHERE name = 'hidden_for_approval'");
				return true;
			}
			else
				return false;
		}
		return false;
	}
	
	function notifyOwner($cacheid)
	{
		global $stylepath;
		$user_id = getCacheOwnername($cacheid);
		
		$cachename = getCachename($cacheid);
		$email_content = read_file($stylepath . '/email/activated_cache.email');
		
		$email_content = mb_ereg_replace('%cachename%', $cachename, $email_content);
		$email_content = mb_ereg_replace('%cacheid%', $cacheid, $email_content);	
		$email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
		$email_headers .= "From: Opencaching.pl <rr@opencaching.pl>\r\n";
		$email_headers .= "Reply-To: rr@opencaching.pl\r\n";
		
		$query = sql("SELECT `email` FROM `user` WHERE `user_id`='&1'", $user_id);
		$owner_email = sql_fetch_array($query);
		
		//send email to owner
		mb_send_mail($owner_email['email'], "[OC PL] Akceptacja skrzynki: ".$cachename, $email_content, $email_headers);
		
		//send email to approver
		mb_send_mail($usr['email'], "[OC PL] Akceptacja skrzynki: ".$cachename, "Kopia potwierdzenia akceptacji skrzynki:\n".$email_content, $email_headers);
	}
	
	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	$tplname = 'viewpendings';
	$content = '';
	// tylko dla członków Rady
	if ($error == false && $usr['admin'])
	{
		if( isset($_GET['cacheid']) )
		{
			if( actionRequired($_GET['cacheid']) )
			{
				// requires activation
				if( isset($_GET['confirm']) && $_GET['confirm'] == 1 )
				{
					// confirmed - change the status and notify the owner now
					if( activateCache($_GET['cacheid']) )
					{
						notifyOwner($_GET['cacheid']);
						$confirm = "<p>Skrzynka została zaakceptowana. Właściciel został powiadomiony o tym fakcie.</p>";
					}
					else
					{
						$confirm = "<p>Wystąpił problem z akceptacją skrzynki. Żadna zmiana nie została wprowadzona.</p>";
					}
					
					
				}
				else
				{
					// require confirmation
					$confirm = "<p>Zamierzasz zaakceptować skrzynkę \"<a href='viewcache.php?cacheid=".$_GET['cacheid']."'>".getCachename($_GET['cacheid'])."</a>\" użytkownika ".getCacheOwnername($_GET['cacheid']).". Status skrzynki zostanie zmieniony na \"Jeszcze niedostępna\".</p>";
					$confirm .= "<p><a href='viewpendings.php?cacheid=".$_GET['cacheid']."&amp;confirm=1'>Potwierdzam</a> | 
					<a href='viewpendings.php'>Powrót</a></p>";
				}
				tpl_set_var('confirm', $confirm);
			}
			else
			{
				tpl_set_var('confirm', '<p>Wybrana skrzynka jest już aktywna albo nie istnieje.</p>');
			}
		}
		else
		{
			tpl_set_var('confirm', '');
		}
		
		$sql = "SELECT cache_status.id AS cs_id, 
									 cache_status.pl AS cache_status, 
									 user.username AS username, 
									 user.user_id AS user_id, 
									 caches.cache_id AS cache_id,
									 caches.name AS cachename, 
									 caches.date_created AS date_created
						FROM cache_status, user, caches 
						WHERE cache_status.id = caches.status 
									AND caches.user_id = user.user_id
									AND caches.status = 4";

		$query = mysql_query($sql) or die("DB error");
		$row_num = 0;
		while( $report = mysql_fetch_array($query) )
		{
			if( $row_num % 2 )
				$bgcolor = "bgcolor1";
			else
				$bgcolor = "bgcolor2";
		
			$content .= "<tr>\n";
			$content .= "<td class='".$bgcolor."'><a href='viewcache.php?cacheid=".$report['cache_id']."'>".nonEmptyCacheName($report['cachename'])."</a></td>\n";
			$content .= "<td class='".$bgcolor."'>".$report['date_created']."</td>\n";
			$content .= "<td class='".$bgcolor."'><a href='viewprofile.php?userid=".$report['user_id']."'>".$report['username']."</a></td>\n";
			$content .= "<td class='".$bgcolor."'><a href='viewpendings.php?cacheid=".$report['cache_id']."'>Zaakceptuj</a></td>\n";
			$content .= "</tr>\n";
			$row_num++;
		}
		tpl_set_var('content', $content);
		
	}
	else
	{
		$tplname = 'viewpendings_error';
	}
	tpl_BuildTemplate();
	
?>
