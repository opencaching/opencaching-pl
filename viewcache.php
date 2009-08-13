<?php
/***************************************************************************
																./viewcache.php
															-------------------
		begin                : June 24 2004
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

	 view a cache

	 used template(s): viewcache, viewcache_error

	 GET Parameter: cacheid[, desc_lang][, nocrypt]

 ****************************************************************************/
  //prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require_once('lib/cache_icon.inc.php');
	global $caches_list, $usr, $hide_coords, $cache_menu;
	global $dynbasepath;
	
	function onTheList($theArray, $item)
	{
		for( $i=0;$i<count($theArray);$i++)
		{
			if( $theArray[$i] == $item )
				return $i;
		}
		return -1;
	}

	//Preprocessing
	if ($error == false)
	{
		
		//set here the template to process
		if(isset($_REQUEST['print']) && $_REQUEST['print'] == 'y')
			$tplname = 'viewcache_print';
		else
			$tplname = 'viewcache';

		require_once($rootpath . 'lib/caches.inc.php');
		require_once($stylepath . '/lib/icons.inc.php');
		require($stylepath . '/viewcache.inc.php');
		require($stylepath . '/viewlogs.inc.php');
		require($stylepath.'/smilies.inc.php');
		
		$cache_id = 0;
		if (isset($_REQUEST['cacheid']))
		{
			$cache_id = $_REQUEST['cacheid']+0;
		}
		else if (isset($_REQUEST['uuid']))
		{
			$uuid = $_REQUEST['uuid'];

			$rs = sql("SELECT `cache_id` FROM `caches` WHERE uuid='&1' LIMIT 1", $uuid);
			if ($r = sql_fetch_assoc($rs))
			{
				$cache_id = $r['cache_id'];
			}
			mysql_free_result($rs);
		}
		else if (isset($_REQUEST['wp']))
		{
			$wp = $_REQUEST['wp'];
			
			$sql = 'SELECT `cache_id` FROM `caches` WHERE wp_';
			if (mb_strtoupper(mb_substr($wp, 0, 2)) == 'GC')
				$sql .= 'gc';
			else if (mb_strtoupper(mb_substr($wp, 0, 2)) == 'NC')
				$sql .= 'nc';
			else
				$sql .= 'oc';
			
			$sql .= '=\'' . sql_escape($wp) . '\' LIMIT 1';
			
			$rs = sql($sql);
			if ($r = sql_fetch_assoc($rs))
			{
				$cache_id = $r['cache_id'];
			}
			mysql_free_result($rs);
		}

		$no_crypt = 0;
		if (isset($_REQUEST['nocrypt']))
		{
			$no_crypt = $_REQUEST['nocrypt'];
		}

		if ($cache_id != 0)
		{	//mysql_query("SET NAMES 'utf8'");
			//get cache record
			$rs = sql("SELECT `caches`.`cache_id` `cache_id`,
			                  `caches`.`user_id` `user_id`,
			                  `caches`.`status` `status`,
			                  `caches`.`latitude` `latitude`,
			                  `caches`.`longitude` `longitude`,
			                  `caches`.`name` `name`,
			                  `caches`.`type` `type`,
			                  `caches`.`size` `size`,
			                  `caches`.`search_time` `search_time`,
			                  `caches`.`way_length` `way_length`,
			                  `caches`.`country` `country`,
			                  `caches`.`logpw` `logpw`,
			                  `caches`.`date_hidden` `date_hidden`,
			                  `caches`.`wp_oc` `wp_oc`,
			                  `caches`.`wp_gc` `wp_gc`,
			                  `caches`.`wp_nc` `wp_nc`,
			                  `caches`.`date_created` `date_created`,
			                  `caches`.`difficulty` `difficulty`,
			                  `caches`.`terrain` `terrain`,
			                  `caches`.`founds` `founds`,
			                  `caches`.`notfounds` `notfounds`,
			                  `caches`.`notes` `notes`,
			                  `caches`.`watcher` `watcher`,
												`caches`.`votes` `votes`,
												`caches`.`score` `score`,
			                  `caches`.`picturescount` `picturescount`,
			                  `caches`.`desc_languages` `desc_languages`,
				          `caches`.`topratings` `topratings`,
			                  `caches`.`ignorer_count` `ignorer_count`,
												`caches`.`votes` `votes_count`,
			                  `cache_type`.`icon_large` `icon_large`,
			                  `user`.`username` `username`
			             FROM `caches`, `cache_type`, `user`
				          WHERE `caches`.`user_id` = `user`.`user_id` AND
					              `cache_type`.`id`=`caches`.`type` AND
					              `caches`.`cache_id`='&1'", $cache_id);

			if (mysql_num_rows($rs) == 0)
			{
				$cache_id = 0;
			}
			else
			{
				$cache_record = sql_fetch_array($rs);
			}
			mysql_free_result($rs);
			if( $cache_record['user_id'] == $usr['userid'] || $usr['admin'])
			{
				$show_edit = true;
			}
			else
				$show_edit = false;
			//mysql_query("SET NAMES 'utf8'");
			//get last last_modified
			$rs = sql("SELECT MAX(`last_modified`) `last_modified` FROM
			             (SELECT `last_modified` FROM `caches` WHERE `cache_id` ='&1'
				            UNION
				            SELECT `last_modified` FROM `cache_desc` WHERE `cache_id` ='&1') `tmp_result`", 
				            $cache_id);

			if (mysql_num_rows($rs) == 0)
			{
				$cache_id = 0;
			}
			else
			{
				$lm = sql_fetch_array($rs);
				$last_modified = strtotime($lm['last_modified']);
				tpl_set_var('last_modified', htmlspecialchars(strftime("%d %B %Y", $last_modified), ENT_COMPAT, 'UTF-8'));
			}
			mysql_free_result($rs);
			unset($ls);
		}

		if( isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'y')
		{
			// add cache to print (do not duplicate items)
			if( count($_SESSION['print_list']) == 0 )
				$_SESSION['print_list'] = array();
			if( onTheList($_SESSION['print_list'], $cache_id) == -1 )
				array_push($_SESSION['print_list'],$cache_id);
		}
		if( isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'n')
		{
			// remove cache from print list
			while( onTheList($_SESSION['print_list'], $cache_id) != -1 )
				unset($_SESSION['print_list'][onTheList($_SESSION['print_list'], $cache_id)]);
			$_SESSION['print_list'] = array_values($_SESSION['print_list']);
		}
		
		if ($cache_id != 0 && (($cache_record['status'] != 5 && ($cache_record['status'] != 6 /*|| $cache_record['type'] == 6*/))|| $usr['userid'] == $cache_record['user_id'] || $usr['admin'] ))
		{
			//ok, cache is here, let's process
			$owner_id = $cache_record['user_id'];
			
			// get cache waypoint
			$cache_wp = '';
			if( $cache_record['wp_oc'] != '' ) 
				$cache_wp = $cache_record['wp_oc'];
			else if( $cache_record['wp_gc'] != '' ) 
				$cache_wp = $cache_record['wp_gc'];
			else if( $cache_record['wp_nc'] != '' ) 
				$cache_wp = $cache_record['wp_nc'];
			
			// check if there is geokret in this cache
			//mysql_query("SET NAMES 'utf8'");
			$geokret_sql = "SELECT id, name, distancetravelled as distance FROM gk_item WHERE id IN (SELECT id FROM gk_item_waypoint WHERE wp = '".sql_escape($cache_wp)."') AND stateid<>1 AND stateid<>4 AND typeid<>2";
			$geokret_query = sql($geokret_sql);
			if (mysql_num_rows($geokret_query) == 0)
			{
				// no geokrets in this cache
				tpl_set_var('geokrety_begin', '<!--');
				tpl_set_var('geokrety_end', '-->');
				tpl_set_var('geokrety_content', '');
			}
			else
			{
				// geokret is present in this cache
				$geokrety_content = '';
				while( $geokret = sql_fetch_array($geokret_query) )
				{
					$geokrety_content .= "- <a href='http://geokrety.org/konkret.php?id=".$geokret['id']."'>".$geokret['name']."</a> - ".$language[$lang]['total_distance'].": ".$geokret['distance']." km<br/>";
//					$geokrety_content .= "Przebyty dystans: ".$geokret['distance']."km<br><br>";
				}
				tpl_set_var('geokrety_begin', '');
				tpl_set_var('geokrety_end', '');
				tpl_set_var('geokrety_content', $geokrety_content);


			}
			mysql_free_result($geokret_query);
						
			
			if( $cache_record['votes'] < 3 )
			{
				// DO NOT show cache's score
				$score = "";
				$scorecolor = "";
				$font_size = "";
				tpl_set_var('noscore_start', "");
				tpl_set_var('noscore_end', "");
				tpl_set_var('score_start', "<!--");
				tpl_set_var('score_end', "-->");
			}
			else
			{
				// show cache's score
				
				tpl_set_var('noscore_start', "<!--");
				tpl_set_var('noscore_end', "-->");
				tpl_set_var('score_start', "");
				tpl_set_var('score_end', "");
				$score = sprintf("%.1f",$cache_record['score']);
				$font_size = "4";
				if( $score <= 0.5 )
					$scorecolor = "#FF0000";
				else
				if( $score > 0.5 && $score <= 1.0 )
					$scorecolor = "#FF3300";
				else
				if( $score > 1.0 && $score <= 1.5 )
					$scorecolor = "#FF6600";
				else
				if( $score > 1.5 && $score <= 3.5 )
					$scorecolor = "#FF9900";
				else
				if( $score > 3.5 && $score <= 4.5 )
					$scorecolor = "#99FF00";
				else
				if( $score > 4.5 && $score <= 5.0 )
					$scorecolor = "#66FF00";
				else
				if( $score > 5.0 && $score <= 5.5 )
					$scorecolor = "#33FF00";
				else
				if( $score > 5.5)
					$scorecolor = "#00FF00";
			}
			tpl_set_var('score', $score);
			tpl_set_var('scorecolor', $scorecolor);
			tpl_set_var('font_size', $font_size);
			// begin visit-counter
			// delete cache_visits older 1 day 60*60*24 = 86400
			sql("DELETE FROM `cache_visits` WHERE `cache_id`=&1 AND `user_id_ip` != '0' AND NOW()-`last_visited` > 86400", $cache_id);

			// first insert record for visit counter if not in db
			$chkuserid = isset($usr['userid']) ? $usr['userid'] : $_SERVER["REMOTE_ADDR"];
			
			// note the visit of this user
			sql("INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`, `last_visited`) VALUES (&1, '&2', 1, NOW())
					ON DUPLICATE KEY UPDATE `count`=`count`+1", $cache_id, $chkuserid);

			if ($chkuserid != $owner_id)
			{
				// if the previous statement does an INSERT, it was the first visit for this user
				if (mysql_affected_rows($dblink) == 1)
				{
					// increment the counter for this cache
					sql("INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`, `last_visited`) VALUES (&1, '0', 1, NOW())
							ON DUPLICATE KEY UPDATE `count`=`count`+1, `last_visited`=NOW()", $cache_id);
				}
			}
			// end visit-counter

			// hide coordinates when user is not logged in
			if( $usr == true || !$hide_coords)
			{
				$coords = mb_ereg_replace(" ", "&nbsp;",htmlspecialchars(help_latToDegreeStr($cache_record['latitude']), ENT_COMPAT, 'UTF-8')) . '&nbsp;' . mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_lonToDegreeStr($cache_record['longitude']), ENT_COMPAT, 'UTF-8'));
				$coords_other = "<a href=\"#\" onclick=\"javascript:window.open('http://www.opencaching.de/coordinates.php?lat=".$cache_record['latitude']."&amp;lon=".$cache_record['longitude']."&amp;popup=y&amp;wp=".htmlspecialchars($cache_record['wp_oc'], ENT_COMPAT, 'UTF-8')."','Koordinatenumrechnung','width=240,height=334,resizable=no,scrollbars=0')\">".$language[$lang]['coords_other']."</a>";
			}
			else
			{
				$coords = $language[$lang]['hidden_coords'];
				$coords_other = "";
			}

			//cache data
			list($iconname) = getCacheIcon($usr['userid'], $cache_record['cache_id'], $cache_record['status'], $cache_record['user_id'], $cache_record['icon_large']);

			list($lat_dir, $lat_h, $lat_min) = help_latToArray($cache_record['latitude']);
			list($lon_dir, $lon_h, $lon_min) = help_lonToArray($cache_record['longitude']);

			$tpl_subtitle = htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8') . ' - ';
			$map_msg = mb_ereg_replace("{target}", urlencode("viewcache.php?cacheid=".$cache_id), $language[$lang]['map_msg']);
			
			tpl_set_var('googlemap_key', $googlemap_key);
			tpl_set_var('map_msg', $map_msg);
			tpl_set_var('cache', $language[$lang]['cache']);
			tpl_set_var('created_by', $language[$lang]['created_by']);
			tpl_set_var('coords_other', $coords_other);
			tpl_set_var('size', $language[$lang]['size']);
			tpl_set_var('time', $language[$lang]['time']);
			tpl_set_var('length', $language[$lang]['length']);
			tpl_set_var('status_label', $language[$lang]['status_label']);
			tpl_set_var('date_hidden_label', $language[$lang]['date_hidden_label']);
			tpl_set_var('date_created_label', $language[$lang]['date_created_label']);
			tpl_set_var('last_modified_label', $language[$lang]['last_modified_label']);
			tpl_set_var('waypoint', $language[$lang]['waypoint']);
			tpl_set_var('listed_also_on', $language[$lang]['listed_also_on']);
			tpl_set_var('comments', $language[$lang]['comments']);
			tpl_set_var('scored', $language[$lang]['scored']);
			tpl_set_var('watchers', $language[$lang]['watchers']);
			tpl_set_var('visitors', $language[$lang]['visitors']);
			tpl_set_var('description', $language[$lang]['description']);
			tpl_set_var('additional_hints', $language[$lang]['additional_hints']);
			tpl_set_var('images', $language[$lang]['images']);
			tpl_set_var('utilities', $language[$lang]['utilities']);
			tpl_set_var('log_entries', $language[$lang]['log_entries']);
			tpl_set_var('cache_attributes_label', $language[$lang]['cache_attributes']);
			tpl_set_var('download_as_file', $language[$lang]['download_as_file']);
			tpl_set_var('search_geocaches_nearby', $language[$lang]['search_geocaches_nearby']);
			tpl_set_var('accept_terms_of_use', $language[$lang]['accept_terms_of_use']);
			tpl_set_var('find_geocaches_on', $language[$lang]['find_geocaches_on']);
			tpl_set_var('send_to_gps', $language[$lang]['send_to_gps']);
			tpl_set_var('searchable', $language[$lang]['searchable']);
			tpl_set_var('all_geocaches', $language[$lang]['all_geocaches']);
			tpl_set_var('typeLetter', typeToLetter($cache_record['type']));
			
			tpl_set_var('cacheid_urlencode', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'));
			tpl_set_var('cachename', htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('coords', $coords);
			if( $usr || !$hide_coords )
			{
				tpl_set_var('longitude', $cache_record['longitude']);
				tpl_set_var('latitude',  $cache_record['latitude']);
				tpl_set_var('lon_h', $lon_h);
				tpl_set_var('lon_min', $lon_min);
				tpl_set_var('lonEW', $lon_dir);
				tpl_set_var('lat_h', $lat_h);
				tpl_set_var('lat_min', $lat_min);
				tpl_set_var('latNS', $lat_dir);
			}
			tpl_set_var('cacheid', $cache_id);
			tpl_set_var('cachetype', htmlspecialchars(cache_type_from_id($cache_record['type'], $lang), ENT_COMPAT, 'UTF-8'));
//			tpl_set_var('icon_cache', htmlspecialchars("$stylepath/images/".$cache_record['icon_large'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('icon_cache', htmlspecialchars("$stylepath/images/$iconname", ENT_COMPAT, 'UTF-8'));
			tpl_set_var('cachesize', htmlspecialchars(cache_size_from_id($cache_record['size'], $lang), ENT_COMPAT, 'UTF-8'));
			tpl_set_var('oc_waypoint', htmlspecialchars($cache_record['wp_oc'], ENT_COMPAT, 'UTF-8'));
			if ($cache_record['topratings'] == 1)
				tpl_set_var('rating_stat', mb_ereg_replace('{ratings}', $cache_record['topratings'], $rating_stat_show_singular));
			else if ($cache_record['topratings'] > 1)
				tpl_set_var('rating_stat', mb_ereg_replace('{ratings}', $cache_record['topratings'], $rating_stat_show_plural));
			else
				tpl_set_var('rating_stat', '');


			if ((($cache_record['way_length'] == null) && ($cache_record['search_time'] == null)) ||
			    (($cache_record['way_length'] == 0) && ($cache_record['search_time'] == 0)))
			{
				tpl_set_var('hidetime_start', '<!-- ');
				tpl_set_var('hidetime_end', ' -->');

				tpl_set_var('search_time', 'b.d.');
				tpl_set_var('way_length', 'b.d.');
			}
			else
			{
				tpl_set_var('hidetime_start', '');
				tpl_set_var('hidetime_end', '');

				if (($cache_record['search_time'] == null) || ($cache_record['search_time'] == 0))
					tpl_set_var('search_time', 'b.d.');
				else
				{
					$time_hours = floor($cache_record['search_time']);
					$time_min = sprintf('%02d', ($cache_record['search_time'] - $time_hours) * 60);
					tpl_set_var('search_time', $time_hours . ':' . $time_min . ' h');
				}

				if (($cache_record['way_length'] == null) || ($cache_record['way_length'] == 0))
					tpl_set_var('way_length', 'b.d.');
				else
					tpl_set_var('way_length', sprintf('%01.2f km', $cache_record['way_length']));
			}

			tpl_set_var('country', htmlspecialchars(db_CountryFromShort($cache_record['country']), ENT_COMPAT, 'UTF-8'));
			tpl_set_var('cache_log_pw', (($cache_record['logpw'] == NULL) || ($cache_record['logpw'] == '')) ? '' : $cache_log_pw);
			tpl_set_var('nocrypt', $no_crypt);
			$hidden_date = strtotime($cache_record['date_hidden']);
			tpl_set_var('hidden_date', htmlspecialchars(strftime("%d %B %Y", $hidden_date), ENT_COMPAT, 'UTF-8'));

			$listed_on = array();
			if($cache_record['wp_gc'] != '')
				$listed_on[] = '<a href="http://www.geocaching.com/seek/cache_details.aspx?wp='.$cache_record['wp_gc'].'" target="_blank">geocaching.com</a>';

			if($cache_record['wp_nc'] != '')
				$listed_on[] = '<a href="http://geocaching.gpsgames.org/cgi-bin/ge.pl?wp='.$cache_record['wp_nc'].'" target="_blank">GPSgames.org</a>';

			tpl_set_var('listed_on', sizeof($listed_on) == 0 ? $listed_only_oc : implode(", ", $listed_on));
			if (sizeof($listed_on) == 0)
			{
				tpl_set_var('hidelistingsites_start', '<!--');
				tpl_set_var('hidelistingsites_end', '-->');
			}
			else
			{
				tpl_set_var('hidelistingsites_start', '');
				tpl_set_var('hidelistingsites_end', '');
			}

			//cache available
			if ($cache_record['status'] != 1)
			{
				tpl_set_var('status', $error_prefix . htmlspecialchars(cache_status_from_id($cache_record['status'], $lang), ENT_COMPAT, 'UTF-8') . $error_suffix);
			}
			else
			{
				tpl_set_var('status', htmlspecialchars(cache_status_from_id($cache_record['status'], $lang), ENT_COMPAT, 'UTF-8'));
			}

			$date_created = strtotime($cache_record['date_created']);
			tpl_set_var('date_created', htmlspecialchars(strftime("%d %B %Y", $date_created), ENT_COMPAT, 'UTF-8'));

			tpl_set_var('difficulty_icon_diff', icon_difficulty("diff", $cache_record['difficulty']));
			tpl_set_var('difficulty_text_diff', htmlspecialchars(sprintf($difficulty_text_diff, $cache_record['difficulty'] / 2), ENT_COMPAT, 'UTF-8'));
			tpl_set_var('difficulty_icon_terr', icon_difficulty("terr", $cache_record['terrain']));
			tpl_set_var('difficulty_text_terr', htmlspecialchars(sprintf($difficulty_text_terr, $cache_record['terrain'] / 2), ENT_COMPAT, 'UTF-8'));

			tpl_set_var('founds', htmlspecialchars($cache_record['founds'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('notfounds', htmlspecialchars($cache_record['notfounds'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('notes', htmlspecialchars($cache_record['notes'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('total_number_of_logs', htmlspecialchars($cache_record['notes'] + $cache_record['notfounds'] + $cache_record['founds'], ENT_COMPAT, 'UTF-8'));

			// number of watchers
//			$rs = sql("SELECT COUNT(*) as `count` FROM `cache_watches` WHERE `cache_id`='&1'", $cache_id);
//			if (mysql_num_rows($rs) == 0)
//				tpl_set_var('watcher', '0');
//			else
//			{
//				$watcher_record = sql_fetch_array($rs);
//				tpl_set_var('watcher', $watcher_record['count']);
//			}
			tpl_set_var('watcher', $cache_record['watcher'] + 0);
			tpl_set_var('ignorer_count', $cache_record['ignorer_count'] + 0);
			tpl_set_var('votes_count', $cache_record['votes_count'] + 0);

			tpl_set_var('note_icon', $note_icon);
			tpl_set_var('vote_icon', $vote_icon);
			tpl_set_var('watch_icon', $watch_icon);
			tpl_set_var('visit_icon', $visit_icon);
			
			if ($cache_record['type'] == 6)
			{
				tpl_set_var('found_icon', $exist_icon);
				tpl_set_var('notfound_icon', $trash_icon);

				$event_attendance_list = mb_ereg_replace('{id}', urlencode($cache_id), $event_attendance_list);
				tpl_set_var('event_attendance_list', $event_attendance_list);
				tpl_set_var('found_text', $event_attended_text);
				tpl_set_var('notfound_text', $event_will_attend_text);
			}
			else
			{
				tpl_set_var('found_icon', $found_icon);
				tpl_set_var('notfound_icon', $notfound_icon);

				tpl_set_var('event_attendance_list', '');
				tpl_set_var('found_text', $cache_found_text);
				tpl_set_var('notfound_text', $cache_notfound_text);
			}

			// number of visits
			$rs = sql("SELECT `count` FROM `cache_visits` WHERE `cache_id`='&1' AND `user_id_ip`='0'", $cache_id);
			if (mysql_num_rows($rs) == 0)
				tpl_set_var('visits', '0');
			else
			{
				$watcher_record = sql_fetch_array($rs);
				tpl_set_var('visits', $watcher_record['count']);
			}


			if (($cache_record['founds'] + $cache_record['notfounds'] + $cache_record['notes']) > $logs_to_display)
			{
				tpl_set_var('viewlogs_last', mb_ereg_replace('{cacheid_urlencode}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $viewlogs_last));
				tpl_set_var('viewlogs', mb_ereg_replace('{cacheid_urlencode}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $viewlogs));
				tpl_set_var('viewlogs_start', "");
				tpl_set_var('viewlogs_end', "");
			}
			else
			{
				tpl_set_var('viewlogs_last', '');
				tpl_set_var('viewlogs', '');
				tpl_set_var('viewlogs_start', "<!--");
				tpl_set_var('viewlogs_end', "-->");
			}

			tpl_set_var('cache_watcher', '');
			if ($cache_record['watcher'] > 0)
			{
				tpl_set_var('cache_watcher', mb_ereg_replace('{watcher}', htmlspecialchars($cache_record['watcher'], ENT_COMPAT, 'UTF-8'), $cache_watchers));
			}

			tpl_set_var('owner_name', htmlspecialchars($cache_record['username'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('userid_urlencode', htmlspecialchars(urlencode($cache_record['user_id']), ENT_COMPAT, 'UTF-8'));

			//get description languages
			$desclangs = mb_split(',', $cache_record['desc_languages']);

			$desclang = mb_strtoupper($lang);
			//is a description language wished?
			if (isset($_REQUEST['desclang']))
			{
				$desclang = $_REQUEST['desclang'];
			}
			
			$enable_google_translation = false;
			
			//is no description available in the wished language?
			if (array_search($desclang, $desclangs) === false)
			{
				$desclang = $desclangs[0];
			}

			if( strtolower($desclang) != $lang && $lang != 'pl' )
				$enable_google_translation = true;
			else
				$enable_google_translation = false;
			
			//build langs list
			$langlist = '';
			foreach ($desclangs AS $desclanguage)
			{
				if ($langlist != '') $langlist .= ', ';

				$langlist .= '<a href="viewcache.php?cacheid=' . urlencode($cache_id) . '&amp;desclang=' . urlencode($desclanguage) . $linkargs .'">';
				if ($desclanguage == $desclang)
				{
					$langlist .= '<i>' . htmlspecialchars($desclanguage, ENT_COMPAT, 'UTF-8') . '</i>';
				}
				else
				{
					$langlist .= htmlspecialchars($desclanguage, ENT_COMPAT, 'UTF-8');
				}
				$langlist .= '</a>';
			}

			tpl_set_var('desc_langs', $langlist);

			// show pictures
			//

			if ($cache_record['picturescount'] > 0)
			{
				//if(isset($_REQUEST['print']) && $_REQUEST['print'] == 'y')
					//tpl_set_var('pictures', viewcache_getpicturestable($cache_id, true, true, false, true, $cache_record['picturescount']));
				if( isset($_REQUEST['spoiler_only']) && $_REQUEST['spoiler_only'] == 1 )
					$spoiler_only = true;
				else
					$spoiler_only = false;
				if(isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'big')
					tpl_set_var('pictures', viewcache_getfullsizedpicturestable($cache_id, true, $spoiler_only, $cache_record['picturescount']));
				else if(isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'small')
					tpl_set_var('pictures', viewcache_getpicturestable($cache_id, true, true, $spoiler_only, true, $cache_record['picturescount']));
				else if(isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'no')
					tpl_set_var('pictures', "");
				else
					tpl_set_var('pictures', viewcache_getpicturestable($cache_id, true, true, false, false, $cache_record['picturescount']));

				tpl_set_var('hidepictures_start', '');
				tpl_set_var('hidepictures_end', '');
			}
			else
			{
				tpl_set_var('pictures', '<br>');
				tpl_set_var('hidepictures_start', '<!--');
				tpl_set_var('hidepictures_end', '-->');
			}

			// add RR comment
			if( $usr['admin'] && isset($_POST['rr_comment']) && $_POST['rr_comment']!= "" && $_SESSION['submitted'] != true)
			{
				$rr_comment = nl2br($_POST['rr_comment']);
				$sql = "UPDATE cache_desc 
					SET rr_comment=CONCAT('".sql_escape($rr_comment)."<br/>', rr_comment), 
							last_modified = NOW() 
					WHERE cache_id='".sql_escape(intval($cache_id))."'";
				@mysql_query($sql);
				$_SESSION['submitted'] = true;
			}
			
			// remove RR comment
			if( $usr['admin'] && isset($_GET['removerrcomment']) && isset($_GET['cacheid']) )
			{
				$sql = "UPDATE cache_desc SET rr_comment='' WHERE cache_id='".sql_escape(intval($cache_id))."'";
				@mysql_query($sql);
			}
			
			// show descriptions
			//
			$rs = sql("SELECT `short_desc`, `desc`, `desc_html`, `hint`, `rr_comment` FROM `cache_desc` WHERE `cache_id`='&1' AND `language`='&2'", sql_escape($cache_id), sql_escape($desclang));
			$desc_record = sql_fetch_array($rs);
			mysql_free_result($rs);

			$short_desc = $desc_record['short_desc'];

			//replace { and } to prevent replacing
			$short_desc = mb_ereg_replace('{', '&#0123;', $short_desc);
			$short_desc = mb_ereg_replace('}', '&#0125;', $short_desc);
			tpl_set_var('short_desc', htmlspecialchars($short_desc, ENT_COMPAT, 'UTF-8'));

			//replace { and } to prevent replacing
			$desc = $desc_record['desc'];
			$desc = mb_ereg_replace('{', '&#0123;', $desc);
			$desc = mb_ereg_replace('}', '&#0125;', $desc);

			// TODO: UTF-8 compatible str_replace (with arrays)
			$desc = str_replace($smileytext, $smileyimage, $desc);

			if ($desc_record['desc_html'] == 0)
				$desc = help_addHyperlinkToURL($desc);
			$res = '';
			
			tpl_set_var('desc', $desc, true);
			
			if( $usr['admin'] )
			{
				tpl_set_var('add_rr_comment', '[<a href="add_rr_comment.php?cacheid='.$cache_id.'">'.$language[$lang]['add_rr_comment'].'</a>]');
				if( $desc_record['rr_comment'] == "" )
					tpl_set_var('remove_rr_comment', '');
				else
					tpl_set_var('remove_rr_comment', '[<a href="viewcache.php?cacheid='.$cache_id.'&amp;removerrcomment=1">'.$language[$lang]['remove_rr_comment'].'</a>]');
				
			}
			else
			{
				tpl_set_var('add_rr_comment', '');
				tpl_set_var('remove_rr_comment', '');
			}
			
			if( $desc_record['rr_comment'] != "" )
			{
				tpl_set_var('start_rr_comment', '', true);
				tpl_set_var('end_rr_comment','', true);
				tpl_set_var('rr_comment_label', $language[$lang]['rr_comment_label']);
				tpl_set_var('rr_comment', $desc_record['rr_comment'], true);
			}
			else
			{
				tpl_set_var('rr_comment_label', '', true);
				tpl_set_var('rr_comment', '', true);
				tpl_set_var('start_rr_comment', '<!--', true);
				tpl_set_var('end_rr_comment','-->', true);
				$_POST['rr_comment']="";
			}
			// show hints
			//
			$hint = $desc_record['hint'];

			if ($hint == '')
			{
				tpl_set_var('cryptedhints', '');
				tpl_set_var('hints', '');
				tpl_set_var("decrypt_link_start", '');
				tpl_set_var("decrypt_link_end", '');
				tpl_set_var("decrypt_table_start", '');
				tpl_set_var("decrypt_table_end", '');

				tpl_set_var('hidehint_start', '<!--');
				tpl_set_var('hidehint_end', '-->');
			}
			else
			{
				tpl_set_var('hidehint_start', '');
				tpl_set_var('hidehint_end', '');

				if ($no_crypt == 0)
				{
					$link = mb_ereg_replace('{cacheid_urlencode}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $decrypt_link);
					$link = mb_ereg_replace('{desclang}', htmlspecialchars(urlencode($desclang), ENT_COMPAT, 'UTF-8'), $link);

					tpl_set_var('decrypt_link', $link);
					tpl_set_var("decrypt_link_start", '');
					tpl_set_var("decrypt_link_end", '');
					tpl_set_var("decrypt_table_start", '');
					tpl_set_var("decrypt_table_end", '');

					//crypt the hint ROT13, but keep HTML-Tags and Entities
					$hint = str_rot13_html($hint);

					//TODO: mark all that isn't ROT13 coded
				}
				else
				{
					$cryptedhints = mb_ereg_replace('{decrypt_link}', '', $cryptedhints);
					tpl_set_var("decrypt_link_start", "<!--");
					tpl_set_var("decrypt_link_end", "-->");
					tpl_set_var("decrypt_table_start", "<!--");
					tpl_set_var("decrypt_table_end", "-->");
				}

				//replace { and } to prevent replacing
				$hint = mb_ereg_replace('{', '&#0123;', $hint);
				$hint = mb_ereg_replace('}', '&#0125;', $hint);

				tpl_set_var('hints', $hint);
			}

			// prepare the last n logs
			//
			$rs = sql("SELECT `cache_logs`.`user_id` `userid`,
			                  `cache_logs`.`id` `logid`,
			                  `cache_logs`.`date` `date`,
			                  `cache_logs`.`type` `type`,
			                  `cache_logs`.`text` `text`,
			                  `cache_logs`.`text_html` `text_html`,
			                  `cache_logs`.`picturescount` `picturescount`,
			                  `user`.`username` `username`,
			                  `log_types`.`icon_small` `icon_small`,
			                  `log_types_text`.`text_listing` `text_listing`,
			                  IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`
			             FROM `cache_logs` INNER JOIN `log_types` ON `cache_logs`.`type`=`log_types`.`id`
			                               INNER JOIN `log_types_text` ON `log_types`.`id`=`log_types_text`.`log_types_id` AND `log_types_text`.`lang`='&2'
			                               INNER JOIN `user` ON `cache_logs`.`user_id`=`user`.`user_id`
			                               LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
			            WHERE `cache_logs`.`cache_id`='&1'
			         ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`id` DESC
			            LIMIT &3", $cache_id, $lang, $logs_to_display+0);

			$logs = '';
			for ($i = 0; $i < mysql_num_rows($rs); $i++)
			{
				$record = sql_fetch_array($rs);
				$tmplog = read_file($stylepath . '/viewcache_log.tpl.php');

				$tmplog_username = htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8');
				$tmplog_date = htmlspecialchars(strftime("%d %B %Y", strtotime($record['date'])), ENT_COMPAT, 'UTF-8');
				$tmplog_text = $record['text'];

				// replace smilies in log-text with images and add hyperlinks
				$tmplog_text = str_replace($smileytext, $smileyimage, $tmplog_text);

				if ($record['text_html'] == 0)
					$tmplog_text = help_addHyperlinkToURL($tmplog_text);

				if ($record['picturescount'] > 0)
				{
					$logpicturelines = '';
					$rspictures = sql("SELECT `url`, `title`, `user_id`, `uuid` FROM `pictures` WHERE `object_id`='&1' AND `object_type`=1", $record['logid']);

					for ($j = 0; $j < mysql_num_rows($rspictures); $j++)
					{
						$pic_record = sql_fetch_array($rspictures);
						$thisline = $logpictureline;

						$thisline = mb_ereg_replace('{link}', $pic_record['url'], $thisline);
						$thisline = mb_ereg_replace('{title}', htmlspecialchars($pic_record['title'], ENT_COMPAT, 'UTF-8'), $thisline);

						if ($pic_record['user_id'] == $usr['userid'])
							$thisline = mb_ereg_replace('{functions}', mb_ereg_replace('{uuid}', $pic_record['uuid'], $remove_picture), $thisline);
						else
							$thisline = mb_ereg_replace('{functions}', '', $thisline);

						$logpicturelines .= $thisline;
					}
					mysql_free_result($rspictures);

					$logpicturelines = mb_ereg_replace('{lines}', $logpicturelines, $logpictures);
					$tmplog = mb_ereg_replace('{logpictures}', $logpicturelines, $tmplog);
				}
				else
					$tmplog = mb_ereg_replace('{logpictures}', '', $tmplog);

				// logfunktionen erstellen
				if ((!isset($_REQUEST['print']) || $_REQUEST['print'] != 'y') && (($usr['userid'] == $record['userid']) || ($usr['userid'] == $cache_record['user_id']) || $usr['admin']))
				{
					$tmpFunctions = $functions_start;

					if ($usr['userid'] == $record['userid'] || $usr['admin'])
					{
						$tmpFunctions .= $edit_log . $functions_middle;
					}
					$tmpFunctions .= $remove_log;

					if ($usr['userid'] == $record['userid'])
						$tmpFunctions = $tmpFunctions . $functions_middle . $upload_picture;

					$tmpFunctions .= $functions_end;

					$tmpFunctions = mb_ereg_replace('{logid}', $record['logid'], $tmpFunctions);

					$tmplog = mb_ereg_replace('{logfunctions}', $tmpFunctions, $tmplog);
				}
				else
					$tmplog = mb_ereg_replace('{logfunctions}', '', $tmplog);

				$tmplog = mb_ereg_replace('{username}', $tmplog_username, $tmplog);
				$tmplog = mb_ereg_replace('{userid}', $record['userid'], $tmplog);
				$tmplog = mb_ereg_replace('{date}', $tmplog_date, $tmplog);
				$tmplog = mb_ereg_replace('{type}', $record['text_listing'], $tmplog);
				$tmplog = mb_ereg_replace('{logtext}', $tmplog_text, $tmplog);
				$tmplog = mb_ereg_replace('{logimage}', icon_log_type($record['icon_small'], $tmplog['type']), $tmplog);

				if ($record['recommended'] == 1)
					$tmplog = mb_ereg_replace('{ratingimage}', $rating_picture, $tmplog);
				else
					$tmplog = mb_ereg_replace('{ratingimage}', '', $tmplog);

				$logs .= "$tmplog\n";
			}

			//replace { and } to prevent replacing
			$logs = mb_ereg_replace('{', '&#0123;', $logs);
			$logs = mb_ereg_replace('}', '&#0125;', $logs);

			tpl_set_var('logs', $logs, true);

			// action functions
			$edit_action = "";
			$log_action = "";
			$watch_action = "";
			$ignore_action = "";
			$print_action = "";
			
			
			//is this cache watched by this user?
				$rs = sql("SELECT * FROM `cache_watches` WHERE `cache_id`='&1' AND `user_id`='&2'", $cache_id, $usr['userid']);
				if (mysql_num_rows($rs) == 0)
				{
					$watch_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_watch);
					$is_watched = 'watchcache.php?cacheid='.$cache_id.'&amp;target=viewcache.php%3Fcacheid='.$cache_id;
					$watch_label = $language[$lang]['watch'];
				}
				else
				{
					$watch_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_watch_not);
					$is_watched = 'removewatch.php?cacheid='.$cache_id.'&amp;target=viewcache.php%3Fcacheid='.$cache_id;
					$watch_label = $language[$lang]['watch_not'];
				}
				//is this cache ignored by this user?
				$rs = sql("SELECT `cache_id` FROM `cache_ignore` WHERE `cache_id`='&1' AND `user_id`='&2'", $cache_id, $usr['userid']);
				if (mysql_num_rows($rs) == 0)
				{
					$ignore_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_ignore);
					$is_ignored = "addignore.php?cacheid=".$cache_id."&amp;target=viewcache.php%3Fcacheid%3D".$cache_id;
					$ignore_label = $language[$lang]['ignore'];
				}
				else
				{
					$ignore_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_ignore_not);
					$is_ignored = "removeignore.php?cacheid=".$cache_id."&amp;target=viewcache.php%3Fcacheid%3D".$cache_id;
					$ignore_label = $language[$lang]['ignore_not'];
				}

					
				mysql_free_result($rs);

				
			if ($usr !== false)
			{
				//user logged in => he can log
				$log_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_log);
				
				
				$printt=$language[$lang]['print'];
				$addToPrintList = $language[$lang]['add_to_list'];
				$removeFromPrintList = $language[$lang]['remove_from_list'];
							
				if( onTheList($_SESSION['print_list'], $cache_id)==-1 )
				{
					$print_list = "viewcache.php?cacheid=$cache_id&amp;print_list=y";
					$print_list_label = $addToPrintList;
				}
				else
				{
					$print_list = "viewcache.php?cacheid=$cache_id&amp;print_list=n";
					$print_list_label = $removeFromPrintList;
				}
				
				$cache_menu = array(
					'title' => "Menu skrzynki",
					'menustring' => "Menu skrzynki",
					'siteid' => 'cachelisting',
					'navicolor' => '#E8DDE4',
					'visible' => false,
					'filename' => 'viewcache.php',
					'submenu' => array(
						array(
							'title' => $language[$lang]['new_log_entry'],
							'menustring' => $language[$lang]['new_log_entry'],
							'visible' => true,
							'filename' => 'log.php?cacheid='.$cache_id,
							'newwindow' => false,
							'siteid' => 'new_log'
						),
						array(
							'title' => $watch_label,
							'menustring' => $watch_label,
							'visible' => true,
							'filename' => $is_watched,
							'newwindow' => false,
							'siteid' => 'observe_cache'
						),
						array(
							'title' => $language[$lang]['report_problem'],
							'menustring' => $language[$lang]['report_problem'],
							'visible' => true,
							'filename' => 'reportcache.php?cacheid='.$cache_id,
							'newwindow' => false,
							'siteid' => 'report_cache'
						),
						array(
							'title' => $language[$lang]['print'],
							'menustring' => $language[$lang]['print'],
							'visible' => true,
							'filename' => 'viewcache.php?cacheid='.$cache_id.'&amp;print=y',
							'newwindow' => false,
							'siteid' => 'print_cache'
						),
						array(
							'title' => $print_list_label,
							'menustring' => $print_list_label,
							'visible' => true,
							'filename' => $print_list,
							'newwindow' => false,
							'siteid' => 'print_list_cache'
						),
						array(
							'title' => $ignore_label,
							'menustring' => $ignore_label,
							'visible' => true,
							'filename' => $is_ignored,
							'newwindow' => false,
							'siteid' => 'ignored_cache'
						),
						array(
							'title' => $language[$lang]['edit'],
							'menustring' => $language[$lang]['edit'],
							'visible' => $show_edit,
							'filename' => 'editcache.php?cacheid='.$cache_id,
							'newwindow' => false,
							'siteid' => 'edit_cache'
						)
					)
				);
				$report_action = "<li><a href=\"reportcache.php?cacheid=$cache_id\">".$language[$lang]['report_problem']."</a></li>";
			}

			tpl_set_var('log', $log_action);
			tpl_set_var('watch', $watch_action);
			tpl_set_var('report', $report_action);
			tpl_set_var('ignore', $ignore_action);
			tpl_set_var('edit', $edit_action);
			tpl_set_var('print', $print_action);
			tpl_set_var('print_list', $print_list);

			// check if password is required
			$has_password = isPasswordRequired($cache_id);
			
			// cache-attributes
			$rs = sql("SELECT `cache_attrib`.`text_long`, `cache_attrib`.`icon_large`
						FROM `cache_attrib`, `caches_attributes`
						WHERE `cache_attrib`.`id`=`caches_attributes`.`attrib_id`
						  AND `cache_attrib`.`language`='&1'
						  AND `caches_attributes`.`cache_id`='&2'
						ORDER BY `cache_attrib`.`category`, `cache_attrib`.`id`", $default_lang, $cache_id);
			$num_of_attributes = mysql_num_rows($rs);
			if( $num_of_attributes > 0 || $has_password)
			{
				$cache_attributes = '';
				if( $num_of_attributes > 0 )
				{
					while($record = sql_fetch_array($rs))
					{
						$cache_attributes .= '<img src="'.htmlspecialchars($record['icon_large'], ENT_COMPAT, 'UTF-8').'" border="0" title="'.htmlspecialchars($record['text_long'], ENT_COMPAT, 'UTF-8').'" alt="'.htmlspecialchars($record['text_long'], ENT_COMPAT, 'UTF-8').'" />&nbsp;';
					}
				}
			
				if( $has_password )
					tpl_set_var('password_req', '<img src="images/attributes/password.png" alt="Potrzebne hasło"/>');
				else
					tpl_set_var('password_req', '');
				tpl_set_var('cache_attributes', $cache_attributes);
				tpl_set_var('cache_attributes_start', '');
				tpl_set_var('cache_attributes_end', '');
			}
			else
			{
				tpl_set_var('cache_attributes_start', '<!--');
				tpl_set_var('cache_attributes_end', '-->');
				tpl_set_var('cache_attributes', '');
				tpl_set_var('password_req', '');
			}
		}
		else
		{
			//display search page
			$tplname = 'viewcache_error';
		}
	}

	//make the template and send it out


$viewcache_header = '
<script type="text/javascript" src="tpl/{style}/js/lytebox.js"></script>
<link rel="stylesheet" href="tpl/{style}/css/lytebox.css" type="text/css" media="screen" />
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="rot13.js"></script>
    <script type="text/javascript">

    google.load("language", "1");

    function translateDesc() 
		{
			var maxlen = 1100;
			var i=0;
			
			// tekst do przetlumaczenia
			var text = document.getElementById("description").innerHTML;
			
			// tablica wyrazow
			var splitted = text.split(" ");
			
			// liczba wyrazow
			var totallen = splitted.length;
			
			var toTranslate="";
			var container = document.getElementById("description");
			container.innerHTML = "";
			
			'.(($enable_google_translation)?"google.language.getBranding('branding');":"").'
			while( i < totallen )
			{
				var loo = splitted[i].length;
				while(( toTranslate.length + loo) < maxlen )
				{
					toTranslate += " " + splitted[i];
					i++;
					if( i >= totallen )
						break;
				}
				
				google.language.translate(toTranslate, "pl", "'.$lang.'", function(result) 
				{
				//	var container = document.getElementById("description");
					
					// poprawki
					var toHTML = (result.translation).replace(/[eE]nglish/g, "Polish");
					toHTML = toHTML.replace(/[iI]nbox/g, "Geocache");
					toHTML = toHTML.replace(/[iI]nboxes/g, "Geocaches");					
					toHTML = toHTML.replace(/[mM]ailbox/g, "Geocache");
					toHTML = toHTML.replace(/[mM]ailboxes/g, "Geocaches");
					toHTML = toHTML.replace(/[dD]eutsch/g, "Polnisch");
					toHTML = toHTML.replace(/[sS]houlder/g, "shovel");
					
					container.innerHTML += toHTML;
				});
				toTranslate = "";
			}
    }
		
		function translateHint() 
		{
			var maxlen = 1100;
			var i=0;
			
			// tekst do przetlumaczenia
			var container = document.getElementById("hint");
			if( container == null )
				return "";
			';
			
			
			if( isset($_REQUEST['nocrypt']) )
				$viewcache_header .= 'var text = container.innerHTML;';
			else
				$viewcache_header .= 'var text = rot13(container.innerHTML);';
			$viewcache_header .= '
			
			// tablica wyrazow
			var splitted = text.split(" ");
			
			// liczba wyrazow
			var totallen = splitted.length;
			
			var toTranslate="";
			container.innerHTML = "";
			while( i < totallen )
			{
				var loo = splitted[i].length;
				while(( toTranslate.length + loo) < maxlen )
				{
					toTranslate += " " + splitted[i];
					i++;
					if( i >= totallen )
						break;
				}
				
				google.language.translate(toTranslate, "pl", "'.$lang.'", function(result) 
				{
					//var container = document.getElementById("description");
					
					// poprawki
					var toHTML = (result.translation).replace(/[eE]nglish/g, "Polish");
					toHTML = toHTML.replace(/[iI]nbox/g, "Geocache");
					toHTML = toHTML.replace(/[iI]nboxes/g, "Geocaches");					
					toHTML = toHTML.replace(/[mM]ailbox/g, "Geocache");
					toHTML = toHTML.replace(/[mM]ailboxes/g, "Geocaches");
					toHTML = toHTML.replace(/[dD]eutsch/g, "Polnisch");
					toHTML = toHTML.replace(/[sS]houlder/g, "shovel");
					';
		if( isset($_REQUEST['nocrypt']) )
			$viewcache_header .= 'container.innerHTML += toHTML;';
		else
			$viewcache_header .= 'container.innerHTML += rot13(toHTML);';
		
		$viewcache_header .= '
				});
				toTranslate = "";
			}
    }
		
			google.setOnLoadCallback(translateDesc);
			google.setOnLoadCallback(translateHint);
    </script>
';
	
//opencaching.pl

if( !$enable_google_translation )
{
	tpl_set_var('branding', "");
	tpl_set_var('viewcache_header', "");

}
else
{
	tpl_set_var('branding', "<span class='txt-green07'>Automatic translation thanks to:</span>");
	tpl_set_var('viewcache_header', $viewcache_header);
}

tpl_set_var('bodyMod', '');


	tpl_BuildTemplate();
?>
