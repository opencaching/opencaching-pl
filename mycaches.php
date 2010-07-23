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
	global $lang, $rootpath, $usr;

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
		
			//get user record
			$user_id = $usr['userid'];
			tpl_set_var('userid',$user_id);		
		
	if (isset($_REQUEST['status']))
		{
			$stat_cache = $_REQUEST['status'];	
		} else {
		$stat_cache =1;}
		
	//get the news
	$tplname = 'mycaches';
	require($stylepath . '/newlogs.inc.php');
	        function cleanup_text($str)
        {
          $str = strip_tags($str, "<li>");
	      $from[] = '&nbsp;'; $to[] = ' ';
          $from[] = '<p>'; $to[] = '';
         $from[] = '\n'; $to[] = '';
         $from[] = '\r'; $to[] = '';
          $from[] = '</p>'; $to[] = "";
          $from[] = '<br>'; $to[] = "";
          $from[] = '<br />'; $to[] = "";
    	 $from[] = '<br/>'; $to[] = "";
            
          $from[] = '<li>'; $to[] = " - ";
          $from[] = '</li>'; $to[] = "";
          
          $from[] = '&oacute;'; $to[] = 'o';
          $from[] = '&quot;'; $to[] = '"';
          $from[] = '&[^;]*;'; $to[] = '';
          
          $from[] = '&'; $to[] = '';
          $from[] = '\''; $to[] = '';
          $from[] = '"'; $to[] = '';
          $from[] = '<'; $to[] = '';
          $from[] = '>'; $to[] = '';
          $from[] = '('; $to[] = ' -';
          $from[] = ')'; $to[] = '- ';
          $from[] = ']]>'; $to[] = ']] >';
	 $from[] = ''; $to[] = '';
              
          for ($i = 0; $i < count($from); $i++)
            $str = str_replace($from[$i], $to[$i], $str);
                                 
          return filterevilchars($str);
        }
        
	
        function filterevilchars($str)
	{
		return str_replace('[\\x00-\\x09|\\x0A-\\x0E-\\x1F]', '', $str);
	}	
			if(checkField('cache_status',$lang) )
				$lang_db = $lang;
			else
				$lang_db = "en";	
				
	$rs_stat = sqlValue("SELECT cache_status.$lang_db  FROM cache_status 
			WHERE `cache_status`.`id` = '$stat_cache'",0);
	
	tpl_set_var('cache_stat',$rs_stat);	


	$LOGS_PER_PAGE = 50;
	$PAGES_LISTED = 10;
		
	$rs = sql("SELECT count(cache_id) FROM caches 
			WHERE `caches`.`status` = '$stat_cache'
				AND `caches`.`user_id`=$user_id");


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
		$pages .= '<a href="mycaches.php?status='.$stat_cache.'&amp;start='.max(0,($startat-$PAGES_LISTED-1)*$LOGS_PER_PAGE).'">{first_img}</a> '; 
	else
		$pages .= "{first_img_inactive}";
	for( $i=max(1,$startat);$i<$startat+$PAGES_LISTED;$i++ )
	{
		$page_number = ($i-1)*$LOGS_PER_PAGE;
		if( $page_number == $start )
			$pages .= '<b>';
		$pages .= '<a href="mycaches.php?status='.$stat_cache.'&amp;start='.$page_number.'">'.$i.'</a> '; 
		if( $page_number == $start )
			$pages .= '</b>';
		
	}
	if( $total_pages > $PAGES_LISTED )
		$pages .= '<a href="mycaches.php?status='.$stat_cache.'&amp;start='.(($i-1)*$LOGS_PER_PAGE).'">{last_img}</a> '; 
	else
		$pages .= '{last_img_inactive}';

			//get last hidden caches

				
			$rs = sql("SELECT `cache_id`, `cache_status`.`&1` AS `cache_status_text`
						FROM `caches`, `cache_status`
						WHERE `user_id`='&2'
						  AND `cache_status`.`id`=`caches`.`status`
						  AND `caches`.`status` = '$stat_cache'
						ORDER BY `date_hidden` DESC, `caches`.`date_created` DESC
						LIMIT ".intval($start).", ".intval($LOGS_PER_PAGE), $lang_db, $user_id);


	$log_ids = '';
	if (mysql_num_rows($rs)==0) $log_ids = '0';
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
		tpl_set_var('cache_status',$record['cache_status_text']);	
	mysql_free_result($rs);

			$rs = sql("SELECT `cache_id`, `name`, `date_hidden`, `status`,cache_type.icon_small AS cache_icon_small,
							`cache_status`.`id` AS `cache_status_id`, `cache_status`.`&1` AS `cache_status_text`
						FROM `caches`  INNER JOIN cache_type ON (caches.type = cache_type.id),`cache_status`
						WHERE `user_id`='&2'
						  AND `cache_status`.`id`=`caches`.`status`
						  AND `caches`.`status` = '$stat_cache'
						ORDER BY `date_hidden` DESC, `caches`.`date_created` DESC
						LIMIT ".intval($start).", ".intval($LOGS_PER_PAGE), $lang_db,$user_id);

		if (mysql_num_rows($rs) != 0)
		{
				$file_content ='';
				for ($i = 0; $i < mysql_num_rows($rs); $i++)
				{
				$log_record = sql_fetch_array($rs);
				
				$file_content .= '<tr>';
				$file_content .= '<td style="width: 90px;">'. htmlspecialchars(date("Y-m-d", strtotime($log_record['date_hidden'])), ENT_COMPAT, 'UTF-8') . '</td>';			
				$file_content .= '<td width="32"><a href="editcache.php?cacheid='. htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '"><img src="tpl/stdstyle/images/free_icons/pencil.png" alt="" title="Edit geocache"/></a></td>';	
//				$file_content .= '<td width="22">&nbsp;' . icon_cache_status($log_record['status'], $log_record['cache_status_text']) . '</td>';
				$file_content .= '<td width="22">&nbsp;<img src="tpl/stdstyle/images/' . $log_record['cache_icon_small'] . '" border="0" alt=""/></td>';
				$file_content .= '<td><b><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($log_record['name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';

	$rs_logs = sql("SELECT cache_logs.id, 
	                          cache_logs.type AS log_type,
				cache_logs.text AS log_text,
	                          DATE_FORMAT(cache_logs.date,'%Y-%m-%d') AS log_date,
	                          user.username AS user_name,
				user.user_id AS user_id,
				log_types.icon_small AS icon_small,
				IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`, COUNT(gk_item.id) AS geokret_in							  
	FROM ((cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id))) INNER JOIN user ON (cache_logs.user_id = user.user_id) INNER JOIN log_types ON (cache_logs.type = log_types.id) INNER JOIN cache_type ON (caches.type = cache_type.id) LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
	LEFT JOIN	gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
	LEFT JOIN	gk_item ON gk_item.id = gk_item_waypoint.id AND
	gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5
					  WHERE cache_logs.deleted=0 AND `cache_logs`.`cache_id`='&1'
					  		AND `cache_logs`.`cache_id`=`caches`.`cache_id` 
							GROUP BY cache_logs.id
	                   ORDER BY `cache_logs`.`date_created` DESC
					LIMIT 1", $log_record['cache_id']);
		if (mysql_num_rows($rs_logs) != 0)
		{
				$logs = sql_fetch_array($rs_logs);
				$file_content .= '<td style="width: 80px;">'. htmlspecialchars(date("Y-m-d", strtotime($logs['log_date'])), ENT_COMPAT, 'UTF-8') . '</td>';			
//				$file_content .= '<td width="22">&nbsp;<a class="links" href="viewlogs.php?logid=' . htmlspecialchars($logs['id'], ENT_COMPAT, 'UTF-8') . '"><img src="tpl/stdstyle/images/' . $logs['icon_small'] . '" border="0" alt=""/></a></td>';
				$file_content .= '<td width="22"><b><a class="links" href="viewlogs.php?logid=' . htmlspecialchars($logs['id'], ENT_COMPAT, 'UTF-8') . '" onmouseover="Tip(\''; 
				$file_content .= '<b>'.$logs['user_name'].'</b>:<br/>';
				$data = cleanup_text(str_replace("\r\n", " ", $logs['log_text']));
				$file_content .= str_replace("\n", " ",$data);
				$file_content .= '\',OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()"><img src="tpl/stdstyle/images/' . $logs['icon_small'] . '" border="0" alt=""/></a></b></td>';
				$file_content .= '<td>&nbsp;&nbsp;<b><a class="links" href="viewprofile.php?userid=' . htmlspecialchars($logs['user_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($logs['user_name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';

				}
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
