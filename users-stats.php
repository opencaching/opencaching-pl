<?php
//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
		global $stat_menu;
	
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
		
		}
				$tplname = 'users-stats';
				$stat_menu = array(
					'title' => tr('Statictics'),
					'menustring' => tr('Statictics'),
					'siteid' => 'statlisting',
					'navicolor' => '#E8DDE4',
					'visible' => false,
					'filename' => 'users-stats.php?userid='.$user_id,
					'submenu' => array(
						array(
							'title' => tr('graph_created'),
							'menustring' => tr('graph_created'),
							'visible' => true,
							'filename' => 'ustatsg1.php?userid='.$user_id,
							'newwindow' => false,
							'siteid' => 'createstat'
						),
						array(
							'title' => tr('graph_find'),
							'menustring' => tr('graph_find'),
							'visible' => true,
							'filename' => 'ustatsg2.php?userid='.$user_id,
							'newwindow' => false,
							'siteid' => 'findstat'
						)
					)
				);

	$content="";

	  $rdd=sql("select TO_DAYS(NOW()) - TO_DAYS(`date_created`) `diff` from `user` WHERE user_id=&1 ",$user_id);
	  $ddays = mysql_fetch_array($rdd);
	  mysql_free_result($rdd);

	$rsGeneralStat =sql("SELECT hidden_count, founds_count, log_notes_count, notfounds_count, username FROM `user` WHERE user_id=&1 ",$user_id);

			$user_record = sql_fetch_array($rsGeneralStat);
			tpl_set_var('username',$user_record['username']);
			$content .= '<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;&nbsp;Statystyka liczbowa skrzynek założonych</p></div><br />';			
		if ($user_record['hidden_count'] == 0) {
			$content .= '<br /><p> <b>Nie ma jeszcze żadnej założonej skrzynki</b></p>';
						  }
						  else 
						  { 
			$rscc1=sql("SELECT cache_id, wp_oc, DATE_FORMAT(date_created,'%Y-%m-%d') data FROM caches WHERE `status` != 5 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC, DAY(`date_created`) ASC LIMIT 1",$user_id);
			$rcc1 = mysql_fetch_array($rscc1);
			$rscc2=sql("SELECT cache_id, wp_oc, DATE_FORMAT(date_created,'%Y-%m-%d') data FROM caches WHERE `status` != 5 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`) ORDER BY YEAR(`date_created`) DESC, MONTH(`date_created`) DESC, DAY(`date_created`) DESC LIMIT 1",$user_id);
			$rcc2 = mysql_fetch_array($rscc2);
			$rsc=sql("SELECT COUNT(*) number FROM caches WHERE status != 5 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`) ORDER BY number DESC LIMIT 1",$user_id);
			$rc = sql_fetch_array($rsc);
			$rsncd= sql ("SELECT COUNT(*) FROM caches WHERE `status` != 5 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`)",$user_id);
			$num_rows = mysql_num_rows($rsncd); 
			$aver1= round(($user_record['hidden_count']/$ddays['diff']), 2);
			$aver2= round(($user_record['hidden_count']/$num_rows), 2);			
			$content .= '<p><span style="color: rgb(88,144,168)">Liczba wszystkich założonych skrzynek: </span><strong>' . $user_record['hidden_count'] . '</strong></p>';
			$content .= '<p><span style="color: rgb(88,144,168)">Liczba dni "keszowania":</span> <strong>' . $num_rows . '</strong> z całkowitej ilości dni: <strong>' . $ddays['diff'] . '</strong></p>';
			$content .= '<p><span style="color: rgb(88,144,168)">Średnio skrzynek/dzień:</span> <strong>' . $aver2 . '</strong>/dzień keszowania i <strong>' . $aver1 . '</strong>/dzień</p>';
			$content .= '<p><span style="color: rgb(88,144,168)">Najwięcej skrzynek/dzień:</span> <strong>' . $rc['number'] . '</strong></p>';
			$content .= '<p><span style="color: rgb(88,144,168)">Pierwsza założona skrzynka:</span>&nbsp;&nbsp;<strong><a href="viewcache.php?cacheid=' . $rcc1['cache_id'] . '">' . $rcc1['wp_oc'] . '</a>&nbsp;&nbsp;</strong>(' . $rcc1['data'] . ')</p>';
			$content .= '<p><span style="color: rgb(88,144,168)">Najnowsza założona skrzynka:</span>&nbsp;&nbsp;<strong><a href="viewcache.php?cacheid=' . $rcc2['cache_id'] . '">' . $rcc2['wp_oc'] . '</a>&nbsp;&nbsp;</strong>(' . $rcc2['data'] . ')</p>';	
			mysql_free_result($rsncd);
			mysql_free_result($rsc);
			mysql_free_result($rscc1);
			mysql_free_result($rscc2);
			}
		$content .= '<p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;&nbsp;Statystyka liczbowa skrzynek znalezionych</p></div>';
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
			$rsfc1=sql("SELECT cache_logs.cache_id cache_id,  DATE_FORMAT(cache_logs.date_created,'%Y-%m-%d') data, caches.wp_oc cache_wp FROM cache_logs, caches WHERE caches.cache_id=cache_logs.cache_id AND cache_logs.type='1' AND cache_logs.user_id=&1 AND cache_logs.deleted='0' ORDER BY cache_logs.date_created ASC LIMIT 1",$user_id);
			$rfc1 = mysql_fetch_array($rsfc1);
			$rsfc2=sql("SELECT cache_logs.cache_id cache_id,  DATE_FORMAT(cache_logs.date_created,'%Y-%m-%d') data, caches.wp_oc cache_wp FROM cache_logs, caches WHERE caches.cache_id=cache_logs.cache_id AND cache_logs.type='1' AND cache_logs.user_id=&1 AND cache_logs.deleted='0' ORDER BY cache_logs.date_created DESC LIMIT 1",$user_id);
			$rfc2 = mysql_fetch_array($rsfc2);
	        $rsc=sql("SELECT COUNT(*) number FROM cache_logs WHERE type=1 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`) ORDER BY number DESC LIMIT 1",$user_id);
			$rc = sql_fetch_array($rsc);
			$rsncd= sql ("SELECT COUNT(*) FROM cache_logs WHERE type=1 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`)",$user_id);
			$num_rows = mysql_num_rows($rsncd);
			$aver1= round(($user_record['founds_count']/$ddays['diff']), 2);
			$aver2= round(($user_record['founds_count']/$num_rows), 2);
			$content .= '<p><span style="color: rgb(88,144,168)">Liczba znalezionych skrzynek:</span><strong> ' . $user_record['founds_count'] . '</strong></p>';
			$content .= '<p><span style="color: rgb(88,144,168)">Liczba nie znalezionych skrzynek:</span> <strong>' . $user_record['notfounds_count'] . '</strong></p>';
			$content .= '<p><span style="color: rgb(88,144,168)">Liczba komentarzy w logach:</span> <strong>' . $user_record['log_notes_count'] . '</strong></p>';
			$content .= '<p><span style="color: rgb(88,144,168)">Liczba uczestnictw w spotkaniach:</span> <strong>' . $events_count . '</strong></p>';
			$content .= '<p><span style="color: rgb(88,144,168)">Liczba przyznanych rekomendacji:</span> <strong>' . sqlValue("SELECT COUNT(*) FROM `cache_rating` WHERE `user_id`='" . sql_escape($_REQUEST['userid']) . "'", 0) . '</strong></p>';
			$content .= '<p><span style="color: rgb(88,144,168)">Liczba dni "keszowania":</span> <strong>' . $num_rows . '</strong> z całkowitej ilości dni: <strong>' . $ddays['diff'] . '</strong></p>';
			$content .= '<p><span style="color: rgb(88,144,168)">Średnio skrzynek/dzień:</span> <strong>' . $aver2 . '</strong>/dzień keszowania i <strong>' . $aver1 . '</strong>/dzień</p>';
			$content .= '<p><span style="color: rgb(88,144,168)">Najwięcej skrzynek/dzień:</span> <strong>' . $rc['number'] . '</strong></p>';
			$content .= '<p><span style="color: rgb(88,144,168)">Pierwsza znaleziona skrzynka:</span>&nbsp;&nbsp;<strong><a href="viewcache.php?cacheid=' . $rfc1['cache_id'] . '">' . $rfc1['cache_wp'] . '</a>&nbsp;&nbsp;</strong>(' . $rfc1['data'] . ')</p>';
			$content .= '<p><span style="color: rgb(88,144,168)">Ostatnia znaleziona skrzynka:</span>&nbsp;&nbsp;<strong><a href="viewcache.php?cacheid=' . $rfc2['cache_id'] . '">' . $rfc2['cache_wp'] . '</a>&nbsp;&nbsp;</strong>(' . $rfc2['data'] . ')</p>';	
			mysql_free_result($rsncd);
			mysql_free_result($rsc);
			mysql_free_result($rsfc1);
			mysql_free_result($rsfc2);
			$content .='<p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;&nbsp;Odwiedzone województwa podczas poszukiwań (w przygotowaniu)</p></div><p><img src="images/PLmapa250.jpg" alt="" /></p>';
						  

						  }
			
			mysql_free_result($rsGeneralStat);
			tpl_set_var('content',$content);
	$tplname = 'users-stats';
	}
}
	tpl_BuildTemplate();
?>
