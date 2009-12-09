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

   Unicode Reminder ăĄă˘

	 include the newcaches HTML file

 ****************************************************************************/

	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require_once('./lib/cache_icon.inc.php');
	
	//Preprocessing
	if ($error == false)
	{
		//get the news
		$tplname = 'newcaches';
//		require('tpl/stdstyle/newcaches.inc.php');
		require($stylepath . '/newcaches.inc.php');

		$startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
		$startat = $startat + 0;

		$perpage = 100;
		$startat -= $startat % $perpage;

		$content = '';
		$rs = sql('SELECT `caches`.`cache_id` `cacheid`, 
							`user`.`user_id` `userid`, 
							`caches`.`country` `country`, 
							`caches`.`name` `cachename`, 
							`user`.`username` `username`, 
							`caches`.`date_created` `date_created`, 
							`caches`.`date_hidden` `date_hidden`, 
							IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) AS `date`,
							`cache_type`.`icon_large` `icon_large` 
						FROM `caches`, `user`, `cache_type` 
						WHERE `caches`.`date_hidden` <= NOW() 
						AND `caches`.`date_created` <= NOW()
						AND `caches`.`user_id`=`user`.`user_id` 
						AND `caches`.`type`=`cache_type`.`id` 
						AND `caches`.`status` = 1 
						ORDER BY IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) DESC, 
						`caches`.`cache_id` DESC 
						LIMIT ' . ($startat+0) . ', ' . ($perpage+0));
		while ($r = sql_fetch_array($rs))
		{
			$thisline = $tpl_line;

			$thisline = mb_ereg_replace('{cacheid}', $r['cacheid'], $thisline);
			$thisline = mb_ereg_replace('{userid}', $r['userid'], $thisline);
			$thisline = mb_ereg_replace('{cachename}', htmlspecialchars($r['cachename'], ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{username}', htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{date}', date('d.m.Y', strtotime($r['date'])), $thisline);
			$thisline = mb_ereg_replace('{country}', htmlspecialchars($r['country'], ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{imglink}', 'tpl/stdstyle/images/'.getSmallCacheIcon($r['icon_large']), $thisline);
			$thisline = mb_ereg_replace('{created_by}', htmlspecialchars(tr('created_by'), ENT_COMPAT, 'UTF-8'), $thisline);
			

			$content .= $thisline . "\n";
		}
		mysql_free_result($rs);
		tpl_set_var('newcaches', $content);

		$rs = sql('SELECT COUNT(*) `count` FROM `caches`');
		$r = sql_fetch_array($rs);
		$count = $r['count'];
		mysql_free_result($rs);

		$frompage = $startat / 100 - 3;
		if ($frompage < 1) $frompage = 1;

		$topage = $frompage + 8;
		if (($topage - 1) * $perpage > $count)
			$topage = ceil($count / $perpage);

		$thissite = $startat / 100 + 1;

		$pages = '';
		if ($startat > 0)
			$pages .= '<a href="newcaches.php?startat=0">{first_img}</a> <a href="newcaches.php?startat=' . ($startat - 100) . '">{prev_img}</a> ';
		else
			$pages .= '{first_img_inactive} {prev_img_inactive} ';

		for ($i = $frompage; $i <= $topage; $i++)
		{
			if ($i == $thissite)
				$pages .= $i . ' ';
			else
				$pages .= '<a href="newcaches.php?startat=' . ($i - 1) * 100 . '">' . $i . '</a> ';
		}
		if ($thissite < $topage)
			$pages .= '<a href="newcaches.php?startat=' . ($startat + 100) . '">{next_img}</a> <a href="newcaches.php?startat=' . (ceil($count / 100) * 100 - 100) . '">{last_img}</a>';
		else
			$pages .= '{next_img_inactive} {last_img_inactive}';

		$pages = mb_ereg_replace('{prev_img}', $prev_img, $pages);
		$pages = mb_ereg_replace('{next_img}', $next_img, $pages);
		$pages = mb_ereg_replace('{last_img}', $last_img, $pages);
		$pages = mb_ereg_replace('{first_img}', $first_img, $pages);
		
		$pages = mb_ereg_replace('{prev_img_inactive}', $prev_img_inactive, $pages);
		$pages = mb_ereg_replace('{next_img_inactive}', $next_img_inactive, $pages);
		$pages = mb_ereg_replace('{first_img_inactive}', $first_img_inactive, $pages);
		$pages = mb_ereg_replace('{last_img_inactive}', $last_img_inactive, $pages);
		
		tpl_set_var('pages', $pages);
		tpl_set_var('newcaches_label', tr('new_caches'));
		tpl_set_var('created_by', tr('created_by'));
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
