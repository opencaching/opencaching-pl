<?php
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
		$tplname = 'gallery_log';

		require_once('./lib/caches.inc.php');
		require($stylepath . '/lib/icons.inc.php');
		require($stylepath . '/viewcache.inc.php');
		require($stylepath . '/gallery_log.inc.php');
		require($stylepath.'/smilies.inc.php');
		global $usr;
		
		$cache_id = 0;
		if (isset($_REQUEST['cacheid']))
		{
			$cache_id = $_REQUEST['cacheid'];
		}
		if (isset($_REQUEST['logid']))
		{
			$logid = $_REQUEST['logid'];
		$show_one_log = " AND `cache_logs`.`id` ='".$logid."'  ";
		}

		$start = 0;
		if (isset($_REQUEST['start']))
		{
			$start = $_REQUEST['start'];
			if (!is_numeric($start)) $start = 0;
		}
		$count = 99999;
		if (isset($_REQUEST['count']))
		{
			$count = $_REQUEST['count'];
			if (!is_numeric($count)) $count = 999999;
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
				if(($cache_record['status'] == 4 || $cache_record['status'] == 5 || $cache_record['status'] == 6 ) && ($cache_record['user_id'] != $usr['userid'] && !$usr['admin']))
				{
					$cache_id = 0;
				}
			}
			mysql_free_result($rs);
		} else {
		
					//get cache record
			$rs = sql("SELECT `cache_logs`.`cache_id`,`caches`.`user_id`, `caches`.`name`, `caches`.`founds`, `caches`.`notfounds`, `caches`.`notes`, `caches`.`status`, `caches`.`type` FROM `caches`,`cache_logs` WHERE `cache_logs`.`id`='&1' AND `caches`.`cache_id`=`cache_logs`.`cache_id` ", $logid);

			if (mysql_num_rows($rs) == 0)
			{
				$cache_id = 0;
			}
			else
			{
				$cache_record = sql_fetch_array($rs);
				// check if the cache is published, if not only the owner is allowed to view the log
				if(($cache_record['status'] == 4 || $cache_record['status'] == 5 || $cache_record['status'] == 6 ) && ($cache_record['user_id'] != $usr['userid'] && !$usr['admin']))
				{
					$cache_id = 0;
				} else { $cache_id =$cache_record['cache_id'] ;}
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


			// prepare the logs - show logs marked as deleted if admin
			//
			$show_deleted_logs = "";
			$show_deleted_logs2 = " AND `cache_logs`.`deleted` = 0 ";
//			if( $usr['admin'] )
//			{
//				$show_deleted_logs = "`cache_logs`.`deleted` `deleted`,";
//				$show_deleted_logs2 = "";
//			}
 
			
			$rs = sql("SELECT `cache_logs`.`user_id` `userid`,
					".$show_deleted_logs."
					`cache_logs`.`id` AS `log_id`,
			         `cache_logs`.`encrypt` `encrypt`,
					`cache_logs`.`picturescount` AS `picturescount`,
					`cache_logs`.`user_id` AS `user_id`,
					`cache_logs`.`date` AS `date`,
					`cache_logs`.`type` AS `type`,
					`cache_logs`.`text` AS `text`,
					`cache_logs`.`text_html` AS `text_html`,
					`user`.`username` AS `username`,
                    `user`.`admin` AS `admin`,
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
				".$show_one_log."
				ORDER BY `cache_logs`.`date` ASC, `cache_logs`.`Id` DESC LIMIT &3, &4", $lang, $cache_id, $start+0, $count+0);

			$pictureslog = '';

				// replace smilies in log-text with images

				// pictures

					$logpicturelines = '';
					$append_atag='';
					$rspictures = sql("SELECT `pictures`.`url`, `pictures`.`title`, `pictures`.`uuid`, `pictures`.`user_id`,`pictures`.`object_id` FROM `pictures`,`cache_logs` WHERE `pictures`.`object_id`=`cache_logs`.`id` AND `pictures`.`object_type`=1 AND `cache_logs`.`cache_id`=&1", $cache_id);

					for ($j = 0; $j < mysql_num_rows($rspictures); $j++)
					{
						$pic_record = sql_fetch_array($rspictures);
						$thisline = $logpicture;


                        $thisline = mb_ereg_replace('{link}', $pic_record['url'], $thisline);
                        $thisline = mb_ereg_replace('{longdesc}', str_replace("uploads","uploads",$pic_record['url']), $thisline);
	                $thisline = mb_ereg_replace('{imgsrc}', 'thumbs2.php?'.$showspoiler.'uuid=' . urlencode($pic_record['uuid']), $thisline);
                        $thisline = mb_ereg_replace('{log}', $tmplog_username .": ". htmlspecialchars($record['text'], ENT_COMPAT, 'UTF-8'), $thisline);
                        if ($pic_record['title']=="") {$title="link";} else { $title=htmlspecialchars($pic_record['title'],ENT_COMPAT,'UTF-8');}
                        $thisline = mb_ereg_replace('{title}', "<a class=links href=viewlogs.php?logid=".$pic_record['object_id'].">".$title."</a>", $thisline);


				
						$logpicturelines .= $thisline;
					}
					mysql_free_result($rspictures);

//					$logpicturelines = mb_ereg_replace('{lines}', $logpicturelines, $logpictures2);
					$tmplog = $logpicturelines;


				$logs .= "$tmplog\n";
			
			tpl_set_var('logpictures', $logs);
		}
		else
		{
			//display search page
			// redirection
		    tpl_redirect('viewcache.php?cacheid=' . urlencode($cache_id));
			exit;
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
