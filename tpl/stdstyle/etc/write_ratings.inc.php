<?php
	/***************************************************************************
												./lang/<speach>/<style>/etc/write_newcaches.php
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

   Unicode Reminder ??

		writing /html/newcaches.inc.php and /html/start_newcaches.inc.php
		/html/nextevents.inc.php

						<tr><td style="padding-bottom:3px;"><font size="1" color="#001BBC">Rekomendowane: {rating_absolute}</font></td></tr>';

	****************************************************************************/

// ini_set ('display_errors',on);
	$cacheline = '<tr><td rowspan="1" align="right"><img src="{cacheicon}" border="0" width="16" height="16" alt="Cache" title="Cache" />&nbsp;</td><td><font size="1"><a href="viewcache.php?cacheid={cacheid}">{cachename}</a> {{hidden_by}} <a href="viewprofile.php?userid={userid}">{username}</a>&nbsp; => &nbsp;<font size="1" color="#001BBC">Rekomendowane: {rating_absolute}</font></td></tr>';
		
	global $lang, $rootpath;

	if (!isset($rootpath)) $rootpath = '../../../';

	//include template handling
	require_once($rootpath . 'lib/common.inc.php');
	require_once($rootpath . 'lib/cache_icon.inc.php');

	//start_ratings.include
	$rs = sql("	SELECT	`user`.`user_id` `user_id`,
				`user`.`username` `username`,
				`caches`.`cache_id` `cache_id`,
				`caches`.`name` `name`,
				`cache_type`.`icon_large` `icon_large`,
				count(`cache_rating`.`cache_id`) as `anzahl`
			FROM `caches`, `user`, `cache_type`, `cache_rating`
			WHERE `caches`.`user_id`=`user`.`user_id`
			  AND `cache_rating`.`cache_id`=`caches`.`cache_id`
			  AND `status`=1
			  AND `caches`.`type`=`cache_type`.`id`
			GROUP BY `user`.`user_id`, `user`.`username`, `caches`.`cache_id`, `caches`.`name`, `cache_type`.`icon_large`
			ORDER BY `anzahl` DESC, `caches`.`name` ASC
			LIMIT 0 , 1000");
if (mysql_num_rows($rs) == 0)
	{
		$file_content = 'Nie ma nowych skrzynek z rekomendacjami';
	}
	else
	{


	$file_content = '<table class="content"> <colgroup> <col width="100"></colgroup>
			<tr><td class="header"><img src="/images/32x32-rating-star.png" border="0" width="32" height="32" alt="" title="Cache" align="middle"></td><td class="header" align="middle" vlign="top"><font size="4"> <b>Skrzynki rekomendowane</b></font></td></tr>';	
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = sql_fetch_array($rs);
		$cacheicon = 'tpl/stdstyle/images/'.getSmallCacheIcon($record['icon_large']);

		$thisline = $cacheline;
		$thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
		$thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
		$thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
		$thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
		$thisline = mb_ereg_replace('{rating_absolute}', $record['anzahl'], $thisline);
		$thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);

		$file_content .= $thisline . "\n";
	}

	$file_content .= '</table>';
}
	$n_file = fopen($dynstylepath . "ratings.tpl.php", 'w');
	fwrite($n_file, $file_content);
	fclose($n_file);
/*
	//user definied sort function
	function cmp($a, $b)
	{
		if ($a == $b)
		{
			return 0;
		}
		return ($a > $b) ? 1 : -1;
	}
*/
?>
