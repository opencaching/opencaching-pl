<?php
	/***************************************************************************
												./nlogs.php
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

		new logs

	****************************************************************************/
	global $lang, $rootpath;

	if (!isset($rootpath)) $rootpath = '';

	//include template handling
	require_once($rootpath . 'lib/common.inc.php');
	require_once($rootpath . 'lib/cache_icon.inc.php');
//	require_once($stylepath . '/lib/icons.inc.php');

//Preprocessing
if ($error == false)
{
	//get the news
	$tplname = 'newlogs';
	require($stylepath . '/newlogs.inc.php');
	
	$LOGS_PER_PAGE = 50;
	$PAGES_LISTED = 10;
		
	$rs = sql("SELECT count(id) FROM cache_logs WHERE deleted=0");
	$total_logs = mysql_result($rs,0);
	mysql_free_result($rs);
	
	$pages = "";
	$total_pages = ceil($total_logs/$LOGS_PER_PAGE);
	
	if( !isset($_GET['start']) || intval($_GET['start'])<0 || intval($_GET['start']) > $total_logs)
		$start = 0;
	else
		$start = intval($_GET['start']);
	
	$startat = max(0,floor((($start/$LOGS_PER_PAGE)+1)/$PAGES_LISTED)*$PAGES_LISTED);
	
	if( $start > 0 )
	{
		$pages .= '<a href="newlogs.php?start='.max(0,($startat-$PAGES_LISTED-1)*$LOGS_PER_PAGE).'">{first_img}</a> ';
		$pages .= '<a href="newlogs.php?start='.max(0,$start-$LOGS_PER_PAGE).'">{prev_img}</a> ';
	}
	else
		$pages .= "{first_img_inactive} {prev_img_inactive} ";
	for( $i=max(1,$startat);$i<$startat+$PAGES_LISTED;$i++ )
	{
		$page_number = ($i-1)*$LOGS_PER_PAGE;
		if( $page_number == $start )
			$pages .= "<b>$i</b> ";
		else
			$pages .= "<a href='newlogs.php?start=$page_number'>$i</a> ";
	}
	if( $total_pages > $PAGES_LISTED )
	{
		$pages .= '<a href="newlogs.php?start='.($start+$LOGS_PER_PAGE).'">{next_img}</a> ';
		$pages .= '<a href="newlogs.php?start='.(($i-1)*$LOGS_PER_PAGE).'">{last_img}</a> ';
	}
	else
		$pages .= ' {next_img_inactive} {last_img_inactive}';
	$rs = sql("SELECT `cache_logs`.`id`
			FROM `cache_logs`, `caches`
			WHERE `cache_logs`.`cache_id`=`caches`.`cache_id`
				AND `cache_logs`.`deleted`=0 
			  AND `caches`.`status` != 4
				AND `caches`.`status` != 5 
				AND `caches`.`status` != 6
			ORDER BY  `cache_logs`.`date_created` DESC
			LIMIT ".intval($start).", ".intval($LOGS_PER_PAGE));
	$log_ids = '';
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = sql_fetch_array($rs);
		if ($i > 0)
		{
			$log_ids .= ', ' . $record['id'];
		}
		else
		{
			$log_ids = $record['id'];
		}
	}
	mysql_free_result($rs);

$rs = sql("SELECT cache_logs.id, cache_logs.cache_id AS cache_id,
	                          cache_logs.type AS log_type,
	                          cache_logs.date AS log_date,
				   cache_logs.text AS log_text,
				  cache_logs.text_html AS text_html,
	                          caches.name AS cache_name,
	                          user.username AS user_name,
							  user.user_id AS user_id,
							  caches.wp_oc AS wp_name,
							  caches.type AS cache_type,
							  cache_type.icon_small AS cache_icon_small,
							  log_types.icon_small AS icon_small,
							  IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`,
							COUNT(gk_item.id) AS geokret_in
							FROM (cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id)) INNER JOIN user ON (cache_logs.user_id = user.user_id) INNER JOIN log_types ON (cache_logs.type = log_types.id) INNER JOIN cache_type ON (caches.type = cache_type.id) LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
							LEFT JOIN	gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
							LEFT JOIN	gk_item ON gk_item.id = gk_item_waypoint.id AND
							gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5	
							WHERE cache_logs.deleted=0 AND cache_logs.id IN (" . $log_ids . ")
							GROUP BY cache_logs.id
							ORDER BY cache_logs.date_created DESC");
	//$rs = mysql_query($sql);
	$file_content = '';
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		//group by country
		$log_record = sql_fetch_array($rs);

//		$newlogs[$record['country_name']][] = array(
//			'cache_id'   		=> $record['cache_id'],
//			'log_type'   		=> $record['log_type'],
//			'log_date'   		=> $record['log_date'],
//			'cache_name' 		=> $record['cache_name'],
//			'wp_name' 			=> $record['wp_name'],
//			'user_name'  		=> $record['user_name'],
//			'icon_small' 		=> $record['icon_small'],
//			'user_id'	 		=> $record['user_id'],
//			'cache_type'	 	=> $record['cache_type'],
//			'cache_icon_small'	=> $record['cache_icon_small'],
//			'recommended'	=> $record['recommended']			
//		);
//	}

	//sort by country name
//	uksort($newlogs, 'cmp');



//	if (isset($newlogs))
//	{
//		foreach ($newlogs AS $countryname => $country_record)
//		{
//			$file_content .= '<tr><td colspan="6" class="content-title-noshade-size3">' . htmlspecialchars($countryname, ENT_COMPAT, 'UTF-8') . '</td></tr>';

//			foreach ($country_record AS $log_record)
//			{

				$file_content .= '<tr>';
				$file_content .= '<td style="width: 70px;">'. htmlspecialchars(date("d-m-Y", strtotime($log_record['log_date'])), ENT_COMPAT, 'UTF-8') . '</td>';

			if ( $log_record['geokret_in'] !='0')
					{
					$file_content .= '<td width="22">&nbsp;<img src="images/gk.png" border="0" alt="" title="GeoKret" /></td>';
					}
					else
					{
					$file_content .='<td width="22">&nbsp;</td>';
					}					
				
				        //$rating_picture
				if ($log_record['recommended'] == 1 && $log_record['log_type']==1) 
					{
					$file_content .= '<td width="22"><img src="images/rating-star.png" border="0" alt=""/></td>';
					}
					else
					{
					$file_content .= '<td width="22">&nbsp;</td>';
					}	
				$file_content .= '<td width="22"><img src="tpl/stdstyle/images/' . $log_record['icon_small'] . '" border="0" alt="" /></td>';
				$file_content .= '<td width="22"><img src="tpl/stdstyle/images/' . $log_record['cache_icon_small'] . '" border="0" alt=""/></td>';
				$file_content .= '<td><b><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($log_record['cache_name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
				$file_content .= '<td><b><a class="links" href="viewprofile.php?userid='. htmlspecialchars($log_record['user_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($log_record['user_name'], ENT_COMPAT, 'UTF-8'). '</a></b></td>';
				$file_content .= "</tr>";
			}
//		}
//	}

	$pages = mb_ereg_replace('{prev_img}', $prev_img, $pages);
	$pages = mb_ereg_replace('{next_img}', $next_img, $pages);
	$pages = mb_ereg_replace('{last_img}', $last_img, $pages);
	$pages = mb_ereg_replace('{first_img}', $first_img, $pages);
	
	$pages = mb_ereg_replace('{prev_img_inactive}', $prev_img_inactive, $pages);
	$pages = mb_ereg_replace('{next_img_inactive}', $next_img_inactive, $pages);
	$pages = mb_ereg_replace('{first_img_inactive}', $first_img_inactive, $pages);
	$pages = mb_ereg_replace('{last_img_inactive}', $last_img_inactive, $pages);
		
	tpl_set_var('file_content',$file_content);
	tpl_set_var('pages', $pages);
//	unset($newcaches);

	//user definied sort function
	
}
        function cleanup_text($str)
        {
//          $str = PLConvert('UTF-8','POLSKAWY',$str);
          $str = strip_tags($str, "<p><br /><li>");
          // <p> -> nic
          // </p>, <br /> -> nowa linia
          $from[] = '<p>'; $to[] = '';
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
		return str_replace('[\\x00-\\x09|\\x0B-\\x0C|\\x0E-\\x1F]', '', $str);
	}
function cmp($a, $b)
	{
		if ($a == $b)
		{
			return 0;
		}
		return ($a > $b) ? 1 : -1;
	}
//make the template and send it out
tpl_BuildTemplate();
?>
