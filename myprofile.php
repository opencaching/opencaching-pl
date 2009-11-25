<?php
/***************************************************************************
																./myprofile.php
															-------------------
		begin                : Mon June 14 2004
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

	 the users profile page

	 used template(s): myprofile, myprofile_change
	 parameter(s):     none

 ****************************************************************************/

	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');

	//Preprocessing
	if ($error == false)
	{
		$description = "";
		//user logged in?
		if ($usr == false)
		{
		    $target = urlencode(tpl_get_current_page());
		    tpl_redirect('login.php?target='.$target);
		}
		else
		{
			tpl_set_var('desc_updated', '');
			tpl_set_var('your_data', $language[$lang]['your_data']);
			tpl_set_var('data_in_profile', $language[$lang]['data_in_profile']);
			tpl_set_var('username_label', $language[$lang]['username_label']);
			tpl_set_var('gray_field_is_hidden', $language[$lang]['gray_field_is_hidden']);
			tpl_set_var('email_address', $language[$lang]['email_address']);
			tpl_set_var('coordinates', $language[$lang]['coordinates']);
			tpl_set_var('notification', $language[$lang]['notification']);
			tpl_set_var('country_label', $language[$lang]['country_label']);
			tpl_set_var('other', $language[$lang]['other']);
			tpl_set_var('registered_since_label', $language[$lang]['registered_since_label']);
			tpl_set_var('statpic_label', $language[$lang]['statpic_label']);
			tpl_set_var('my_description', $language[$lang]['my_description']);
			tpl_set_var('html_statpic_link', $language[$lang]['html_statpic_link']);
			tpl_set_var('bbcode_statpic', $language[$lang]['bbcode_statpic']);
			tpl_set_var('change_account_data', $language[$lang]['change_account_data']);
			tpl_set_var('from_home_coords', $language[$lang]['from_home_coords']);
			tpl_set_var('notify_new_caches_radius', $language[$lang]['notify_new_caches_radius']);
			tpl_set_var('radius_hint', $language[$lang]['radius_hint']);
			tpl_set_var('no_auto_logout', $language[$lang]['no_auto_logout']);
			tpl_set_var('hide_html_editor', $language[$lang]['hide_html_editor']);
			tpl_set_var('no_auto_logout_warning', $language[$lang]['no_auto_logout_warning']);
			tpl_set_var('pmr_message', $language[$lang]['pmr_message']);
			tpl_set_var('change', $language[$lang]['change']);
			tpl_set_var('reset', $language[$lang]['reset']);
			
			if( isset($_POST['description']) )
			{
				$sql = "UPDATE user SET description = '".strip_tags(sql_escape($_POST['description']))."' WHERE user_id='".sql_escape($usr['userid'])."'";
				@mysql_query($sql);
				tpl_set_var('desc_updated',"<font color='green'>".$language[$lang]['desc_updated']."</font>");
			
			}
			if( isset($_POST['submit']) )
			{
				$sql = "UPDATE user SET get_bulletin = ".intval(sql_escape($_POST['bulletin']))." WHERE user_id='".sql_escape($usr['userid'])."'";
				@mysql_query($sql);
			}
			$sql = "SELECT description, get_bulletin FROM user WHERE user_id = ".$usr['userid'];
			$query = @mysql_query($sql);
			$userinfo = @mysql_fetch_array($query);
			$description = $userinfo['description'];
			$bulletin = $userinfo['get_bulletin'];
			tpl_set_var('bulletin_label', $bulletin==1?($language[$lang]['bulletin_label_yes']):($language[$lang]['bulletin_label_no']));
			tpl_set_var('bulletin_value', $bulletin);
			tpl_set_var('bulletin', $language[$lang]['bulletin']);
			tpl_set_var('is_checked', $bulletin==1?("checked"):(""));
			tpl_set_var('get_bulletin', $language[$lang]['get_bulletin']);
			tpl_set_var('description',$description);

			$tplname = 'myprofile';
			require($stylepath . '/myprofile.inc.php');

			$rs = sql("SELECT `username`, `email`, `country`, `latitude`, `longitude`, `date_created`, `pmr_flag`, `permanent_login_flag`, `no_htmledit_flag`, `notify_radius`, `ozi_filips` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
			$record = sql_fetch_array($rs);

			tpl_set_var('userid', $usr['userid']+0);
			tpl_set_var('profileurl', $absolute_server_URI.'viewprofile.php?userid=' . ($usr['userid']+0));
			tpl_set_var('statlink', $absolute_server_URI.'statpics/' . ($usr['userid']+0) . '.jpg');
			tpl_set_var('username', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('username_html', htmlspecialchars(htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), ENT_COMPAT, 'UTF-8'));
			tpl_set_var('email', htmlspecialchars($record['email'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('country', htmlspecialchars(db_CountryFromShort($record['country']), ENT_COMPAT, 'UTF-8'));
			tpl_set_var('coords', htmlspecialchars(help_latToDegreeStr($record['latitude']), ENT_COMPAT, 'UTF-8') . '<br>' . htmlspecialchars(help_lonToDegreeStr($record['longitude']), ENT_COMPAT, 'UTF-8'));
			tpl_set_var('registered_since', htmlspecialchars(strftime($dateformat, strtotime($record['date_created'])), ENT_COMPAT, 'UTF-8'));
			tpl_set_var('notify_radius', htmlspecialchars($record['notify_radius'] + 0, ENT_COMPAT, 'UTF-8'));
			tpl_set_var('ozi_path_label',$language[$lang]['ozi_path_label']);
			if($record['notify_radius'] + 0 > 0)
			{
				tpl_set_var('notify', mb_ereg_replace('{radius}', $record['notify_radius'] + 0, $notify_radius_message));
			}
			else
			{
				tpl_set_var('notify', $no_notify_message);
			}

			//misc user options
			$using_pmr = $record['pmr_flag'];
			$using_permantent_login = $record['permanent_login_flag'];
			$no_htmledit = $record['no_htmledit_flag'];

			if (isset($_REQUEST['action']))
			{
				$action = $_REQUEST['action'];

				if ($action == 'change')
				{
					//display the change form
					$tplname = 'myprofile_change';
					require_once($stylepath . '/myprofile_change.inc.php');

					if (isset($_POST['submit']) || isset($_POST['submit_all_countries']))
					{
						//load datas from form
						$show_all_countries = $_POST['show_all_countries'];
						$username = $_POST['username'];
						$country = $_POST['country'];
						$radius = $_POST['notify_radius'];
						$ozi_path = strip_tags($_POST['ozi_path']);
						tpl_set_var('ozi_path', $ozi_path);
						
						$using_permantent_login = isset($_POST['using_permanent_login']) ? (int)$_POST['using_permanent_login'] : 0;
						if ($using_permantent_login == 1)
						{
							tpl_set_var('permanent_login_sel', ' checked="checked"');
						}
						else
						{
							tpl_set_var('permanent_login_sel', '');
						}

						$using_pmr = isset($_POST['using_pmr']) ? (int)$_POST['using_pmr'] : 0;
						if ($using_pmr == 1)
						{
							tpl_set_var('pmr_sel', ' checked="checked"');
						}
						else
						{
							tpl_set_var('pmr_sel', '');
						}

						$no_htmledit = isset($_POST['no_htmledit']) ? (int)$_POST['no_htmledit'] : 0;
						if ($no_htmledit == 1)
						{
							tpl_set_var('no_htmledit_sel', ' checked="checked"');
						}
						else
						{
							tpl_set_var('no_htmledit_sel', '');
						}

						$latNS = $_POST['latNS'];
						if ($latNS == 'N')
						{
							tpl_set_var('latNsel', ' selected="selected"');
							tpl_set_var('latSsel', '');
						}
						else
						{
							tpl_set_var('latSsel', ' selected="selected"');
							tpl_set_var('latNsel', '');
						}
						$lonEW = $_POST['lonEW'];
						if ($lonEW == 'E')
						{
							tpl_set_var('lonEsel', ' selected="selected"');
							tpl_set_var('lonWsel', '');
						}
						else
						{
							tpl_set_var('lonWsel', ' selected="selected"');
							tpl_set_var('lonEsel', '');
						}

						$lat_h = $_POST['lat_h'];
						$lat_min = $_POST['lat_min'];
						$lon_h = $_POST['lon_h'];
						$lon_min = $_POST['lon_min'];

						tpl_set_var('username', $username);
						
						tpl_set_var('notify_radius', $radius);

						//set user messages
						tpl_set_var('username_message', '');
						tpl_set_var('lat_message', '');
						tpl_set_var('lon_message', '');
						tpl_set_var('notify_message', '');

						//validate data
						$username_not_ok = mb_ereg_match(regex_username, $username) ? false : true;
						if ($username_not_ok == false)
						{
							// username should not be formatted like an email-address
							$username_not_ok = is_valid_email_address($username) ? true : false;
						}

						//check coordinates
						if ($lat_h!='' || $lat_min!='')
						{
							if (!mb_ereg_match('^[0-9]{1,2}$', $lat_h))
							{
								tpl_set_var('lat_message', $error_coords_not_ok);
								$lat_h_not_ok = true;
							}
							else
							{
								if (($lat_h >= 0) && ($lat_h < 90))
								{
									$lat_h_not_ok = false;
								}
								else
								{
									tpl_set_var('lat_message', $error_coords_not_ok);
									$lat_h_not_ok = true;
								}
							}

							if (is_numeric($lat_min))
							{
								if (($lat_min >= 0) && ($lat_min < 60))
								{
									$lat_min_not_ok = false;
								}
								else
								{
									tpl_set_var('lat_message', $error_coords_not_ok);
									$lat_min_not_ok = true;
								}
							}
							else
							{
								tpl_set_var('lat_message', $error_coords_not_ok);
								$lat_min_not_ok = true;
							}

							$latitude = $lat_h + $lat_min / 60;
							if ($latNS == 'S') $latitude = -$latitude;
						}
						else
						{
							$latitude = NULL;
							$lat_h_not_ok = false;
							$lat_min_not_ok = false;
						}

						if ($lon_h!='' || $lon_min!='')
						{
							if (!mb_ereg_match('^[0-9]{1,3}$', $lon_h))
							{
								tpl_set_var('lon_message', $error_coords_not_ok);
								$lon_h_not_ok = true;
							}
							else
							{
								if (($lon_h >= 0) && ($lon_h < 180))
								{
									$lon_h_not_ok = false;
								}
								else
								{
									tpl_set_var('lon_message', $error_coords_not_ok);
									$lon_h_not_ok = true;
								}
							}

							if (is_numeric($lon_min))
							{
								if (($lon_min >= 0) && ($lon_min < 60))
								{
									$lon_min_not_ok = false;
								}
								else
								{
									tpl_set_var('lon_message', $error_coords_not_ok);
									$lon_min_not_ok = true;
								}
							}
							else
							{
								tpl_set_var('lon_message', $error_coords_not_ok);
								$lon_min_not_ok = true;
							}

							$longitude = $lon_h + $lon_min / 60;
							if ($lonEW == 'W') $longitude = -$longitude;
						}
						else
						{
							$longitude = NULL;
							$lon_h_not_ok = false;
							$lon_min_not_ok = false;
						}

						$lon_not_ok = $lon_min_not_ok || $lon_h_not_ok;
						$lat_not_ok = $lat_min_not_ok || $lat_h_not_ok;

						//check if username is in the database
						$username_exists = false;
						$username_not_ok = mb_ereg_match(regex_username, $username) ? false : true;
						if ($username_not_ok == false)
						{
							// username should not be formatted like an email-address
							// exception: $username == $email
							$username_not_ok = is_valid_email_address($username) ? true : false;
						}
						if ($username_not_ok)
						{
							tpl_set_var('username_message', $error_username_not_ok);
						}
						else
						{
							if ($username != $usr['username'])
							{
								$rs = sql("SELECT `username` FROM `user` WHERE `username`='&1'", $username);
								if (mysql_num_rows($rs) > 0)
								{
									$username_exists = true;
									tpl_set_var('username_message', $error_username_exists);
								}
							}
						}

						if ($radius != '')
						{
							$radius = $radius+0;
							$radius_not_ok = (($radius >= 0) && ($radius <= 150)) ? false : true;
							if ($radius_not_ok)
							{
								tpl_set_var('notify_message', $error_radius_not_ok);
							}
						}
						else
						{
							$radius_not_ok = false;
						}

						//submit
						if (isset($_POST['submit']))
						{
							//try to save
							if (!($username_not_ok ||
								$username_exists ||
								$lon_not_ok ||
								$lat_not_ok ||
								$radius_not_ok))
							{
								//in DB updaten
								sql("UPDATE `user` SET `username`='&1', `last_modified`=NOW(), `latitude`='&2', `longitude`='&3', `pmr_flag`='&4', `country`='&5', `permanent_login_flag`='&6', `no_htmledit_flag`='&8' , `notify_radius`='&9', `ozi_filips`='&10' WHERE `user_id`='&7'", $username, $latitude, $longitude, $using_pmr, $country, $using_permantent_login, $usr['userid'], $no_htmledit, $radius, $ozi_path);

								//wieder normal anzeigen
								$tplname = 'myprofile';

								//variablen updaten
								tpl_set_var('country', htmlspecialchars(db_CountryFromShort($country), ENT_COMPAT, 'UTF-8'));
								tpl_set_var('coords', htmlspecialchars(help_latToDegreeStr($latitude), ENT_COMPAT, 'UTF-8') . '<br>' . htmlspecialchars(help_lonToDegreeStr($longitude), ENT_COMPAT, 'UTF-8'));

								if($radius + 0 > 0)
								{
									tpl_set_var('notify', mb_ereg_replace('{radius}', $radius + 0, $notify_radius_message));
								}
								else
								{
									tpl_set_var('notify', $no_notify_message);
								}
							}
						}
					}
					else
					{
						//load from database
						$show_all_countries = 0;
						$country = $record['country'];
						$longitude = $record['longitude'];
						$latitude = $record['latitude'];
						$using_pmr = $record['pmr_flag'];
						$using_permantent_login = $record['permanent_login_flag'];
						$ozi_path = strip_tags($record['ozi_filips']);
						tpl_set_var('ozi_path', $ozi_path);
						
						if ($using_permantent_login == 1)
						{
							tpl_set_var('permanent_login_sel', ' checked="checked"');
						}
						else
						{
							tpl_set_var('permanent_login_sel', '');
						}

						if ($using_pmr == 1)
						{
							tpl_set_var('pmr_sel', ' checked="checked"');
						}
						else
						{
							tpl_set_var('pmr_sel', '');
						}

						if ($no_htmledit == 1)
						{
							tpl_set_var('no_htmledit_sel', ' checked="checked"');
						}
						else
						{
							tpl_set_var('no_htmledit_sel', '');
						}

						if ($longitude < 0)
						{
							$lonEW = 'W';
							$longitude = -$longitude;
						}
						else
						{
							$lonEW = 'E';
						}
						if ($latitude < 0)
						{
							$latNS = 'S';
							$latitude = -$latitude;
						}
						else
						{
							$latNS = 'N';
						}

						if ($latNS == 'N')
						{
							tpl_set_var('latNsel', ' selected="selected"');
							tpl_set_var('latSsel', '');
						}
						else
						{
							tpl_set_var('latSsel', ' selected="selected"');
							tpl_set_var('latNsel', '');
						}
						if ($lonEW == 'E')
						{
							tpl_set_var('lonEsel', ' selected="selected"');
							tpl_set_var('lonWsel', '');
						}
						else
						{
							tpl_set_var('lonWsel', ' selected="selected"');
							tpl_set_var('lonEsel', '');
						}

						$lat_h = floor($latitude);
						$lon_h = floor($longitude);

						$lat_min = sprintf("%02.3f", round(($latitude - $lat_h) * 60, 3));
						$lon_min = sprintf("%02.3f", round(($longitude - $lon_h) * 60, 3));

						//set user messages
						tpl_set_var('username_message', '');
						tpl_set_var('lat_message', '');
						tpl_set_var('lon_message', '');
						tpl_set_var('notify_message', '');
					}

					tpl_set_var('lat_h', $lat_h);
					tpl_set_var('lon_h', $lon_h);
					tpl_set_var('lat_min', $lat_min);
					tpl_set_var('lon_min', $lon_min);

					//load the country list
					if ($country == 'XX')
					{
						$stmp = '<option value="XX" selected="selected">' . $no_answer . '</option>';
					}
					else
					{
						$stmp = '<option value="XX">' . $no_answer . '</option>';
					}

					if (isset($_POST['submit_all_countries']))
					{
						$show_all_countries = 1;
					}

					if(checkField('countries','list_default_'.$lang) )
						$lang_db = $lang;
					else
						$lang_db = "en";
					
					//Country in defaults ?
					if (($show_all_countries == 0) && ($country != 'XX'))
					{
						$rs2 = sql("SELECT `list_default_" . sql_escape($lang_db) . "` FROM `countries` WHERE `short`='&1'", $country);
						$record2 = sql_fetch_array($rs2);
						if ($record2['list_default_' . $lang_db] == 0)
						{
							$show_all_countries = 1;
						}
						else
						{
							$show_all_countries = 0;
						}
						mysql_free_result($rs2);
					}

					if ($show_all_countries == 1)
					{
						$rs2 = sql("SELECT `&1`, `list_default_" . sql_escape($lang_db) . "`, `short`, `sort_" . sql_escape($lang_db) . "` FROM `countries` ORDER BY `sort_" . sql_escape($lang_db) . '` ASC', $lang_db);
					}
					else
					{
						$rs2 = sql("SELECT `&1`, `list_default_" . sql_escape($lang_db) . "`, `short`, `sort_" . sql_escape($lang_db) . "` FROM `countries` WHERE `list_default_" . sql_escape($lang_db) . "`=1 ORDER BY `sort_" . sql_escape($lang_db) . '` ASC', $lang_db);
					}

					for ($i = 0; $i < mysql_num_rows($rs2); $i++)
					{
						$record2 = sql_fetch_array($rs2);

						if ($record2['short'] == $country)
						{
							$stmp .= '<option value="' . $record2['short'] . '" selected="selected">' . htmlspecialchars($record2[$lang_db], ENT_COMPAT, 'UTF-8') . "</option>\n";
						}
						else
						{
							$stmp .= '<option value="' . $record2['short'] . '">' . htmlspecialchars($record2[$lang_db], ENT_COMPAT, 'UTF-8') . "</option>\n";
						}
					}

					tpl_set_var('countrylist', $stmp);
					unset($stmp);
					tpl_set_var('show_all_countries', $show_all_countries);

					if ($show_all_countries == 0)
					{
						tpl_set_var('allcountriesbutton', '<input type="submit" class="formbuttons" name="submit_all_countries" value="' . $allcountries . '" />');
					}
					else
					{
						tpl_set_var('allcountriesbutton', '');
					}
				}
			}

			//build useroptions
			$user_options = '';
			if ($using_permantent_login == 1)
			{
				$user_options .= $using_permantent_login_message . "\n";
			}
			if ($no_htmledit == 1)
			{
				$user_options .= $no_htmledit_message . "\n";
			}
			if ($using_pmr == 1)
			{
				$user_options .= $using_pmr_message . "\n";
			}
			if ($user_options == '') $user_options = '&nbsp;';
			tpl_set_var('user_options', $user_options);
			$ozi_path = strip_tags($record['ozi_filips']);
			if( isset($_POST['ozi_path']))
				tpl_set_var('ozi_path', strip_tags($_POST['ozi_path']));
			else
				tpl_set_var('ozi_path', $ozi_path);
			tpl_set_var('ozi_path_info', $language[$lang]['ozi_path_info']);
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>