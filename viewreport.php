<?php
$saved="";
$email_sent = "";
$email_form = "";

	function writeReason($type)
	{
		switch( $type )
		{
			case '1':
				return "Uwaga co do lokalizacji skrzynki";
			case '2':
				return "Nieodpowiedni wpis w logu";
			case '3':
				return "Nieodpowiednia zawartość skrzynki";
			case '4':
				return "Inny";
		}
	}
	
	function writeStatus($status)
	{
		switch( $status )
		{
			case '0':
				return "<font color='red'>nowe</font>";
			case '1':
				return "<font color='orange'>w toku</font>";
			case '2':
				return "<font color='green'>zamknięte</font>";
			case '3':
				return "<font color='blue'>zajrzyj tu!</font>";
		}
	}

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
	
	function writeRe($status)
	{
		switch( $status )
		{
			case '0':
				return "zgłaszającego";
			case '1':
				return "właściciela skrzynki";
			case '2':
				return "zgłaszającego i właściciela skrzynki";
		}
	}
	
	function getUsername($userid)
	{
		$sql = "SELECT username FROM user WHERE user_id='".sql_escape(intval($userid))."'";
		$query = mysql_query($sql) or die();
		if( mysql_num_rows($query) > 0)
			return mysql_result($query,0);
		return null;
	}
	
	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	$tplname = 'viewreport';
	
	// tylko dla członków Rady
	if ($error == false && $usr['admin'])
	{
		// sprawdzenie czy nastąpiło żądanie zmiany statusu lub usunięcia zgłoszenia, lub edycja notatki

		/*if( isset($_GET['delete']) && isset($_REQUEST['reportid']))
		{
			$sql = "DELETE FROM reports WHERE id='".sql_escape(intval($_REQUEST['reportid']))."'";
			@mysql_query($sql);
			header('Location: viewreports.php');
		}*/
		if( isset($_GET['mailto']) )
		{
			// show mail form
			$email_form = "<form action='viewreport.php' method='post'>
	<input type='hidden' name='reportid' value='".intval($_REQUEST['reportid'])."'>
	<input type='hidden' name='mailto' value='".intval($_REQUEST['mailto'])."'>
	<textarea name='email_content' cols='80' rows='5'></textarea>
	<br />
	<input type='submit' value='Wyślij e-mail'>
	<a href='viewreport.php?reportid=".$_REQUEST['reportid']."'>Anuluj</a>
</form>";
		}
		if( isset($_REQUEST['reportid']) && isset($_REQUEST['email_content']) && isset($_REQUEST['mailto']) && $_REQUEST['email_content'] != "")
		{
			$sql = "SELECT reports.user_id as user_id, reports.cache_id as cache_id FROM reports WHERE reports.id = '".sql_escape(intval($_REQUEST['reportid']))."'";
			$query = mysql_query($sql) or die("DB Error. Bad report id (well... probably).");
			
			$report = mysql_fetch_array($query);
			$sql = "SELECT user_id, name FROM caches WHERE cache_id='".sql_escape(intval($report['cache_id']))."'";
			$email_content = stripslashes($_REQUEST['email_content']);
			$note_content = " Wysłanie e-maila do ".writeRe($_REQUEST['mailto']).":<br/><i>".$email_content."</i>";
			$cache_info = mysql_fetch_array(mysql_query($sql));
			$cache_user_id = $cache_info['user_id'];
			$report_user_id = $report['user_id'];
			$email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
			$email_headers .= "From: Opencaching.pl <rr@opencaching.pl>\r\n";
			$email_headers .= "Reply-To: rr@opencaching.pl\r\n";
//			$email_headers .= "Reply-To: ".$usr['email']."\r\n";
//			$email_headers .= "CC: rr@opencaching.pl\r\n";

			switch( $_REQUEST['mailto'] )
			{
				case "0":
					//get email address of reporter
					$query = sql("SELECT `email` FROM `user` WHERE `user_id`='&1'", $report_user_id);
					$report_email = sql_fetch_array($query);
					//send email to reporter
					mb_send_mail($report_email['email'], "[OC PL] Dot. skrzynki: ".$cache_info['name'], $email_content, $email_headers);
					mb_send_mail($usr['email'], "[OC PL] Dot. skrzynki: ".$cache_info['name'], "Kopia Twojej wiadomości:\n".$email_content, $email_headers);
					break;
				
				case "1":
					//get email address of cache owner
					$query = sql("SELECT `email` FROM `user` WHERE `user_id`='&1'", $cache_user_id);
					$report_email = sql_fetch_array($query);
					//send email to cache owner
					mb_send_mail($report_email['email'], "[OC PL] Dot. skrzynki: ".$cache_info['name'], $email_content, $email_headers);
					mb_send_mail($usr['email'], "[OC PL] Dot. skrzynki: ".$cache_info['name'], "Kopia Twojej wiadomości:\n".$email_content, $email_headers);
				break;
				
				case "2":
					//get email address of reporter
					$query = sql("SELECT `email` FROM `user` WHERE `user_id`='&1'", $report_user_id);
					$report_email = sql_fetch_array($query);
					//send email to reporter
					mb_send_mail($report_email['email'], "[OC PL] Dot. skrzynki: ".$cache_info['name'], $email_content, $email_headers);
					
					//get email address of cache owner
					$query = sql("SELECT `email` FROM `user` WHERE `user_id`='&1'", $cache_user_id);
					$report_email = sql_fetch_array($query);

					//send email to cache owner
					mb_send_mail($report_email['email'], "[OC PL] Dot. skrzynki: ".$cache_info['name'], $email_content, $email_headers);
					mb_send_mail($usr['email'], "[OC PL] Dot. skrzynki: ".$cache_info['name'], "Kopia Twojej wiadomości:\n".$email_content, $email_headers);

				break;
			}
			
			$email_sent = "<b><font color='green'>E-mail został wysłany do ".writeRe($_REQUEST['mailto']).".</font></b>";
	
			$note = nl2br(sql_escape($note_content));
			$sql = "UPDATE reports SET note=CONCAT('[".sql_escape(date("Y-m-d H:i:s"))."] <b>".sql_escape($usr['username'])."</b>: ".$note."<br />', note), changed_by='".sql_escape(intval($usr['userid']))."', changed_date='".sql_escape(date("Y-m-d H:i:s"))."' WHERE id='".sql_escape(intval($_REQUEST['reportid']))."'";
			@mysql_query($sql);
			//$saved = "<b><font color='green'>Informacja o wysłaniu e-maila została zapisana w notatkach poniżej.</font></b>";
		}
		
		tpl_set_var('confirm_resp_change', "");
		tpl_set_var('confirm_status_change', "");
		if( isset($_POST['new_resp'])&& isset($_REQUEST['reportid']))
		{
			$sql = "UPDATE reports SET responsible_id = '".sql_escape(intval($_POST['respSel']))."' WHERE id='".sql_escape(intval($_REQUEST['reportid']))."'";
			@mysql_query($sql);
			if( $_POST['respSel'] != 0 )
				tpl_set_var('confirm_resp_change', "<b><font color='green'>Nowym prowadzącym problem jest ".getUsername($_POST['respSel']).".</font></b>");
			else
				tpl_set_var('confirm_resp_change', "<b><font color='green'>Nie wybrano prowadzącego problem.</font></b>");
		}
		
		if( isset($_POST['new_status']) && isset($_REQUEST['reportid']))
		{
			$sql = "UPDATE reports SET status='".sql_escape(intval($_POST['statusSel']))."', changed_by='".sql_escape(intval($usr['userid']))."', changed_date='".sql_escape(date("Y-m-d H:i:s"))."' WHERE id='".sql_escape(intval($_REQUEST['reportid']))."'";
			@mysql_query($sql);
			tpl_set_var('confirm_status_change', "<b><font color='green'>Zmieniono status zgłoszenia na ".writeStatus($_POST['statusSel']).".</font></b>");
			if( $_POST['statusSel'] == 3 )
			{
				// jezeli zmieniono status na "zajrzyj tu!", nastepuje rozeslanie maili do rr
				$sql = "SELECT reports.cache_id as cache_id, reports.`type` as `type`, caches.cache_id, caches.name as name FROM reports, caches WHERE reports.id = '".sql_escape(intval($_REQUEST['reportid']))."' AND reports.cache_id = caches.cache_id";
				$query = mysql_query($sql) or die("DB Error. Bad report id (well... probably).");
			
				$report = mysql_fetch_array($query);
				
				$email_content = $usr['username']." prosi, żebyś zajrzał do zgłoszenia problemu http://www.opencaching.pl/viewreport.php?reportid=".intval($_REQUEST['reportid'])." - ".$report['name']." (".writeReason($report['type']).").";
			
			$email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
			$email_headers .= "From: Opencaching.pl <rr@opencaching.pl>\r\n";
			$email_headers .= "Reply-To: rr@opencaching.pl\r\n";

			//send email to rr
			mb_send_mail("rr@opencaching.pl", "[OC PL] Dot. skrzynki: ".$report['name'], $email_content, $email_headers);
			}
		}
		if( isset($_POST['note']) && isset($_REQUEST['reportid']) )
		{
			$sql = "SELECT responsible_id FROM reports WHERE id ='".sql_escape(intval($_REQUEST['reportid']))."'";
			
			$res = mysql_query($sql);
			if( mysql_num_rows($res) > 0 )
			{
				$responsible_id = mysql_result($res,0);
				if( $responsible_id == "" )
				{
					$sql2 = "UPDATE reports SET responsible_id = ".sql_escape($usr['userid'])." WHERE id = '".sql_escape(intval($_REQUEST['reportid']))."'";
					@mysql_query($sql2);
				}
			}
			$note = strip_tags(sql_escape(($_POST['note'])));
			if( $note != "" )
			{
				$sql = "UPDATE reports SET note=CONCAT('[".sql_escape(date("Y-m-d H:i:s"))."] <b>".sql_escape($usr['username'])."</b>: ".$note."<br />', note), changed_by='".sql_escape(intval($usr['userid']))."', changed_date='".sql_escape(date("Y-m-d H:i:s"))."' WHERE id='".sql_escape(intval($_REQUEST['reportid']))."'";
				@mysql_query($sql);
				$saved = "<b><font color='green'>Notatka została zapisana.</font></b>";
			}
		}

		$sql = "SELECT cache_status.id as cs_id, cache_status.pl as cache_status, reports.id as report_id, reports.user_id as user_id, reports.note as note, reports.changed_by as changed_by, reports.changed_date as changed_date, reports.cache_id as cache_id, reports.type as type, reports.text as text, reports.submit_date as submit_date, reports.responsible_id as responsible_id, reports.status as status, user.username as username, user.user_id as user_id, caches.name as cachename, caches.status AS c_status FROM cache_status, reports, user, caches WHERE cache_status.id = caches.status AND reports.id = '".sql_escape(intval($_REQUEST['reportid']))."'AND user.user_id = reports.user_id AND caches.cache_id = reports.cache_id ORDER BY submit_date ASC";
		$query = mysql_query($sql) or die("DB Error. Bad report id (well... probably).");
		if( mysql_num_rows($query) > 0 )
		{
			$report = mysql_fetch_array($query);
			
			$username_sql = "SELECT username FROM users WHERE user_id='".sql_escape($report['user_id'])."'";
			$username_query = mysql_query($sql) or die("DB error");
			$username = mysql_result($username_query,0);
			
			$admins_sql = "SELECT user_id, username FROM user WHERE admin=1";
			$admins_query = mysql_query($admins_sql);
			
			$content .= "<tr>";
			
			$content .= "<td>".$report['report_id']."</td>";			
			$content .= "<td>".$report['submit_date']."</td>";
			$content .= "<td><a href='viewcache.php?cacheid=".$report['cache_id']."'>".nonEmptyCacheName($report['cachename'])."</a></td>";
			$content .= "<td>".colorCacheStatus($report['cache_status'], $report['c_status'])."</td>";
			$content .= "<td>".writeReason($report['type'])."</td>";
			$content .= "<td><a href='viewprofile.php?userid=".$report['user_id']."'>".$report['username']."</a></td>";
			//$content .= "<td><a href='viewprofile.php?userid=".$report['responsible_id']."'>".getUsername($report['responsible_id'])."</a></td>";
			
			$content .= "<td>";
			$content .= "<form action='viewreport.php' method='post'><select name='respSel'>";
			$content .= "<option value='0'>brak</option>";
			$selected = "";
			while( $admins = mysql_fetch_array($admins_query) )
			{
				if( $report['responsible_id'] == $admins['user_id'] )
				{
					$selected = "selected";
				}
				else
					$selected = "";
				$content .= "<option value='".$admins['user_id']."' $selected>".$admins['username']."</option>";
			}
			$content .= "</select><br /><input type='submit' name='new_resp' value='Zmień'>";
			$content .= "</td>";
			
			$content .= "<td>";
			$content .= "<select name='statusSel'>";
			for( $i=0;$i<4;$i++)
			{
				if( $report['status']==$i )
				{
					$selected = "selected";
				}
				else
				{
					$selected = "";
				}
				$content .= "<option value='".$i."' $selected>".writeStatus($i)."</option>";
			
			}
			
			$content .= "</select><br /><input type='hidden' name='reportid' value='".$report['report_id']."'><input type='submit' name='new_status' value='Zmień'></form>";
			
			$content .= "</td>";
			$content .= "<td>".($report['changed_by']=='0'?'':(getUsername($report['changed_by']).'<br/><font size=\"1\">('.($report['changed_date']).')</font>'))."</td>\n";
			$content .= "</tr>\n";
			
			tpl_set_var('content', $content);
			tpl_set_var('report_text_lbl', 'Treść zgłoszenia');
			tpl_set_var('report_text', strip_tags($report['text']));
			tpl_set_var('perform_action_lbl', 'Podejmij działania');
			
			if( !isset($_GET['mailto']))
			{
				$active_form = "<form action='viewreport.php' method='POST'><input type='hidden' name='reportid' value='".intval($_REQUEST['reportid'])."'><textarea name='note' cols='80' rows='5'></textarea><br /><input type='submit' value='Zapisz'></form>&nbsp;".$saved;
				tpl_set_var('note_lbl', "Notatka");
			}
			else
			{
				// display email form
				tpl_set_var('note_lbl', "Wyślij e-mail do ".writeRe($_REQUEST['mailto']));
				$active_form = $email_form;
			}
			tpl_set_var('note_area', nl2br($report['note']));
			tpl_set_var('active_form', $active_form);
			
			$actions = '';
			//$actions .= "<li><a href='voting.php?reportid=".$report['report_id']."'>Zarządź głosowanie</a></li>";
			for( $i=0;$i<3;$i++)
				$mail_actions .= "<li><a href='viewreport.php?reportid=".$report['report_id']."&amp;mailto=$i'>Wyślij e-mail do ".writeRe($i)."</a></li>";
			
			//$actions .= "<br /><li><a href='viewreport.php?reportid=".$report['report_id']."&amp;delete=1'>usuń zgłoszenie</a></li>";
			
			
			tpl_set_var('reportid', $report['report_id']);
			tpl_set_var('actions', $actions);
			tpl_set_var('mail_actions', $mail_actions);
			tpl_set_var('email_form', $email_form);
			tpl_set_var('email_sent', $email_sent);
		}
		else
			$tplname = 'viewreport_notfound';
	}
	else
		$tplname = 'viewreports_error';
	tpl_BuildTemplate();
	
?>
