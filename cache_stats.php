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
			//user logged in?
		if ($usr == false)
		{
		    $target = urlencode(tpl_get_current_page());
		    tpl_redirect('login.php?target='.$target);
		}
		else
		{

		// check for old-style parameters
		if (isset($_REQUEST['cacheid']))
		{
			$cache_id = $_REQUEST['cacheid'];
		
		}
		
	$tplname = 'cache_stats';

	$content="";
	$cachename=sqlValue("SELECT name FROM caches WHERE `cache_id`=$cache_id", 0);
	tpl_set_var('cachename',$cachename);
	$rsGeneralStat =sql("SELECT count(*) count FROM `cache_logs` WHERE cache_logs.deleted=0 AND type=1 AND cache_id=&1 ",$cache_id);

			$cache_record = sql_fetch_array($rsGeneralStat);	
		if ($cache_record['count'] == 0) {
//			$content .= '<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="Caches Find" title="Caches Find" />&nbsp;&nbsp;&nbsp;Wykresy statystyk dla skrzynki: ' . $cachename . '</p></div><br /><br /><p> <b>Ta skrzynka nie była jeszcze znaleziona</b></p>';
						  }
						  else 
						  {

//				$content .='<p>&nbsp;</p><p>&nbsp;</p><div style="overflow: hidden; width: 400px; margin: 0px 0px 10px 0px; padding: 0px 0px 2px 0px; border-bottom: solid 3px rgb(88,144,168); color: rgb(88,144,168); font-weight: bold;  background-color: rgb(219,230,241);"><p style="margin: 0px; padding: 0px; color: rgb(88,144,168); font-weight: bold; font-size: 60%;">&nbsp;<img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="Caches Find" title="Caches Find" />&nbsp;&nbsp;&nbsp;Wykresy statystyk dla skrzynki: ' . $cachename . '</p></div><br />';	
				$content .= '<center><p><img src="graphs/PieGraphcstat.php?cacheid=' . $cache_id . '&t=cs"  border="0" alt="" /></p>';	

		$year=date("Y");

		$content .= '<p><img src="graphs/BarGraphcstat.php?cacheid=' . $cache_id . '&t=csm' . $year . '"  border="0" alt="" /></p>';								
		
		$content .= '<p><img src="graphs/BarGraphcstat.php?cacheid=' . $cache_id . '&t=csy"  border="0" alt="" /></p></center>';					
			mysql_free_result($rsGeneralStat);
		}
			tpl_set_var('content',$content);
	}
}
	tpl_BuildTemplate();
?>
