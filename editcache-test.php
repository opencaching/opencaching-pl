<?php
/***************************************************************************
																./editlog.php
															-------------------
		begin                : July 5 2004
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

	 edit a cache listing

	 used template(s): editcache

	 GET/POST Parameter: cacheid

 ****************************************************************************/

  //prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');

	//Preprocessing
	if ($error == false)
	{
		//cacheid
		$cache_id = 0;
		if (isset($_REQUEST['cacheid']))
		{
			$cache_id = $_REQUEST['cacheid'];
		}

		//user logged in?
		if ($usr == false)
		{
		    $target = urlencode(tpl_get_current_page());
		    tpl_redirect('login.php?target='.$target);
		}
		else
		{
			$cache_rs = sql("SELECT `user_id`, `name`, `picturescount`, `mp3count`,`type`, `size`, `date_hidden`, `date_activate`, `longitude`, `latitude`, `country`, `terrain`, `difficulty`, `desc_languages`, `status`, `search_time`, `way_length`, `logpw`, `wp_gc`, `wp_nc` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
			if (mysql_num_rows($cache_rs) == 1)
			{
				$cache_record = sql_fetch_array($cache_rs);

				if ($cache_record['user_id'] == $usr['userid'] || $usr['admin'])
				{
					$tplname = 'editcache-test';

					require_once($rootpath . 'lib/caches.inc.php');
					require($stylepath . '/editcache.inc.php');
					

					
					//here we read all used information from the form if submitted, otherwise from DB
					
					// wihout virtuals and webcams
					if( ($_POST['type'] == 4 && $cache_record['type'] != 4 ) || ($_POST['type'] == 5 && $cache_record['type'] != 5 ) )
						$_POST['type'] = $cache_record['type'];
						
					$cache_name = isset($_POST['name']) ? $_POST['name'] : $cache_record['name'];
					$cache_type = isset($_POST['type']) ? $_POST['type'] : $cache_record['type'];
					if (!isset($_POST['size']))
					{
						if ($cache_type == 4 || $cache_type == 5 || $cache_type == 6)
						{
							$sel_size = 7;
						}
						else
						{
							$sel_size = $cache_record['size'];
						}
					}
					else
					{
						$sel_size = isset($_POST['size']) ? $_POST['size'] : $cache_record['size'];
						if ($cache_type == 4 || $cache_type == 5 || $cache_type == 6)
						{
							$sel_size = 7;
						}
					}
					
					$cache_hidden_day = isset($_POST['hidden_day']) ? $_POST['hidden_day'] : date('d', strtotime($cache_record['date_hidden']));
					$cache_hidden_month = isset($_POST['hidden_month']) ? $_POST['hidden_month'] : date('m', strtotime($cache_record['date_hidden']));
					$cache_hidden_year = isset($_POST['hidden_year']) ? $_POST['hidden_year'] : date('Y', strtotime($cache_record['date_hidden']));

					if(is_null($cache_record['date_activate']))
					{
						$cache_activate_day = isset($_POST['activate_day']) ? $_POST['activate_day'] : date('d');
						$cache_activate_month = isset($_POST['activate_month']) ? $_POST['activate_month'] : date('m');
						$cache_activate_year = isset($_POST['activate_year']) ? $_POST['activate_year'] : date('Y');
						$cache_activate_hour = isset($_POST['activate_hour']) ? $_POST['activate_hour'] : date('H');
					}
					else
					{
						$cache_activate_day = isset($_POST['activate_day']) ? $_POST['activate_day'] : date('d', strtotime($cache_record['date_activate']));
						$cache_activate_month = isset($_POST['activate_month']) ? $_POST['activate_month'] : date('m', strtotime($cache_record['date_activate']));
						$cache_activate_year = isset($_POST['activate_year']) ? $_POST['activate_year'] : date('Y', strtotime($cache_record['date_activate']));
						$cache_activate_hour = isset($_POST['activate_hour']) ? $_POST['activate_hour'] : date('H', strtotime($cache_record['date_activate']));
					}

					$cache_difficulty = isset($_POST['difficulty']) ? $_POST['difficulty'] : $cache_record['difficulty'];
					$cache_terrain = isset($_POST['terrain']) ? $_POST['terrain'] : $cache_record['terrain'];
					$cache_country = isset($_POST['country']) ? $_POST['country'] : $cache_record['country'];
					$show_all_countries = isset($_POST['show_all_countries']) ? $_POST['show_all_countries'] : 0;
					$status = isset($_POST['status']) ? $_POST['status'] : $cache_record['status'];
					$status_old = $cache_record['status'];
					$search_time = isset($_POST['search_time']) ? $_POST['search_time'] : $cache_record['search_time'];
					$way_length = isset($_POST['way_length']) ? $_POST['way_length'] : $cache_record['way_length'];

					if($status_old == 5 && $status == 5)
					{
						if(isset($_POST['publish']))
						{
							$publish = $_POST['publish'];
							if(!($publish == 'now' || $publish == 'later' || $publish == 'notnow'))
							{
								// somebody messed up the POST-data, so we do not publish the cache, since he isn't published right now (status=5)
								$publish = 'notnow';
							}
						}
						else
						{
							if(is_null($cache_record['date_activate']))
							{
								$publish = 'notnow';
							}
							else
							{
								$publish = 'later';
							}
						}
					}
					else
					{
						$publish = isset($_POST['publish']) ? $_POST['publish'] : 'now';
						if(!($publish == 'now' || $publish == 'later' || $publish == 'notnow'))
						{
							// somebody messed up the POST-data, so the cache has to be published (status<5)
							$publish = 'now';
						}
					}

					$search_time = mb_ereg_replace(',', '.', $search_time);
					$way_length = mb_ereg_replace(',', '.', $way_length);

					if (mb_strpos($search_time, ':') == mb_strlen($search_time) - 3)
					{
						$st_hours = mb_substr($search_time, 0, mb_strpos($search_time, ':'));
						$st_minutes = mb_substr($search_time, mb_strlen($st_hours) + 1);

						if (is_numeric($st_hours) && is_numeric($st_minutes))
						{
							if (($st_minutes >= 0) && ($st_minutes < 60))
							{
								$search_time = $st_hours + $st_minutes / 60;
							}
						}
					}

					$log_pw = isset($_POST['log_pw']) ? mb_substr($_POST['log_pw'], 0, 20) : $cache_record['logpw'];
					$wp_gc = isset($_POST['wp_gc']) ? $_POST['wp_gc'] : $cache_record['wp_gc'];
					$wp_nc = isset($_POST['wp_nc']) ? $_POST['wp_nc'] : $cache_record['wp_nc'];

					// name
					$name_not_ok = false;
					if(isset($_POST['name']))
					{
						if($_POST['name'] == "")
							$name_not_ok = true;
					}

					if (isset($_POST['latNS']))
					{
						//get coords from post-form
						$coords_latNS = $_POST['latNS'];
						$coords_lonEW = $_POST['lonEW'];
						$coords_lat_h = $_POST['lat_h'];
						$coords_lon_h = $_POST['lon_h'];
						$coords_lat_min = $_POST['lat_min'];
						$coords_lon_min = $_POST['lon_min'];
					}
					else
					{
						//get coords from DB
						$coords_lon = $cache_record['longitude'];
						$coords_lat = $cache_record['latitude'];

						if ($coords_lon < 0)
						{
							$coords_lonEW = 'W';
							$coords_lon = -$coords_lon;
						}
						else
						{
							$coords_lonEW = 'E';
						}

						if ($coords_lat < 0)
						{
							$coords_latNS = 'S';
							$coords_lat = -$coords_lat;
						}
						else
						{
							$coords_latNS = 'N';
						}

						$coords_lat_h = floor($coords_lat);
						$coords_lon_h = floor($coords_lon);

						$coords_lat_min = sprintf("%02.3f", round(($coords_lat - $coords_lat_h) * 60, 3));
						$coords_lon_min = sprintf("%02.3f", round(($coords_lon - $coords_lon_h) * 60, 3));
					}

					//here we validate the data

					//coords
					$lon_not_ok = false;

					if (!mb_ereg_match('^[0-9]{1,3}$', $coords_lon_h))
					{
						$lon_not_ok = true;
					}
					else
					{
						$lon_not_ok = (($coords_lon_h >= 0) && ($coords_lon_h < 180)) ? false : true;
					}

					if (is_numeric($coords_lon_min))
					{
						// important: use here |=
						$lon_not_ok |= (($coords_lon_min >= 0) && ($coords_lon_min < 60)) ? false : true;
					}
					else
					{
						$lon_not_ok = true;
					}

					//same with lat
					$lat_not_ok = false;

					if (!mb_ereg_match('^[0-9]{1,3}$', $coords_lat_h))
					{
						$lat_not_ok = true;
					}
					else
					{
						$lat_not_ok = (($coords_lat_h >= 0) && ($coords_lat_h < 180)) ? false : true;
					}

					if (is_numeric($coords_lat_min))
					{
						// important: use here |=
						$lat_not_ok |= (($coords_lat_min >= 0) && ($coords_lat_min < 60)) ? false : true;
					}
					else
					{
						$lat_not_ok = true;
					}

					//check effort
					$time_not_ok = true;
					tpl_set_var('effort_message', '');
					if (is_numeric($search_time) || ($search_time == ''))
					  {
					    $time_not_ok = false;
					  }
					if ($time_not_ok)
					  {
					    tpl_set_var('effort_message', $time_not_ok_message);
					    $error = true;
					  }
					$way_length_not_ok =true;
					if  (is_numeric($way_length) || ($way_length == ''))
					  {
					    $way_length_not_ok = false;
					  }
					if ($way_length_not_ok)
					  {
					    tpl_set_var('effort_message', $way_length_not_ok_message);
					    $error = true;
					  }


					//check hidden_since
					$hidden_date_not_ok = true;
					if (is_numeric($cache_hidden_day) && is_numeric($cache_hidden_month) && is_numeric($cache_hidden_year))
					{
						$hidden_date_not_ok = (checkdate($cache_hidden_month, $cache_hidden_day, $cache_hidden_year) == false);
					}

					//check date_activate
					if($status == 5)
					{
						$activate_date_not_ok = true;
						if (is_numeric($cache_activate_day) && is_numeric($cache_activate_month) && is_numeric($cache_activate_year) && is_numeric($cache_activate_hour))
						{
							$activate_date_not_ok = ((checkdate($cache_activate_month, $cache_activate_day, $cache_activate_year) == false) || $cache_activate_hour < 0 || $cache_activate_hour > 23);
						}
					}
					else
					{
						$activate_date_not_ok = false;
					}

					//check status and publish options
					if(($status == 5 && $publish == 'now') || ($status != 5 && ($publish == 'later' || $publish == 'notnow')))
					{
						tpl_set_var('status_message', $status_message);
						$status_not_ok = true;
					}
					else
					{
						tpl_set_var('status_message', '');
						$status_not_ok = false;
					}

					//check cache size
					$size_not_ok = false;
					if($sel_size != 7 && ($cache_type == 4 || $cache_type == 5 || $cache_type == 6))
					{
						$error = true;
						$size_not_ok = true;
					}
					
					// check if the user haven't changed type to 'without container
					if( (($_POST['type'] == 1 && $cache_record['type'] != 1) 
						|| ($_POST['type'] == 2 )
						|| ($_POST['type'] == 3 )
						|| ($_POST['type'] == 7 )
						|| ($_POST['type'] == 8 ) ) && $sel_size == 7 )
					{
						$error = true;
						$size_not_ok = true;
					}
					
					// if there is already a cache without container, let it stay this way
					if( $cache_record['type'] == 1 && $cache_record['size'] == 7)
						tpl_set_var('other_nobox', 'true');
					else
						tpl_set_var('other_nobox', 'false');
					// cache-attributes
					if (isset($_POST['cache_attribs']))
					{
						$cache_attribs = mb_split(';', $_POST['cache_attribs']);
					}
					else
					{
						// get attribs for this cache from db
						$rs = sql("SELECT `attrib_id` FROM `caches_attributes` WHERE `cache_id`='&1'", $cache_id);
						if(mysql_num_rows($rs) > 0)
						{
							unset($cache_attribs);
							while($record = sql_fetch_array($rs))
							{
								$cache_attribs[] = $record['attrib_id'];
							}
							unset($record);
						}
						else
						{
							$cache_attribs = array();
						}
						mysql_free_result($rs);
					}

					//try to save to DB?
					if (isset($_POST['submit']))
					{
						//all validations ok?
						if (!($hidden_date_not_ok || $lat_not_ok || $lon_not_ok || $name_not_ok || $time_not_ok || $way_length_not_ok || $size_not_ok || $activate_date_not_ok || $status_not_ok))
						{
							$cache_lat = $coords_lat_h + $coords_lat_min / 60;
							if ($coords_latNS == 'S') $cache_lat = -$cache_lat;

							$cache_lon = $coords_lon_h + $coords_lon_min / 60;
							if ($coords_lonEW == 'W') $cache_lon = -$cache_lon;

							if($publish == 'now')
							{
								$activation_date = 'NULL';
							}
							elseif($publish == 'later')
							{
								$status = 5;
								$activation_date = "'".sql_escape(date('Y-m-d H:i:s', mktime($cache_activate_hour, 0, 0, $cache_activate_month, $cache_activate_day, $cache_activate_year)))."'";
							}
							elseif($publish == 'notnow')
							{
								$status = 5;
								$activation_date = 'NULL';
							}
							else
							{
								// should never happen
								$activation_date = 'NULL';
							}

							//save to DB
							sql("UPDATE `caches` SET `last_modified`=NOW(), `name`='&1', `longitude`='&2', `latitude`='&3', `type`='&4', `date_hidden`='&5', `country`='&6', `size`='&7', `difficulty`='&8', `terrain`='&9', `status`='&10', `search_time`='&11', `way_length`='&12', `logpw`='&13', `wp_gc`='&14', `wp_nc`='&15', `date_activate` = $activation_date WHERE `cache_id`='&16'", $cache_name, $cache_lon, $cache_lat, $cache_type, date('Y-m-d', mktime(0, 0, 0, $cache_hidden_month, $cache_hidden_day, $cache_hidden_year)), $cache_country, $sel_size, $cache_difficulty, $cache_terrain, $status, $search_time, $way_length, $log_pw, $wp_gc, $wp_nc, $cache_id);

							// delete old cache-attributes
							sql("DELETE FROM `caches_attributes` WHERE `cache_id`='&1'", $cache_id);

							// insert new cache-attributes
							for($i=0; $i<count($cache_attribs); $i++)
							{
								if(($cache_attribs[$i]+0) > 0)
								{
									sql("INSERT INTO `caches_attributes` (`cache_id`, `attrib_id`) VALUES('&1', '&2')", $cache_id, $cache_attribs[$i]+0);
								}
							}

							//call eventhandler
							require_once($rootpath . 'lib/eventhandler.inc.php');
							event_edit_cache($cache_id, $usr['userid']+0);

							// if old status is not yet published and new status is published => notify-event
							if ($status_old == 5 && $status != 5)
							{
								touchCache($cache_id);
								// send new cache event
								event_notify_new_cache($cache_id);
							}

							//display cache-page
							tpl_redirect('viewcache.php?cacheid=' . urlencode($cache_id));
							exit;
						}
					}
					elseif (isset($_POST['show_all_countries_submit']))
					{
						$show_all_countries = 1;
					}

					//here we only set up the template variables

					//build countrylist
					$countriesoptions = '';

					//check if selected country is in list_default
					if ($show_all_countries == 0)
					{
						$rs = sql("SELECT `short` FROM `countries` WHERE (`list_default_" . sql_escape($lang) . "`=1) AND (lower(`short`) = lower('&1'))", $cache_country);
						if (mysql_num_rows($rs) == 0) $show_all_countries = 1;
					}

					//get the record
					if ($show_all_countries == 0)
						$rs = sql('SELECT `' . sql_escape($lang) . '`, `short` FROM `countries` WHERE `list_default_' . sql_escape($lang) . '`=1 ORDER BY `sort_' . sql_escape($lang) . '` ASC');
					else
						$rs = sql('SELECT `' . sql_escape($lang) . '`, `short` FROM `countries` ORDER BY `sort_' . sql_escape($lang) . '` ASC');

					for ($i = 0; $i < mysql_num_rows($rs); $i++)
					{
						$record = sql_fetch_array($rs);
						if ($record['short'] == $cache_country)
						{
							$countriesoptions .= '<option value="' . htmlspecialchars($record['short'], ENT_COMPAT, 'UTF-8') . '" selected="selected">' . htmlspecialchars($record[$lang], ENT_COMPAT, 'UTF-8') . '</option>';
						}
						else
						{
							$countriesoptions .= '<option value="' . htmlspecialchars($record['short'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($record[$lang], ENT_COMPAT, 'UTF-8') . '</option>';
						}
						$countriesoptions .= "\n";
					}
					tpl_set_var('countryoptions', $countriesoptions);

					// cache-attributes
					$cache_attrib_list = '';
					$cache_attrib_array = '';
					$cache_attribs_string = '';

					$rs = sql("SELECT `id`, `text_long`, `icon_undef`, `icon_large` FROM `cache_attrib` WHERE `language`='&1' ORDER BY `category`, `id`", $default_lang);
					if(mysql_num_rows($rs) > 0)
					{
						while($record = sql_fetch_array($rs))
						{
							$line = $cache_attrib_pic;

							$line = mb_ereg_replace('{attrib_id}', $record['id'], $line);
							$line = mb_ereg_replace('{attrib_text}', $record['text_long'], $line);
							if (in_array($record['id'], $cache_attribs))
								$line = mb_ereg_replace('{attrib_pic}', $record['icon_large'], $line);
							else
								$line = mb_ereg_replace('{attrib_pic}', $record['icon_undef'], $line);
							$cache_attrib_list .= $line;

							$line = $cache_attrib_js;
							$line = mb_ereg_replace('{id}', $record['id'], $line);
							if (in_array($record['id'], $cache_attribs))
								$line = mb_ereg_replace('{selected}', 1, $line);
							else
								$line = mb_ereg_replace('{selected}', 0, $line);
							$line = mb_ereg_replace('{img_undef}', $record['icon_undef'], $line);
							$line = mb_ereg_replace('{img_large}', $record['icon_large'], $line);
							if ($cache_attrib_array != '') $cache_attrib_array .= ',';
							$cache_attrib_array .= $line;

							if (in_array($record['id'], $cache_attribs))
							{
								if ($cache_attribs_string != '') $cache_attribs_string .= ';';
								$cache_attribs_string .= $record['id'];
							}
						}
					}
					tpl_set_var('cache_attrib_list', $cache_attrib_list);
					tpl_set_var('jsattributes_array', $cache_attrib_array);
					tpl_set_var('cache_attribs', $cache_attribs_string);

					//difficulty
					$difficulty_options = '';
					for ($i = 2; $i <= 10; $i++)
					{
						if ($cache_difficulty == $i)
						{
							$difficulty_options .= '<option value="' . $i . '" selected="selected">' . $i / 2 . '</options>';
						}
						else
						{
							$difficulty_options .= '<option value="' . $i . '">' . $i / 2 . '</options>';
						}
						$difficulty_options .= "\n";
					}
					tpl_set_var('difficultyoptions', $difficulty_options);

					//build terrain options
					$terrain_options = '';
					for ($i = 2; $i <= 10; $i++)
					{
						if ($cache_terrain == $i)
						{
							$terrain_options .= '<option value="' . $i . '" selected="selected">' . $i / 2 . '</options>';
						}
						else
						{
							$terrain_options .= '<option value="' . $i . '">' . $i / 2 . '</options>';
						}
						$terrain_options .= "\n";
					}
					tpl_set_var('terrainoptions', $terrain_options);

					//build typeoptions
					$types = '';
					foreach ($cache_types as $type)
					{
						if( ($cache_type != 4 && $type['id'] == 4 ) || ($cache_type != 5 && $type['id'] == 5 ) )
						{
							// jeśli nie (wirtual lub webcam)
							// to nie wyświetlaj tych pozycji
							continue;
						}
						if ($type['id'] == $cache_type)
						{
							$types .= '<option value="' . $type['id'] . '" selected="selected">' . htmlspecialchars($type[$lang], ENT_COMPAT, 'UTF-8') . '</option>';
						}
						else
						{
							$types .= '<option value="' . $type['id'] . '">' . htmlspecialchars($type[$lang], ENT_COMPAT, 'UTF-8') . '</option>';
						}
					}
					tpl_set_var('typeoptions', $types);

					//build sizeoptions
					$sizes = '';
					foreach ($cache_size as $size)
					{
						if( $size['id'] == 7 && $sel_size != 7)
						{	
							continue;
						}
						if ($size['id'] == $sel_size)
						{
							$sizes .= '<option value="' . $size['id'] . '" selected="selected">' . htmlspecialchars($size[$lang], ENT_COMPAT, 'UTF-8') . '</option>';
						}
						else
						{
							$sizes .= '<option value="' . $size['id'] . '">' . htmlspecialchars($size[$lang], ENT_COMPAT, 'UTF-8') . '</option>';
						}
					}
					tpl_set_var('sizeoptions', $sizes);

					//Cachedescs
					$desclangs = mb_split(',', $cache_record['desc_languages']);
					$cache_descs = '';
					$gc_com_refs = false;
					foreach ($desclangs AS $desclang)
					{
						if (count($desclangs) > 1)
						{
							$remove_url = 'removedesc.php?cacheid=' . urlencode($cache_id) . '&desclang=' . urlencode($desclang);
							$removedesc = '&nbsp;<img src="tpl/stdstyle/images/log/16x16-trash.png" border="0" align="middle" class="icon16" alt="" title="Delete" />[<a href="' . htmlspecialchars($remove_url, ENT_COMPAT, 'UTF-8') . '">' . $remove . '</a>]';
						}
						else
						{
							$removedesc = '';
						}

						$resp = sql("SELECT `desc` FROM `cache_desc` WHERE `cache_id`='&1' AND `language`='&2'", $cache_id, $desclang);
						$row = sql_fetch_array($resp);
						if(mb_strpos($row['desc'], "http://img.groundspeak.com/") !== false)
							$gc_com_refs = true;
						sql_free_result($resp);

						$edit_url = 'editdesc.php?cacheid=' . urlencode($cache_id) . '&desclang=' . urlencode($desclang);

						$cache_descs .= '<tr><td colspan="2"><img src="images/flags/'.strtolower($desclang).'.gif" class="icon16" alt=""  />&nbsp;' . htmlspecialchars(db_LanguageFromShort($desclang), ENT_COMPAT, 'UTF-8') . '&nbsp;&nbsp;<img src="images/actions/edit-16.png" border="0" align="middle" border="0" alt="" title="Edit" /> [<a href="' . htmlspecialchars($edit_url, ENT_COMPAT, 'UTF-8') . '">' . $edit . '</a>]' . $removedesc . '</td></tr>';
					}
					tpl_set_var('cache_descs', $cache_descs);

					if($gc_com_refs)
						{
						tpl_set_var('gc_com_refs_start', "");
						tpl_set_var('gc_com_refs_end', "");
						}
					else
						{
						tpl_set_var('gc_com_refs_start', "<!--");
						tpl_set_var('gc_com_refs_end', "-->");
						}

					//Status
					$statusoptions = '';
					if( ( ($status_old == 3 || $status_old == 6 ) && !$usr['admin']) || $status_old == 4)
					{
						$disablestatusoption = ' disabled';
					}
					else
						$disablestatusoption = '';
					tpl_set_var('disablestatusoption', $disablestatusoption);
					
					foreach ($cache_status AS $tmpstatus)
					{
						//hide id 4 => hidden by approvers, hide id 5 if it is not the current status
						if (($tmpstatus['id'] != 4 || $status_old == 4 ) && ($tmpstatus['id'] != 5 || $status_old == 5) && ($tmpstatus['id'] != 6 || $usr['admin'])  )
						{
							if ($tmpstatus['id'] == $status)
							{
								$statusoptions .= '<option value="' . htmlspecialchars($tmpstatus['id'], ENT_COMPAT, 'UTF-8') . '" selected="selected">' . htmlspecialchars($tmpstatus[$lang], ENT_COMPAT, 'UTF-8') . '</option>';
							}
							else
							{
								$statusoptions .= '<option value="' . htmlspecialchars($tmpstatus['id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($tmpstatus[$lang], ENT_COMPAT, 'UTF-8') . '</option>';
							}
						}
					}
					tpl_set_var('statusoptions', $statusoptions);
					// show activation form?
					if($status_old == 5) // status = not yet published
					{
						$tmp = $activation_form;

						$tmp = mb_ereg_replace('{activate_day}', htmlspecialchars($cache_activate_day, ENT_COMPAT, 'UTF-8'), $tmp);
						$tmp = mb_ereg_replace('{activate_month}', htmlspecialchars($cache_activate_month, ENT_COMPAT, 'UTF-8'), $tmp);
						$tmp = mb_ereg_replace('{activate_year}', htmlspecialchars($cache_activate_year, ENT_COMPAT, 'UTF-8'), $tmp);
						$tmp = mb_ereg_replace('{publish_now_checked}', ($publish == 'now') ? 'checked' : '', $tmp);
						$tmp = mb_ereg_replace('{publish_later_checked}', ($publish == 'later') ? 'checked' : '', $tmp);
						$tmp = mb_ereg_replace('{publish_notnow_checked}', ($publish == 'notnow') ? 'checked' : '', $tmp);

						$activation_hours = '';
						for ($i = 0; $i <= 23; $i++)
						{
							if ($cache_activate_hour == $i)
							{
								$activation_hours .= '<option value="' . $i . '" selected="selected">' . $i . '</options>';
							}
							else
							{
								$activation_hours .= '<option value="' . $i . '">' . $i . '</options>';
							}
							$activation_hours .= "\n";
						}
						$tmp = mb_ereg_replace('{activation_hours}', $activation_hours, $tmp);

						if($activate_date_not_ok)
						{
							$tmp = mb_ereg_replace('{activate_on_message}', $date_not_ok_message, $tmp);
						}
						else
						{
							$tmp = mb_ereg_replace('{activate_on_message}', '', $tmp);
						}

						tpl_set_var('activation_form', $tmp);
					}
					else
					{
						tpl_set_var('activation_form', '');
					}

					if ($cache_record['picturescount'] > 0)
					{
						$pictures = '';
						$rspictures = sql("SELECT `url`, `title`, `uuid` FROM `pictures` WHERE `object_id`='&1' AND `object_type`=2", $cache_id);

						for ($i = 0; $i < mysql_num_rows($rspictures); $i++)
						{
							$tmpline = $pictureline;
							$pic_record = sql_fetch_array($rspictures);

							$tmpline = mb_ereg_replace('{link}', htmlspecialchars($pic_record['url'], ENT_COMPAT, 'UTF-8'), $tmpline);
							$tmpline = mb_ereg_replace('{title}', htmlspecialchars($pic_record['title'], ENT_COMPAT, 'UTF-8'), $tmpline);
							$tmpline = mb_ereg_replace('{uuid}', htmlspecialchars($pic_record['uuid'], ENT_COMPAT, 'UTF-8'), $tmpline);

							$pictures .= $tmpline;
						}

						$pictures = mb_ereg_replace('{lines}', $pictures, $picturelines);
						mysql_free_result($rspictures);
						tpl_set_var('pictures', $pictures);
					}
					else
						tpl_set_var('pictures', $nopictures);
					
					//MP3 files only for type of cache: PodCache ID 11
					if ($cache_record['type'] == 11 )
					{
						if ($cache_record['mp3count'] > 0)
						{
						$mp3files = '';
						$rsmp3 = sql("SELECT `url`, `title`, `uuid` FROM `mp3` WHERE `object_id`='&1' AND `object_type`=2", $cache_id);

						for ($i = 0; $i < mysql_num_rows($rsmp3); $i++)
							{
							$tmpline1 = $mp3line;
							$mp3_record = sql_fetch_array($rsmp3);

							$tmpline1 = mb_ereg_replace('{link}', htmlspecialchars($mp3_record['url'], ENT_COMPAT, 'UTF-8'), $tmpline1);
							$tmpline1 = mb_ereg_replace('{title}', htmlspecialchars($mp3_record['title'], ENT_COMPAT, 'UTF-8'), $tmpline1);
							$tmpline1 = mb_ereg_replace('{uuid}', htmlspecialchars($mp3_record['uuid'], ENT_COMPAT, 'UTF-8'), $tmpline1);

							$mp3files .= $tmpline1;
							}

						$mp3files = mb_ereg_replace('{lines}', $mp3files, $mp3lines);
						mysql_free_result($rsmp3);
						tpl_set_var('mp3files', $mp3files);
						tpl_set_var('hidemp3_start', '');
						tpl_set_var('hidemp3_end', '');
						}
					else
						tpl_set_var('mp3files', $nomp3);
						tpl_set_var('hidemp3_start', '');
						tpl_set_var('hidemp3_end', '');
					}
					else
					{
					tpl_set_var('mp3files', '<br />');
					tpl_set_var('hidemp3_start', '<!--');
					tpl_set_var('hidemp3_end', '-->');
					}
		
		//Add Waypoint
			$wp_rs = sql("SELECT `wp_id`, `type`, `longitude`, `latitude`,  `desc`, `status`, `stage`, waypoint_type.pl wp_type, waypoint_type.icon wp_icon FROM `waypoints` INNER JOIN waypoint_type ON (waypoints.type = waypoint_type.id) WHERE `cache_id`='&1' ORDER BY `stage`,`wp_id` DESC", $cache_id);
			if (mysql_num_rows($wp_rs) != 0)
			{	
						$waypoints = '<table width="97%" border="1" style="border-collapse: collapse; font-size: 10px; line-height: 1.6em">';
						$waypoints .= '<tr><td><b>Etap</b></td><td><b>Symbol</b></td><td><b>Typ</b></td><td><b>Współrzędne</b></td><td><b>Opis</b></td><td><b>Status</b></td><td><b>Edycja</b></td></tr>';
						for ($i = 0; $i < mysql_num_rows($wp_rs); $i++)
							{
							$tmpline1 = $wpline;
							$wp_record = sql_fetch_array($wp_rs);

							$tmpline1 = mb_ereg_replace('{wp_icon}', htmlspecialchars($wp_record['wp_icon'], ENT_COMPAT, 'UTF-8'), $tmpline1);
							$tmpline1 = mb_ereg_replace('{type}', htmlspecialchars($wp_record['wp_type'], ENT_COMPAT, 'UTF-8'), $tmpline1);
							$tmpline1 = mb_ereg_replace('{lon}', htmlspecialchars($wp_record['longitude'], ENT_COMPAT, 'UTF-8'), $tmpline1);
							$tmpline1 = mb_ereg_replace('{lat}', htmlspecialchars($wp_record['latitude'], ENT_COMPAT, 'UTF-8'), $tmpline1);
							$tmpline1 = mb_ereg_replace('{desc}', htmlspecialchars($wp_record['desc'], ENT_COMPAT, 'UTF-8'), $tmpline1);
							$tmpline1 = mb_ereg_replace('{wpid}',$wp_record['wp_id'], $tmpline1);
							if ($wp_record['stage']==0) {$tmpline1 = mb_ereg_replace('{number}',"", $tmpline1);
							}else{
							$tmpline1 = mb_ereg_replace('{number}',$wp_record['stage'], $tmpline1);}

							if ($wp_record['status']==1) {$status_icon="tpl/stdstyle/images/free_icons/accept.png";}
							if ($wp_record['status']==2) {$status_icon="tpl/stdstyle/images/free_icons/error.png";}
							if ($wp_record['status']==3) {$status_icon="tpl/stdstyle/images/free_icons/stop.png";}
							$tmpline1 = mb_ereg_replace('{status}', $status_icon, $tmpline1);							
							$waypoints .= $tmpline1;
							}
							$waypoints .= '</table>';
							$waypoints .= '<br/><img src="tpl/stdstyle/images/free_icons/accept.png" class="icon32" alt=""  />&nbsp;<span>Show all information for this waypoint, including coordinates</span>';
							$waypoints .= '<br /><img src="tpl/stdstyle/images/free_icons/error.png" class="icon32" alt=""  />&nbsp;<span>Show the details of this waypoint but hide the coordinates</span>';
							$waypoints .= '<br /><img src="tpl/stdstyle/images/free_icons/stop.png" class="icon32" alt=""  />&nbsp;<span>Hide this waypoint from view except by the owner or administrator</span>';
//						$waypoints = mb_ereg_replace('{lines}', $mp3files, $wplist);
						mysql_free_result($wp_rs);
						tpl_set_var('cache_wp_list', $waypoints);
					}
					else
					{
					tpl_set_var('cache_wp_list', $nowp);
					}
					

					tpl_set_var('cacheid', htmlspecialchars($cache_id, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('name', htmlspecialchars($cache_name, ENT_COMPAT, 'UTF-8'));

					tpl_set_var('date_day', htmlspecialchars($cache_hidden_day, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('date_month', htmlspecialchars($cache_hidden_month, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('date_year', htmlspecialchars($cache_hidden_year, ENT_COMPAT, 'UTF-8'));

					tpl_set_var('selLatN', ($coords_latNS == 'N') ? ' selected="selected"' : '');
					tpl_set_var('selLatS', ($coords_latNS == 'S') ? ' selected="selected"' : '');
					tpl_set_var('selLonE', ($coords_lonEW == 'E') ? ' selected="selected"' : '');
					tpl_set_var('selLonW', ($coords_lonEW == 'W') ? ' selected="selected"' : '');
					tpl_set_var('lat_h', htmlspecialchars($coords_lat_h, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('lat_min', htmlspecialchars($coords_lat_min, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('lon_h', htmlspecialchars($coords_lon_h, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('lon_min', htmlspecialchars($coords_lon_min, ENT_COMPAT, 'UTF-8'));

					tpl_set_var('name_message', ($name_not_ok == true) ? $name_not_ok_message : '');
					tpl_set_var('lon_message', ($lon_not_ok == true) ? $error_coords_not_ok : '');
					tpl_set_var('lat_message', ($lat_not_ok == true) ? $error_coords_not_ok : '');
					tpl_set_var('date_message', ($hidden_date_not_ok == true) ? $date_not_ok_message : '');
					tpl_set_var('size_message', ($size_not_ok == true) ? $size_not_ok_message : '');

					if($lon_not_ok || $lat_not_ok || $hidden_date_not_ok || $name_not_ok)
						tpl_set_var('general_message', $error_general);
					else
						tpl_set_var('general_message', "");

					tpl_set_var('cacheid_urlencode', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'));
					tpl_set_var('show_all_countries', $show_all_countries);
					tpl_set_var('show_all_countries_submit', ($show_all_countries == 0) ? $all_countries_submit: '');

					$st_hours = floor($search_time);
					$st_minutes = sprintf('%02d', ($search_time - $st_hours) * 60);

					tpl_set_var('search_time', $st_hours . ':' . $st_minutes);

					tpl_set_var('way_length', $way_length);
					tpl_set_var('log_pw', htmlspecialchars($log_pw, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('wp_gc', htmlspecialchars($wp_gc, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('wp_nc', htmlspecialchars($wp_nc, ENT_COMPAT, 'UTF-8'));

					tpl_set_var('reset', $reset);
					tpl_set_var('submit', $submit);
				}
				else
				{
					//TODO: not the owner
				}
			}
			else
			{
				//TODO: cache not exist
			}
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
