<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *  UTF-8 ąść%d-%m-%Y
 ***************************************************************************/

require_once 'lib/db.php';

//prepare the templates and include all neccessary
if (!isset($rootpath))
	$rootpath = '';
require_once ('./lib/common.inc.php');
global $stat_menu;
$lang;

//Preprocessing
if ($error == false) {
	//user logged in?
	if ($usr == false) {

		function myUrlEncode($string) {
			$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
			$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
			return str_replace($entities, $replacements, urlencode($string));
		}

		$target = myUrlEncode(tpl_get_current_page());
		tpl_redirect('login.php?target=' . $target);
	} else {

		// check for old-style parameters
		if (isset($_REQUEST['userid'])) {
			$user_id = $_REQUEST['userid'];
			tpl_set_var('userid', $user_id);
		}
		require ($stylepath . '/viewprofile.inc.php');
		require ($stylepath . '/lib/icons.inc.php');
		$tplname = 'viewprofile';
		$stat_menu = array('title' => tr('Statictics'), 'menustring' => tr('Statictics'), 'siteid' => 'statlisting', 'navicolor' => '#E8DDE4', 'visible' => false, 'filename' => 'viewprofile.php?userid=' . $user_id, 'submenu' => array( array('title' => tr('graph_find'), 'menustring' => tr('graph_find'), 'visible' => true, 'filename' => 'ustatsg2.php?userid=' . $user_id, 'newwindow' => false, 'siteid' => 'findstat', 'icon' => 'images/actions/stat'), array('title' => tr('graph_created'), 'menustring' => tr('graph_created'), 'visible' => true, 'filename' => 'ustatsg1.php?userid=' . $user_id, 'newwindow' => false, 'siteid' => 'createstat', 'icon' => 'images/actions/stat')));

		$content = "";
		function cleanup_text($str) {
			$from[] = '<p>&nbsp;</p>';
			$to[] = '';
			$str = strip_tags($str, "<li>");
			$from[] = '&nbsp;';
			$to[] = ' ';
			$from[] = '<p>';
			$to[] = '';
			$from[] = '\n';
			$to[] = '';
			$from[] = '\r';
			$to[] = '';
			$from[] = '</p>';
			$to[] = "";
			$from[] = '<br>';
			$to[] = "";
			$from[] = '<br />';
			$to[] = "";
			$from[] = '<br/>';
			$to[] = "";

			$from[] = '<li>';
			$to[] = " - ";
			$from[] = '</li>';
			$to[] = "";

			$from[] = '&oacute;';
			$to[] = 'o';
			$from[] = '&quot;';
			$to[] = '"';
			$from[] = '&[^;]*;';
			$to[] = '';

			$from[] = '&';
			$to[] = '';
			$from[] = '\'';
			$to[] = '';
			$from[] = '"';
			$to[] = '';
			$from[] = '<';
			$to[] = '';
			$from[] = '>';
			$to[] = '';
			$from[] = ']]>';
			$to[] = ']] >';
			$from[] = '';
			$to[] = '';

			for ($i = 0; $i < count($from); $i++)
				$str = str_replace($from[$i], $to[$i], $str);

			return filterevilchars($str);
		}

		function filterevilchars($str) {
			return str_replace('[\\x00-\\x09|\\x0A-\\x0E-\\x1F]', '', $str);
		}

		$rdd = sql("select TO_DAYS(NOW()) - TO_DAYS(`date_created`) `diff` from `user` WHERE user_id=&1 ", $user_id);
		$ddays = mysql_fetch_array($rdd);
		mysql_free_result($rdd);


		// select proper language depend on $lang
		if (isset($lang)) {
			$countryCode = strtolower($lang);
		} else {
			$countryCode = 'en';
		}

		$database = new dataBase(false);
		$query = "SELECT admin, guru, hidden_count, founds_count, is_active_flag, email, password, log_notes_count, notfounds_count, username, last_login, countries.$countryCode country, date_created, description, hide_flag FROM user LEFT JOIN countries ON (user.country=countries.short) WHERE user_id=:1 LIMIT 1";
		$database->multiVariableQuery($query, $user_id);
		// if specified language is in database
		if($database->rowCount() > 0) {
			$user_record = $database->dbResultFetch();
		} else { // if we have not specified language in db, just use english.
			$countryCode = 'en';
			$database->multiVariableQuery($query, $user_id);
			$user_record = $database->dbResultFetch();
		}
		unset($database);
	
		//	echo "<pre>";
		//	print_r($user_record);
		
		// $rsGeneralStat = sql("SELECT admin,guru,hidden_count, founds_count, is_active_flag,email, password,log_notes_count, notfounds_count, username, last_login, countries.pl country, date_created, description, hide_flag FROM user LEFT JOIN countries ON (user.country=countries.short) WHERE user_id=&1", $user_id);
		// $user_record = sql_fetch_array($rsGeneralStat);
		// print_r($user_record);
		// exit;
		
		tpl_set_var('username', $user_record['username']);
		if ((date('m') == 4) and (date('d') == 1)) {
			tpl_set_var('username', tr('primaAprilis1'));
		}
		tpl_set_var('country', htmlspecialchars($user_record['country'], ENT_COMPAT, 'UTF-8'));
		tpl_set_var('registered', fixPlMonth(strftime($dateformat, strtotime($user_record['date_created']))));
		$description = $user_record['description'];
		tpl_set_var('description', nl2br($description));
		if ($description != "") {
			tpl_set_var('description_start', '');
			tpl_set_var('description_end', '');
		} else {
			tpl_set_var('description_start', '<!--');
			tpl_set_var('description_end', '-->');
		}
		$pimage = 'profile2';
		$pinfo = "OC user";
		if ($user_record['guru'] == 1) {
			$pimage = 'guide';
			$pinfo = "Przewodnik";
		}
		if ($user_record['admin'] == 1) {
			$pimage = 'admins';
			$pinfo = "OC Team user";
		}
		tpl_set_var('profile_img', $pimage);
		tpl_set_var('profile_info', $pinfo);

		$guide_info = '<br/>';
		if ($user_id == $usr['userid']) {
			// check user can set as Geocaching guide
			/*
			 $rsnfc = sql("SELECT COUNT(`cache_logs`.`cache_id`) as num_fcaches FROM cache_logs,caches WHERE cache_logs.cache_id=caches.cache_id AND (caches.type='1' OR caches.type='2' OR caches.type='3' OR caches.type='7') AND cache_logs.type='1' AND cache_logs.deleted='0' AND `cache_logs`.`user_id` = ".sql_escape($usr['userid'])."");
			 $rec1 = sql_fetch_array($rsnfc);
			 $num_find_caches = $rec1['num_fcaches'];
			 $rsnc = sql("SELECT COUNT(`caches`.`cache_id`) as num_caches FROM `caches` WHERE `user_id` = ".sql_escape($usr['userid'])."
			 AND (status = 1 OR status=2 OR status=3) AND (caches.type='1' OR caches.type='2' OR caches.type='3' OR caches.type='7')");
			 $rec2 = sql_fetch_array($rsnc);
			 $num_caches = $rec2['num_caches'];
			 */
			// Number of recommendations
			$nrec = sql("SELECT SUM(topratings) as nrecom FROM caches WHERE `caches`.`user_id`=&1", $usr['userid']);
			$nr = sql_fetch_array($nrec);
			$nrecom = $nr['nrecom'];

			// old
			//			if ($num_caches>=5 && $num_find_caches>=5 && $user_record['guru'] ==0 && $user_id == $usr['userid'] )
			// new with recommendations
			if ($nrecom >= 20 && $user_record['guru'] == 0 && $user_id == $usr['userid']) {
				$guide_info = '<div class="content-title-noshade box-blue"><table><tr><td><img style="margin-right: 10px;margin-left:10px;" src="tpl/stdstyle/images/blue/info-b.png" alt="guide"></td><td>
					<span style="font-size:12px;"> '.tr('guru_17').'
					<a class="links" href="myprofile.php?action=change"> '.tr('guru_18').'</a>. 
					'.tr('guru_19').' <a class="links" href="cacheguides.php">'.tr('guru_20').'</a>.</span>
					</td></tr></table></div><br/>';
			}
		}
		tpl_set_var('guide_info', $guide_info);
		/* set last_login to one of 5 categories
		 *   1 = this month or last month
		 *   2 = between one and 6 months
		 *   3 = between 6 and 12 months
		 *   4 = more than 12 months
		 *   5 = unknown, we need this, because we dont
		 *       know the last_login of all accounts.
		 *       Can be removed after one year.
		 *   6 = user account is not active
		 */
		if ($user_record['password'] == null || $user_record['email'] == null || $user_record['is_active_flag'] != 1)
			tpl_set_var('lastlogin', tr('user_not_active'));
		else if ($user_record['last_login'] == null)
			tpl_set_var('lastlogin', tr('unknown'));
		else {
			$user_record['last_login'] = strtotime($user_record['last_login']);
			$user_record['last_login'] = mktime(date('G', $user_record['last_login']), date('i', $user_record['last_login']), date('s', $user_record['last_login']), date('n', $user_record['last_login']), date(1, $user_record['last_login']), date('Y', $user_record['last_login']));
			if ($user_record['last_login'] >= mktime(0, 0, 0, date("m") - 1, 1, date("Y")))
				tpl_set_var('lastlogin', tr('this_month'));
			else if ($user_record['last_login'] >= mktime(0, 0, 0, date("m") - 6, 1, date("Y")))
				tpl_set_var('lastlogin', tr('more_one_month'));
			else if ($user_record['last_login'] >= mktime(0, 0, 0, date("m") - 12, 1, date("Y")))
				tpl_set_var('lastlogin', tr('more_six_month'));
			else
				tpl_set_var('lastlogin', tr('more_12_month'));
		}

		$ars = sql("SELECT
					`user`.`hidden_count` AS    `ukryte`,
					`user`.`founds_count` AS    `znalezione`, 	
					`user`.`notfounds_count` AS `nieznalezione`
				FROM `user` WHERE `user_id`='&1'", $user_id);
		$record = sql_fetch_array($ars);
		$act = $record['ukryte'] + $record['znalezione'] + $record['nieznalezione'];

		if ((date('m') == 4) and (date('d') == 1)) {
				$act = rand(-10, 10);
		}

		$content .= '<br /><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/event.png" class="icon32" alt="Caches Find" title="Caches Find" />&nbsp;&nbsp;&nbsp;' . tr('user_activity01') . '</p></div><br /><p><span class="content-title-noshade txt-blue08">' . tr('user_activity02') . '</span>:&nbsp;<strong>' . $act . '</strong></p>';

		// PowerTrails stats
		
		if ($powerTrailModuleSwitchOn) {
			
			$content .= '<div class="content2-container bg-blue02">
							<p class="content-title-noshade-size1">
							<img src="tpl/stdstyle/images/blue/powerTrailGenericLogo.png" width="33" class="icon32" alt="geoPaths" title="geoPaths" />&nbsp'.tr('pt001').'</div>';
			require_once 'powerTrail/powerTrailBase.php';
			//geoPaths medals
			$content .= getPowerTrailsCompletedByUser($user_id);
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('pt140') . '</span>:&nbsp;<strong>'.powerTrailBase::getUserPoints($user_id).'</strong> (' . tr('pt093') . ' '.powerTrailBase::getPoweTrailCompletedCountByUser($user_id).')</p>';
			$pointsEarnedForPlacedCaches = powerTrailBase::getOwnerPoints($user_id);
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('pt224') . '</span>:&nbsp;<strong>'.$pointsEarnedForPlacedCaches['totalPoints'].'</strong> ('.tr('pt222').' '.$pointsEarnedForPlacedCaches['geoPathCount'].' '.tr('pt223').')</p>';
			//var_dump($a);
			db_connect(); // db mysql_* connection is switched off in geoPatch module, so re-open it.
		}
		
		//$content .= '</div>';

		// -----------  begin Find section -------------------------------------
		$rs_seek = sql("SELECT COUNT(*) FROM cache_logs WHERE (type=1 OR type=2) AND cache_logs.deleted='0' AND user_id=&1 GROUP BY YEAR(`date`), MONTH(`date`), DAY(`date`)", $user_id);
		$seek = mysql_num_rows($rs_seek);

		$content .= '<p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/cache-open.png" class="icon32" alt="Caches Find" title="Caches Find" />&nbsp;&nbsp;&nbsp;' . tr('stat_number_found') . '</p></div><br />';
		if ($seek == 0) {
			$content .= '<br /><p> <b>' . tr('not_found_caches') . '</b></p>';
		} else {
			$sql = "SELECT COUNT(*) events_count 
							FROM cache_logs 
							WHERE user_id=$user_id AND type=7 AND deleted=0";

			if ($odp = mysql_query($sql))
				$events_count = mysql_result($odp, 0);
			else
				$events_count = 0;
			
			$days_since_first_find = @mysql_result(@mysql_query("SELECT datediff(now(), date) as old FROM cache_logs WHERE deleted=0 AND user_id = $user_id AND type=1 ORDER BY date LIMIT 1"), 0);
			$rsfc2 = sql("SELECT cache_logs.cache_id cache_id,  DATE_FORMAT(cache_logs.date,'%d-%m-%Y') data, caches.wp_oc cache_wp FROM cache_logs, caches WHERE caches.cache_id=cache_logs.cache_id AND cache_logs.type='1' AND cache_logs.user_id=&1 AND cache_logs.deleted='0' ORDER BY cache_logs.date DESC LIMIT 1", $user_id);
			$rfc2 = mysql_fetch_array($rsfc2);
			$rsc = sql("SELECT COUNT(*) number FROM cache_logs WHERE type=1 AND cache_logs.deleted='0' AND user_id=&1 GROUP BY YEAR(`date`), MONTH(`date`), DAY(`date`) ORDER BY number DESC LIMIT 1", $user_id);
			$rc = sql_fetch_array($rsc);
			$moved = sqlValue("SELECT COUNT(*) FROM `cache_logs` WHERE type=4 AND cache_logs.deleted='0' AND user_id='" . sql_escape($_REQUEST['userid']) . "'", 0);
			$rsncd = sql("SELECT COUNT(*) FROM cache_logs WHERE type=1 AND cache_logs.deleted='0' AND user_id=&1 GROUP BY YEAR(`date`), MONTH(`date`), DAY(`date`)", $user_id);
			$num_rows = mysql_num_rows($rsncd);
			$sql = "SELECT COUNT(*) founds_count 
					FROM cache_logs
					WHERE user_id=$user_id AND type=1 AND deleted=0";
			if ($odp = mysql_query($sql))
				$found = mysql_result($odp, 0);
			else
				$found = 0;
			if ($ddays['diff'] == 0) {
				$aver1 = 0;
			} else {
				$aver1 = round(($found / $ddays['diff']), 2);
			}
			if ($num_rows == 0) {
				$aver2 = 0;
			} else {
				$aver2 = round(($found / $num_rows), 2);
			}
			if ((date('m') == 4) and (date('d') == 1)) {
				$found = rand(-10, 10);
				$user_record['notfounds_count'] = rand(666, 9999);
			}
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_number_found_caches') . ':</span><strong> ' . $found . '</strong>';
			if ($found == 0) {$content .= '</p>';
			} else { $content .= '&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> [<a class="links" href="my_logsFind.php?userid=' . $user_id . '">' . tr('show') . '</a>]</p>';
			}

			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_dnf_caches') . ':</span> <strong>' . $user_record['notfounds_count'] . '</strong>';

			if ($user_record['notfounds_count'] == 0) {$content .= '</p>';
			} else { $content .= '&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> [<a class="links" href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=bycreated&amp;finderid=' . $user_id . '&amp;searchbyfinder=&amp;logtype=2&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0">' . tr('show') . '</a>]</p>';
			}
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_comments') . ':</span> <strong>' . $user_record['log_notes_count'] . '</strong></p>';
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_moved') . ':</span> <strong>' . $moved . '</strong>';
			if ($moved == 0) {$content .= '</p>';
			} else { $content .= '&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> [<a class="links" href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=bycreated&amp;finderid=' . $user_id . '&amp;searchbyfinder=&amp;logtype=4&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0">' . tr('show') . '</a>]</p>';
			}
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_attended_events') . ':</span> <strong>' . $events_count . '</strong>';
			if ($events_count == 0) {$content .= '</p>';
			} else { $content .= '&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> [<a class="links" href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=bycreated&amp;finderid=' . $user_id . '&amp;searchbyfinder=&amp;logtype=7&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0">' . tr('show') . '</a>]</p>';
			}

			$recomendf = sqlValue("SELECT COUNT(*) FROM `cache_rating` WHERE `user_id`='" . sql_escape($_REQUEST['userid']) . "'", 0);
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('number_recommendations_given') . ':</span> <strong>' . $recomendf . '</strong>';

			if ($recomendf == 0) {$content .= '</p>';
			} else 
			{
				if ( $usr['userid'] == $user_id ) $link_togo = "mytop5.php"; 
				else $link_togo = "usertops.php?userid=$user_id";
					
					$content .= '&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> [<a class="links" href="' . $link_togo . '">' . tr('show') . '</a>]</p>';
			}

			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('days_caching') . ':</span> <strong>' . $num_rows . '</strong>&nbsp;' . tr('from_total_days') . ': <strong>' . $ddays['diff'] . '</strong></p>';
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('average_caches') . ':</span> <strong>' . sprintf("%u", $aver2) . '</strong></p>';
			///dzień keszowania i <strong>' . sprintf("%.1f",$aver1) . '</strong>/dzień
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('most_caches') . ':</span> <strong>' . sprintf("%u", $rc['number']) . '</strong></p>';
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('latest_cache') . ':</span>&nbsp;&nbsp;';
			if (mysql_num_rows($rsfc2) != 0) {
				$content .= '<strong><a class="links" href="viewcache.php?cacheid=' . $rfc2['cache_id'] . '">' . $rfc2['cache_wp'] . '</a>&nbsp;&nbsp;</strong>(' . $rfc2['data'] . ')</p>';
			} else { $content .= '</p>';
			}
			$content .= '<br /><table style="border-collapse: collapse; font-size: 110%;" width="250" border="1"><tr><td colspan="3" align="center" bgcolor="#DBE6F1"><b>' . tr('milestones') . '</b></td> </tr><tr><td bgcolor="#EEEDF9"><b> Nr </b></td> <td bgcolor="#EEEDF9"><b> Data </b></td> <td bgcolor="#EEEDF9"><b> Geocache</b> </td> </tr>';
			$rsms = sql("SELECT cache_logs.cache_id cache_id,  DATE_FORMAT(cache_logs.date,'%d-%m-%Y') data, caches.wp_oc cache_wp FROM cache_logs, caches WHERE caches.cache_id=cache_logs.cache_id AND cache_logs.type='1' AND cache_logs.user_id=&1 AND cache_logs.deleted='0' ORDER BY cache_logs.date ASC", $user_id);
			if (mysql_num_rows($rsms) != 0) {
				if (mysql_num_rows($rsms) < 101) {
					for ($i = 0; $i <= mysql_num_rows($rsms); $i += 10) {
						$ii = $i;
						$is = $i - 1;
						if ($i == 0) {$ii = 1;
							$is = 0;
						}
						mysql_data_seek($rsms, $is);
						$rms = mysql_fetch_array($rsms);
						$content .= '<tr> <td>' . $ii . '</td><td>' . $rms['data'] . '</td><td><a class="links" href="viewcache.php?cacheid=' . $rms['cache_id'] . '">' . $rms['cache_wp'] . '</a></td></tr>';
					}
				}
				if (mysql_num_rows($rsms) > 100) {
					for ($i = 0; $i <= mysql_num_rows($rsms); $i += 100) {
						$ii = $i;
						$is = $i - 1;
						if ($i == 0) {$ii = 1;
							$is = 0;
						}
						mysql_data_seek($rsms, $is);

						$rms = mysql_fetch_array($rsms);
						$content .= '<tr><td>' . $ii . '</td><td>' . $rms['data'] . '</td><td><a class="links" href="viewcache.php?cacheid=' . $rms['cache_id'] . '">' . $rms['cache_wp'] . '</a></td></tr>';
					}
				}
			}
			$content .= '</table>';
			mysql_free_result($rsms);
			mysql_free_result($rsncd);
			mysql_free_result($rsc);
			mysql_free_result($rsfc2);

			//------------ begin owner section
			//			if ($user_id == $usr['userid'])
			//			{

			$rs_logs = sql("SELECT cache_logs.id, cache_logs.cache_id AS cache_id,
	                          cache_logs.type AS log_type,
				cache_logs.text AS log_text,
	                          DATE_FORMAT(cache_logs.date,'%d-%m-%Y')  AS log_date,
	                          caches.name AS cache_name,
				caches.wp_oc AS wp_name,
	                          user.username AS user_name,
							  `cache_logs`.`encrypt` `encrypt`,
							cache_logs.user_id AS luser_id,
							  user.user_id AS user_id,
							  caches.user_id AS cache_owner,
							  caches.type AS cache_type,
							  cache_type.icon_small AS cache_icon_small,
							  log_types.icon_small AS icon_small,
							  IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`,COUNT(gk_item.id) AS geokret_in
	                  FROM ((cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id))) INNER JOIN user ON (cache_logs.user_id = user.user_id) INNER JOIN log_types ON (cache_logs.type = log_types.id) INNER JOIN cache_type ON (caches.type = cache_type.id) LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id` 
							LEFT JOIN	gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
							LEFT JOIN	gk_item ON gk_item.id = gk_item_waypoint.id AND
							gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5	
					  WHERE (caches.status=1 OR caches.status=2 OR caches.status=3) AND cache_logs.deleted=0 AND `cache_logs`.`user_id`='&1'
					  AND cache_logs.type <> 12 
					   GROUP BY cache_logs.id
	                   ORDER BY cache_logs.date_created DESC
					LIMIT 5", $user_id);

			if (mysql_num_rows($rs_logs) != 0) {

				$content .= '<p>&nbsp;</p><p><span class="content-title-noshade txt-blue08">' . tr('latest_logs_by_user') . ':</span>&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> [<a class="links" href="my_logs.php?userid=' . $user_id . '">' . tr('show_all') . '</a>] ';
				if ($user_id == $usr['userid'] || $usr['admin']) {
					$content .= '&nbsp;&nbsp;<a class="links" href="rss/my_logs.xml?userid=' . $user_id . '"><img src=images/rss.gif alt="" /></a>';
				}
				$content .= '</p><br /><div><ul style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.2em; font-size: 115%;">';

				for ($i = 0; $i < mysql_num_rows($rs_logs); $i++) {
					$record_logs = sql_fetch_array($rs_logs);
					$tmp_log = $log_line;
					if ($record_logs['geokret_in'] != '0') {
						$tmp_log = mb_ereg_replace('{gkimage}', '<img src="images/gk.png" border="0" alt="" title="GeoKret" />', $tmp_log);
					} else {
						$tmp_log = mb_ereg_replace('{gkimage}', '<img src="images/rating-star-empty.png" border="0" alt=""/>', $tmp_log);
					}

					if ($record_logs['recommended'] == 1 && $record_logs['log_type'] == 1) {
						$tmp_log = mb_ereg_replace('{rateimage}', '<img src="images/rating-star.png" border="0" alt=""/>', $tmp_log);
					} else {
						$tmp_log = mb_ereg_replace('{rateimage}', '<img src="images/rating-star-empty.png" border="0" alt=""/>', $tmp_log);
					}
					$tmp_log = mb_ereg_replace('{logimage}', icon_log_type($record_logs['icon_small'], "..."), $tmp_log);
					$tmp_log = mb_ereg_replace('{cacheimage}', $record_logs['cache_icon_small'], $tmp_log);
					$tmp_log = mb_ereg_replace('{date}', $record_logs['log_date'], $tmp_log);
					$tmp_log = mb_ereg_replace('{cachename}', htmlspecialchars($record_logs['cache_name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
					$tmp_log = mb_ereg_replace('{wpname}', htmlspecialchars($record_logs['wp_name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
					$tmp_log = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($record_logs['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_log);
					$tmp_log = mb_ereg_replace('{logid}', htmlspecialchars(urlencode($record_logs['id']), ENT_COMPAT, 'UTF-8'), $tmp_log);

					$logtext = '<b>' . $record_logs['user_name'] . '</b>: &nbsp;';
					if ($record_logs['encrypt'] == 1 && $record_logs['cache_owner'] != $usr['userid'] && $record_logs['luser_id'] != $usr['userid']) {
						$logtext .= "<img src=\'/tpl/stdstyle/images/free_icons/lock.png\' alt=\`\` /><br/>";
					}
					if ($record_logs['encrypt'] == 1 && ($record_logs['cache_owner'] == $usr['userid'] || $record_logs['luser_id'] == $usr['userid'])) {
						$logtext .= "<img src=\'/tpl/stdstyle/images/free_icons/lock_open.png\' alt=\`\` /><br/>";
					}
					$data_text = cleanup_text(str_replace("\r\n", " ", $record_logs['log_text']));
					$data_text = str_replace("\n", " ", $data_text);
					if ($record_logs['encrypt'] == 1 && $record_logs['cache_owner'] != $usr['userid'] && $record_logs['luser_id'] != $usr['userid']) {//crypt the log ROT13, but keep HTML-Tags and Entities
						$data_text = str_rot13_html($data_text);
					} else {$logtext .= "<br/>";
					}
					$logtext .= $data_text;
					$tmp_log = mb_ereg_replace('{logtext}', $logtext, $tmp_log);

					$content .= "\n" . $tmp_log;
				}
				$content .= '</ul></div>';
				mysql_free_result($rs_logs);
			}

			//			}
			// ----------- end owner section
			//			$content .='<p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/event.png" class="icon32" alt="Caches Find" title="Caches Find" />&nbsp;&nbsp;&nbsp;Odwiedzone województwa podczas poszukiwań (w przygotowaniu)</p></div><p><img src="images/PLmapa250.jpg" alt="" /></p>';

		}
		//------------ end find section
		//------------ begin created caches ---------------------------
		$content .= '<p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Caches created" title="Caches created" />&nbsp;&nbsp;&nbsp;' . tr('stat_created_caches') . '</p></div><br />';

		if ($user_record['hidden_count'] == 0) {
			$content .= '<br /><p> <b>' . tr('not_caches_created') . '</b></p>';
		} else {
			// nie licz spotkan, skrzynek jeszcze nieaktywnych, zarchiwizowanych i wstrzymanych
			$sql = "SELECT COUNT(*) FROM caches WHERE user_id='$user_id' AND status <> 2 AND status <> 3 AND status <> 4 AND status <> 5 AND status <> 6 AND type <> 6";
			if ($odp = mysql_query($sql))
				$hidden = mysql_result($odp, 0);
			else
				$hidden = 0;
			$sql = "SELECT COUNT(*) FROM caches WHERE user_id='$user_id' AND status <> 4 AND status <> 5 AND status <> 6 AND type=6";
			if ($odp = mysql_query($sql))
				$hidden_event = mysql_result($odp, 0);
			else
				$hidden_event = 0;
			$rsms = sql("SELECT cache_id, wp_oc, DATE_FORMAT(date_created,'%d-%m-%Y') data FROM caches WHERE user_id=&1 AND status <> 4 AND status <> 5 AND status <> 6 AND type <> 6 ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC, DAY(`date_created`) ASC, HOUR(`date_created`) ASC", $user_id);
			$hidden_all = mysql_num_rows($rsms);
			$rscc2 = sql("SELECT cache_id, wp_oc, DATE_FORMAT(date_created,'%d-%m-%Y') data FROM caches WHERE status <> 4 AND status <> 5 AND status <> 6 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`) ORDER BY YEAR(`date_created`) DESC, MONTH(`date_created`) DESC, DAY(`date_created`) DESC, HOUR(`date_created`) DESC LIMIT 1", $user_id);
			$rcc2 = mysql_fetch_array($rscc2);
			$rsc = sql("SELECT COUNT(*) number FROM caches WHERE status <> 4 AND status <> 5 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`) ORDER BY number DESC LIMIT 1", $user_id);
			$rc = sql_fetch_array($rsc);
			$rsncd = sql("SELECT COUNT(*) FROM caches WHERE status <> 5 AND status <> 4 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`)", $user_id);
			$rsnca = sql("SELECT COUNT(*) FROM caches WHERE status <> 2 AND status <> 3 AND status <> 5 AND status <> 4 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`)", $user_id);
			$num_rows = mysql_num_rows($rsnca);
			$num_rows = mysql_num_rows($rsncd);
			if($ddays['diff'] != 0) {
				$aver1 = round(($user_record['hidden_count'] / $ddays['diff']), 2);
			} else {
				$aver1 = 0;
			}
			$aver2 = round(($user_record['hidden_count'] / $num_rows), 2);
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_created_caches') . ':  </span><strong>' . $hidden_all . '</strong>';
			if ($user_record['hidden_count'] == 0) {$content .= '</p>';
			} else {$content .= '&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> [<a class="links" href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bycreated&amp;ownerid=' . $user_id . '&amp;cachetype=1111101111&amp;searchbyowner=&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0">' . tr('show') . '</a>]</p>';
			}

			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_of_active_caches') . ':  </span><strong>' . $hidden . '</strong>';
			if ($hidden == 0) {$content .= '</p>';
			} else {$content .= '&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> [<a class="links" href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bycreated&amp;ownerid=' . $user_id . '&amp;cachetype=1111101111&amp;searchbyowner=&amp;f_inactive=1&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0">' . tr('show') . '</a>]</p>';
			}

			$hidden_temp = sqlValue("SELECT COUNT(*) FROM `caches` WHERE status=2 AND `user_id`='" . sql_escape($_REQUEST['userid']) . "'", 0);
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('number_temp_caches') . ':  </span><strong>' . $hidden_temp . '</strong></p>';

			$hidden_arch = sqlValue("SELECT COUNT(*) FROM `caches` WHERE status=3 AND type <> 6 AND `user_id`='" . sql_escape($_REQUEST['userid']) . "'", 0);
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('number_archived_caches') . ': </span><strong>' . $hidden_arch . '</strong></p>';

			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('number_created_events') . ':  </span><strong>' . $hidden_event . '</strong>';
			if ($hidden_event == 0) {$content .= '</p>';
			} else {$content .= '&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> [<a class="links" href="search.php?searchto=searchbyowner&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bycreated&amp;ownerid=' . $user_id . '&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;f_geokret=0&amp;country=&amp;cachetype=0000010000">' . tr('show') . '</a>]</p>';
			}
			$recomendr = sqlValue("SELECT COUNT(*) FROM `cache_rating`, caches WHERE `cache_rating`.`cache_id`=`caches`.`cache_id` AND caches.type <> 6 AND `caches`.`user_id`='" . sql_escape($_REQUEST['userid']) . "'", 0);
			$recommend_caches = sqlValue("SELECT COUNT(*) FROM caches WHERE `caches`.`topratings` >= 1 AND caches.type <> 6 AND  `caches`.`user_id`='" . sql_escape($_REQUEST['userid']) . "'", 0);
			if ($recomendr != 0) {

				$ratio = sprintf("%u", ($recommend_caches / $hidden_all) * 100);
				$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('number_obtain_recommendations') . ':</span> <strong>' . $recomendr . '</strong> ' . tr('for') . ' <strong>' . $recommend_caches . '</strong> ' . tr('_caches_') . ' &nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> [<a class="links" href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;cachetype=1111101111&amp;sort=bycreated&amp;ownerid=' . $user_id . '&amp;searchbyowner=&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;cacherating=1">' . tr('show') . '</a>]</p>
<p><span class="content-title-noshade txt-blue08">' . tr('ratio_recommendations') . ':</span> <strong>' . $ratio . '%</strong></p>';
			}

			$numberGK_in_caches = sqlValue("SELECT count(*) FROM gk_item, gk_item_waypoint,caches
				WHERE gk_item_waypoint.wp = caches.wp_oc AND
			       gk_item.id = gk_item_waypoint.id AND
				gk_item.stateid <> 1 AND gk_item.stateid <> 4 AND gk_item.stateid <> 5 AND gk_item.typeid <> 2 AND `caches`.`user_id`='" . sql_escape($_REQUEST['userid']) . "'", 0);
			if ($numberGK_in_caches != 0) {
				$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('number_gk_in_caches') . ':</span> <strong>' . $numberGK_in_caches . '</strong></p>';
				$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('days_caching') . ':</span> <strong>' . $num_rows . '</strong> ' . tr('from_total_days') . ': <strong>' . $ddays['diff'] . '</strong></p>';
			}

			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('average_caches') . ':</span> <strong>' . sprintf("%u", $aver2) . '</strong></p>';
			//' . sprintf("%.1f",$aver1) . '</strong>/dzień
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('most_caches') . ':</span> <strong>' . sprintf("%u", $rc['number']) . '</strong></p>';
			$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('latest_created_cache') . ':</span>&nbsp;&nbsp;<strong><a class="links" href="viewcache.php?cacheid=' . $rcc2['cache_id'] . '">' . $rcc2['wp_oc'] . '</a>&nbsp;&nbsp;</strong>(' . $rcc2['data'] . ')</p>';
			$content .= '<br /><table style="border-collapse: collapse; font-size: 110%;" width="250" border="1"><tr><td colspan="3" align="center" bgcolor="#DBE6F1"><b>' . tr('milestones') . '</b></td> </tr><tr><td bgcolor="#EEEDF9"><b> Nr </b></td> <td bgcolor="#EEEDF9"><b> Data </b></td> <td bgcolor="#EEEDF9"><b> Geocache</b> </td> </tr>';

			$rms = mysql_fetch_array($rsms);
			if (mysql_num_rows($rsms) < 101) {
				for ($i = 0; $i <= mysql_num_rows($rsms); $i += 10) {
					$ii = $i;
					$is = $i - 1;
					if ($i == 0) {$ii = 1;
						$is = 0;
					}
					if($is !=0) mysql_data_seek($rsms, $is);
					$rms = mysql_fetch_array($rsms);
					$content .= '<tr> <td>' . $ii . '</td><td>' . $rms['data'] . '</td><td><a class="links" href="viewcache.php?cacheid=' . $rms['cache_id'] . '">' . $rms['wp_oc'] . '</a></td></tr>';
				}
			}
			if (mysql_num_rows($rsms) > 100) {
				for ($i = 0; $i < mysql_num_rows($rsms); $i += 100) {
					$ii = $i;
					$is = $i - 1;
					if ($i == 0) {$ii = 1;
						$is = 0;
					}
					mysql_data_seek($rsms, $is);

					$rms = mysql_fetch_array($rsms);
					$content .= '<tr><td>' . $ii . '</td><td>' . $rms['data'] . '</td><td><a class="links" href="viewcache.php?cacheid=' . $rms['cache_id'] . '">' . $rms['wp_oc'] . '</a></td></tr>';
				}
			}
			$content .= '</table>';
			mysql_free_result($rsms);
			mysql_free_result($rsncd);
			mysql_free_result($rsc);
			mysql_free_result($rscc2);

			$rs_logs = sql("SELECT cache_logs.id, cache_logs.cache_id AS cache_id,
	                       cache_logs.type AS log_type,
				           cache_logs.text AS log_text,
	                       DATE_FORMAT(cache_logs.date,'%d-%m-%Y') AS log_date,
	                       caches.name AS cache_name,
						   caches.wp_oc AS wp_name,
						   cache_logs.encrypt AS encrypt,
						   caches.user_id AS cache_owner,
						   cache_logs.user_id AS luser_id,
	                       user.username AS user_name,
						   user.user_id AS user_id,
						   caches.type AS cache_type,
						   cache_type.icon_small AS cache_icon_small,
						   log_types.icon_small AS icon_small,
							  IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`, COUNT(gk_item.id) AS geokret_in							  
	FROM ((cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id))) INNER JOIN user ON (cache_logs.user_id = user.user_id) INNER JOIN log_types ON (cache_logs.type = log_types.id) INNER JOIN cache_type ON (caches.type = cache_type.id) LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
	LEFT JOIN	gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
	LEFT JOIN	gk_item ON gk_item.id = gk_item_waypoint.id AND
	gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5
					  WHERE (caches.status=1 OR caches.status=2 OR caches.status=3) AND cache_logs.deleted=0 AND `caches`.`user_id`='&1'
					  		AND `cache_logs`.`cache_id`=`caches`.`cache_id` 
							AND `user`.`user_id`=`cache_logs`.`user_id`
							GROUP BY cache_logs.id
	                   ORDER BY `cache_logs`.`date_created` DESC
					LIMIT 5", $user_id);

			if (mysql_num_rows($rs_logs) != 0) {
				$content .= '<p>&nbsp;</p><p><span class="content-title-noshade txt-blue08">' . tr('latest_logs_in_caches') . ':</span>&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> [<a class="links" href="mycaches_logs.php?userid=' . $user_id . '">' . tr('show_all') . '</a>] ';
				if ($user_id == $usr['userid'] || $usr['admin']) {
					$content .= '&nbsp;&nbsp;<a class="links" href="rss/mycaches_logs.xml?userid=' . $user_id . '"><img src=images/rss.gif alt="" /></a>';
				}

				$content .= '</p><br /><div><ul style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.6em; font-size: 12px;">';
				for ($i = 0; $i < mysql_num_rows($rs_logs); $i++) {
					$record_logs = sql_fetch_array($rs_logs);

					$tmp_log = $cache_line_my_caches;

					if ($record_logs['geokret_in'] != '0') {
						$tmp_log = mb_ereg_replace('{gkimage}', '<img src="images/gk.png" border="0" alt="" title="GeoKret" />', $tmp_log);
					} else {
						$tmp_log = mb_ereg_replace('{gkimage}', '<img src="images/rating-star-empty.png" border="0" alt=""/>', $tmp_log);
					}

					if ($record_logs['recommended'] == 1 && $record_logs['log_type'] == 1) {
						$tmp_log = mb_ereg_replace('{rateimage}', '<img src="images/rating-star.png" border="0" alt=""/>', $tmp_log);
					} else {
						$tmp_log = mb_ereg_replace('{rateimage}', '<img src="images/rating-star-empty.png" border="0" alt=""/>', $tmp_log);
					}
					$tmp_log = mb_ereg_replace('{logimage}', icon_log_type($record_logs['icon_small'], "..."), $tmp_log);
					$tmp_log = mb_ereg_replace('{cacheimage}', $record_logs['cache_icon_small'], $tmp_log);
					$tmp_log = mb_ereg_replace('{date}', $record_logs['log_date'], $tmp_log);
					$tmp_log = mb_ereg_replace('{cachename}', htmlspecialchars($record_logs['cache_name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
					$tmp_log = mb_ereg_replace('{wpname}', htmlspecialchars($record_logs['wp_name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
					$tmp_log = mb_ereg_replace('{cacheid}', htmlspecialchars($record_logs['cache_id'], ENT_COMPAT, 'UTF-8'), $tmp_log);

					// ukrywanie nicka autora komentarza COG przed zwykłym userem
					// (Łza)
					if (($record_logs['log_type'] == 12) && (!$usr['admin'])) {
						$record_logs['user_name'] = 'Centrum Obsługi Geocachera';
						$record_logs['user_id'] = 0;
					}
					// koniec ukrywania nicka autora komentarza COG

					$tmp_log = mb_ereg_replace('{userid}', htmlspecialchars($record_logs['user_id'], ENT_COMPAT, 'UTF-8'), $tmp_log);
					$tmp_log = mb_ereg_replace('{username}', htmlspecialchars($record_logs['user_name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
					$tmp_log = mb_ereg_replace('{logid}', htmlspecialchars(urlencode($record_logs['id']), ENT_COMPAT, 'UTF-8'), $tmp_log);

					$logtext = '<b>' . $record_logs['user_name'] . '</b>: &nbsp;';
					if ($record_logs['encrypt'] == 1 && $record_logs['cache_owner'] != $usr['userid'] && $record_logs['luser_id'] != $usr['userid']) {
						$logtext .= "<img src=\'/tpl/stdstyle/images/free_icons/lock.png\' alt=\`\` /><br/>";
					}
					if ($record_logs['encrypt'] == 1 && ($record_logs['cache_owner'] == $usr['userid'] || $record_logs['luser_id'] == $usr['userid'])) {
						$logtext .= "<img src=\'/tpl/stdstyle/images/free_icons/lock_open.png\' alt=\`\` /><br/>";
					}
					$data_text = cleanup_text(str_replace("\r\n", " ", $record_logs['log_text']));
					$data_text = str_replace("\n", " ", $data_text);
					if ($record_logs['encrypt'] == 1 && $record_logs['cache_owner'] != $usr['userid'] && $record_logs['luser_id'] != $usr['userid']) {//crypt the log ROT13, but keep HTML-Tags and Entities
						$data_text = str_rot13_html($data_text);
					} else {$logtext .= "<br/>";
					}
					$logtext .= $data_text;

					$tmp_log = mb_ereg_replace('{logtext}', $logtext, $tmp_log);

					$content .= "\n" . $tmp_log;
				}
				mysql_free_result($rs_logs);
				$content .= '</ul></div><br />';
			}

		}

		//  ----------------- begin  owner section  ----------------------------------
		if ($user_id == $usr['userid'] || $usr['admin']) {
			$rscheck = sqlValue("SELECT count(*) FROM caches WHERE (status = 4 OR status = 5 OR status = 6) AND `user_id`='" . sql_escape($_REQUEST['userid']) . "'", 0);

			if ($rscheck != 0) {$content .= '<br /><div class="content-title-noshade box-blue">';
			}

			if (checkField('cache_status', $lang))
				$lang_db = $lang;
			else
				$lang_db = "en";

			//get not published caches DATE_FORMAT(`caches`.`date_activate`,'%d-%m-%Y'),
			$rs_caches1 = sql("	SELECT  `caches`.`cache_id`, `caches`.`name`, `caches`.`date_hidden`, `caches`.`date_activate`, `caches`.`status`, `cache_status`.`&1` AS `cache_status_text`, `caches`.`wp_oc` AS `wp_name`
						FROM `caches`, `cache_status`
						WHERE `user_id`='&2'
						AND `cache_status`.`id`=`caches`.`status`
						AND `caches`.`status` = 5
						ORDER BY `date_activate` DESC, `caches`.`date_created` DESC ", $lang_db, $user_id);

			if (mysql_num_rows($rs_caches1) != 0) {

				$content .= '<p><span class="content-title-noshade txt-blue08">' . tr('not_yet_published') . ':</span></p><br /><div><ul style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.2em; font-size: 115%;">';
				for ($i = 0; $i < mysql_num_rows($rs_caches1); $i++) {
					$record_caches = sql_fetch_array($rs_caches1);

					$tmp_cache = $cache_notpublished_line;

					$tmp_cache = mb_ereg_replace('{cacheimage}', icon_cache_status($record_caches['status'], $record_caches['cache_status_text']), $tmp_cache);
					$tmp_cache = mb_ereg_replace('{cachestatus}', htmlspecialchars($record_caches['cache_status_text'], ENT_COMPAT, 'UTF-8'), $tmp_cache);
					$tmp_cache = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($record_caches['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_cache);
					if (is_null($record_caches['date_activate'])) {
						$tmp_cache = mb_ereg_replace('{date}', $no_time_set, $tmp_cache);
					} else {
						$tmp_cache = mb_ereg_replace('{date}', $record_caches['date_activate'], $tmp_cache);
					}
					$tmp_cache = mb_ereg_replace('{cachename}', htmlspecialchars($record_caches['name'], ENT_COMPAT, 'UTF-8'), $tmp_cache);
					$tmp_cache = mb_ereg_replace('{wpname}', htmlspecialchars($record_caches['wp_name'], ENT_COMPAT, 'UTF-8'), $tmp_cache);
					$content .= "\n" . $tmp_cache;
				}
				mysql_free_result($rs_caches1);
				$content .= '</ul></div>';
			}
			//get waiting to approve caches by OC Team
			//get last hidden caches
			if (checkField('cache_status', $lang))
				$lang_db = $lang;
			else
				$lang_db = "en";

			$rs_caches2 = sql("	SELECT	`cache_id`, `name`, DATE_FORMAT(`date_hidden`,'%d-%m-%Y') AS `date`, `status`,
							`cache_status`.`id` AS `cache_status_id`, `cache_status`.`&1` AS `cache_status_text`, `caches`.`wp_oc` AS `wp_name`
						FROM `caches`, `cache_status`
						WHERE `user_id`='&2'
						  AND `cache_status`.`id`=`caches`.`status` 
						  AND `caches`.`status` = 4
						ORDER BY `date_hidden` DESC, `caches`.`date_created` DESC", $lang_db, $user_id);

			if (mysql_num_rows($rs_caches2) != 0) {
				$content .= '<br /><p><span class="content-title-noshade txt-blue08">' . tr('caches_waiting_approve') . ':</span></p><br /><div><ul style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.2em; font-size: 115%;">';

				for ($i = 0; $i < mysql_num_rows($rs_caches2); $i++) {
					$record_logs = sql_fetch_array($rs_caches2);

					$tmp_cache = $cache_line;

					$tmp_cache = mb_ereg_replace('{cacheimage}', icon_cache_status($record_logs['status'], $record_logs['cache_status_text']), $tmp_cache);
					$tmp_cache = mb_ereg_replace('{cachestatus}', htmlspecialchars($record_logs['cache_status_text'], ENT_COMPAT, 'UTF-8'), $tmp_cache);
					$tmp_cache = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($record_logs['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_cache);
					$tmp_cache = mb_ereg_replace('{date}', $record_logs['date'], $tmp_cache);
					$tmp_cache = mb_ereg_replace('{cachename}', htmlspecialchars($record_logs['name'], ENT_COMPAT, 'UTF-8'), $tmp_cache);
					$tmp_cache = mb_ereg_replace('{wpname}', htmlspecialchars($record_logs['wp_name'], ENT_COMPAT, 'UTF-8'), $tmp_cache);

					$content .= "\n" . $tmp_cache;
				}
				mysql_free_result($rs_caches2);
				$content .= '</ul></div>';
			}

			//get blocked caches by OC Team
			//get last hidden caches
			if (checkField('cache_status', $lang))
				$lang_db = $lang;
			else
				$lang_db = "en";

			$rs_caches3 = sql("	SELECT	`cache_id`, `name`, DATE_FORMAT(`date_hidden`,'%d-%m-%Y') AS `date`, `status`,
							`cache_status`.`id` AS `cache_status_id`, `cache_status`.`&1` AS `cache_status_text`, `caches`.`wp_oc` AS `wp_name`
						FROM `caches`, `cache_status`
						WHERE `user_id`='&2'
						  AND `cache_status`.`id`=`caches`.`status` 
						  AND `caches`.`status` = 6
						ORDER BY `date_hidden` DESC, `caches`.`date_created` DESC", $lang_db, $user_id);

			if (mysql_num_rows($rs_caches3) != 0) {
				$content .= '<br /><p><span class="content-title-noshade txt-blue08">' . tr('caches_blocked') . ':</span></p><br /><div><ul style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.2em; font-size: 115%;">';

				for ($i = 0; $i < mysql_num_rows($rs_caches3); $i++) {
					$record_logs = sql_fetch_array($rs_caches3);

					$tmp_cache = $cache_line;

					$tmp_cache = mb_ereg_replace('{cacheimage}', icon_cache_status($record_logs['status'], $record_logs['cache_status_text']), $tmp_cache);
					$tmp_cache = mb_ereg_replace('{cachestatus}', htmlspecialchars($record_logs['cache_status_text'], ENT_COMPAT, 'UTF-8'), $tmp_cache);
					$tmp_cache = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($record_logs['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_cache);
					$tmp_cache = mb_ereg_replace('{date}', $record_logs['date'], $tmp_cache);
					$tmp_cache = mb_ereg_replace('{cachename}', htmlspecialchars($record_logs['name'], ENT_COMPAT, 'UTF-8'), $tmp_cache);
					$tmp_cache = mb_ereg_replace('{wpname}', htmlspecialchars($record_logs['wp_name'], ENT_COMPAT, 'UTF-8'), $tmp_cache);
					$content .= "\n" . $tmp_cache;
				}
				mysql_free_result($rs_caches3);
				$content .= '</ul></div>';
			}

			// if user have blocked create new cache, display this info for owner of profile
			if ($user_record['hide_flag'] == 10) {		$content .= '<p>&nbsp</p><p><span class="content-title-noshade txt-red08"><strong>' . tr('blocked_create_caches') . '</strong></span></p><br />';
			}

			if ($rscheck != 0) {	$content .= '</div>';
			}
		}
		// ------------------ end owner section ---------------------------------
		//------------ end created caches section ------------------------------

		tpl_set_var('content', $content);
	}
}
tpl_BuildTemplate();

/**
 * generate html string displaying geoPaths completed by user (power trail) medals
 * @author Andrzej Łza Woźniak, 2013-11-23
 */
function getPowerTrailsCompletedByUser($userId){
	$ptCompletedList = powerTrailBase::getPowerTrailsCompletedByUser($userId);
	// var_dump($ptCompletedList);
	$result = '<table width="100%"><tr><td>';
	foreach ($ptCompletedList as $pt) {
		if($pt['image'] == '') $pt['image'] = 'tpl/stdstyle/images/blue/powerTrailGenericLogo.png';
		$result .= '<div class="ptMedal ptType'.$pt['type'].'"><table style="padding-top: 7px;" align="center" height="51" width="51"><tr><td valign="center" align="center"><a title="'.$pt['name'].'" href="powerTrail.php?ptAction=showSerie&ptrail='.$pt['id'].'"><img class="imgPtMedal" src="'.$pt['image'].'"></a></td></tr></table></div><div class="ptMedalSpacer"></div>';
	}
	return $result.'</td></tr><tr><td></td></tr></table>';
}
?>