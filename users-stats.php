<?php
//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
		global $stat_menu;
	
	//Preprocessing
	if ($error == false)
	{
		// check for old-style parameters
		if (isset($_REQUEST['userid']))
		{
			$user_id = $_REQUEST['userid'];
		
		}
				$tplname = 'users-stats';
				$stat_menu = array(
					'title' => tr('Statystyka'),
					'menustring' => tr('Statystyka'),
					'siteid' => 'statlisting',
					'navicolor' => '#E8DDE4',
					'visible' => false,
					'filename' => 'users-stats.php?userid='.$user_id,
					'submenu' => array(
						array(
							'title' => tr('Skrzynki założone'),
							'menustring' => tr('Wykresy skrzynek założonych'),
							'visible' => true,
							'filename' => 'ustatsg1.php?userid='.$user_id,
							'newwindow' => false,
							'siteid' => 'createstat'
						),
						array(
							'title' => tr('Skrzynki znalezione'),
							'menustring' => tr('Wykresy skrzynek znalezionych'),
							'visible' => true,
							'filename' => 'ustatsg2.php?userid='.$user_id,
							'newwindow' => false,
							'siteid' => 'findstat'
						)
					)
				);

	$content="";
	// calculate diif days between date of register on OC  to current date
	  $rdd=sql("select TO_DAYS(NOW()) - TO_DAYS(`date_created`) `diff` from `user` WHERE user_id=&1 ",$user_id);
	  $ddays = mysql_fetch_array($rdd);
	  mysql_free_result($rdd);
	  // calculate days caching
	 // sql ("SELECT COUNT(*) FROM cache_logs WHERE type=1 AND user_id=&1 GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`)",$user_id);

	$rsGeneralStat =sql("SELECT hidden_count, founds_count, log_notes_count, notfounds_count, username FROM `user` WHERE user_id=&1 ",$user_id);

			$user_record = sql_fetch_array($rsGeneralStat);
			tpl_set_var('username',$user_record['username']);
			$content .= '<p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;&nbsp;Statystyka liczbowa skrzynek założonych</p></div>';			
		if ($user_record['hidden_count'] == 0) {
			$content .= '<p> <b>Nie ma jeszcze żadnej skrzynki ukrytej<b></p>';
						  }
						  else 
						  { 
			$rscc1=sql("SELECT cache_id, wp_oc, DATE_FORMAT(date_hidden,'%Y-%m-%d') data FROM caches WHERE `status` != 5 AND user_id=&1 GROUP BY YEAR(`date_hidden`), MONTH(`date_hidden`), DAY(`date_hidden`) ORDER BY YEAR(`date_hidden`) ASC, MONTH(`date_hidden`) ASC, DAY(`date_hidden`) ASC LIMIT 1",$user_id);
			$rcc1 = mysql_fetch_array($rscc1);
			$rscc2=sql("SELECT cache_id, wp_oc, DATE_FORMAT(date_hidden,'%Y-%m-%d') data FROM caches WHERE `status` != 5 AND user_id=&1 GROUP BY YEAR(`date_hidden`), MONTH(`date_hidden`), DAY(`date_hidden`) ORDER BY YEAR(`date_hidden`) DESC, MONTH(`date_hidden`) DESC, DAY(`date_hidden`) DESC LIMIT 1",$user_id);
			$rcc2 = mysql_fetch_array($rscc2);
			$content .= '<p>Liczba wszystkich założonych skrzynek: <strong>' . $user_record['hidden_count'] . '</strong></p><p>Liczba dni "keszowania": <strong></strong></p><p>Średnio skrzynek/dzień: <strong></strong></p><p>Najwięcej skrzynek/dzień: <strong></strong></p><p>Pierwsza założona skrzynka:&nbsp;&nbsp;<strong><a href="viewcache.php?cacheid=' . $rcc1['cache_id'] . '">' . $rcc1['wp_oc'] . '</a>&nbsp;&nbsp;' . $rcc1['data'] . '</strong></p><p>Najnowsza założona skrzynka:&nbsp;&nbsp;<strong><a href="viewcache.php?cacheid=' . $rcc2['cache_id'] . '">' . $rcc2['wp_oc'] . '</a>&nbsp;&nbsp;' . $rcc2['data'] . '</strong></p>';	
			mysql_free_result($rscc1);
			mysql_free_result($rscc2);
			}
		$content .= '<p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;&nbsp;Statystyka liczbowa skrzynek znalezionych</p></div>';
		if ($user_record['founds_count'] == 0) {
			$content .= '<p> <b>Nie ma jeszcze żadnej skrzynki znalezionej</b></p>';
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
			$rsfc1=sql("SELECT cache_id,  DATE_FORMAT(date_created,'%Y-%m-%d') data FROM cache_logs WHERE type='1' AND user_id=&1 AND cache_logs.deleted='0' GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC, DAY(`date_created`) ASC LIMIT 1",$user_id);
			$rfc1 = mysql_fetch_array($rsfc1);
			$rsfc2=sql("SELECT cache_id,  DATE_FORMAT(date_created,'%Y-%m-%d') data FROM cache_logs WHERE type='1' AND user_id=&1 AND deleted='0' GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`) ORDER BY YEAR(`date_created`) DESC, MONTH(`date_created`) DESC, DAY(`date_created`) DESC LIMIT 1",$user_id);
			$rfc2 = mysql_fetch_array($rsfc2);

			$content .= '<p>Liczba znalezionych skrzynek: <strong>' . $user_record['founds_count'] . '</strong></p><p>Liczba nie znalezionych skrzynek: <strong>' . $user_record['notfounds_count'] . '</strong></p><p>Liczba komentarzy w logach: <strong>' . $user_record['log_notes_count'] . '</strong></p><p>Liczba uczestnictw w spotkaniach: <strong>' . $events_count . '</strong></p><p>Liczba dni "keszowania": <strong></strong></p><p>Średnio skrzynek/dzień: <strong></strong></p><p>Najwięcej skrzynek/dzień: <strong></strong></p><p>Pierwsza znaleziona skrzynka:&nbsp;&nbsp;<strong><a href="viewcache.php?cacheid=' . $rfc1['cache_id'] . '">Skrzynka</a>&nbsp;&nbsp;' . $rfc1['data'] . '</strong></p><p>Ostatnia znaleziona skrzynka:&nbsp;&nbsp;<strong><a href="viewcache.php?cacheid=' . $rfc2['cache_id'] . '">Skrzynka</a>&nbsp;&nbsp;' . $rfc2['data'] . '</strong></p>';	
			mysql_free_result($rsfc1);
			mysql_free_result($rsfc2);
			$content .='<p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;&nbsp;Odwiedzone województwa podczas poszukiwań (w przygotowaniu)</p></div><p><img src="images/PLmapa250.jpg" alt="" /></p>';
						  

						  }
			
			mysql_free_result($rsGeneralStat);
			tpl_set_var('content',$content);
	$tplname = 'users-stats';
}
	tpl_BuildTemplate();
?>
