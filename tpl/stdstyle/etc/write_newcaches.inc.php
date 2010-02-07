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


	// Read coordinates of the newest caches
	$markerpositions = get_marker_positions();

	// Generate include file for map with new caches
	$file_content = '<img src="' . create_map_url($markerpositions, -1) . '" basesrc="' . create_map_url($markerpositions, -1) . '" id="main-cachemap" name="main-cachemap" alt="{{map}}" />';
	$n_file = fopen($dynstylepath . "main_cachemap.inc.php", 'w');
	fwrite($n_file, $file_content);
	fclose($n_file);

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
			
	
	$cacheline =	'<li class="newcache_list_multi" style="margin-bottom:8px;">' .
			'<img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" />&nbsp;{date}&nbsp;' .
			'<a id="newcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}" onmouseover="Lite({nn})" onmouseout="Unlite()" maphref="{smallmapurl}">{cachename}</a>&nbsp;' .
			tr(hidden_by) . '&nbsp;<a class="links" href="viewprofile.php?userid={userid}">{username}</a><br/>' .
			'<b><p class="content-title-noshade">{kraj} {dziubek} {woj}</p></b></li>';
	
	$file_content = '<ul style="font-size: 11px;">';
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = sql_fetch_array($rs);
		setlocale(LC_ALL, "pl_PL");

		$loc = coordToLocation($record['latitude'], $record['longitude']);
		
		$cacheicon = 'tpl/stdstyle/images/'.getSmallCacheIcon($record['icon_large']);
	
		$thisline = $cacheline;
		$thisline = mb_ereg_replace('{nn}', $i, $thisline);
		$thisline = mb_ereg_replace('{kraj}',$loc['kraj'], $thisline);
		$thisline = mb_ereg_replace('{woj}',$loc['woj'], $thisline);
		$thisline = mb_ereg_replace('{miasto}',$loc['miasto'], $thisline);
		$thisline = mb_ereg_replace('{dziubek}',$loc['dziubek'], $thisline);
		$thisline = mb_ereg_replace('{date}', htmlspecialchars(date("d-m-Y", strtotime($record['date'])), ENT_COMPAT, 'UTF-8'), $thisline);
		$thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
		$thisline = mb_ereg_replace('{cache_count}',$i, $thisline);
		$thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
		$thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
		$thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
		$thisline = mb_ereg_replace('{locationstring}', $locationstring, $thisline);
		$thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);
		$thisline = mb_ereg_replace('{smallmapurl}', create_map_url($markerpositions, $i), $thisline);

		$file_content .= $thisline . "\n";
	}

	$file_content .= '</ul>';
	$n_file = fopen($dynstylepath . "start_newcaches.inc.php", 'w');
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
		$file_content = tr("list_of_events_is_empty");
	}
	else
	{
		$cacheline = '<li class="newcache_list_multi" style="margin-bottom:8px;"><img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" />&nbsp;{date}&nbsp;<a id="newcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}" onmouseover="Lite({nn})" onmouseout="Unlite()" maphref="{smallmapurl}">{cachename}</a>&nbsp;' .tr(hidden_by). '&nbsp;<a class="links" href="viewprofile.php?userid={userid}">{username}</a><br/><b><p class="content-title-noshade">{kraj} {dziubek} {woj}</p></b></li>';
		$file_content = '<ul style="font-size: 11px;">';
		for ($i = 0; $i < mysql_num_rows($rs); $i++)
		{
			$record = sql_fetch_array($rs);
			$loc = coordToLocation($record['latitude'], $record['longitude']);
		
			$thisline = $cacheline;
			$thisline = mb_ereg_replace('{nn}', $i + $markerpositions['plain_cache_num'], $thisline);
			$thisline = mb_ereg_replace('{kraj}',$loc['kraj'], $thisline);
			$thisline = mb_ereg_replace('{woj}',$loc['woj'], $thisline);
			$thisline = mb_ereg_replace('{miasto}',$loc['miasto'], $thisline);
			$thisline = mb_ereg_replace('{dziubek}',$loc['dziubek'], $thisline);
			$thisline = mb_ereg_replace('{date}', htmlspecialchars(date("d-m-Y", strtotime($record['date_hidden'])), ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
			$thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
			$thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{locationstring}', $locationstring, $thisline);
			$thisline = mb_ereg_replace('{cacheicon}', 'tpl/stdstyle/images/cache/22x22-event.png', $thisline);
			$thisline = mb_ereg_replace('{smallmapurl}', create_map_url($markerpositions, $i + $markerpositions['plain_cache_num']), $thisline);

			$file_content .= $thisline . "\n";
		}
		$file_content .= '</ul>';
	}

	$n_file = fopen($dynstylepath . "nextevents.inc.php", 'w');
	fwrite($n_file, $file_content);
	fclose($n_file);


function get_marker_positions()
{
	$markerpos = array();
	$markers = array();

	$rs = sql("
		SELECT	`cache_id`, `longitude`, `latitude`, `type`		FROM	`caches`
		WHERE	`type` != 6 AND			`status` = 1 AND
			`date_hidden` <= NOW() AND
			`date_created` <= NOW()
		ORDER BY IF((`date_hidden`>`date_created`), `date_hidden`, `date_created`) DESC, `cache_id` DESC
		LIMIT 0, 10");

	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = sql_fetch_array($rs);
		$lat = $record['latitude'];
		$lon = $record['longitude'];
		$type = $record['type'];
		$markers[] = array('lat' => $lat, 'lon' => $lon, 'type' => $type);
	}

	$markerpos['plain_cache_num'] = count($markers);

	$rs = sql("
		SELECT	`cache_id`, `longitude`, `latitude`, `type`
		FROM	`caches`		WHERE	`date_hidden` >= curdate() AND			`type` = 6 AND			`status` = 1
		ORDER BY `date_hidden` ASC		LIMIT 0, 10");

	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = sql_fetch_array($rs);
		$lat = $record['latitude'];
		$lon = $record['longitude'];
		$type = $record['type'];
		$markers[] = array('lat' => $lat, 'lon' => $lon, 'type' => $type);
	}

	$markerpos['markers'] = $markers;

	return $markerpos;
}

function create_map_url($markerpos, $index)
{
	global $googlemap_key;

	$markers = $markerpos['markers'];
	$markers_str = "markers=color:blue|size:small|";
	$markers_ev_str = "&markers=color:orange|size:small|";
	$sel_marker_str = "";
	foreach ($markers as $i => $marker)
	{
		$lat = sprintf("%.3f", $marker['lat']);
		$lon = sprintf("%.3f", $marker['lon']);
		$type = strtoupper(typeToLetter($marker['type']));
		if (strcmp($type, 'E') == 0)
			if ($i != $index)
				$markers_ev_str .= "$lat,$lon|";
			else
				$sel_marker_str = "&markers=color:orange|label:$type|$lat,$lon|";
		else
			if ($i != $index)
				$markers_str .= "$lat,$lon|";
			else
				$sel_marker_str = "&markers=color:blue|label:$type|$lat,$lon|";
	}

	$google_map = "http://maps.google.com/maps/api/staticmap?center=52.13,19.20&zoom=5&size=250x260&maptype=roadmap&key=".$googlemap_key."&sensor=false&".$markers_str.$markers_ev_str.$sel_marker_str;

	return $google_map;
}

?>
