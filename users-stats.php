<?php
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
	$rsCreateCachesYear= sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` FROM `caches` WHERE user_id=&1 GROUP BY YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC",$user_id);

	if ($rsCreateCachesYear !== false){
	$content .= '<tr><td><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&t=ccy" border="0" alt="" /></td></tr>';
	mysql_free_result($rsCreateCachesYear);
			
	}
	$rsCreateCachesMonth = sql("SELECT COUNT(*) `count`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `caches` WHERE user_id=&1 AND YEAR(`date_created`)=&2 GROUP BY MONTH(`date_created`), YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC",$user_id,$year);

 				if ($rsCreateCachesMonth !== false) {
		$content .= '<tr><td><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&t=ccm2009" border="0" alt="" /></td></tr>';

 			mysql_free_result($rsCreateCachesMonth);
		}



	$rsCachesFindYear = sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` FROM `cache_logs` WHERE type=1 AND user_id=&1 GROUP BY YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC",$user_id);

  				if ($rsCachesFindYear !== false) {

		$content .= '<tr><td><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&t=cfy"  border="0" alt="" /></td></tr>';					
				mysql_free_result($rsCachesFindYear);
			}

$rsCachesFindMonth= sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` , MONTH(`date_created`) `month` FROM `cache_logs` WHERE type=1 AND user_id=&1 AND YEAR(`date_created`)=&2 GROUP BY MONTH(`date_created`) , YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC",$user_id,$year);

 				if ($rsCachesFindMonth !== false){


		$content .= '<tr><td><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&t=cfm2009"  border="0" alt="" /></td></tr>';					
				mysql_free_result($rsCachesFindMonth);
		}



	tpl_set_var('content',$content);
	$tplname = 'users-stats';
}
	tpl_BuildTemplate();
?>
