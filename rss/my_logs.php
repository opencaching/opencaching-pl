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

   Unicode Reminder ąść

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

		
	if (isset($_REQUEST['userid']))
		{
			$user_id = $_REQUEST['userid'];
			tpl_set_var('userid',$user_id);		
		}

	$rsGeneralStat =sql("SELECT  username FROM user WHERE user_id=&1",$user_id);

			$user_record = sql_fetch_array($rsGeneralStat);
			tpl_set_var('username',$user_record['username']);

		$content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<rss version=\"2.0\">\n<channel>\n<title>OC PL - Najnowsze logi</title>\n<ttl>60</ttl><description>Najnowsze logi na OpenCaching.PL </description>\n<link>http://www.opencaching.pl/newlogs.php</link><image>
		<title>OC PL - Najnowsze logi</title>
		<url>http://www.opencaching.pl/images/oc.png</url>
		<link>http://www.opencaching.pl/newlogs.php</link><width>100</width><height>28</height></image>\n\n";



	$rs = sql("SELECT cache_logs.id, cache_logs.cache_id AS cache_id,
	                          cache_logs.type AS log_type,
	                          cache_logs.date AS log_date,
	                          caches.name AS cache_name,
	                          user.username AS user_name,
							  user.user_id AS user_id,
							  caches.wp_oc AS wp_name,
							  caches.type AS cache_type,
							  cache_type.icon_small AS cache_icon_small,
							  log_types.icon_small AS icon_small,
							  IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`,COUNT(gk_item.id) AS geokret_in
	                  FROM ((cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id)) INNER JOIN user ON (cache_logs.user_id = user.user_id) INNER JOIN log_types ON (cache_logs.type = log_types.id) INNER JOIN cache_type ON (caches.type = cache_type.id) LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id` 
							LEFT JOIN	gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
							LEFT JOIN	gk_item ON gk_item.id = gk_item_waypoint.id AND
							gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5	
					  WHERE cache_logs.deleted=0 AND `cache_logs`.`user_id`='" . sql_escape($_REQUEST['userid']) . "'
	                   GROUP BY cache_logs.id ORDER BY cache_logs.date_created DESC LIMIT 20");

				for ($i = 0; $i < mysql_num_rows($rs); $i++)
				{
				$r = sql_fetch_array($rs);

			$thisline = "<item>\n<title>{cachename}</title>\n<description>  Data: {date} - Wpis: {logtype} - Rekomendacja: {rate} - GeoKrety: {gk} </description>\n<link>http://www.opencaching.pl/viewlogs.php?cacheid={cacheid}</link>\n</item>\n";				



			if ( $r['geokret_in'] !='0')
					{
					$thisline = str_replace('{gk}', 'Tak', $thisline);
					}
					else
					{
					$thisline = str_replace('{gk}', 'Nie', $thisline);
					}					
				
				        //$rating_picture
				if ($r['recommended'] == 1) 
					{
					$thisline = str_replace('{rate}', 'Tak', $thisline);
					}
					else
					{
					$thisline = str_replace('{rate}', 'Nie', $thisline);
					}
			$thisline = str_replace('{cacheid}', $r['cache_id'], $thisline);;
			$thisline = str_replace('{cachename}', htmlspecialchars($r['cache_name']), $thisline);
			$thisline = str_replace('{logtype}', htmlspecialchars($r['log_name']), $thisline););
			$thisline = str_replace('{date}', date('d-m-Y', strtotime($r['log_date'])), $thisline);

			$content .= $thisline . "\n";
		}
		mysql_free_result($rs);
		$content .= "</channel>\n</rss>\n";

		echo $content;
	}
?>
