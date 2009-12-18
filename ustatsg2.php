<?php

/**************************************************************

 Statictics of users OC PL 
 Graphs statictics created caches by users


*/


//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	
	//Preprocessing
	if ($error == false)
	{
		// check for old-style parameters
		if (isset($_REQUEST['userid']))
		{
			$user_id = $_REQUEST['userid'];
		
		}

	$content="";
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
			$content .= '<table style="border-collapse: collapse" border="0" width="750"><tr><td width="150"><b><a href="users-stats.php?userid=' . $user_id . '">Statystyka ogolna</a></b></td> <td width="200"><b><a href="ustatsg1.php?userid=' . $user_id . '">Wykresy skrzynek zalozonych</a></b></td> <td width="200"  bgcolor="#C6E2FF"><b> Wykresy skrzynek znalezionych</b></td><td width="300" ></td> </tr></table><br /><br />';
			$content .='<br><br><table style="border-collapse: collapse" border="0" width="500"><tr><td colspan="4" bgcolor="#C6E2FF"><b>Statystyka skrzynek znalezionych </b></td></tr></table><br /><br />';	

		
			mysql_free_result($rsGeneralStat);
	$rsCachesFindYear = sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` FROM `cache_logs` WHERE type=1 AND user_id=&1 GROUP BY YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC",$user_id);

  				if ($rsCachesFindYear !== false) {

		$content .= '<tr><td><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&t=cfy"  border="0" alt="" /></td></tr>';					

			}


$rsCachesFindMonth= sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` , MONTH(`date_created`) `month` FROM `cache_logs` WHERE type=1 AND user_id=&1 GROUP BY MONTH(`date_created`) , YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC",$user_id);

 				if ($rsCachesFindMonth !== false){
				while ($rcfm = mysql_fetch_array($rsCachesFindYear)){

		$content .= '<tr><td><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&t=cfm' . $rcfm['year'] . '"  border="0" alt="" /></td></tr>';					
			}
		}
				mysql_free_result($rsCachesFindMonth);

			mysql_free_result($rsCachesFindYear);

			tpl_set_var('content',$content);
	$tplname = 'users-stats';
}
	tpl_BuildTemplate();
?>
