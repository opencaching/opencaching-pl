<?php

/**************************************************************

 Statictics of users OC PL 
 Graphs statictics created caches by users


*/


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
				$tplname = 'createstat';

	$stat_menu = array(
					'title' => 'Statystyka',
					'menustring' => 'Statystyka',
					'siteid' => 'statlisting',
					'visible' => false,
					'filename' => 'ustatsg2.php?userid='.$user_id,
					'submenu' => array(
									array(
							'title' => 'Statystyka ogolna',
							'menustring' => 'Statystyka ogólna',
							'visible' => true,
							'filename' => 'users-stats.php?userid='.$user_id,
							'newwindow' => false,
							'siteid' => 'general_stat'
						),
						array(
							'title' => 'Skrzynki zalozone',
							'menustring' => 'Wyrkesy skrzynek założonych',
							'visible' => true,
							'filename' => 'ustatsg1.php?userid='.$user_id,
							'newwindow' => false,
							'siteid' => 'createstat'
						)
					)
				);

	$content="";
	
	$rsGeneralStat =sql("SELECT hidden_count, founds_count, log_notes_count, notfounds_count, username FROM `user` WHERE user_id=&1 ",$user_id);

			$user_record = sql_fetch_array($rsGeneralStat);
			tpl_set_var('username',$user_record['username']);		
		if ($user_record['founds_count'] == 0) {
			$content .= '<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;&nbsp;Wykresy statystyk dla skrzynek znalezionych</p></div><br /><br /><p> <b>Nie ma jeszcze żadnej znalezionej skrzynki</b></p>';
						  }
						  else 
						  { 

	// calculate diif days between date of register on OC  to current date
	  $rdd=sql("select TO_DAYS(NOW()) - TO_DAYS(`date_created`) `diff` from `user` WHERE user_id=&1 ",$user_id);
	  $ddays = mysql_fetch_array($rdd);
	  mysql_free_result($rdd);
	  // calculate days caching
	 // sql ("SELECT COUNT(*) FROM cache_logs WHERE type=1 AND user_id=&1 GROUP BY GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`)",$user_id);

	$rsGeneralStat =sql("SELECT hidden_count, founds_count, log_notes_count, username FROM `user` WHERE user_id=&1 ",$user_id);
	if ($rsGeneralStat !== false){
			$user_record = sql_fetch_array($rsGeneralStat);

			tpl_set_var('username',$user_record['username']);
}
				$content .='<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;&nbsp;Wykresy statystyk dla skrzynek znalezionych</p></div><br />';	

		
			mysql_free_result($rsGeneralStat);
	$rsCachesFindYear = sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` FROM `cache_logs` WHERE type=1 AND user_id=&1 GROUP BY YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC",$user_id);

  				if ($rsCachesFindYear !== false) {

		$content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&t=cfy"  border="0" alt="" /></p>';					

			}


$rsCachesFindMonth= sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` , MONTH(`date_created`) `month` FROM `cache_logs` WHERE type=1 AND user_id=&1 GROUP BY MONTH(`date_created`) , YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC",$user_id);

 				if ($rsCachesFindMonth !== false){
				while ($rcfm = mysql_fetch_array($rsCachesFindYear)){

		$content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&t=cfm' . $rcfm['year'] . '"  border="0" alt="" /></p>';					
			}
		}
				mysql_free_result($rsCachesFindMonth);


			mysql_free_result($rsCachesFindYear);
}
			tpl_set_var('content',$content);
	$tplname = 'users-stats';
}
	tpl_BuildTemplate();
?>
