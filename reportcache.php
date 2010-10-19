<?php

function reason($reason)
{
	switch( $reason )
	{
		case 1:
			return "Uwaga co do lokalizacji skrzynki";
		case 2:
			return "Skrzynka wymaga archiwizacji";
		case 3:
			return "Nieodpowiednia zawartość skrzynki";
		case 4:
			return "Inny";
	}				
}

	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
if($usr==true)
{
	//Preprocessing
	if ($error == false)
	{
		$tplname = 'reportcache';
		tpl_set_var('noreason_error', '');
		tpl_set_var('notext_error', '');
		$cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid']+0 : 0;
		$sql = "SELECT name, wp_oc, user_id FROM caches WHERE cache_id='".sql_escape($cacheid)."'";
		$query = mysql_query($sql);
					
		if (mysql_num_rows($query) == 0)
		{
			$tplname = 'reportcache_nocache';
		}
		else
		{
			$cache = @mysql_fetch_array($query) or die("DB error.");
			tpl_set_var('cachename', htmlspecialchars($cache['name'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('cacheid', $cacheid);
		
			if( isset($_POST['ok']) )
			{
				if( $_POST['text'] == "" )
				{
					tpl_set_var('notext_error','&nbsp;<b><font size="1" color="#ff0000">Brak opisu problemu.</font></b>');
					$tplname = 'reportcache_notext';
				}
				else if( $_POST['reason'] == 0)
					tpl_set_var('noreason_error', '&nbsp;<b><font size="1" color="#ff0000">Nie wybrano powodu.</font></b>');
				else
				{
					// formularz został wysłany
										
					// pobierz adres email zglaszajacego
					$query = sql("SELECT `email` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
					$cache_reporter = sql_fetch_array($query);
					
					if( $_POST['adresat'] == "rr")
					{
						$tplname = 'reportcache_sent';
						// zapisz zgłoszenie w bazie
						$sql = "INSERT INTO reports (user_id, cache_id, text, type) VALUES ('".sql_escape($usr['userid'])."', '".sql_escape($cacheid)."', '".strip_tags(sql_escape($_POST['text']))."', '".sql_escape(intval($_POST['reason']))."' )";
						@mysql_query($sql) or die("DB error");
						
						// wysłanie powiadomień
						$email_content = read_file($stylepath . '/email/newreport_octeam.email');

						$email_content = mb_ereg_replace('%date%', date("Y.m.d H:i:s"), $email_content);
						$email_content = mb_ereg_replace('%submitter%', $usr['username'], $email_content);		
						$email_content = mb_ereg_replace('%cachename%', $cache['name'], $email_content);
						$email_content = mb_ereg_replace('%cache_wp%', $cache['wp_oc'], $email_content);
						$email_content = mb_ereg_replace('%cacheid%', $cacheid, $email_content);		
						$email_content = mb_ereg_replace('%reason%', reason($_POST['reason']), $email_content);		
						$email_content = mb_ereg_replace('%text%', strip_tags(addslashes($_POST['text'])), $email_content);		
						// send email to RR
						
						$emailheaders = "Content-Type: text/plain; charset=utf-8\r\n";
						$emailheaders .= "From: Opencaching.pl <".$cache_reporter['email'].">\r\n";
						$emailheaders .= "Reply-To: Opencaching.pl <".$cache_reporter['email'].">";
						
						mb_send_mail("cog@opencaching.pl", "Nowe zgłoszenie problemu na OC PL (".$cache['wp_oc'].")", $email_content, $emailheaders);
						//echo("cog@opencaching.pl". "Nowe zgłoszenie problemu na OC PL". $email_content. $emailheaders);
					}
					else
						$tplname = 'reportcache_sent_owner';
					//get email address of cache owner
					if( $_POST['adresat'] == "rr")
					{
						$email_content = read_file($stylepath . '/email/newreport_cacheowner.email');
					}
					else
					{
						$email_content = read_file($stylepath . '/email/newreport_cacheowneronly.email');
					}
					
					$query = sql("SELECT `email` FROM `user` WHERE `user_id`='&1'", $cache['user_id']);
					$cache_owner = sql_fetch_array($query);

					$email_content = mb_ereg_replace('%date%', date("Y.m.d H:i:s"), $email_content);
					$email_content = mb_ereg_replace('%submitter%', $usr['username'], $email_content);		
					$email_content = mb_ereg_replace('%cachename%', $cache['name'], $email_content);
					$email_content = mb_ereg_replace('%cache_wp%', $cache['wp_oc'], $email_content);
					$email_content = mb_ereg_replace('%cacheid%', $cacheid, $email_content);		
					$email_content = mb_ereg_replace('%reason%', reason($_POST['reason']), $email_content);		
					$email_content = mb_ereg_replace('%text%', strip_tags(addslashes($_POST['text'])), $email_content);
					
					//send email to cache owner
					$emailheaders = "Content-Type: text/plain; charset=utf-8\r\n";
					$emailheaders .= "From: ".$usr['username']." <".$usr['email'].">\r\n";
					$emailheaders .= "Reply-To: ".$usr['username']." <".$usr['email'].">";
					
					mb_send_mail($cache_owner['email'], "[OC PL] Zgłoszono problem dotyczący Twojej skrzynki ".$cache['wp_oc'], $email_content, $emailheaders);
					
					// send email to cache reporter
					$emailheaders = "Content-Type: text/plain; charset=utf-8\r\n";
					$emailheaders .= "From: Opencaching.pl <cog@opencaching.pl>\r\n";
						
					$email_content = read_file($stylepath . '/email/newreport_reporter.email');
					
					$email_content = mb_ereg_replace('%date%', date("Y.m.d H:i:s"), $email_content);
					$email_content = mb_ereg_replace('%cachename%', $cache['name'], $email_content);
					$email_content = mb_ereg_replace('%cache_wp%', $cache['wp_oc'], $email_content);
					$email_content = mb_ereg_replace('%cacheid%', $cacheid, $email_content);		
					$email_content = mb_ereg_replace('%reason%', reason($_POST['reason']), $email_content);		
					$email_content = mb_ereg_replace('%text%', strip_tags(addslashes($_POST['text'])), $email_content);
					
					mb_send_mail($cache_reporter['email'], "[OC PL] Zgłosiłeś problem dotyczący skrzynki ".$cache['wp_oc'], $email_content, $emailheaders);
					
					//echo($cache_owner['email']. "[OC PL] Zgłoszono problem dotyczący Twojej skrzynki". $email_content. $emailheaders);
				}
			}
		}
	}
	tpl_BuildTemplate();
}
?>
