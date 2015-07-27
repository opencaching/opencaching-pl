<?php
//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');

	if( $usr['admin'] )
	{
		tpl_set_var('bulletin', "");
		if( isset($_POST['bulletin']) && $_POST['bulletin']!= "" && $_SESSION['submitted'] != true)
		{
			// podgląd
			$bulletin = addslashes($_POST['bulletin']);
			
			$_SESSION['bulletin'] = $bulletin;
			tpl_set_var('bulletin', stripslashes(nl2br($bulletin)));
			$tplname = 'admin_bulletin_preview';
			tpl_BuildTemplate();
		}
		else
		if( isset($_POST['bulletin_final']) && $_POST['bulletin_final']!= "" && $_SESSION['submitted'] != true)
		{
			// wysłanie
			$email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
			$email_headers .= "From: Zespol Opencaching.pl <rr@opencaching.pl>\r\n";
			$email_headers .= "Reply-To: rr@opencaching.pl\r\n";
			
			$bulletin = ($_SESSION['bulletin']);
			$sql = "INSERT INTO bulletins (content, user_id) VALUES ('".sql_escape($bulletin)."', ".sql_escape(intval($usr['userid'])).")";
			@mysql_query($sql);
			
			$bulletin .= "\r\n\r\nJeśli nie chcesz więcej otrzymywać biuletynów informacyjnych z Opencaching.pl, zmień ustawienia konta na http://www.opencaching.pl/myprofile.php?action=change.";
			//get emails
			$sql = "SELECT `email` FROM `user` WHERE `is_active_flag`=1 AND get_bulletin=1 AND rules_confirmed=1";
			$query = @mysql_query($sql);
			while( $email = @mysql_fetch_array($query))
			{
				mail($email['email'], "[OC PL] Biuletyn informacyjny ".date("Y-m-d"), stripslashes($bulletin), $email_headers);
			}
			$_SESSION['submitted'] = true;
			tpl_set_var('bulletin', stripslashes($_SESSION['bulletin']));
			unset($_SESSION['bulletin']);
			$tplname = 'admin_bulletin_sent';
			tpl_BuildTemplate();
		}
		else
		{
			// formularz
			$_SESSION['submitted'] = false;
			$tplname = 'admin_bulletin';
			tpl_BuildTemplate();
		}
		
	}
?>