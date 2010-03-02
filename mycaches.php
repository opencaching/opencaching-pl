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

   Unicode Reminder  ąść

		

	****************************************************************************/
	global $lang, $rootpath;

	if (!isset($rootpath)) $rootpath = '';

	//include template handling
	require_once($rootpath . 'lib/common.inc.php');
	require_once($rootpath . 'lib/cache_icon.inc.php');
	require_once($stylepath . '/lib/icons.inc.php');

//Preprocessing
if ($error == false)
{
		//user logged in?
		if ($usr == false)
		{
		    $target = urlencode(tpl_get_current_page());
		    tpl_redirect('login.php?target='.$target);
		}
		else
		{
		
	if (isset($_REQUEST['userid']))
		{
			$user_id = $_REQUEST['userid'];
			tpl_set_var('userid',$user_id);		
		}

	//get the news
	$tplname = 'mycaches';
	require($stylepath . '/newlogs.inc.php');
	$rsGeneralStat =sql("SELECT  username FROM user WHERE user_id=&1",$user_id);

			$user_record = sql_fetch_array($rsGeneralStat);
			tpl_set_var('username',$user_record['username']);
			mysql_free_result($rsGeneralStat);	
	$LOGS_PER_PAGE = 50;
	$PAGES_LISTED = 10;
		
	$rs = sql("SELECT count(cache_id) FROM caches 
			WHERE `caches`.`status` != 5 
				AND `caches`.`user_id`='" . sql_escape($_REQUEST['userid']) . "'");

	$total_logs = mysql_result($rs,0);
	mysql_free_result($rs);
	
	$pages = "";
	$total_pages = ceil($total_logs/$LOGS_PER_PAGE);
	
	if( !isset($_GET['start']) || intval($_GET['start'])<0 || intval($_GET['start']) > $total_logs)
		$start = 0;
	else
		$start = intval($_GET['start']);
	
	$startat = max(0,floor((($start/$LOGS_PER_PAGE)+1)/$PAGES_LISTED)*$PAGES_LISTED);
	
	if( ($start/$LOGS_PER_PAGE)+1 >= $PAGES_LISTED )
		$pages .= '<a href="mycaches.php?userid='.$user_id.'&amp;start='.max(0,($startat-$PAGES_LISTED-1)*$LOGS_PER_PAGE).'">{first_img}</a> '; 
	else
		$pages .= "{first_img_inactive}";
	for( $i=max(1,$startat);$i<$startat+$PAGES_LISTED;$i++ )
	{
		$page_number = ($i-1)*$LOGS_PER_PAGE;
		if( $page_number == $start )
			$pages .= '<b>';
		$pages .= '<a href="mycaches.php?userid='.$user_id.'&amp;start='.$page_number.'">'.$i.'</a> '; 
		if( $page_number == $start )
			$pages .= '</b>';
		
	}
	if( $total_pages > $PAGES_LISTED )
		$pages .= '<a href="mycaches.php?userid='.$user_id.'&amp;start='.(($i-1)*$LOGS_PER_PAGE).'">{last_img}</a> '; 
	else
		$pages .= '{last_img_inactive}';

			//get last hidden caches
			if(checkField('cache_status',$lang) )
				$lang_db = $lang;
			else
				$lang_db = "en";
				
			$rs = sql("SELECT `cache_id`, `name`, `date_hidden`, `status`,
						`cache_status`.`id` AS `cache_status_id`, `cache_status`.`&1` AS `cache_status_text`
						FROM `caches`, `cache_status`
						WHERE `user_id`='" . sql_escape($_REQUEST['userid']) . "'
						  AND `cache_status`.`id`=`caches`.`status`
						  AND `caches`.`status` != 5
						ORDER BY `date_hidden` DESC, `caches`.`date_created` DESC
						LIMIT ".intval($start).", ".intval($LOGS_PER_PAGE), $lang_db);



	$log_ids = '';
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = sql_fetch_array($rs);
		if ($i > 0)
		{
			$log_ids .= ', ' . $record['cache_id'];
		}
		else
		{
			$log_ids = $record['cache_id'];
		}
	}
	mysql_free_result($rs);

			$rs = sql("SELECT `cache_id`, `name`, `date_hidden`, `status`,cache_type.icon_small AS cache_icon_small,
							`cache_status`.`id` AS `cache_status_id`, `cache_status`.`&1` AS `cache_status_text`
						FROM `caches`  INNER JOIN cache_type ON (caches.type = cache_type.id),`cache_status`
						WHERE `user_id`='" . sql_escape($_REQUEST['userid']) . "'
						  AND `cache_status`.`id`=`caches`.`status`
						  AND `caches`.`status` != 5
						ORDER BY `date_hidden` DESC, `caches`.`date_created` DESC
						LIMIT ".intval($start).", ".intval($LOGS_PER_PAGE), $lang_db);

		if (mysql_num_rows($rs) != 0)
		{
				$file_content ='';
				for ($i = 0; $i < mysql_num_rows($rs); $i++)
				{
				$log_record = sql_fetch_array($rs);
				
				$file_content .= '<tr>';
				$file_content .= '<td style="width: 70px;">'. htmlspecialchars(date("d-m-Y", strtotime($log_record['date_hidden'])), ENT_COMPAT, 'UTF-8') . '</td>';
				
	$file_content .= '<td><b><a class="links" href="editcache.php?cacheid='. htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '"><img src="tpl/stdstyle/images/log/16x16-note.png" alt="" title="Edit"/></a></b></td>';	
				$file_content .= '<td width="22">' . icon_cache_status($log_record['status'], $log_record['cache_status_text']) . '</td>';
				$file_content .= '<td width="22"><img src="tpl/stdstyle/images/' . $log_record['cache_icon_small'] . '" border="0" alt=""/></td>';
				$file_content .= '<td><b><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($log_record['name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
				
				$file_content .= "</tr>";
				}
		}

	$pages = mb_ereg_replace('{last_img}', $last_img, $pages);
	$pages = mb_ereg_replace('{first_img}', $first_img, $pages);
	
	$pages = mb_ereg_replace('{first_img_inactive}', $first_img_inactive, $pages);
	$pages = mb_ereg_replace('{last_img_inactive}', $last_img_inactive, $pages);
		
	tpl_set_var('file_content',$file_content);
	tpl_set_var('pages', $pages);

	}	
}
//make the template and send it out
tpl_BuildTemplate();
?>
