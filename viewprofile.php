<?php
/***************************************************************************
																./viewprofile.php
															-------------------
		begin                : August 21 2004
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder ăĄă˘

	 view the profile of an other user

 ****************************************************************************/

	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	
 function type_found($userid, $logtype, $lang, $language)
 {
	$logtype = intval($logtype);
	$userid = intval($userid);
	$retval = "";
	
	$types_sql = "SELECT id, pl, en, icon_large FROM cache_type";
	$types_query = mysql_query($types_sql);
	while( $types = mysql_fetch_array($types_query) )
	{
		$sql = "SELECT count(*)
						FROM cache_logs, caches 	
						WHERE cache_logs.`deleted`=0 AND cache_logs.user_id='$userid' AND cache_logs.`type`='$logtype' AND cache_logs.`cache_id` = caches.cache_id AND caches.`type` = '".intval($types['id'])."'";
						
		$founds = mysql_result(mysql_query($sql),0);
		if( $founds > 0 ) {
			$cache_type = '0000000000';
			$index = $types['id'] - 1;
			$cache_type[$index] = '1';
			$retval .= "
			<tr>
				<td class='content-title-noshade'>
					".$types[$lang]."
				</td>
				<td class='content-title-noshade'>".$founds."&nbsp;
					<span style=\"color: rgb(102, 102, 102); font-size: 10px;\">
				  (<a href=\"search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=byname&amp;finderid=$userid&amp;searchbyfinder=&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;cachetype=".$cache_type."&amp;logtype=$logtype\">".$language[$lang]['show']."</a>)
				  </span>
				</td>
			</tr>";
		}
	}
	return $retval;
 }
 
 function type_hidden($userid, $lang, $language)
 {
	$retval = "";
	
	$types_sql = "SELECT id, pl, en, icon_large FROM cache_type";
	$types_query = mysql_query($types_sql);
	while( $types = mysql_fetch_array($types_query) )
	{
		$sql = "SELECT count(*)
						FROM  caches 	
						WHERE caches.user_id='$userid'  AND caches.`type` = '".intval($types['id'])."'  AND caches.status <> 5";
						
		$hiddens = mysql_result(mysql_query($sql),0);
		if( $hiddens > 0 ) {
			$cache_type = '0000000000';
			$index = $types['id'] - 1;
			$cache_type[$index] = '1';
			$retval .= "<tr><td class='content-title-noshade'>".$types[$lang]."</td><td class='content-title-noshade'>".$hiddens."&nbsp;	
			<span style=\"color: rgb(102, 102, 102); font-size: 10px;\">
				  (<a href=\"search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=byname&amp;ownerid=$userid&amp;searchbyowner=&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;cachetype=".$cache_type."&amp;logtype=$logtype\">".$language[$lang]['show']."</a>)
				  </span></td></tr>";
		}
	}
	return $retval;
 }


	//Preprocessing
	if ($error == false)
	{
		require($stylepath . '/viewprofile.inc.php');
		require($stylepath . '/myprofile.inc.php');
		require($stylepath . '/lib/icons.inc.php');

		$tplname = 'viewprofile';

		$userid = intval(isset($_REQUEST['userid']) ? $_REQUEST['userid']+0 : 0);
		if( $_GET['stat_ban'] == 1 && $usr['admin'] )
		{
			$sql = "UPDATE user SET stat_ban = 1 - stat_ban WHERE user_id = ".intval($userid);
			mysql_query($sql);
		}
		if($_GET['hide_flag'] == 1 && $usr['admin'] )
		{
			$sql = "UPDATE user SET hide_flag = 1 - hide_flag WHERE user_id = ".intval($userid);
			mysql_query($sql);
		}		
		tpl_set_var('user_profile', $language[$lang]['user_profile']);
		tpl_set_var('email_user', $language[$lang]['email_user']);
		tpl_set_var('show_user_map', $language[$lang]['show_user_map']);
		tpl_set_var('user_desc', $language[$lang]['user_desc']);
		tpl_set_var('profile_data', $language[$lang]['profile_data']);
		tpl_set_var('country_label', $language[$lang]['country_label']);
		tpl_set_var('registered_since_label', $language[$lang]['registered_since_label']);
		tpl_set_var('statpic_label', $language[$lang]['statpic_label']);
		tpl_set_var('hidden_caches', $language[$lang]['hidden_caches']);
		tpl_set_var('found_caches', $language[$lang]['found_caches']);
		tpl_set_var('not_found_caches', $language[$lang]['not_found_caches']);
		tpl_set_var('show_all', $language[$lang]['show_all']);
		tpl_set_var('my_recommendations', $language[$lang]['my_recommendations']);
		tpl_set_var('out_of', $language[$lang]['out_of']);
		tpl_set_var('show', $language[$lang]['show']);
		tpl_set_var('statistics', $language[$lang]['statistics']);
		
		$days_since_first_find = @mysql_result(@mysql_query("SELECT datediff(now(), date) as old FROM cache_logs WHERE deleted=0 AND user_id = $userid AND type=1 ORDER BY date LIMIT 1"),0);
		$days_went_caching;
		$days_no_caching;
		$obsession_indicator;
		$hide_to_find;
		$caching_karma;
		$verbosity;
		$total_dist_attempted_caches;
		$median_dist_attempted_caches;
		$average_dist_attempted_caches;
		$total_dist_hidden_caches;
		$median_dist_hidden_caches;
		$average_dist_hidden_caches;

		
		tpl_set_var('days_since_first_find_label', $language[$lang]['days_since_first_find_label']);
		tpl_set_var('days_since_first_find', $days_since_first_find);
		tpl_set_var('days_went_caching_label', $language[$lang]['days_went_caching_label']);
		tpl_set_var('days_went_caching', $days_went_caching);
		tpl_set_var('days_no_caching_label', $language[$lang]['days_no_caching_label']);
		tpl_set_var('days_no_caching', $days_no_caching);
		tpl_set_var('obsession_indicator_label', $language[$lang]['obsession_indicator_label']);
		tpl_set_var('obsession_indicator', $obsession_indicator);
		tpl_set_var('hide_to_find_label', $language[$lang]['hide_to_find_label']);
		tpl_set_var('hide_to_find', $hide_to_find);
		tpl_set_var('caching_karma_label', $language[$lang]['caching_karma_label']);
		tpl_set_var('caching_karma', $caching_karma);
		tpl_set_var('verbosity_label', $language[$lang]['verbosity_label']);
		tpl_set_var('verbosity', $verbosity);
		tpl_set_var('total_dist_attempted_caches_label', $language[$lang]['total_dist_attempted_caches_label']);
		tpl_set_var('total_dist_attempted_caches', $total_dist_attempted_caches);
		tpl_set_var('median_dist_attempted_caches_label', $language[$lang]['median_dist_attempted_caches_label']);
		tpl_set_var('median_dist_attempted_caches', $median_dist_attempted_caches);
		tpl_set_var('average_dist_attempted_caches_label', $language[$lang]['average_dist_attempted_caches_label']);
		tpl_set_var('average_dist_attempted_caches', $average_dist_attempted_caches);
		tpl_set_var('total_dist_hidden_caches_label', $language[$lang]['total_dist_hidden_caches_label']);
		tpl_set_var('total_dist_hidden_caches', $total_dist_hidden_caches);
		tpl_set_var('median_dist_hidden_caches_label', $language[$lang]['median_dist_hidden_caches_label']);
		tpl_set_var('median_dist_hidden_caches', $median_dist_hidden_caches);
		tpl_set_var('average_dist_hidden_caches_label', $language[$lang]['average_dist_hidden_caches_label']);
		tpl_set_var('average_dist_hidden_caches', $average_dist_hidden_caches);

		$rs = sql("SELECT `user`.`username`, `user`.`stat_ban`, `user`.`email`, `user`.`pmr_flag`, `user`.`date_created`, `user`.`latitude`, `user`.`longitude`, `countries`.`pl` AS `country`, `user`.`hidden_count`, `user`.`founds_count`, `user`.`uuid` FROM `user` LEFT JOIN `countries` ON (`user`.`country`=`countries`.`short`) WHERE `user`.`user_id`='&1'", $userid);

		if (mysql_num_rows($rs) == 0)
		{
			$tplname = 'error';
			tpl_set_var('tplname', 'viewprofile');
			tpl_set_var('error_msg', $err_no_user);
		}
		else
		{
			$sql = "SELECT description FROM user WHERE user_id = ".$userid;
			$description = @mysql_result(@mysql_query($sql),0);
			tpl_set_var('description',nl2br($description));
			
			if( $description != "" )
			{
				tpl_set_var('opis_start', '');
				tpl_set_var('opis_end', '');
			}
			else
			{
				tpl_set_var('opis_start', '<!--');
				tpl_set_var('opis_end', '-->');
			}
			
			$sql = "SELECT COUNT(*) FROM caches WHERE user_id='$userid' AND status <> 5";
			if( $odp = mysql_query($sql) )
				$hidden_count = mysql_result($odp,0);
			else 
				$hidden_count = 0;
			
			$sql = "SELECT COUNT(*) founds_count 
							FROM cache_logs 
							WHERE user_id='$userid' AND type=1 AND deleted=0";
			
			if( $odp = mysql_query($sql) )
				$founds_count = mysql_result($odp,0);
			else 
				$founds_count = 0;
			
			$sql = "SELECT COUNT(*) not_founds_count 
							FROM cache_logs 
							WHERE user_id='$userid' AND type=2 AND deleted=0";
			
			if( $odp = mysql_query($sql) )
				$not_founds_count = mysql_result($odp,0);
			else 
				$not_founds_count = 0;
			
			
			$record = sql_fetch_array($rs);
			tpl_set_var('statlink', $absolute_server_URI.'statpics/' . ($userid+0) . '.jpg');
			tpl_set_var('username', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('type_found', type_found($userid, 1, $lang, $language));
			tpl_set_var('type_notfound', type_found($userid, 2, $lang, $language));
			tpl_set_var('type_hidden', type_hidden($userid, $lang, $language));
			tpl_set_var('hidden', $hidden_count);
			tpl_set_var('userid', htmlspecialchars($userid, ENT_COMPAT, 'UTF-8'));
			tpl_set_var('hidden', $hidden_count);
			tpl_set_var('founds', $founds_count);
			tpl_set_var('not_founds', $not_founds_count);
			tpl_set_var('recommended', sqlValue("SELECT COUNT(*) FROM `cache_rating` WHERE `user_id`='" . sql_escape($_REQUEST['userid']) . "'", 0));
			tpl_set_var('maxrecommended', floor($founds_count * rating_percentage / 100));

			tpl_set_var('country', htmlspecialchars($record['country'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('registered', strftime($dateformat, strtotime($record['date_created'])));
			if( $usr['userid']==$super_admin_id )
			{
				tpl_set_var('remove_all_logs', '<img src="'.$stylepath.'/images/misc/32x32-impressum.png" width="32" height="32" border="0" alt="" title="" align="middle"/>&nbsp;<a href="removelog.php?userid='.$userid.'"><font color="#ff0000">Usuń wszystkie logi tego użytkownika</font></a>');
			}
			else
				tpl_set_var('remove_all_logs', '');
			
			if( $usr['admin'] )
			{
				tpl_set_var('email', '(<a href="mailto:'.strip_tags($record['email']).'">'.strip_tags($record['email']).'</a>)');
				if( !$record['stat_ban'] )
					tpl_set_var('stat_ban', '<img src="'.$stylepath.'/images/misc/32x32-impressum.png" width="32" height="32" border="0" alt="" title="" align="middle"/>&nbsp;<a href="viewprofile.php?userid='.$userid.'&stat_ban=1"><font color="#ff0000">'.$language[$lang]['lock'].' '.$language[$lang]['user_stats'].'</font></a>');
				else
					tpl_set_var('stat_ban', '<img src="'.$stylepath.'/images/misc/32x32-impressum.png" width="32" height="32" border="0" alt="" title="" align="middle"/>&nbsp;<a href="viewprofile.php?userid='.$userid.'&stat_ban=1"><font color="#00ff00">'.$language[$lang]['unlock'].' '.$language[$lang]['user_stats'].'</font></a>');
			}
			else
			{
				tpl_set_var('stat_ban', '');
				tpl_set_var('email', '');
			}
			$options = '';
			if ($record['pmr_flag'] == 1)
			{
				$options .= $using_pmr_message;
			}

			tpl_set_var('options', $options);
			tpl_set_var('uuid', htmlspecialchars($record['uuid'], ENT_COMPAT, 'UTF-8'));
			
			//get last logs
			
			tpl_set_var('user_new_log_entries', $language[$lang]['user_new_log_entries']);
			
			$rs_logs = sql("
					SELECT `cache_logs`.`cache_id` `cache_id`, `cache_logs`.`type` `type`, `cache_logs`.`date` `date`, `caches`.`name` `name`,
						`log_types`.`icon_small`, `log_types_text`.`text_combo`
					FROM `cache_logs`, `caches`, `log_types`, `log_types_text`
					WHERE `cache_logs`.`user_id`='&1'
					AND `cache_logs`.`cache_id`=`caches`.`cache_id`
					AND `cache_logs`.`deleted`=0 
					AND `log_types`.`id`=`cache_logs`.`type`
					AND `log_types_text`.`log_types_id`=`log_types`.`id` AND `log_types_text`.`lang`='&2'
					ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`date_created` DESC
					LIMIT 10", $userid, $lang);

			if (mysql_num_rows($rs_logs) == 0)
			{
				tpl_set_var('lastlogs', $no_logs);
			}
			else
			{
				$logs = '';
				for ($i = 0; $i < mysql_num_rows($rs_logs); $i++)
				{
					$record_logs = sql_fetch_array($rs_logs);

					$tmp_log = $log_line;
					$tmp_log = mb_ereg_replace('{logimage}', icon_log_type($record_logs['icon_small'], $record_logs['text_combo']), $tmp_log);
					//$tmp_log = mb_ereg_replace('{logtype}', $record_logs['text_combo'], $tmp_log);
					$tmp_log = mb_ereg_replace('{date}', strftime($simpledateformat , strtotime($record_logs['date'])), $tmp_log);
					$tmp_log = mb_ereg_replace('{cachename}', htmlspecialchars($record_logs['name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
					$tmp_log = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($record_logs['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_log);

					$logs .= "\n" . $tmp_log;
				}
				tpl_set_var('lastlogs', $logs);
			}
			
			// Umożliwienie zakładania skrzynek dla nowych użytkowników

			if( $usr['admin'] && $block_new_user_caches ) {
				$rs = sql("SELECT `user_id` as data FROM `user` WHERE `date_created` < CURDATE() + INTERVAL -1 MONTH AND `user_id` =  ". sql_escape($userid)."");
				$data = mysql_num_rows($rs);
			
				$rs = sql("SELECT COUNT(`cache_logs`.`id`) as ilosc FROM `cache_logs`, `caches` WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`type` = 1 AND `caches`.`cache_id` = `cache_logs`.`cache_id` AND `caches`.`type` NOT IN(4,5) AND `cache_logs`.`user_id` = ". sql_escape($userid)."");
				$record = sql_fetch_array($rs);
				$ilosc = $record['ilosc'];
			
				if (($data == 0) || ($ilosc < 5)) {
					
					$rs = sql("SELECT `hide_flag` as hide_flag FROM `user` WHERE `user_id` =  ". sql_escape($userid)."");
					$record = sql_fetch_array($rs);
					$hide_flag = $record['hide_flag'];
					
					if ($hide_flag == 0) {
						tpl_set_var('hide_flag', '<img src="'.$stylepath.'/images/misc/32x32-impressum.png" width="32" height="32" border="0" alt="" title="" align="middle"/>&nbsp;<a href="viewprofile.php?userid='.$userid.'&hide_flag=1">Dodaj możliwość zakładania skrzynek dla użytkownika</a>');
					} else {
						tpl_set_var('hide_flag', '<img src="'.$stylepath.'/images/misc/32x32-impressum.png" width="32" height="32" border="0" alt="" title="" align="middle"/>&nbsp;<a href="viewprofile.php?userid='.$userid.'&hide_flag=1">Usuń możliwość zakładania skrzynek dla użytkownika</a>');
					}
	
				} else {
					tpl_set_var('hide_flag', '');
				}
	
			} else {
				tpl_set_var('hide_flag', '');
			}

		}
	}

	tpl_BuildTemplate();
?>