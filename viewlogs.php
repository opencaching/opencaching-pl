<?php
/***************************************************************************
																./viewlogs.php
															-------------------
		begin                : July 9 2004
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

	 view all logs of a cache

	 used template(s): viewlogs

	 GET Parameter: cacheid, start, count

 ****************************************************************************/

  //prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	
	//Preprocessing
	if ($error == false)
	{
		//set here the template to process
		$tplname = 'viewlogs';

		require_once('./lib/caches.inc.php');
		require($stylepath . '/lib/icons.inc.php');
		require($stylepath . '/viewcache.inc.php');
		require($stylepath . '/viewlogs.inc.php');
		require($stylepath.'/smilies.inc.php');

		$cache_id = 0;
		if (isset($_REQUEST['cacheid']))
		{
			$cache_id = $_REQUEST['cacheid'];
		}
		$start = 0;
		if (isset($_REQUEST['start']))
		{
			$start = $_REQUEST['start'];
			if (!is_numeric($start)) $start = 0;
		}
		$count = 200;
		if (isset($_REQUEST['count']))
		{
			$count = $_REQUEST['count'];
			if (!is_numeric($count)) $count = 200;
		}

		if ($cache_id != 0)
		{
			//get cache record
			$rs = sql("SELECT `user_id`, `name`, `founds`, `notfounds`, `notes`, `status`, `type` FROM `caches` WHERE `caches`.`cache_id`='&1'", $cache_id);

			if (mysql_num_rows($rs) == 0)
			{
				$cache_id = 0;
			}
			else
			{
				$cache_record = sql_fetch_array($rs);
				// check if the cache is published, if not only the owner is allowed to view the log
				if(($cache_record['status'] == 5 || $cache_record['status'] == 6 ) && ($cache_record['user_id'] != $usr['userid'] && !$usr['admin']))
				{
					$cache_id = 0;
				}
			}
			mysql_free_result($rs);
		}

		if ($cache_id != 0)
		{
			//ok, cache is here, let's process
			$owner_id = $cache_record['user_id'];

			//cache data
			tpl_set_var('cachename', htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('cacheid', $cache_id);

			if ($cache_record['type'] == 6)
			{
				tpl_set_var('found_icon', $exist_icon);
				tpl_set_var('notfound_icon', $trash_icon);
			}
			else
			{
				tpl_set_var('found_icon', $found_icon);
				tpl_set_var('notfound_icon', $notfound_icon);
			}
		    tpl_set_var('note_icon', $note_icon);

			tpl_set_var('founds', htmlspecialchars($cache_record['founds'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('notfounds', htmlspecialchars($cache_record['notfounds'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('notes', htmlspecialchars($cache_record['notes'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('total_number_of_logs', htmlspecialchars($cache_record['notes'] + $cache_record['notfounds'] + $cache_record['founds'], ENT_COMPAT, 'UTF-8'));
			
			// prepare the logs - show logs marked as deleted if admin
			//
			$show_deleted_logs = "";
			$show_deleted_logs2 = " AND `cache_logs`.`deleted` = 0 ";
			if( $usr['admin'] )
			{
				$show_deleted_logs = "`cache_logs`.`deleted` `deleted`,";
				$show_deleted_logs2 = "";
			}
			
			$rs = sql("SELECT `cache_logs`.`user_id` `userid`,
					".$show_deleted_logs."
					`cache_logs`.`id` AS `log_id`,
					`cache_logs`.`picturescount` AS `picturescount`,
					`cache_logs`.`user_id` AS `user_id`,
					`cache_logs`.`date` AS `date`,
					`cache_logs`.`type` AS `type`,
					`cache_logs`.`text` AS `text`,
					`cache_logs`.`text_html` AS `text_html`,
					`user`.`username` AS `username`,
					`log_types`.`icon_small` AS `icon_small`,
					`log_types_text`.`text_listing` AS `text_listing`,
			    IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`
				FROM `cache_logs`
				INNER JOIN `log_types` ON `log_types`.`id`=`cache_logs`.`type`
				INNER JOIN `log_types_text` ON `log_types_text`.`log_types_id`=`log_types`.`id` AND `log_types_text`.`lang`='&1'
				INNER JOIN `user` ON `user`.`user_id` = `cache_logs`.`user_id`
				LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
				WHERE `cache_logs`.`cache_id`='&2'
				".$show_deleted_logs2."
				ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`Id` DESC LIMIT &3, &4", $lang, $cache_id, $start+0, $count+0);

			$logs = '';
			for ($i = 0; $i < mysql_num_rows($rs); $i++)
			{
				$record = sql_fetch_array($rs);
				$show_deleted = "";
				if( isset( $record['deleted'] ) && $record['deleted'] )
				{
					$show_deleted = "show_deleted";
				}
				$tmplog = read_file($stylepath . '/viewcache_log.tpl.php');

				$tmplog_username = htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8');
				$tmplog_date = htmlspecialchars(strftime($dateformat, strtotime($record['date'])), ENT_COMPAT, 'UTF-8');
				// replace smilies in log-text with images
				$tmplog_text = str_replace($smileytext, $smileyimage, $record['text']);
				
				if ($record['text_html'] == 0)
					$tmplog_text = help_addHyperlinkToURL($tmplog_text);

				$tmplog_text = tidy_html_description($tmplog_text);

				$tmplog = mb_ereg_replace('{show_deleted}', $show_deleted, $tmplog);
				$tmplog = mb_ereg_replace('{username}', $tmplog_username, $tmplog);
				$tmplog = mb_ereg_replace('{userid}', $record['userid'], $tmplog);
				$tmplog = mb_ereg_replace('{date}', $tmplog_date, $tmplog);
				$tmplog = mb_ereg_replace('{type}', $record['text_listing'], $tmplog);
				$tmplog = mb_ereg_replace('{logtext}', $tmplog_text, $tmplog);
				$tmplog = mb_ereg_replace('{logimage}', icon_log_type($record['icon_small'], $tmplog['type']), $tmplog);
//$rating_picture
				if ($record['recommended'] == 1)
					$tmplog = mb_ereg_replace('{ratingimage}','<img src="images/rating-star.png" alt="'.tr('recommendation').'" />', $tmplog);
				else
					$tmplog = mb_ereg_replace('{ratingimage}', '', $tmplog);

				//user der owner
				$logfunctions = '';
				$tmpedit = mb_ereg_replace('{logid}', $record['log_id'], $edit_log);
				$tmpremove = mb_ereg_replace('{logid}', $record['log_id'], $remove_log);
				$tmpnewpic = mb_ereg_replace('{logid}', $record['log_id'], $upload_picture);
				if( $record['deleted']!=1 )
				{
					if( $usr['admin']) 
					{
						$logfunctions = $functions_start . $tmpedit . $functions_middle . $tmpremove . $functions_middle . $functions_end;
					} 
					else if ($record['user_id'] == $usr['userid'])
					{
						$logfunctions = $functions_start . $tmpedit . $functions_middle . $tmpremove . $functions_middle . $tmpnewpic . $functions_end;
					}
					elseif ($owner_id == $usr['userid'])
					{
						$logfunctions = $functions_start . $tmpremove . $functions_end;
					}
				}
				$tmplog = mb_ereg_replace('{logfunctions}', $logfunctions, $tmplog);

				// pictures
				if ($record['picturescount'] > 0)
				{
					$logpicturelines = '';
					$append_atag='';
					$rspictures = sql("SELECT `url`, `title`, `uuid`, `user_id` FROM `pictures` WHERE `object_id`='&1' AND `object_type`=1", $record['log_id']);

					for ($j = 0; $j < mysql_num_rows($rspictures); $j++)
					{
						$pic_record = sql_fetch_array($rspictures);
						$thisline = $logpictureline;


                        $thisline = mb_ereg_replace('{link}', $pic_record['url'], $thisline);
                        $thisline = mb_ereg_replace('{longdesc}', $pic_record['url'], $thisline);
	                    $thisline = mb_ereg_replace('{imgsrc}', 'http://opencaching.pl/thumbs2.php?'.$showspoiler.'uuid=' . urlencode($pic_record['uuid']), $thisline);
                        $thisline = mb_ereg_replace('{title}', htmlspecialchars($pic_record['title'], ENT_COMPAT, 'UTF-8'), $thisline);


						if ($pic_record['user_id'] == $usr['userid'])
						{
							$thisfunctions = $remove_picture;
							$thisfunctions = mb_ereg_replace('{uuid}', urlencode($pic_record['uuid']), $thisfunctions);
							$thisline = mb_ereg_replace('{functions}', $thisfunctions, $thisline);
						}
						else
							$thisline = mb_ereg_replace('{functions}', '', $thisline);

						$logpicturelines .= $thisline;
					}
					mysql_free_result($rspictures);

					$logpicturelines = mb_ereg_replace('{lines}', $logpicturelines, $logpictures);
					$tmplog = mb_ereg_replace('{logpictures}', $logpicturelines, $tmplog);
				}
				else
					$tmplog = mb_ereg_replace('{logpictures}', '', $tmplog);

				$logs .= "$tmplog\n";
			}
			tpl_set_var('logs', $logs);
		}
		else
		{
			//display search page
			exit;
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
