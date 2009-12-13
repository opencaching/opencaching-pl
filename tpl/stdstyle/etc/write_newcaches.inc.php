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

	****************************************************************************/
//ini_set ('display_errors', On);

	setlocale(LC_TIME, 'pl_PL.UTF-8');

	global $lang, $rootpath;
	// setlocale(LC_ALL, "pl_PL");

	if (!isset($rootpath)) $rootpath = '../../../';

	//include template handling
	require_once($rootpath . 'lib/common.inc.php');
	require_once($rootpath . 'lib/cache_icon.inc.php');
	global $dynbasepath;
	
	$dynbasepath = "./";
	//start_newcaches.include
	$rs = sql("	SELECT	`user`.`user_id` `user_id`,
				`user`.`username` `username`,
				`caches`.`cache_id` `cache_id`,
				`caches`.`name` `name`,
				`caches`.`longitude` `longitude`,
				`caches`.`latitude` `latitude`,
				`caches`.`date_hidden` `date_hidden`,
				`caches`.`date_created` `date_created`,
				IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) AS `date`,
				`caches`.`country` `country`,
				`caches`.`difficulty` `difficulty`,
				`caches`.`terrain` `terrain`,
				`cache_type`.`icon_large` `icon_large`
			FROM `caches`, `user`, `cache_type`
			WHERE `caches`.`user_id`=`user`.`user_id`
			  AND `caches`.`type`!=6
			  AND `caches`.`status`=1
			  AND `caches`.`type`=`cache_type`.`id`
				AND `caches`.`date_hidden` <= NOW() 
				AND `caches`.`date_created` <= NOW() 
			ORDER BY IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) DESC, `caches`.`cache_id` DESC
			LIMIT 0 , 10");

			
	
	$cacheline = '<li class="newcache_list_multi" style="margin-bottom:8px;"><img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" /><b>&nbsp;{date}&nbsp;<a href="viewcache.php?cacheid={cacheid}" onmouseover="Lite(\'c{cache_count}\')" onmouseout="Lite(\'map\')">{cachename}</a> {{hidden_by}} <a href="viewprofile.php?userid={userid}">{username}</a><br/><p class="content-title-noshade">{kraj} {dziubek} {woj}</p></b></li>';
		
	$file_content = '<ul>';
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = sql_fetch_array($rs);
		setlocale(LC_ALL, "pl_PL");

		$loc = coordToLocation($record['latitude'], $record['longitude']);
		
		$cacheicon = 'tpl/stdstyle/images/'.getSmallCacheIcon($record['icon_large']);
	
		$thisline = $cacheline;
		$thisline = mb_ereg_replace('{kraj}',$loc['kraj'], $thisline);
		$thisline = mb_ereg_replace('{woj}',$loc['woj'], $thisline);
		$thisline = mb_ereg_replace('{miasto}',$loc['miasto'], $thisline);
		$thisline = mb_ereg_replace('{dziubek}',$loc['dziubek'], $thisline);
		$thisline = mb_ereg_replace('{date}', htmlspecialchars(date("d.m.Y", strtotime($record['date'])), ENT_COMPAT, 'UTF-8'), $thisline);
		$thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
		$thisline = mb_ereg_replace('{cache_count}',$i, $thisline);
		$thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
		$thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
		$thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
		$thisline = mb_ereg_replace('{locationstring}', $locationstring, $thisline);
		$thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);
//		$thisline = mb_ereg_replace('{cacheicon}', 'tpl/stdstyle/images/'.$record['icon_large'], $thisline);

		$file_content .= $thisline . "\n";
	}

	$file_content .= '</ul>';
	//$n_file = fopen($dynstylepath . "start_newcaches.inc.php", 'w');
	$n_file = fopen("./start_newcaches.inc.php", 'w');
	fwrite($n_file, $file_content);
	fclose($n_file);


	//nextevents.include
	$rs = sql('	SELECT	`user`.`user_id` `user_id`,
				`user`.`username` `username`,
				`caches`.`cache_id` `cache_id`,
				`caches`.`name` `name`,
				`caches`.`longitude` `longitude`,
				`caches`.`latitude` `latitude`,
				`caches`.`date_created` `date_created`,
				`caches`.`country` `country`,
				`caches`.`difficulty` `difficulty`,
				`caches`.`terrain` `terrain`,
				`caches`.`date_hidden`
			FROM `caches`, `user`
			WHERE `user`.`user_id`=`caches`.`user_id`
			  AND `caches`.`date_hidden` >= curdate()
			  AND `caches`.`type` = 6
			  AND `caches`.`status` = 1
			ORDER BY `date_hidden` ASC
			LIMIT 0 , 10', $dblink);

	$file_content = '';
	if (mysql_num_rows($rs) == 0)
	{
		$file_content = '{{list_of_events_is_empty}}';
	}
	else
	{
		$cacheline = '<li class="newcache_list_multi" style="margin-bottom:8px;"><img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" /><b>&nbsp;{date}&nbsp;<a href="viewcache.php?cacheid={cacheid}">{cachename}</a> {{hidden_by}} <a href="viewprofile.php?userid={userid}">{username}</a><br/><p class="content-title-noshade">{kraj} {dziubek} {woj}</p></b></li>';
		$file_content = '<ul>';
		for ($i = 0; $i < mysql_num_rows($rs); $i++)
		{
			$record = sql_fetch_array($rs);
			$loc = coordToLocation($record['latitude'], $record['longitude']);
		
			$thisline = $cacheline;
			$thisline = mb_ereg_replace('{kraj}',$loc['kraj'], $thisline);
			$thisline = mb_ereg_replace('{woj}',$loc['woj'], $thisline);
			$thisline = mb_ereg_replace('{miasto}',$loc['miasto'], $thisline);
			$thisline = mb_ereg_replace('{dziubek}',$loc['dziubek'], $thisline);
			$thisline = mb_ereg_replace('{date}', htmlspecialchars(date("d.m.Y", strtotime($record['date_hidden'])), ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
			$thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
			$thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{locationstring}', $locationstring, $thisline);
		  $thisline = mb_ereg_replace('{cacheicon}', 'tpl/stdstyle/images/cache/22x22-event.png', $thisline);

			$file_content .= $thisline . "\n";
		}
		$file_content .= '</ul>';
	}

	//$n_file = fopen($dynstylepath . "nextevents.inc.php", 'w');
	$n_file = fopen("./nextevents.inc.php", 'w');
	fwrite($n_file, $file_content);
	fclose($n_file);

	// Mini Mapka

	$rs = sql("	SELECT	`user`.`user_id` `user_id`,
				`user`.`username` `username`,
				`caches`.`cache_id` `cache_id`,
				`caches`.`name` `name`,
				`caches`.`longitude` `longitude`,
				`caches`.`latitude` `latitude`,
				`caches`.`date_hidden` `date_hidden`,
				`caches`.`date_created` `date_created`,
				IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) AS `date`,
				`caches`.`country` `country`,
				`caches`.`difficulty` `difficulty`,
				`caches`.`terrain` `terrain`,
				`cache_type`.`icon_large` `icon_large`
			FROM `caches`, `user`, `cache_type`
			WHERE `caches`.`user_id`=`user`.`user_id`
			  AND `type`!=6
			  AND `status`=1
			  AND `caches`.`type`=`cache_type`.`id`
				AND `caches`.`date_hidden` <= NOW() 
				AND `caches`.`date_created` <= NOW()
			ORDER BY IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) DESC, `caches`.`cache_id` DESC
			LIMIT 0, 10");
			


	$markers = array();
	$markers_str = "";
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = sql_fetch_array($rs);
		$long=$record['longitude'];
		$lat=$record['latitude'];
		$markers[] = $lat.",".$long.",tinyblue|";
		$markers_str .= $lat.",".$long.",tinyblue|";
	}
	
	$google_map = "http://maps.google.com/staticmap?center=52.025459,19.204102&zoom=5&size=250x250&maptype=terrain&key=".$googlemap_key."&sensor=false&format=png&markers=".$markers_str;
 //imagejpeg($im,	$dynbasepath."images/mini-mapa/mapa-new.jpg",80);
	$im = imagecreatefrompng($google_map);
  $c0 = imagecolorallocate ($im, 255,0,0);
  $color2 = imagecolorallocate ($im, 0,0,0);
	imagepng($im,	$rootpath."tmp/mapa.png");
	imagedestroy($im);
	
	$rs = sql("	SELECT	`user`.`user_id` `user_id`,
			`user`.`username` `username`,
			`caches`.`cache_id` `cache_id`,
			`caches`.`name` `name`,
			`caches`.`longitude` `longitude`,
			`caches`.`latitude` `latitude`,
			`caches`.`date_hidden` `date_hidden`,
			`caches`.`date_created` `date_created`,
			IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) AS `date`,
			`caches`.`country` `country`,
			`caches`.`difficulty` `difficulty`,
			`caches`.`terrain` `terrain`,
			`cache_type`.`icon_large` `icon_large`,
			`caches`.`type`
		FROM `caches`, `user`, `cache_type`
		WHERE `caches`.`user_id`=`user`.`user_id`
			AND `type`!=6
			AND `caches`.`status`=1
			AND `caches`.`type`=`cache_type`.`id`
			AND `caches`.`date_hidden` <= NOW() 
			AND `caches`.`date_created` <= NOW()
		ORDER BY IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) DESC, `caches`.`cache_id` DESC
		LIMIT 0, 10");	

	$liczba_punktow = mysql_num_rows($rs);
	for ($i = 0; $i < $liczba_punktow; $i++)
	{
		$markers_str = "";
		$record = sql_fetch_array($rs);
		$long=$record['longitude'];
		$lat=$record['latitude'];
		for( $j = 0; $j < $liczba_punktow; $j++ )
		{
			if( $j != $i )
				$markers_str .= $markers[$j];
			else
				$markers_str .= $lat.",".$long.",blue".typeToLetter($record['type'])."|";
		}
		$google_map = "http://maps.google.com/staticmap?center=52.025459,19.204102&zoom=5&size=250x250&maptype=terrain&key=".$googlemap_key."&sensor=false&format=png&markers=".$markers_str;
		$im2 = imagecreatefrompng($google_map);
		imagepng($im2, $rootpath."tmp/".$i.".png");
		imagedestroy($im2);
	}

	//user definied sort function
	function cmp($a, $b)
	{
		if ($a == $b)
		{
			return 0;
		}
		return ($a > $b) ? 1 : -1;
	}	
?>
