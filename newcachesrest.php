<?php
/***************************************************************************
																./newcaches.php
															-------------------
		begin                : Mon June 28 2004
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
	         
   Unicode Reminder メモ
                                				                                
	 include the newcaches HTML file
	
 ****************************************************************************/
 
	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require_once('./lib/cache_icon.inc.php');
	$lang = "pl";
	
	//Preprocessing
	if ($error == false)
	{
		//get the news
		$tplname = 'newcachesrest';
//		require('tpl/stdstyle/newcaches.inc.php');
//		require($stylepath . '/newcachesresst.inc.php');

	$rs = sql("	SELECT	`caches`.`cache_id` `cache_id`,
				`caches`.`user_id` `userid`,
				`user`.`username` `username`,
				`caches`.`country` `countryshort`,
				`caches`.`longitude` `longitude`,
				`caches`.`latitude` `latitude`,
				`caches`.`name` `name`,
				`caches`.`date_hidden` `date_hidden`,
				`caches`.`date_created` `date_created`,
				IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) AS `date`,
				`countries`.`&1` `country`,
				`cache_type`.`icon_large` `icon_large`
			FROM `caches`, `user`, `countries`, `cache_type`
			WHERE `caches`.`user_id`=`user`.`user_id`
			  AND `countries`.`short`=`caches`.`country`
			  AND `type` != 6
			  AND `caches`.`status` = 1
			  AND `caches`.`country` != 'PL'
			  AND `caches`.`type`=`cache_type`.`id`
				AND `caches`.`date_hidden` <= NOW() 
				AND `caches`.`date_created` <= NOW()
			ORDER BY IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) DESC, `caches`.`cache_id` DESC
			LIMIT 0, 200", $lang);

	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		//group by country
		$record = sql_fetch_array($rs);
		$newcaches[$record['country']][] = array(
			'name' => $record['name'],
			'userid' => $record['userid'],
			'username' => $record['username'],
			'cache_id' => $record['cache_id'],
			'country' => $record['countryshort'],
			'longitude' => $record['longitude'],
			'latitude' => $record['latitude'],
			'date' => $record['date'],
			'icon_large' => $record['icon_large']
		);
	}
	uksort($newcaches, 'cmp');

	$file_content = '
		<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" border="0" width="32" height="32" alt="Cachesuche" title="Cache" align="middle"/>&nbsp;{{abroad_caches}}</div>
		<div class="content2-container line-box">
			';
	if (isset($newcaches))
	{
		foreach ($newcaches AS $countryname => $country_record)
		{
			$file_content .= '<p class="content-title-noshade-size3">' . htmlspecialchars($countryname, ENT_COMPAT, 'UTF-8') . '</p>';

			foreach ($country_record AS $cache_record)
			{
				$cacheicon = 'tpl/stdstyle/images/'.getSmallCacheIcon($cache_record['icon_large']);

				$file_content .= "<p>";
				$file_content .= htmlspecialchars(date("d.m.Y", strtotime($cache_record['date'])), ENT_COMPAT, 'UTF-8');
				$file_content .= ' - <img src="'.$cacheicon.'" border="0" width="16" height="16" alt="Cache" title="Cache"/>';
				$file_content .= '<a href="viewcache.php?cacheid=' . htmlspecialchars($cache_record['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8') . '</a>';
				$file_content .= ' {{hidden_by}} <a href="viewprofile.php?userid=' . $cache_record['userid'] . '">' . htmlspecialchars($cache_record['username'], ENT_COMPAT, 'UTF-8') . '</a>' . "\n";
				$file_content .= "</p>";
			}
			$content_country .= $file_content;
		}
		$content .= $content_country . "\n";
	}
	mysql_free_result($rs);
	tpl_set_var('newcachesrest', $content);

	//user definied sort function
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
