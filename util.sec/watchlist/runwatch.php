<?php
 /***************************************************************************
													./util/watchlist/runwatch.php
															-------------------
		begin                : Sat September 3 2005
		copyright            : (C) 2005 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

 /***************************************************************************
		
		Unicode Reminder ăĄă˘

		Ggf. muss die Location des php-Binaries angepasst werden.
		
		Dieses Script sucht nach neuen Logs und Caches, die von Usern beobachtet
		werden und verschickt dann die Emails.
		
	***************************************************************************/

	$rootpath = '../../';
	require_once($rootpath . 'lib/clicompatbase.inc.php');
	require_once('settings.inc.php');
	require_once($rootpath . 'lib/consts.inc.php');

/* begin with some constants */

	$sDateformat = 'Y-m-d H:i:s';

/* end with some constants */

/* begin db connect */
	db_connect();
	if ($dblink === false)
	{
		echo 'Unable to connect to database';
		exit;
	}
/* end db connect */
  
/* begin owner notifies */
  $rsNewLogs = sql("SELECT cache_logs.id log_id, caches.user_id user_id FROM cache_logs, caches WHERE cache_logs.deleted=0 AND cache_logs.cache_id=caches.cache_id AND cache_logs.owner_notified=0");
  for ($i = 0; $i < mysql_num_rows($rsNewLogs); $i++)
  {
		$rNewLog = sql_fetch_array($rsNewLogs);
		
		$rsNotified = sql("SELECT `id` FROM watches_notified WHERE user_id='&1' AND object_id='&2' AND object_type=1", $rNewLog['user_id'], $rNewLog['log_id']);
		if (mysql_num_rows($rsNotified) == 0)
		{
			// Benachrichtigung speichern
			sql("INSERT IGNORE INTO `watches_notified` (`user_id`, `object_id`, `object_type`, `date_processed`) VALUES ('&1', '&2', 1, NOW())", $rNewLog['user_id'], $rNewLog['log_id']);
		
			process_owner_log($rNewLog['user_id'], $rNewLog['log_id']);
		}
		mysql_free_result($rsNotified);
		
		sql("UPDATE cache_logs SET owner_notified=1 WHERE id='&1'", $rNewLog['log_id']);
  }
  mysql_free_result($rsNewLogs);
/* end owner notifies */

/* begin cache_watches */
  $rscw = sql("SELECT * FROM cache_watches");
  for ($i = 0; $i < mysql_num_rows($rscw); $i++)
  {
		$rcw = sql_fetch_array($rscw);
	
		$rsLogs = sql("SELECT * FROM cache_logs WHERE deleted=0 AND cache_id='&1' AND date_created > '&2'", $rcw['cache_id'], date($sDateformat, strtotime($rcw['last_executed'])));
		for ($j = 0; $j < mysql_num_rows($rsLogs); $j++)
		{
			$rLog = sql_fetch_array($rsLogs);
			
			// kucken, ob fĂźr dieses Log schon benachrichtigt wurde
			$rsNotified = sql("SELECT `id` FROM watches_notified WHERE user_id='&1' AND object_id='&1' AND object_type=1", $rcw['user_id'], $rLog['id']);
			if (mysql_num_rows($rsNotified) == 0)
			{
				// Benachrichtigung speichern
				sql("INSERT IGNORE INTO `watches_notified` (`user_id`, `object_id`, `object_type`, `date_processed`) VALUES ('&1', '&2', 1, NOW())", $rcw['user_id'], $rLog['id']);
				
				process_log_watch($rcw['user_id'], $rLog['id']);
			}
			mysql_free_result($rsNotified);
		}
		mysql_free_result($rsLogs);

		sql("UPDATE cache_watches SET last_executed=NOW() WHERE cache_id='&1' AND user_id='&2'", $rcw['cache_id'], $rcw['user_id']);
  }
  mysql_free_result($rscw);
/* end cache_watches */

/* begin send out everything that has to be sent */
	
	$email_headers = 'From: "' . $mailfrom . '" <' . $mailfrom . '>';
	
	$rsUsers = sql('SELECT user_id, username, email, watchmail_mode, watchmail_hour, watchmail_day, watchmail_nextmail FROM `user` WHERE watchmail_nextmail<NOW()');
	for ($i = 0; $i < mysql_num_rows($rsUsers); $i++)
	{
		$rUser = sql_fetch_array($rsUsers);

		if ($rUser['watchmail_nextmail'] != '0000-00-00 00:00:00')
		{
			$rsWatches = sql("SELECT COUNT(*) count FROM watches_waiting WHERE user_id='&1'", $rUser['user_id']);
			if (mysql_num_rows($rsWatches) > 0)
			{
				$r = sql_fetch_array($rsWatches);
				if ($r['count'] > 0)
				{
					// ok, eine mail ist fĂ¤llig
					$mailbody = read_file('watchlist.email');
					$mailbody = mb_ereg_replace('{username}', $rUser['username'], $mailbody);

					$rsWatchesOwner = sql("SELECT id, watchtext FROM watches_waiting WHERE user_id='&1' AND watchtype=1 ORDER BY id DESC", $rUser['user_id']);
					if (mysql_num_rows($rsWatchesOwner) > 0)
					{
						$logtexts = '';
						for ($j = 0; $j < mysql_num_rows($rsWatchesOwner); $j++)
						{
							$rWatch = sql_fetch_array($rsWatchesOwner);
							$logtexts .= $rWatch['watchtext'];
						}
						
						while ((mb_substr($logtexts, -1) == "\n") || (mb_substr($logtexts, -1) == "\r"))
							$logtexts = mb_substr($logtexts, 0, mb_strlen($logtexts) - 1);
						
						$mailbody = mb_ereg_replace('{ownerlogs}', $logtexts, $mailbody);
					}
					else
					{
						$mailbody = mb_ereg_replace('{ownerlogs}', $nologs, $mailbody);
					}
					mysql_free_result($rsWatchesOwner);
					
					$rsWatchesLog = sql("SELECT id, watchtext FROM watches_waiting WHERE user_id='&1' AND watchtype=2 ORDER BY id DESC", $rUser['user_id']);
					if (mysql_num_rows($rsWatchesLog) > 0)
					{
						$logtexts = '';
						for ($j = 0; $j < mysql_num_rows($rsWatchesLog); $j++)
						{
							$rWatch = sql_fetch_array($rsWatchesLog);
							$logtexts .= $rWatch['watchtext'];
						}
						
						while ((mb_substr($logtexts, -1) == "\n") || (mb_substr($logtexts, -1) == "\r"))
							$logtexts = mb_substr($logtexts, 0, mb_strlen($logtexts) - 1);
						
						$mailbody = mb_ereg_replace('{watchlogs}', $logtexts, $mailbody);
					}
					else
					{
						$mailbody = mb_ereg_replace('{watchlogs}', $nologs, $mailbody);
					}
					mysql_free_result($rsWatchesLog);
					
					// mail versenden
					if ($debug == true)
						$mailadr = $debug_mailto;
					else
						$mailadr = $rUser['email'];

					mb_send_mail($mailadr, $mailsubject, $mailbody, $email_headers);
					
					// logentry($module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details)																
					logentry('watchlist', 2, $rUser['user_id'], 0, 0, 'Sending mail to ' . $mailadr, array());

					// entries entfernen
					sql("DELETE FROM watches_waiting WHERE user_id='&1' AND watchtype IN (1, 2)", $rUser['user_id']);
				}
			}
		}
			
		// Zeitpunkt der nĂ¤chsten Mail berechnen
		if ($rUser['watchmail_mode'] == 1)
			$nextmail = date($sDateformat);
		elseif ($rUser['watchmail_mode'] == 0)
			$nextmail = date($sDateformat, mktime($rUser['watchmail_hour'], 0, 0, date('n'), date('j') + 1, date('Y')));
		elseif ($rUser['watchmail_mode'] == 2)
		{
			$weekday = date('w');
			if ($weekday == 0) $weekday = 7;

			if ($weekday == $rUser['watchmail_day'])
				$nextmail = date($sDateformat, mktime($rUser['watchmail_hour'], 0, 0, date('n'), date('j') + 7, date('Y')));
			elseif ($weekday > $rUser['watchmail_day'])
				$nextmail = date($sDateformat, mktime($rUser['watchmail_hour'], 0, 0, date('n'), date('j') - $weekday + $rUser['watchmail_day'] + 7, date('Y')));
			else
				$nextmail = date($sDateformat, mktime($rUser['watchmail_hour'], 0, 0, date('n'), date('j') + 6 - $rUser['watchmail_day'], date('Y')));
		}

		sql("UPDATE user SET watchmail_nextmail='&1' WHERE user_id='&2'", $nextmail, $rUser['user_id']);
	}
	mysql_free_result($rsUsers);

/* end send out everything that has to be sent */
 
function process_owner_log($user_id, $log_id)
{
	global $dblink, $logowner_text;

//	echo "process_owner_log($user_id, $log_id)\n";
	
	$rsLog = sql("SELECT cache_logs.cache_id cache_id, cache_logs.text text, cache_logs.text_html text_html, cache_logs.date logdate, user.username username, caches.name cachename, cache_logs.type type FROM `cache_logs`, `user`, `caches` WHERE `cache_logs`.`deleted`=0 AND (cache_logs.user_id = user.user_id) AND (cache_logs.cache_id = caches.cache_id) AND (cache_logs.id ='&1')", $log_id);
	$rLog = sql_fetch_array($rsLog);
	mysql_free_result($rsLog);
	
	$watchtext = $logowner_text;
	$logtext = $rLog['text'];
	if ($rLog['text_html'] != 0){
		$logtext = html_entity_decode($logtext, ENT_COMPAT, 'UTF-8');
		$logtext = mb_ereg_replace("\r", '', $logtext);
		$logtext = mb_ereg_replace("\n", '', $logtext);
		$logtext = mb_ereg_replace('</p>', "</p>\n", $logtext);
		$logtext = mb_ereg_replace('<br/>', "<br/>\n", $logtext);
		$logtext = mb_ereg_replace('<br />', "<br />\n", $logtext);
		$logtext = strip_tags($logtext);
	}
	
	switch( $rLog['type'] )
	{
		case '1':
			$logtype = "znalezienie";
		break;
		case '2':
			$logtype = "nieznalezienie";
		break;
		case '3':
			$logtype = "komentarz";
		break;
		case '7':
			$logtype = "uczestniczył w spotkaniu";
		break;
		case '8':
			$logtype = "będzie uczestniczył w spotkaniu";
		break;
		default:
			$logtype = "";
	}
	
	$watchtext = mb_ereg_replace('{date}', date('d.m.Y', strtotime($rLog['logdate'])), $watchtext);
	$watchtext = mb_ereg_replace('{cacheid}', $rLog['cache_id'], $watchtext);
	$watchtext = mb_ereg_replace('{{text}}', $logtext, $watchtext);
	$watchtext = mb_ereg_replace('{{user}}', $rLog['username'], $watchtext);
	$watchtext = mb_ereg_replace('{logtype}', $logtype, $watchtext);
	$watchtext = mb_ereg_replace('{cachename}', $rLog['cachename'], $watchtext);
	
	sql("INSERT IGNORE INTO watches_waiting (`user_id`, `object_id`, `object_type`, `date_added`, `watchtext`, `watchtype`) VALUES (
																		'&1', '&2', 1, NOW(), '&3', 1)", $user_id, $log_id, $watchtext);
	
	// logentry($module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details)																
	logentry('watchlist', 1, $user_id, $log_id, 0, $watchtext, array());
}

function process_log_watch($user_id, $log_id)
{
	global $dblink, $logwatch_text;

//	echo "process_log_watch($user_id, $log_id)\n";
	
	$rsLog = sql("SELECT cache_logs.cache_id cache_id, cache_logs.text text, cache_logs.text_html text_html, cache_logs.date logdate, user.username username, caches.name cachename, cache_logs.type type FROM `cache_logs`, `user`, `caches` WHERE `cache_logs`.`deleted`=0 AND (cache_logs.user_id = user.user_id) AND (cache_logs.cache_id = caches.cache_id) AND (cache_logs.id = '&1')", $log_id);
	$rLog = sql_fetch_array($rsLog);
	mysql_free_result($rsLog);
	
	switch( $rLog['type'] )
	{
		case '1':
			$logtype = "znalezienie";
		break;
		case '2':
			$logtype = "nieznalezienie";
		break;
		case '3':
			$logtype = "komentarz";
		break;
		case '7':
			$logtype = "uczestniczył w spotkaniu";
		break;
		case '8':
			$logtype = "będzie uczestniczył w spotkaniu";
		break;
		default:
			$logtype = "";
	}

	$watchtext = $logwatch_text;
	$logtext = $rLog['text'];
	if ($rLog['text_html'] != 0){
		$logtext = html_entity_decode($logtext, ENT_COMPAT, 'UTF-8');
		$logtext = mb_ereg_replace("\r", '', $logtext);
		$logtext = mb_ereg_replace("\n", '', $logtext);
		$logtext = mb_ereg_replace('</p>', "</p>\n", $logtext);
		$logtext = mb_ereg_replace('<br/>', "<br/>\n", $logtext);
		$logtext = mb_ereg_replace('<br />', "<br />\n", $logtext);
		$logtext = strip_tags($logtext);
	}
	
	$watchtext = mb_ereg_replace('{date}', date('d.m.Y', strtotime($rLog['logdate'])), $watchtext);
	$watchtext = mb_ereg_replace('{cacheid}', $rLog['cache_id'], $watchtext);
	$watchtext = mb_ereg_replace('{text}', $logtext, $watchtext);
	$watchtext = mb_ereg_replace('{user}', $rLog['username'], $watchtext);
	$watchtext = mb_ereg_replace('{logtype}', $logtype, $watchtext);
	$watchtext = mb_ereg_replace('{cachename}', $rLog['cachename'], $watchtext);
	
	sql("INSERT IGNORE INTO watches_waiting (`user_id`, `object_id`, `object_type`, `date_added`, `watchtext`, `watchtype`) VALUES (
																		'&1', '&2', 1, NOW(), '&3', 2)", $user_id, $log_id, $watchtext);
}
?>
