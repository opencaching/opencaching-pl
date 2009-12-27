<?php
/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*
	***************************************************************************/
	
//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
		global $stat_menu; $lang;
	
	//Preprocessing
	if ($error == false)
	{
		//user logged in?
		if ($usr == false)
		{
		    $target = urlencode(tpl_get_current_page());
		    tpl_redirect('login.php?target='.$target);
		}
		else
		{

		// check for old-style parameters
		if (isset($_REQUEST['userid']))
		{
			$user_id = $_REQUEST['userid'];
			tpl_set_var('userid',$user_id);		
		}
			require($stylepath . '/viewprofile-test.inc.php');
			require($stylepath . '/lib/icons.inc.php');
				$tplname = 'viewprofile-test';
				$stat_menu = array(
					'title' => tr('Statictics'),
					'menustring' => tr('Statictics'),
					'siteid' => 'statlisting',
					'navicolor' => '#E8DDE4',
					'visible' => false,
					'filename' => 'viewprofile.php?userid='.$user_id,
					'submenu' => array(
						array(
							'title' => tr('graph_created'),
							'menustring' => tr('graph_created'),
							'visible' => true,
							'filename' => 'ustatsg1.php?userid='.$user_id,
							'newwindow' => false,
							'siteid' => 'createstat',
							'icon' => 'images/actions/stat'
						),
						array(
							'title' => tr('graph_find'),
							'menustring' => tr('graph_find'),
							'visible' => true,
							'filename' => 'ustatsg2.php?userid='.$user_id,
							'newwindow' => false,
							'siteid' => 'findstat',
							'icon' => 'images/actions/stat'
						)
					)
				);

	$content="";

	  $rdd=sql("select TO_DAYS(NOW()) - TO_DAYS(`date_created`) `diff` from `user` WHERE user_id=&1 ",$user_id);
	  $ddays = mysql_fetch_array($rdd);
	  mysql_free_result($rdd);

	$rsGeneralStat =sql("SELECT hidden_count, founds_count, log_notes_count, notfounds_count, username, countries.pl country, date_created, description FROM user LEFT JOIN countries ON (user.country=countries.short) WHERE user_id=&1",$user_id);

			$user_record = sql_fetch_array($rsGeneralStat);
			tpl_set_var('username',$user_record['username']);
			tpl_set_var('country', htmlspecialchars($user_record['country'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('registered', strftime($dateformat, strtotime($user_record['date_created'])));
			$description = $user_record['description'];
			tpl_set_var('description',nl2br($description));		
			if( $description != "" )
			{
				tpl_set_var('description_start', '');
				tpl_set_var('description_end', '');
			}
			else
			{
				tpl_set_var('description_start', '<!--');
				tpl_set_var('description_end', '-->');
			}
			
//------------ begin created caches ---------------------------			
			$content .= '<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Caches created" title="Caches created" />&nbsp;&nbsp;&nbsp;Statystyka liczbowa skrzynek założonych</p></div><br />';			


			if ($user_record['hidden_count'] == 0) {
			$content .= '<br /><p> <b>Nie ma jeszcze żadnej założonej skrzynki</b></p>';
						  }
						  else 
						  { 
			// nie licz spotkan, skrzynek jeszcze nieaktywnych, zarchiwizowanych i wstrzymanych
			$sql = "SELECT COUNT(*) FROM caches WHERE user_id='$user_id' AND status <> 2 AND status <> 3 AND status <> 4 AND status <> 5 AND status <> 6 AND type <> 6";
			if( $odp = mysql_query($sql) )
				$hidden = mysql_result($odp,0);
			else 
			$hidden = 0;	
			$sql = "SELECT COUNT(*) FROM caches WHERE user_id='$user_id' AND status <> 4 AND status <> 5 AND status <> 6 AND type=6";
			if( $odp = mysql_query($sql) )
				$hidden_event = mysql_result($odp,0);
			else 
			$hidden_event = 0;			
			
//			$rscc1=sql("SELECT cache_id, wp_oc, DATE_FORMAT(date_created,'%Y-%m-%d') data FROM caches WHERE `status` != 5 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC, DAY(`date_created`) ASC LIMIT 1",$user_id);
//			$rcc1 = mysql_fetch_array($rscc1);
			$rscc2=sql("SELECT cache_id, wp_oc, DATE_FORMAT(date_created,'%Y-%m-%d') data FROM caches WHERE status <> 4 AND status <> 5 AND status <> 6 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`) ORDER BY YEAR(`date_created`) DESC, MONTH(`date_created`) DESC, DAY(`date_created`) DESC LIMIT 1",$user_id);
			$rcc2 = mysql_fetch_array($rscc2);
			$rsc=sql("SELECT COUNT(*) number FROM caches WHERE status <> 4 AND status <> 5 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`) ORDER BY number DESC LIMIT 1",$user_id);
			$rc = sql_fetch_array($rsc);
			$rsncd= sql ("SELECT COUNT(*) FROM caches WHERE status <> 5 AND status <> 4 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`)",$user_id);
			$num_rows = mysql_num_rows($rsncd); 
			$aver1= round(($user_record['hidden_count']/$ddays['diff']), 2);
			$aver2= round(($user_record['hidden_count']/$num_rows), 2);			
			$content .= '<p><span class="content-title-noshade txt-blue08" >Liczba wszystkich założonych skrzynek:  </span><strong>' . $user_record['hidden_count'] . '</strong>  w tym aktywnych <strong>' . $hidden . '</strong>';
			if ($user_record['hidden_count'] == 0) 
				{$content .= '</p>';}
				else						

			{$content .= '&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> (<a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bycreated&amp;ownerid=' . $user_id . '&amp;searchbyowner=&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0">'.tr('show').'</a>)</p>';}

			$content .= '<p><span class="content-title-noshade txt-blue08" >Liczba zorganizowanych spotkań (events):  </span><strong>' . $hidden_event . '</strong>';
			if ($hidden_event == 0) 
				{$content .= '</p>';}
				else						
			{$content .= '&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> (<a href="http://www.opencaching.pl/beta/search.php?searchto=searchbyowner&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bycreated&amp;ownerid=' . $user_id . '&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;f_geokret=0&amp;country=&amp;cachetype=0000010000">'.tr('show').'</a>)</p>';}
			$recomendr = sqlValue("SELECT COUNT(*) FROM `cache_rating`, caches WHERE `cache_rating`.`cache_id`=`caches`.`cache_id` AND `caches`.`user_id`='" . sql_escape($_REQUEST['userid']) . "'", 0);
			
			$content .= '<p><span class="content-title-noshade txt-blue08" >Liczba otrzymanych rekomendacji:</span> <strong>' . $recomendr . '</strong>';
				if ($recomendr == 0) 
				{$content .= '</p>';}
				else						
		
			{$content .= '&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> (<a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bycreated&amp;ownerid=' . $user_id . '&amp;searchbyowner=&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;cacherating=1">'.tr('show').'</a>)</p>';}
			
			$content .= '<p><span class="content-title-noshade txt-blue08" >Liczba dni "keszowania":</span> <strong>' . $num_rows . '</strong> z całkowitej ilości dni: <strong>' . $ddays['diff'] . '</strong></p>';
			$content .= '<p><span class="content-title-noshade txt-blue08" >Średnio skrzynek/dzień:</span> <strong>' . $aver2 . '</strong>/dzień keszowania i <strong>' . $aver1 . '</strong>/dzień</p>';
			$content .= '<p><span class="content-title-noshade txt-blue08" >Najwięcej skrzynek/dzień:</span> <strong>' . $rc['number'] . '</strong></p>';
//			$content .= '<p><span class="content-title-noshade txt-blue08" >Pierwsza założona skrzynka:</span>&nbsp;&nbsp;<strong><a href="viewcache.php?cacheid=' . $rcc1['cache_id'] . '">' . $rcc1['wp_oc'] . '</a>&nbsp;&nbsp;</strong>(' . $rcc1['data'] . ')</p>';
			$content .= '<p><span class="content-title-noshade txt-blue08" >Najnowsza założona skrzynka:</span>&nbsp;&nbsp;<strong><a href="viewcache.php?cacheid=' . $rcc2['cache_id'] . '">' . $rcc2['wp_oc'] . '</a>&nbsp;&nbsp;</strong>(' . $rcc2['data'] . ')</p>';	
			$content .= '<br /><table style="border-collapse: collapse;" width="250" border="1"><tr><td colspan="3" align="center" bgcolor="#DBE6F1"><b> Milestones "kamienie milowe"</b></td> </tr><tr><td bgcolor="#EEEDF9"><b> Nr </b></td> <td bgcolor="#EEEDF9"><b> Data </b></td> <td bgcolor="#EEEDF9"><b> Geocache</b> </td> </tr>';

			$rsms=sql("SELECT cache_id, wp_oc, DATE_FORMAT(date_created,'%Y-%m-%d') data FROM caches WHERE status <>4 AND status <>5 AND status <> 6 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC, DAY(`date_created`) ASC",$user_id);
			$rms = mysql_fetch_array($rsms);
			if (mysql_num_rows($rsms) <= 100) {
			for ($i = 0; $i < mysql_num_rows($rsms); $i+=10)
				{		
				$ii=$i;
				$is=$i-1;
				if ($i==0) {$ii=1; $is=0;}
				mysql_data_seek($rsms, $is);	
				$rms = mysql_fetch_array($rsms);
				$content .= '<tr> <td>' . $ii . '</td><td>'  . $rms['data'] . '</td><td><a href="viewcache.php?cacheid=' . $rms['cache_id'] . '">' . $rms['wp_oc'] . '</a></td></tr>';
				}
			}
			if (mysql_num_rows($rsms) > 100) {
			for ($i = 0; $i < mysql_num_rows($rsms); $i+=100)
			{		
			$ii=$i;
				$is=$i-1;
				if ($i==0) {$ii=1; $is=0;}
				mysql_data_seek($rsms, $is);	

			$rms = mysql_fetch_array($rsms);
			$content .= '<tr><td>' . $ii . '</td><td>' . $rms['data'] . '</td><td><a href="viewcache.php?cacheid=' . $rms['cache_id'] . '">' . $rms['cache_wp'] . '</a></td></tr>';
			}}
			$content .='</table>';
			mysql_free_result($rsms);	
			mysql_free_result($rsncd);
			mysql_free_result($rsc);
//			mysql_free_result($rscc1);
			mysql_free_result($rscc2);
//  ----------------- begin  owner section  ----------------------------------
		if ($user_id == $usr['userid']) 
		{
			if(checkField('cache_status',$lang) )
				$lang_db = $lang;
			else
				$lang_db = "en";

			//get not published caches
			$rs_caches = sql("	SELECT  `caches`.`cache_id`, `caches`.`name`, `caches`.`date_hidden`, `caches`.`date_activate`, `caches`.`status`, `cache_status`.`&1` AS `cache_status_text`
						FROM `caches`, `cache_status`
						WHERE `user_id`='&2'
						AND `cache_status`.`id`=`caches`.`status`
						AND `caches`.`status` = 5
						ORDER BY `date_activate` DESC, `caches`.`date_created` DESC ",$lang_db, $usr['userid']);
			if (mysql_num_rows($rs_caches) != 0)
			{
	
				$content .= '<p>&nbsp</p><p><span class="content-title-noshade txt-blue08" >Moje nieopublikowane jeszcze skrzynki:</span></p><p>';
				for ($i = 0; $i < mysql_num_rows($rs_caches); $i++)
				{
					$record_caches = sql_fetch_array($rs_caches);

					$tmp_cache = $cache_notpublished_line;

					$tmp_cache = mb_ereg_replace('{cacheimage}', icon_cache_status($record_caches['status'], $record_caches['cache_status_text']), $tmp_cache);
					$tmp_cache = mb_ereg_replace('{cachestatus}', htmlspecialchars($record_caches['cache_status_text'], ENT_COMPAT, 'UTF-8'), $tmp_cache);
					$tmp_cache = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($record_caches['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_cache);
					if(is_null($record_caches['date_activate']))
					{
						$tmp_cache = mb_ereg_replace('{date}', $no_time_set, $tmp_cache);
					}
					else
					{
						$tmp_cache = mb_ereg_replace('{date}', strftime($datetimeformat , strtotime($record_caches['date_activate'])), $tmp_cache);
					}
					$tmp_cache = mb_ereg_replace('{cachename}', htmlspecialchars($record_caches['name'], ENT_COMPAT, 'UTF-8'), $tmp_cache);

					$content .= "\n" . $tmp_cache;
				}
				$content .='';
			}
//			if(checkField('log_types_text',$lang) )
//				$lang_db = $lang;
//			else
//				$lang_db = "en";

			//get last logs in your caches
			$rs_logs = sql("
					SELECT `cache_logs`.`cache_id` `cache_id`, `cache_logs`.`type` `type`, `cache_logs`.`date` `date`, `caches`.`name` `name`,
						`log_types`.`icon_small`, `log_types_text`.`text_combo`, `cache_logs`.`user_id` `user_id`, `user`.`username` `username`
					FROM `cache_logs`, `caches`, `log_types`, `log_types_text`, `user`
					WHERE `caches`.`user_id`='&1'
					AND `cache_logs`.`cache_id`=`caches`.`cache_id` 
					AND `cache_logs`.`deleted`=0 
					AND `user`.`user_id`=`cache_logs`.`user_id`
					AND `log_types`.`id`=`cache_logs`.`type`
					AND `log_types_text`.`log_types_id`=`log_types`.`id` AND `log_types_text`.`lang`='&2'
					ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`date_created` DESC
					LIMIT 5", $usr['userid'], $lang);

			if (mysql_num_rows($rs_logs) != 0)
			{
				$content .= '<p>&nbsp;</p><p><span class="content-title-noshade txt-blue08" >Najnowsze wpisy w logach w Moich skrzynkach:</span></p><p>';
				for ($i = 0; $i < mysql_num_rows($rs_logs); $i++)
				{
					$record_logs = sql_fetch_array($rs_logs);

					$tmp_log = $cache_line_my_caches;
					$tmp_log = mb_ereg_replace('{logimage}', icon_log_type($record_logs['icon_small'], $record_logs['text_combo']), $tmp_log);
					$tmp_log = mb_ereg_replace('{logtype}', $record_logs['text_combo'], $tmp_log);
					$tmp_log = mb_ereg_replace('{date}', strftime($dateformat , strtotime($record_logs['date'])), $tmp_log);
					$tmp_log = mb_ereg_replace('{cachename}', htmlspecialchars($record_logs['name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
					$tmp_log = mb_ereg_replace('{cacheid}', htmlspecialchars($record_logs['cache_id'], ENT_COMPAT, 'UTF-8'), $tmp_log);
					$tmp_log = mb_ereg_replace('{userid}', htmlspecialchars($record_logs['user_id'], ENT_COMPAT, 'UTF-8'), $tmp_log);
					$tmp_log = mb_ereg_replace('{username}', htmlspecialchars($record_logs['username'], ENT_COMPAT, 'UTF-8'), $tmp_log);

					$lcontent .= "\n" . $tmp_log;
				}
							mysql_free_result($rs_logs);
				$content .='</p>';
			}

		}		
// ------------------ end owner section ---------------------------------			
	}
//------------ end created caches section ------------------------------

// -----------  begin Find section -------------------------------------
		$content .= '<p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="Caches Find" title="Caches Find" />&nbsp;&nbsp;&nbsp;Statystyka liczbowa skrzynek znalezionych</p></div>';
		if ($user_record['founds_count'] == 0) {
			$content .= '<br /><p> <b>Nie ma jeszcze żadnej skrzynki znalezionej</b></p>';
						  }
						  else 
						  { 
			$sql = "SELECT COUNT(*) events_count 
							FROM cache_logs 
							WHERE user_id=$user_id AND type=7 AND deleted=0";
			
			if( $odp = mysql_query($sql) )
				$events_count = mysql_result($odp,0);
			else 
				$events_count = 0;
			$days_since_first_find = @mysql_result(@mysql_query("SELECT datediff(now(), date) as old FROM cache_logs WHERE deleted=0 AND user_id = $user_id AND type=1 ORDER BY date LIMIT 1"),0);					   
//			$rsfc1=sql("SELECT cache_logs.cache_id cache_id,  DATE_FORMAT(cache_logs.date_created,'%Y-%m-%d') data, caches.wp_oc cache_wp FROM cache_logs, caches WHERE caches.cache_id=cache_logs.cache_id AND cache_logs.type='1' AND cache_logs.user_id=&1 AND cache_logs.deleted='0' ORDER BY cache_logs.date_created ASC LIMIT 1",$user_id);
//			$rfc1 = mysql_fetch_array($rsfc1);
			$rsfc2=sql("SELECT cache_logs.cache_id cache_id,  DATE_FORMAT(cache_logs.date,'%Y-%m-%d') data, caches.wp_oc cache_wp FROM cache_logs, caches WHERE caches.cache_id=cache_logs.cache_id AND cache_logs.type='1' AND cache_logs.user_id=&1 AND cache_logs.deleted='0' ORDER BY cache_logs.date DESC LIMIT 1",$user_id);
			$rfc2 = mysql_fetch_array($rsfc2);
	        $rsc=sql("SELECT COUNT(*) number FROM cache_logs WHERE type=1 AND cache_logs.deleted='0' AND user_id=&1 GROUP BY YEAR(`date`), MONTH(`date`), DAY(`date`) ORDER BY number DESC LIMIT 1",$user_id);
			$rc = sql_fetch_array($rsc);
			$rsncd= sql ("SELECT COUNT(*) FROM cache_logs WHERE type=1 AND cache_logs.deleted='0' AND user_id=&1 GROUP BY YEAR(`date`), MONTH(`date`), DAY(`date`)",$user_id);
			$num_rows = mysql_num_rows($rsncd);
			$sql = "SELECT COUNT(*) founds_count 
					FROM cache_logs 
					WHERE user_id=$user_id AND type=1 AND deleted=0";
			if( $odp = mysql_query($sql) )
			$found = mysql_result($odp,0);
			else 
			$found = 0;
			$aver1= round(($found/$ddays['diff']), 2);
			$aver2= round(($found/$num_rows), 2);
			$content .= '<p><span class="content-title-noshade txt-blue08" >Liczba znalezionych skrzynek:</span><strong> ' . $found . '</strong>';
						if ($found == 0) 
				{$content .= '</p>';}
				else
			{ $content .='&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> (<a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bycreated&amp;finderid=' .$user_id . '&amp;searchbyfinder=&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0">'.tr('show').'</a>)</p>';}
			
			$content .= '<p><span class="content-title-noshade txt-blue08" >Liczba nie znalezionych skrzynek:</span> <strong>' . $user_record['notfounds_count'] . '</strong>';
		
			if ($user_record['notfounds_count'] == 0) 
				{$content .= '</p>';}
				else		
			{ $content .='&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> (<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=bycreated&amp;finderid=' .$user_id . '&amp;searchbyfinder=&amp;logtype=2&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0">'.tr('show').'</a>)</p>';}
			$content .= '<p><span class="content-title-noshade txt-blue08" >Liczba komentarzy w logach:</span> <strong>' . $user_record['log_notes_count'] . '</strong></p>';
			$content .= '<p><span class="content-title-noshade txt-blue08" >Liczba uczestnictw w spotkaniach:</span> <strong>' . $events_count . '</strong>';
			if ($events_count == 0) 
				{$content .= '</p>';}
				else
			{ $content .= '&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> (<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=bycreated&amp;finderid=' . $user_id . '&amp;searchbyfinder=&amp;logtype=7&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0">'.tr('show').'</a>)</p>';}

			$recomendf =  sqlValue("SELECT COUNT(*) FROM `cache_rating` WHERE `user_id`='" . sql_escape($_REQUEST['userid']) . "'", 0);
			$content .= '<p><span class="content-title-noshade txt-blue08" >Liczba przyznanych rekomendacji:</span> <strong>' . $recomendf . '</strong>';
		
			if ($recomendf == 0) 
				{$content .= '</p>';}
				else					
			{$content .='&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> (<a href="usertops.php?userid=' . $user_id . '">'.tr('show').'</a>)</p>';}

			$content .= '<p><span class="content-title-noshade txt-blue08" >Liczba dni "keszowania":</span> <strong>' . $num_rows . '</strong> z całkowitej ilości dni: <strong>' . $ddays['diff'] . '</strong></p>';
			$content .= '<p><span class="content-title-noshade txt-blue08" >Średnio skrzynek/dzień:</span> <strong>' . $aver2 . '</strong>/dzień keszowania i <strong>' . $aver1 . '</strong>/dzień</p>';
			$content .= '<p><span class="content-title-noshade txt-blue08" >Najwięcej skrzynek/dzień:</span> <strong>' . $rc['number'] . '</strong></p>';
//			$content .= '<p><span class="content-title-noshade txt-blue08" >Pierwsza znaleziona skrzynka:</span>&nbsp;&nbsp;<strong><a href="viewcache.php?cacheid=' . $rfc1['cache_id'] . '">' . $rfc1['cache_wp'] . '</a>&nbsp;&nbsp;</strong>(' . $rfc1['data'] . ')</p>';
			$content .= '<p><span class="content-title-noshade txt-blue08" >Ostatnia znaleziona skrzynka:</span>&nbsp;&nbsp;<strong><a href="viewcache.php?cacheid=' . $rfc2['cache_id'] . '">' . $rfc2['cache_wp'] . '</a>&nbsp;&nbsp;</strong>(' . $rfc2['data'] . ')</p>';	
//			$content .= '<p><span class="content-title-noshade txt-blue08" >Milestones czyli "kamienie milowe":</span></p><ul class="linklist-indent">';			
			$content .= '<br /><table style="border-collapse: collapse;" width="250" border="1"><tr><td colspan="3" align="center" bgcolor="#DBE6F1"><b> Milestones "kamienie milowe"</b></td> </tr><tr><td bgcolor="#EEEDF9"><b> Nr </b></td> <td bgcolor="#EEEDF9"><b> Data </b></td> <td bgcolor="#EEEDF9"><b> Geocache</b> </td> </tr>';
			$rsms=sql("SELECT cache_logs.cache_id cache_id,  DATE_FORMAT(cache_logs.date,'%Y-%m-%d') data, caches.wp_oc cache_wp FROM cache_logs, caches WHERE caches.cache_id=cache_logs.cache_id AND cache_logs.type='1' AND cache_logs.user_id=&1 AND cache_logs.deleted='0' ORDER BY cache_logs.date ASC",$user_id);
			if (mysql_num_rows($rsms) <= 100) {
			for ($i = 0; $i < mysql_num_rows($rsms); $i+=10)
				{		
				$ii=$i;
				$is=$i-1;
				if ($i==0) {$ii=1; $is=0;}
				mysql_data_seek($rsms, $is);	
				$rms = mysql_fetch_array($rsms);
				$content .= '<tr> <td>' . $ii . '</td><td>'  . $rms['data'] . '</td><td><a href="viewcache.php?cacheid=' . $rms['cache_id'] . '">' . $rms['cache_wp'] . '</a></td></tr>';
				}
			}
			if (mysql_num_rows($rsms) > 100) {
			for ($i = 0; $i < mysql_num_rows($rsms); $i+=100)
			{		
			$ii=$i;
				$is=$i-1;
				if ($i==0) {$ii=1; $is=0;}
				mysql_data_seek($rsms, $is);	

			$rms = mysql_fetch_array($rsms);
			$content .= '<tr><td>' . $ii . '</td><td>' . $rms['data'] . '</td><td><a href="viewcache.php?cacheid=' . $rms['cache_id'] . '">' . $rms['cache_wp'] . '</a></td></tr>';
			}}
			$content .='</table>';
			mysql_free_result($rsms);
//			mysql_free_result($rsfc1);			
			mysql_free_result($rsncd);
			mysql_free_result($rsc);
			mysql_free_result($rsfc2);
			
//------------ begin owner section			
			if ($user_id == $usr['userid']) 
			{
			
						$rs_logs = sql("
					SELECT `cache_logs`.`cache_id` `cache_id`, `cache_logs`.`type` `type`, `cache_logs`.`date` `date`, `caches`.`name` `name`,
						`log_types`.`icon_small`, `log_types_text`.`text_combo`
					FROM `cache_logs`, `caches`, `log_types`, `log_types_text`
					WHERE `cache_logs`.`user_id`='&1'
					AND `cache_logs`.`cache_id`=`caches`.`cache_id` 
					AND `cache_logs`.`deleted`=0 
					AND `log_types`.`id`=`cache_logs`.`type`
					AND `log_types_text`.`log_types_id`=`log_types`.`id` AND `log_types_text`.`lang`='&2'
					ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`date_created` DESC
					LIMIT 5", $usr['userid'], $lang);

			if (mysql_num_rows($rs_logs) != 0) {
			
				$content .= '<p>&nbsp;</p><p><span class="content-title-noshade txt-blue08" >Moje najnowsze wpisy do logów:</span></p><p><ul>';
				for ($i = 0; $i < mysql_num_rows($rs_logs); $i++)
					{
					$record_logs = sql_fetch_array($rs_logs);

					$tmp_log = $log_line;
					$tmp_log = mb_ereg_replace('{logimage}', icon_log_type($record_logs['icon_small'], $record_logs['text_combo']), $tmp_log);
					$tmp_log = mb_ereg_replace('{logtype}', $record_logs['text_combo'], $tmp_log);
					$tmp_log = mb_ereg_replace('{date}', strftime($dateformat , strtotime($record_logs['date'])), $tmp_log);
					$tmp_log = mb_ereg_replace('{cachename}', htmlspecialchars($record_logs['name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
					$tmp_log = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($record_logs['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_log);

					$content .= "\n" . $tmp_log;
					}
					$content .= '</ul></p>';
					mysql_free_result($rs_logs);
				}


			}
// ----------- end owner section			
			$content .='<p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/event.png" class="icon32" alt="Caches Find" title="Caches Find" />&nbsp;&nbsp;&nbsp;Odwiedzone województwa podczas poszukiwań (w przygotowaniu)</p></div><p><img src="images/PLmapa250.jpg" alt="" /></p>';
						  

			}
//------------ end find section			
			mysql_free_result($rsGeneralStat);
			tpl_set_var('content',$content);
	}
}
	tpl_BuildTemplate();
?>
