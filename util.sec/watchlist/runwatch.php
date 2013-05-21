<?php

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


		// watch email translating. Now temporary changes for diagnose/test/debug only.
			if (file_exists(dirname(__FILE__)."/../../lib/common.inc.php")) {
        // require_once (dirname(__FILE__)."/../../lib/settings.inc.php");
        require_once (dirname(__FILE__)."/../../lib/common.inc.php");
				$mailbody = 'tlumaczenie logType1: '.tr('logType1');
			} else {
				$mailbody = 'nie zadzialalo';
			}
			$email_headers = 'From: "runwatch.php" <' . $mailfrom . '>';
			mb_send_mail('wloczynutka@gmail.com', 'runwatch.php', $mailbody, $email_headers);
		// end test watch email translating 


/* begin with some constants */

	$sDateformat = 'Y-m-d H:i:s';

/* end with some constants */

// Check if another instance of the script is running
	$lock_file = fopen("/tmp/watchlist-runwatch.lock", "w");
	if (!flock($lock_file, LOCK_EX | LOCK_NB))
	{
		// Another instance of the script is running - exit
		echo "Another instance of runwatch.php is currently running.\nExiting.\n";
		fclose($lock_file);
		exit;
	}

// No other instance - do normal processing

/* begin db connect */
	db_connect();
	if ($dblink === false)
	{
		echo 'Unable to connect to database';
		exit;
	}
/* end db connect */
  
	$diag_log_file = fopen("/var/log/ocpl/runwatch.log", "a");
	$diag_start_time = microtime(true);
	fprintf($diag_log_file, "start;%s\n", date("Y-m-d H:i:s"));

/* begin owner notifies and cache watches */
  $rsNewLogs = sql("SELECT cache_logs.id log_id, caches.user_id user_id, cache_logs.cache_id cache_id FROM cache_logs, caches WHERE cache_logs.deleted=0 AND cache_logs.cache_id=caches.cache_id AND cache_logs.owner_notified=0");
  for ($i = 0; $i < mysql_num_rows($rsNewLogs); $i++)
  {
		$rNewLog = sql_fetch_array($rsNewLogs);
		$rNewLog_log_id = $rNewLog['log_id'];
		$rNewLog_user_id = $rNewLog['user_id'];
		$rNewLog_cache_id = $rNewLog['cache_id'];
		
		// Notify owner
		$rsNotified = sql("SELECT `id` FROM watches_notified WHERE user_id='&1' AND object_id='&2' AND object_type=1", $rNewLog_user_id, $rNewLog_log_id);
		if (mysql_num_rows($rsNotified) == 0)
		{
			// Benachrichtigung speichern
			sql("INSERT IGNORE INTO `watches_notified` (`user_id`, `object_id`, `object_type`, `date_processed`) VALUES ('&1', '&2', 1, NOW())", $rNewLog_user_id, $rNewLog_log_id);
		
			process_owner_log($rNewLog_user_id, $rNewLog_log_id);
		}
		mysql_free_result($rsNotified);

		// Notify watchers
		$rscw = sql("SELECT user_id FROM cache_watches WHERE cache_id = &1", $rNewLog_cache_id);
		for ($j = 0; $j < mysql_num_rows($rscw); $j++)
		{
			$rcw = sql_fetch_array($rscw);
			$rcw_user_id = $rcw['user_id'];

			// kucken, ob fuer dieses Log schon benachrichtigt wurde
			$rsNotified = sql("SELECT `id` FROM watches_notified WHERE user_id='&1' AND object_id='&2' AND object_type=1", $rcw_user_id, $rNewLog_log_id);
			if (mysql_num_rows($rsNotified) == 0)
			{
				// Benachrichtigung speichern
				sql("INSERT IGNORE INTO `watches_notified` (`user_id`, `object_id`, `object_type`, `date_processed`) VALUES ('&1', '&2', 1, NOW())", $rcw_user_id, $rNewLog_log_id);
				
				process_log_watch($rcw_user_id, $rNewLog_log_id);
			}
			mysql_free_result($rsNotified);
		}
		mysql_free_result($rscw);

		sql("UPDATE cache_logs SET owner_notified=1 WHERE id='&1'", $rNewLog_log_id);
  }
  mysql_free_result($rsNewLogs);
/* end owner notifies and cache watches */

	fprintf($diag_log_file, "after-owner-notifies-cache-watches;%s;%lf\n", date("Y-m-d H:i:s"), microtime(true) - $diag_start_time);
	$diag_start_time = microtime(true);
	
/* begin send out everything that has to be sent */
	
/* First phase - send messages to users who have requested immediate notification */

	$rsWatchesUsers = sql('
		SELECT watches_waiting.id AS id, watches_waiting.watchtext AS watchtext, watches_waiting.watchtype AS watchtype, `user`.user_id AS user_id, `user`.username AS username, `user`.email AS email
		FROM `user`, watches_waiting
		WHERE
			`user`.user_id = watches_waiting.user_id AND
			`user`.watchmail_mode = 1
		ORDER BY watches_waiting.user_id, watches_waiting.id DESC');

	$currUserID = '';
	$currUserName = '';
	$currUserEMail = '';
	$currUserOwnerLogs = '';
	$currUserWatchLogs = '';

	for ($i = 0; $i < mysql_num_rows($rsWatchesUsers); $i++)
	{
		$rWatchUser = sql_fetch_array($rsWatchesUsers);
		if ($currUserID != $rWatchUser['user_id'])
		{
			// Time to send all gathered info for the previous user (if any)
			send_mail_and_clean_watches_waiting($currUserID, $currUserName, $currUserEMail, $currUserOwnerLogs, $currUserWatchLogs);

			// After sending e-mail prepare the stage for the next user
			$currUserID    = $rWatchUser['user_id'];
			$currUserName  = $rWatchUser['username'];
			$currUserEMail = $rWatchUser['email'];
			$currUserOwnerLogs = '';
			$currUserWatchLogs = '';
		}

		if ($rWatchUser['watchtype'] == '1') { $currUserOwnerLogs .= $rWatchUser['watchtext']; }
		if ($rWatchUser['watchtype'] == '2') { $currUserWatchLogs .= $rWatchUser['watchtext']; }
	}
	mysql_free_result($rsWatchesUsers);
	
	// Send all gathered info for the last user (if any)
	send_mail_and_clean_watches_waiting($currUserID, $currUserName, $currUserEMail, $currUserOwnerLogs, $currUserWatchLogs);


/* Second phase - check/send messages to users who have requested daily/weekly notification */

	$rsUsers = sql('SELECT user_id, username, email, watchmail_mode, watchmail_hour, watchmail_day, watchmail_nextmail FROM `user` WHERE watchmail_mode IN (0, 2) AND watchmail_nextmail<NOW()');
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
					$currUserID    = $rUser['user_id'];
					$currUserName  = $rUser['username'];
					$currUserEMail = $rUser['email'];
					$currUserOwnerLogs = '';
					$currUserWatchLogs = '';

					$rsWatchesOwner = sql("SELECT id, watchtext FROM watches_waiting WHERE user_id='&1' AND watchtype=1 ORDER BY id DESC", $rUser['user_id']);
					for ($j = 0; $j < mysql_num_rows($rsWatchesOwner); $j++)
					{
						$rWatch = sql_fetch_array($rsWatchesOwner);
						$currUserOwnerLogs .= $rWatch['watchtext'];
					}
					mysql_free_result($rsWatchesOwner);

					$rsWatchesLog = sql("SELECT id, watchtext FROM watches_waiting WHERE user_id='&1' AND watchtype=2 ORDER BY id DESC", $rUser['user_id']);
					for ($j = 0; $j < mysql_num_rows($rsWatchesLog); $j++)
					{
						$rWatch = sql_fetch_array($rsWatchesLog);
						$currUserWatchLogs .= $rWatch['watchtext'];
					}

					// mail versenden
					send_mail_and_clean_watches_waiting($currUserID, $currUserName, $currUserEMail, $currUserOwnerLogs, $currUserWatchLogs);
				}
			}
		}

		// Zeitpunkt der naechsten Mail berechnen
		if ($rUser['watchmail_mode'] == 0)
			$nextmail = date($sDateformat, mktime($rUser['watchmail_hour'], 0, 0, date('n'), date('j') + 1, date('Y')));
		elseif ($rUser['watchmail_mode'] == 2)
		{
			$weekday = date('w');
			if ($weekday == 0) $weekday = 7;

			if ($weekday >= $rUser['watchmail_day'])
				// We are on or after specified day in the week - next run should be next week
				$nextmail = date($sDateformat, mktime($rUser['watchmail_hour'], 0, 0, date('n'), date('j') - $weekday + $rUser['watchmail_day'] + 7, date('Y')));
			else
				// We are still before specified day in the week - next run should be this week
				$nextmail = date($sDateformat, mktime($rUser['watchmail_hour'], 0, 0, date('n'), date('j') - $weekday + $rUser['watchmail_day'] + 0, date('Y')));
		}

		sql("UPDATE user SET watchmail_nextmail='&1' WHERE user_id='&2'", $nextmail, $rUser['user_id']);
	}
	mysql_free_result($rsUsers);

/* end send out everything that has to be sent */

	fprintf($diag_log_file, "after-send-out;%s;%lf\n", date("Y-m-d H:i:s"), microtime(true) - $diag_start_time);
	$diag_start_time = microtime(true);
	fclose($diag_log_file);

// Release lock
	fclose($lock_file);

function process_owner_log($user_id, $log_id)
{
	global $dblink, $logowner_text;

//	echo "process_owner_log($user_id, $log_id)\n";
	
	$rsLog = sql("SELECT cache_logs.cache_id cache_id, cache_logs.text text, cache_logs.text_html text_html, cache_logs.date logdate, user.username username, caches.wp_oc wp,caches.name cachename, cache_logs.type type,IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended` FROM `cache_logs` LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`, `user`, `caches` WHERE `cache_logs`.`deleted`=0 AND (cache_logs.user_id = user.user_id) AND (cache_logs.cache_id = caches.cache_id) AND (cache_logs.id ='&1')", $log_id);
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
			$logtype =  'znaleziona';
			// $logtype = tr('logType1');
		break;
		case '2':
			$logtype = 'nieznaleziona';
			// $logtype = tr('logType2');
		break;
		case '3':
			$logtype = 'komentarz';
			//$logtype = tr('logType3');
		break;
		case '4':
			$logtype = "skrzynka przeniesiona";
		break;
		case '5':
			$logtype = "potrzebny serwis skrzynki";
		break;
		case '6':
			$logtype = "wskazana archiwizacja skrzynki";
		break;
		case '7':
			$logtype = "uczestniczył w spotkaniu";
		break;
		case '8':
			$logtype = "będzie uczestniczył w spotkaniu";
		break;
		case '9':
			$logtype = "skrzynka została zarchiwizowana";
		break;
		case '10':
			$logtype = "skrzynka gotowa do szukania";
		break;
		case '11':
			$logtype = "skrzynka będzie niedostepna czasowo";
		break;
		case '12':
			$logtype = "komentarz Centrum Obsługi Geocachera";
      $rLog['username'] = 'Centrum Obsługi Geocachera';   
		break;
		default:
			$logtype = "";
	}
	if ($rLog['recommended'] !=0 && $rLog['type']==1) {$recommended=" + rekomendacja";} else {$recommended="";}	
	
	$watchtext = mb_ereg_replace('{date}', date('d.m.Y', strtotime($rLog['logdate'])), $watchtext);
//	$watchtext = mb_ereg_replace('{cacheid}', $rLog['cache_id'], $watchtext);
	$watchtext = mb_ereg_replace('{wp}', $rLog['wp'], $watchtext);
	$watchtext = mb_ereg_replace('{text}', $logtext, $watchtext);
	$watchtext = mb_ereg_replace('{user}', $rLog['username'], $watchtext);
	$watchtext = mb_ereg_replace('{logtype}', $logtype.$recommended, $watchtext);
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
	
	$rsLog = sql("SELECT cache_logs.cache_id cache_id, cache_logs.text text, cache_logs.text_html text_html, cache_logs.date logdate, user.username username, caches.wp_oc wp,caches.name cachename, cache_logs.type type, IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended` FROM `cache_logs` LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`, `user`, `caches` WHERE `cache_logs`.`deleted`=0 AND (cache_logs.user_id = user.user_id) AND (cache_logs.cache_id = caches.cache_id) AND (cache_logs.id = '&1')", $log_id);
	$rLog = sql_fetch_array($rsLog);
	mysql_free_result($rsLog);
	
	switch( $rLog['type'] )
	{
		case '1':
			$logtype = "znaleziona";
		break;
		case '2':
			$logtype = "nieznaleziona";
		break;
		case '3':
			$logtype = "komentarz";
		break;
		case '4':
			$logtype = "skrzynka przeniesiona";
		break;
		case '5':
			$logtype = "potrzebny serwis skrzynki";
		break;
		case '6':
			$logtype = "wskazana archiwizacja skrzynki";
		break;
		case '7':
			$logtype = "uczestniczył w spotkaniu";
		break;
		case '8':
			$logtype = "będzie uczestniczył w spotkaniu";
		break;
		case '9':
			$logtype = "skrzynka została zarchiwizowana";
		break;
		case '10':
			$logtype = "skrzynka gotowa do szukania";
		break;
		case '11':
			$logtype = "skrzynka będzie niedostepna czasowo";
		break;
		case '12':
			$logtype = "komentarz Centrum Obsługi Geocachera";
      $rLog['username'] = "Centrum Obsługi Geocachera";
		break;
		default:
			$logtype = "";
	}
	if ($rLog['recommended'] !=0 && $rLog['type']==1)  {$recommended=" + rekomendacja";} else {$recommended="";}
	
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
//	$watchtext = mb_ereg_replace('{cacheid}', $rLog['cache_id'], $watchtext);
	$watchtext = mb_ereg_replace('{wp}', $rLog['wp'], $watchtext);
	$watchtext = mb_ereg_replace('{text}', $logtext, $watchtext);
	$watchtext = mb_ereg_replace('{user}', $rLog['username'], $watchtext);
	$watchtext = mb_ereg_replace('{logtype}', $logtype.$recommended, $watchtext);
	$watchtext = mb_ereg_replace('{cachename}', $rLog['cachename'], $watchtext);
	
	sql("INSERT IGNORE INTO watches_waiting (`user_id`, `object_id`, `object_type`, `date_added`, `watchtext`, `watchtype`) VALUES (
																		'&1', '&2', 1, NOW(), '&3', 2)", $user_id, $log_id, $watchtext);
}

function send_mail_and_clean_watches_waiting($currUserID, $currUserName, $currUserEMail, $currUserOwnerLogs, $currUserWatchLogs)
{
	global $nologs, $debug, $debug_mailto, $mailfrom, $mailsubject;

	if ($currUserID == '') return;

	$email_headers = 'From: "' . $mailfrom . '" <' . $mailfrom . '>';

	$mailbody = read_file('watchlist.email');
	$mailbody = mb_ereg_replace('{username}', $currUserName, $mailbody);

	if ($currUserOwnerLogs != '')
	{
		$logtexts = $currUserOwnerLogs;
		
		while ((mb_substr($logtexts, -1) == "\n") || (mb_substr($logtexts, -1) == "\r"))
			$logtexts = mb_substr($logtexts, 0, mb_strlen($logtexts) - 1);
	
		$mailbody = mb_ereg_replace('{ownerlogs}', $logtexts, $mailbody);
	}
	else
	{
		$mailbody = mb_ereg_replace('{ownerlogs}', $nologs, $mailbody);
	}

	if ($currUserWatchLogs != '')
	{
		$logtexts = $currUserWatchLogs;

		while ((mb_substr($logtexts, -1) == "\n") || (mb_substr($logtexts, -1) == "\r"))
			$logtexts = mb_substr($logtexts, 0, mb_strlen($logtexts) - 1);

		$mailbody = mb_ereg_replace('{watchlogs}', $logtexts, $mailbody);
	}
	else
	{
		$mailbody = mb_ereg_replace('{watchlogs}', $nologs, $mailbody);
	}

	// mail versenden
	if ($debug == true)
		$mailadr = $debug_mailto;
	else
		$mailadr = $currUserEMail;

	mb_send_mail($mailadr, $mailsubject, $mailbody, $email_headers);

	// logentry($module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details)
	logentry('watchlist', 2, $currUserID, 0, 0, 'Sending mail to ' . $mailadr, array());

	// entries entfernen
	sql("DELETE FROM watches_waiting WHERE user_id='&1' AND watchtype IN (1, 2)", $currUserID);

}

?>
