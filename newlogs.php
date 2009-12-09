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
	
	$LOGS_PER_PAGE = 150;
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
	
	if( ($start/$LOGS_PER_PAGE)+1 >= $PAGES_LISTED )
		$pages .= '<a href="newlogs.php?start='.max(0,($startat-$PAGES_LISTED-1)*$LOGS_PER_PAGE).'">{first_img}</a> '; 
	else
		$pages .= "{first_img_inactive}";
	for( $i=max(1,$startat);$i<$startat+$PAGES_LISTED;$i++ )
	{
		$page_number = ($i-1)*$LOGS_PER_PAGE;
		if( $page_number == $start )
			$pages .= '<b>';
		$pages .= '<a href="newlogs.php?start='.$page_number.'">'.$i.'</a> '; 
		if( $page_number == $start )
			$pages .= '</b>';
		
	}
	if( $total_pages > $PAGES_LISTED )
		$pages .= '<a href="newlogs.php?start='.(($i-1)*$LOGS_PER_PAGE).'">{last_img}</a> '; 
	else
		$pages .= '{last_img_inactive}';
	$rs = sql("SELECT `cache_logs`.`id`
			FROM `cache_logs`, `caches`
			WHERE `cache_logs`.`cache_id`=`caches`.`cache_id`
				AND `cache_logs`.`deleted`=0 
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

	$rs = sql("SELECT cache_logs.cache_id AS cache_id,
	                          cache_logs.type AS log_type,
	                          cache_logs.date AS log_date,
	                          caches.name AS cache_name,
	                          countries.pl AS country_name,
	                          user.username AS user_name,
							  user.user_id AS user_id,
							  caches.type AS cache_type,
							  cache_type.icon_small AS cache_icon_small,
							  log_types.icon_small AS icon_small,
							  IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`
	                  FROM ((cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id)) INNER JOIN countries ON (caches.country = countries.short)) INNER JOIN user ON (cache_logs.user_id = user.user_id) INNER JOIN log_types ON (cache_logs.type = log_types.id) INNER JOIN cache_type ON (caches.type = cache_type.id) LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id` 
	                   WHERE cache_logs.deleted=0 AND cache_logs.id IN (" . $log_ids . ")
	                   ORDER BY cache_logs.date_created DESC");
	//$rs = mysql_query($sql);

	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		//group by country
		$record = sql_fetch_array($rs);

		$newlogs[$record['country_name']][] = array(
			'cache_id'   		=> $record['cache_id'],
			'log_type'   		=> $record['log_type'],
			'log_date'   		=> $record['log_date'],
			'cache_name' 		=> $record['cache_name'],
			'user_name'  		=> $record['user_name'],
			'icon_small' 		=> $record['icon_small'],
			'user_id'	 		=> $record['user_id'],
			'cache_type'	 	=> $record['cache_type'],
			'cache_icon_small'	=> $record['cache_icon_small'],
			'recommended'	=> $record['recommended']			
		);
	}

	//sort by country name
	uksort($newlogs, 'cmp');

	$file_content = '';

	if (isset($newlogs))
	{
		foreach ($newlogs AS $countryname => $country_record)
		{
			$file_content .= '<p class="content-title-noshade-size3">' . htmlspecialchars($countryname, ENT_COMPAT, 'UTF-8') . '</p>';

			foreach ($country_record AS $log_record)
			{

				$file_content .= "<p>";
				$file_content .= htmlspecialchars(date("d.m.Y", strtotime($log_record['log_date'])), ENT_COMPAT, 'UTF-8');
				$file_content .= ' <img src="/tpl/stdstyle/images/' . $log_record['icon_small'] . '" border="0" alt="" />&nbsp;';
				$file_content .= ' <img src="/tpl/stdstyle/images/' . $log_record['cache_icon_small'] . '" border="0" alt=""/>&nbsp;';
        //$rating_picture
				if ($log_record['recommended'] == 1) {
					$file_content .= ' <img src="/images/rating-star.png" border="0" alt=""/>';
				}	
				$file_content .= ' - <a href="viewcache.php?cacheid=' . htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($log_record['cache_name'], ENT_COMPAT, 'UTF-8') . '</a>';

				switch( $log_record['log_type'] )
				{
					case 1:
						$file_content .= ' - znaleziony przez ';
					break;
					case 2:
						$file_content .= ' - nieznaleziony przez ';
					break;
					case 3:
						$file_content .= ' - skomentowany przez ';
					break;
					case 7:
						$file_content .= ' - uczestniczył(a) ';
					break;
					case 8:
						$file_content .= ' - zamierza uczestniczyć ';
					break;
				}
				$file_content .= '<a href="viewprofile.php?userid='. htmlspecialchars($log_record['user_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($log_record['user_name'], ENT_COMPAT, 'UTF-8'). '</a>';

				$file_content .= "</p>";
				$file_content .= "\n";
			}
		}
	}
	//$n_file = fopen("/tpl/stdstyle/html/newlogs.tpl.php", 'w');
	//fwrite($n_file, $file_content);
	//fclose($n_file);
	$pages = mb_ereg_replace('{last_img}', $last_img, $pages);
	$pages = mb_ereg_replace('{first_img}', $first_img, $pages);
	
	$pages = mb_ereg_replace('{first_img_inactive}', $first_img_inactive, $pages);
	$pages = mb_ereg_replace('{last_img_inactive}', $last_img_inactive, $pages);
		
	tpl_set_var('file_content',$file_content);
	tpl_set_var('pages', $pages);
	unset($newcaches);

	//user definied sort function
	
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
