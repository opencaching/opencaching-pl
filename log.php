<?php
/***************************************************************************
																./log.php
															-------------------
		begin                : July 4 2004
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

	 log a cache visit

	 used template(s): log

	 GET Parameter: cacheid

 ****************************************************************************/

	function isGeokretInCache($cacheid)
	{
		$sql = "SELECT wp_oc, wp_gc, wp_nc FROM caches WHERE cache_id = '".sql_escape(intval($cacheid))."'";
		$cache_record = mysql_fetch_array(mysql_query($sql));
		// get cache waypoint
		$cache_wp = '';
		if( $cache_record['wp_oc'] != '' ) 
			$cache_wp = $cache_record['wp_oc'];
		else if( $cache_record['wp_gc'] != '' ) 
			$cache_wp = $cache_record['wp_gc'];
		else if( $cache_record['wp_nc'] != '' ) 
			$cache_wp = $cache_record['wp_nc'];
		

		$geokret_sql = "SELECT id FROM gk_item WHERE id IN (SELECT id FROM gk_item_waypoint WHERE wp = '".sql_escape($cache_wp)."') AND stateid<>1 AND stateid<>4 AND typeid<>2";
		$geokret_query = sql($geokret_sql);
		if (mysql_num_rows($geokret_query) == 0)
		{
			// no geokrets in this cache
			return 0;
		}
		else
			return 1;
		
	}
	
 // ini_set ('display_errors', On); 
	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require($stylepath.'/smilies.inc.php');

	
	$no_tpl_build = false;
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
			//set here the template to process
			$tplname = 'log_cache';

			require($stylepath . '/log_cache.inc.php');
			require_once($rootpath . 'lib/caches.inc.php');
			require($stylepath.'/rating.inc.php');

			$cachename = '';
			if ($cache_id != 0)
			{
				//get cachename
				$rs = sql("SELECT `name`, `user_id`, `logpw`, `wp_gc`, `wp_nc`, `type`, `status` FROM `caches` WHERE `cache_id`='&1'", $cache_id);

				if (mysql_num_rows($rs) == 0)
				{
					$cache_id = 0;
				}
				else
				{
					$record = sql_fetch_array($rs);
					// only OC Team member and the owner allowed to make logs to not published or archived caches
					if ($record['user_id'] == $usr['userid'] || ($record['status'] != 5 && $record['status'] != 3 && $record['status'] != 6) || $usr['admin'])
					{
						$cachename = $record['name'];
						$cache_user_id = $record['user_id'];
						$use_log_pw = (($record['logpw'] == NULL) || ($record['logpw'] == '')) ? false : true;
						if ($use_log_pw) $log_pw = $record['logpw'];
						$wp_gc = $record['wp_gc'];
						$wp_nc = $record['wp_nc'];
						$cache_type = $record['type'];
					}
					else
					{
						$cache_id = 0;
					}
				}
			}

			if ($cache_id != 0)
			{
				$all_ok = false;
				
				$log_text  = isset($_POST['logtext']) ? ($_POST['logtext']) : '';
				$log_type = isset($_POST['logtype']) ? ($_POST['logtype']+0) : $default_logtype_id;
				$log_date_day = isset($_POST['logday']) ? ($_POST['logday']+0) : date('d');
				$log_date_month = isset($_POST['logmonth']) ? ($_POST['logmonth']+0) : date('m');
				$log_date_year = isset($_POST['logyear']) ? ($_POST['logyear']+0) : date('Y');
				$top_cache = isset($_POST['rating']) ? $_POST['rating']+0 : 0;

				$is_top = sqlValue("SELECT COUNT(`cache_id`) FROM `cache_rating` WHERE `user_id`='" . sql_escape($usr['userid']) . "' AND `cache_id`='" . sql_escape($cache_id) . "'", 0);
				// check if user has exceeded his top5% limit
				$user_founds = sqlValue("SELECT `founds_count` FROM `user` WHERE `user_id`='" .  sql_escape($usr['userid']) . "'", 0);
				$user_tops = sqlValue("SELECT COUNT(`user_id`) FROM `cache_rating` WHERE `user_id`='" .  sql_escape($usr['userid']) . "'", 0);

				if ($is_top == 0)
				{
					if (($user_founds * rating_percentage/100) < 1)
					{
						$top_cache = 0;
						$anzahl = (1 - ($user_founds * rating_percentage/100)) / (rating_percentage/100);
						if ($anzahl > 1)
						{
							$rating_msg = mb_ereg_replace('{anzahl}', "$anzahl Founds", $rating_too_few_founds);
						}
						else
						{
							$rating_msg = mb_ereg_replace('{anzahl}', "$anzahl Found", $rating_too_few_founds);
						}
					}
					elseif ($user_tops < floor($user_founds * rating_percentage/100))
					{
						// this user can recommend this cache
						if ($cache_user_id != $usr['userid']) 
						{
							if( $top_cache )
								$rating_msg = mb_ereg_replace('{chk_sel}', ' checked', $rating_allowed.'<br />'.$rating_stat);
							else
								$rating_msg = mb_ereg_replace('{chk_sel}', '', $rating_allowed.'<br />'.$rating_stat);
						}
						else 
						{
							$rating_msg = mb_ereg_replace('{chk_dis}', ' disabled', $rating_own.'<br />'.$rating_stat);
						}
						$rating_msg = mb_ereg_replace('{max}', floor($user_founds * rating_percentage/100), $rating_msg);
						$rating_msg = mb_ereg_replace('{curr}', $user_tops, $rating_msg);
					}
					else
					{
						$top_cache = 0;
						$anzahl = ($user_tops + 1 - ($user_founds * rating_percentage/100)) / (rating_percentage/100);
						if ($anzahl > 1)
						{
							$rating_msg = mb_ereg_replace('{anzahl}', "$anzahl Founds", $rating_too_few_founds);
						}
						else
						{
							$rating_msg = mb_ereg_replace('{anzahl}', "$anzahl Found", $rating_too_few_founds);
						}
						$rating_msg .= '<br />'.$rating_maxreached;
					}
				}
				else
				{
					if ($cache_user_id != $usr['userid']) {
						$rating_msg = mb_ereg_replace('{chk_sel}', ' checked', $rating_allowed.'<br />'.$rating_stat);
					} else {
						$rating_msg = mb_ereg_replace('{chk_dis}', ' disabled', $rating_own.'<br />'.$rating_stat);
					}
					$rating_msg = mb_ereg_replace('{max}', floor($user_founds * rating_percentage/100), $rating_msg);
					$rating_msg = mb_ereg_replace('{curr}', $user_tops, $rating_msg);
				}
				tpl_set_var('rating_message', mb_ereg_replace('{rating_msg}', $rating_msg, $rating_tpl));
				
				
				// enable backscoring
				$sql = "SELECT count(*) FROM scores WHERE user_id='".sql_escape($usr['userid'])."' AND cache_id='".sql_escape(intval($cache_id))."'";

				// disable backscoring
				// $sql = "SELECT count(*) FROM cache_logs WHERE `deleted`=0 AND type='1' AND user_id='".sql_escape($usr['userid'])."' AND cache_id='".sql_escape(intval($cache_id))."'";
				$is_scored_query = mysql_query($sql);
//				mysql_result($is_scored_query,0);
				if( mysql_result($is_scored_query,0) == 0 && $usr['userid'] != $record['user_id'])
				{
					//$color_table = array("#FF0000","#FF6600","#FF9900","#99FF00","#66FF00","#33FF00","#00FF00");
					$score = "<select name='r'>
						";
					if( isset($_POST['r']) && $_POST['r'] == -10)
						$checked = " selected";
					else
						$checked = "";
						
					$score .= "<option value='-10' $checked>".tr('do_not_rate')."</option>";
					for( $score_radio=$MIN_SCORE;$score_radio<=$MAX_SCORE;$score_radio++)
					{
						if( isset($_POST['r']) && $score_radio == $_POST['r'] )
							$checked = " selected";
						else
							$checked = "";
						$score .= "<option value='".new2oldscore($score_radio)."' $checked>".$ratingDesc[$score_radio]."</option>";
					}
					$score .= "</select>";
					/*
					for( $score_radio=$MIN_SCORE;$score_radio<=$MAX_SCORE;$score_radio++)
						$score .= "<td width='14%' align='center'><label style='color:#ffffff;font-weight:bold;font-size:12px;' for='r$score_radio'>".$ratingDesc[$score_radio-1]."</label>";
					$score .= "</tr></table>";
					$score .= "<input style='border-style:none;background : transparent; color : black' type='radio' name='r' id='r-10' value='-10'><label for='r-10'>".tr('do_not_rate')."</label>";
					*/
					$score_header = tr('rate_cache');
					$display = "block";
				}
				else
				{
					$score = "";
					$score_header = "";
					$display = "none";
				}
				tpl_set_var('score', $score );
				tpl_set_var('score_header', $score_header);
				tpl_set_var('display', $display);
				
				// check if geokret is in this cache
				if( isGeokretInCache($cache_id) )
				{
					tpl_set_var('log_geokret', "<br /><b>".tr('geokret_log')." <a href='http://geokrety.org/ruchy.php'>geokrety.org</a></b>");
				}
				else
					tpl_set_var('log_geokret', "");
				
				// descMode auslesen, falls nicht gesetzt aus dem Profil laden
				if (isset($_POST['descMode']))
					$descMode = $_POST['descMode']+0;
				else
				{
					if (sqlValue("SELECT `no_htmledit_flag` FROM `user` WHERE `user_id`='" .  sql_escape($usr['userid']) . "'", 1) == 1)
						$descMode = 1;
					else
						$descMode = 3;
				}
				if (($descMode < 1) || ($descMode > 3)) $descMode = 3;

				// fuer alte Versionen von OCProp
				if (isset($_POST['submit']) && !isset($_POST['version2']))
				{
					$descMode = 1;
					$_POST['submitform'] = $_POST['submit'];
					$log_text = iconv("ISO-8859-1", "UTF-8", $log_text);
				}

				if ($descMode != 1)
				{
					// check input
					require_once($rootpath . 'lib/class.inputfilter.php');
					$myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
					$log_text = $myFilter->process($log_text);
				}
				else
				{
					// escape text
					//if( $all_ok )
						$log_text = nl2br(htmlspecialchars($log_text, ENT_COMPAT, 'UTF-8'));
					//else
						//$log_text = strip_tags(htmlspecialchars($log_text, ENT_COMPAT, 'UTF-8'));
					
				}

				//validate data
				if (is_numeric($log_date_month) && is_numeric($log_date_day) && is_numeric($log_date_year))
				{
					$date_not_ok = (checkdate($log_date_month, $log_date_day, $log_date_year) == false);
					if($date_not_ok == false)
					{
						if (isset($_POST['submitform']))
						{
							if(mktime(0, 0, 0, $log_date_month, $log_date_day, $log_date_year)>=mktime())
							{
								$date_not_ok = true;
							}
							else
							{
								$date_not_ok = false;
							}
						}
					}
				}
				else
				{
					$date_not_ok = true;
				}

				if ($cache_type == 6)
				{
					switch($log_type)
					{
						case 1:
						case 2:
							$logtype_not_ok = true;
							break;
						default:
							$logtype_not_ok = false;
							break;
					}
				}
				else
				{
					switch($log_type)
					{
						case 7:
						case 8:
							$logtype_not_ok = true;
							break;
						default:
							$logtype_not_ok = false;
							break;
					}
				}

				// not a found log? then ignore the rating
				$sql = "SELECT count(*) as founds FROM `cache_logs` WHERE `deleted`=0 AND user_id='".sql_escape($usr['userid'])."' AND cache_id='".sql_escape($cache_id)."' AND type='1'";
				$res = mysql_fetch_array(mysql_query($sql));
				if( $res['founds'] == 0 )
					if ($log_type != 1 && $log_type != 7 /* && $log_type != 3 */)
					{
						$top_cache = 0;
					}

				$pw_not_ok = false;
				if (isset($_POST['submitform']))
				{
					$all_ok = ($date_not_ok == false) && ($logtype_not_ok == false);

					if (($all_ok) && ($use_log_pw) && $log_type == 1)
					{
						if (isset($_POST['log_pw']))
						{
							if (mb_strtolower($log_pw) != mb_strtolower($_POST['log_pw']))
							{
								$pw_not_ok = true;
								$all_ok = false;
							}
						}
						else
						{
							$pw_not_ok = true;
							$all_ok = false;
						}
					}
				}

				if( isset($_POST['submitform']) && ($log_type == 1 || $log_type == 7))
				{
					// fix
					if( $log_type == 7 && $usr['userid'] == $record['user_id'] )
					{
						$_POST['r'] = -10;
					}
					$_POST['r'] = score2ratingnum($_POST['r']); // convert to old score format
					
					if( $_POST['r'] == -10 || ($_POST['r'] >= $MIN_SCORE && $_POST['r'] <= $MAX_SCORE))
					{
						$score_not_ok = false;
					}
					else
					{
						$score_not_ok = true;
						$all_ok = false;
					}
				}
				else
				{
					$score_not_ok = false;
				}
				
				if( isset($_POST['submitform']) && ($all_ok == true) )
				{
					if( $_POST['r'] >= $MIN_SCORE && $_POST['r'] <= $MAX_SCORE )
					{
						// oceniono skrzynkę
						$sql = "SELECT count(*) FROM scores WHERE user_id='".sql_escape($usr['userid'])."' AND cache_id='".sql_escape(intval($cache_id))."'";
						$is_scored_query = mysql_query($sql);
						if( mysql_result($is_scored_query,0) == 0 && $usr['userid'] != $record['user_id'])
						{					
							$sql = "UPDATE caches SET score=(score*votes+".sql_escape(intval($_POST['r'])).")/(votes+1), votes=votes+1 WHERE cache_id=".sql_escape($cache_id);
							mysql_query($sql);
							$sql = "INSERT INTO scores (user_id, cache_id, score) VALUES('".sql_escape($usr['userid'])."', '".sql_escape(intval($cache_id))."', '".sql_escape(intval($_POST['r']))."')";
							mysql_query($sql);						
						}
					}
					else
					{
						// nie wybrano opcji oceny
						
					}
					$log_date = date('Y-m-d', mktime(0, 0, 0, $log_date_month, $log_date_day, $log_date_year));

					$log_uuid = create_uuid();
					//add logentry to db
					
					// if comment is empty, then do not insert data into db
					if( !($log_type == 3 && $log_text == ""))
					{
						sql("INSERT INTO `cache_logs` (`id`, `cache_id`, `user_id`, `type`, `date`, `text`, `text_html`, `text_htmledit`, `date_created`, `last_modified`, `uuid`, `node`)
										 VALUES ('', '&1', '&2', '&3', '&4', '&5', '&6', '&7', NOW(), NOW(), '&8', '&9')",
										 $cache_id, $usr['userid'], $log_type, $log_date, $log_text, (($descMode != 1) ? 1 : 0), (($descMode == 3) ? 1 : 0), $log_uuid, $oc_nodeid);

						//inc cache stat and "last found"
						$rs = sql("SELECT `founds`, `notfounds`, `notes`, `last_found` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
						$record = sql_fetch_array($rs);

						$last_found = '';
						if ($log_type == 1 || $log_type == 7)
						{
							$tmpset_var = '`founds`=\'' . ($record['founds'] + 1) . '\'';

							$dlog_date = mktime(0, 0, 0, $log_date_month, $log_date_day, $log_date_year);
							if ($record['last_found'] == NULL)
							{
								$last_found = ', `last_found`=\'' . sql_escape(date('Y-m-d', $dlog_date)) . '\'';
							}
							elseif (strtotime($record['last_found']) < $dlog_date)
							{
								$last_found = ', `last_found`=\'' . sql_escape(date('Y-m-d', $dlog_date)) . '\'';
							}
						}
						elseif ($log_type == 2 || $log_type == 8) // fuer Events wird not found als will attend Zaehler missbraucht
						{
							$tmpset_var = '`notfounds`=\'' . sql_escape($record['notfounds'] + 1) . '\'';
						}
						elseif ($log_type == 3)
						{
							$tmpset_var = '`notes`=\'' . sql_escape($record['notes'] + 1) . '\'';
						}

						if ($log_type == 1 || $log_type == 2 || $log_type == 3 || $log_type == 7 || $log_type == 8)
						{
							sql("UPDATE `caches` SET " . $tmpset_var . $last_found . " WHERE `cache_id`='&1'", sql_escape($cache_id));
						}

						//inc user stat
						$rs = sql("SELECT `log_notes_count`, `founds_count`, `notfounds_count` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
						$record = sql_fetch_array($rs);

						if ($log_type == 1 || $log_type == 7)
						{
							$tmpset_var = '`founds_count`=\'' . sql_escape($record['founds_count'] + 1) . '\'';
						}
						elseif ($log_type == 2)
						{
							$tmpset_var = '`notfounds_count`=\'' . sql_escape($record['notfounds_count'] + 1) . '\'';
						}
						elseif ($log_type == 3)
						{
							$tmpset_var = '`log_notes_count`=\'' . sql_escape($record['log_notes_count'] + 1) . '\'';
						}
						if ($log_type == 1 || $log_type == 2 || $log_type == 3 || $log_type == 7)
						{
							sql("UPDATE `user` SET " . $tmpset_var . " WHERE `user_id`='&1'", sql_escape($usr['userid']));
						}

						// update cache_status
						$rs = sql("SELECT `log_types`.`cache_status` FROM `log_types` WHERE `id`='&1'", sql_escape($log_type));
						if($record = sql_fetch_array($rs))
						{
							$cache_status = $record['cache_status'];
							if($cache_status != 0)
							{
								$rs = sql("UPDATE `caches` SET `status`='&1' WHERE `cache_id`='&2'", sql_escape($cache_status), sql_escape($cache_id));
							}
						}
						else
						{
							die("OPS!");
						}

						// update top-list
						if ($log_type == 1 || $log_type == 3) {
						if ($top_cache == 1)
							sql("INSERT IGNORE INTO `cache_rating` (`user_id`, `cache_id`) VALUES('&1', '&2')", $usr['userid'], $cache_id);
						else
							sql("DELETE FROM `cache_rating` WHERE `user_id`='&1' AND `cache_id`='&2'", $usr['userid'], $cache_id);
						}
						//call eventhandler
						require_once($rootpath . 'lib/eventhandler.inc.php');
						event_new_log($cache_id, $usr['userid']+0);
					}
					//redirect to viewcache
					$no_tpl_build = true;
					//include('viewcache.php');
					tpl_redirect('viewcache.php?cacheid=' . $cache_id);
				}
				else
				{
					$sql = "SELECT count(*) as founds FROM `cache_logs` WHERE `deleted`=0 AND user_id='".sql_escape($usr['userid'])."' AND cache_id='".sql_escape($cache_id)."' AND type='1'";
					$res = mysql_fetch_array(mysql_query($sql));
					$sql = "SELECT status, type FROM `caches` WHERE cache_id='".sql_escape($cache_id)."'";
					$res2 = mysql_fetch_array(mysql_query($sql));
					//build logtypeoptions
					$logtypeoptions = '';
					foreach ($log_types AS $type)
					{
						// do not allow 'finding' or 'not finding' own or archived cache (events can be logged)
						
						if( $res2['type'] != 6 && ($usr['userid'] == $cache_user_id || $res['founds'] > 0 || $res2['status'] == 2 || $res2['status'] == 3 || $res2['status'] == 6))
						{
							$logtypeoptions .= '<option value="3">Komentarz</option>' . "\n";
							break;
						}
						// skip if permission=O and not owner
						if($type['permission'] == 'O' && $usr['userid'] != $cache_user_id && $type['permission'])
							continue;
						if($cache_type == 6)
						{
							// skip found/notfound if the cache is an event
							if($type['id'] == 1 || $type['id'] == 2)
							{
								continue;
							}
						}
						else
						{
							// skip will attend/attended if the cache no event
							if($type['id'] == 7 || $type['id'] == 8)
							{
								continue;
							}
						}
							if(checkField('log_types',$lang) )
								$lang_db = $lang;
							else
								$lang_db = "en";

						if ($type['id'] == $log_type)
						{
							$logtypeoptions .= '<option value="' . $type['id'] . '" selected="selected">' . htmlspecialchars($type[$lang_db], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
						}
						else
						{
							$logtypeoptions .= '<option value="' . $type['id'] . '">' . htmlspecialchars($type[$lang_db], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
						}
					}

					//set tpl vars
					tpl_set_var('cachename', htmlspecialchars($cachename, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('cacheid', htmlspecialchars($cache_id, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('logday', htmlspecialchars($log_date_day, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('logmonth', htmlspecialchars($log_date_month, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('logyear', htmlspecialchars($log_date_year, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('logtypeoptions', $logtypeoptions);
					tpl_set_var('reset', $reset);
					tpl_set_var('submit', $submit);
					tpl_set_var('date_message', '');
					tpl_set_var('top_cache', $top_cache);


					// Text / normal HTML / HTML editor
					tpl_set_var('use_tinymce', (($desc_htmledit == 1) ? 1 : 0));

					if (($desc_html == 1) && ($desc_htmledit == 1))
					{
						tpl_set_var('descMode', 3);
					}
					else if ($desc_html == 1)
						tpl_set_var('descMode', 2);
					else
						tpl_set_var('descMode', 1);
					// TinyMCE
					$headers = tpl_get_var('htmlheaders') . "\n";
					$headers .= '<script language="javascript" type="text/javascript" src="lib/phpfuncs.js"></script>' . "\n";
					$headers .= '<script language="javascript" type="text/javascript" src="lib/tinymce/tiny_mce.js"></script>' . "\n";
//					$headers .= '<script language="javascript" type="text/javascript" src="lib/tinymce/config/log.js.php?lang='.$lang.'&amp;cacheid=' . ($desc_record['cache_id']+0) . '"></script>' . "\n";
					$headers .= '<script language="javascript" type="text/javascript" src="lib/tinymce/config/log.js.php?lang='.$lang.'&amp;logid=0"></script>' . "\n";
					tpl_set_var('htmlheaders', $headers);

					if ($descMode != 1)
						tpl_set_var('logtext', htmlspecialchars($log_text, ENT_COMPAT, 'UTF-8'), true);
					else
						tpl_set_var('logtext', strip_tags($log_text));
					
					$listed_on = array();
					if($wp_gc > "")
						$listed_on[] = '<a href="http://www.geocaching.com/seek/cache_details.aspx?wp='.$wp_gc.'"  target="_blank">geocaching.com</a> <a href="http://www.geocaching.com/seek/log.aspx?wp='.$wp_gc.'" target="_blank">(loggen)</a>';
					if($wp_nc > "")
						$listed_on[] = 'navicache.com';

					if(sizeof($listed_on))
					{
						tpl_set_var('listed_start', "");
						tpl_set_var('listed_end', "");
						tpl_set_var('listed_on', sizeof($listed_on) == 0 ? $listed_only_oc : implode(", ", $listed_on));
					}
					else
					{
					tpl_set_var('listed_start', "<!--");
					tpl_set_var('listed_end', "-->");
					}
					if ($use_log_pw == true)
					{
						if ($pw_not_ok == true)
						{
							tpl_set_var('log_pw_field', $log_pw_field_pw_not_ok);
						}
						else
						{
							tpl_set_var('log_pw_field', $log_pw_field);
						}
					}
					else
					{
						tpl_set_var('log_pw_field', '');
					}

					if ($date_not_ok == true)
					{
						tpl_set_var('date_message', $date_message);
					}
					
					if ($score_not_ok == true)
					{
						tpl_set_var('score_message', $score_message);
					}
					else
						tpl_set_var('score_message', '');

					// build smilies
					$smilies = '';
					if ($descMode != 3)
					{
						for($i=0; $i<count($smileyshow); $i++)
						{
							if($smileyshow[$i] == '1')
							{
								$tmp_smiley = $smiley_link;
								$tmp_smiley = mb_ereg_replace('{smiley_image}', $smileyimage[$i], $tmp_smiley);
								$smilies = $smilies . mb_ereg_replace('{smiley_text}', ' '.$smileytext[$i].' ', $tmp_smiley) . '&nbsp;';
							}
						}
					}
					tpl_set_var('smilies', $smilies);
				}
			} // end if( cache_id != 0 )
			else
			{
				// cache_id = 0
				header('Location: viewcache.php?cacheid='.$_GET['cacheid']);
			}
		}
	}
	if ($no_tpl_build == false)
	{
		//make the template and send it out
		tpl_BuildTemplate(false);
	}
?>
