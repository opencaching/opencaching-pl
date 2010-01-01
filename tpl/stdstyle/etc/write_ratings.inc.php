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

   Unicode Reminder ?? ąść

	
	****************************************************************************/

// ini_set ('display_errors',on);
		
	global $lang, $rootpath;

	if (!isset($rootpath)) $rootpath = '../../../';

	//include template handling
	require_once($rootpath . 'lib/common.inc.php');
	require_once($rootpath . 'lib/cache_icon.inc.php');
	global $dynbasepath;

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
			LIMIT 0 , 200");
			
	$cacheline = '<tr><td>&nbsp;</td><td><span class="content-title-noshade txt-blue08" >{rating_absolute}</span></td><td><img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" /></td><td><strong><a href="viewcache.php?cacheid={cacheid}">{cachename}</a></strong></td><td><strong><a href="viewprofile.php?userid={userid}">{username}</a></strong></td></tr>';

if (mysql_num_rows($rs) == 0)
	{
		$file_content = '<tr><td colspan="5">Nie ma nowych skrzynek z rekomendacjami</td></tr>';
	}
	else
	{

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
