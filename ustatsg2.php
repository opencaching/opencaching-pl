<?php
/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*   
	*  UTF-8 ąść
	***************************************************************************/

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
				$tplname = 'ustat';

	$stat_menu = array(
					'title' => 'Statictics',
					'menustring' => 'Statistics',
					'siteid' => 'statlisting',
					'visible' => false,
					'filename' => 'ustatsg2.php?userid='.$user_id,
					'submenu' => array(
									array(
							'title' => tr('generla_stat'),
							'menustring' => tr('general_stat'),
							'visible' => true,
							'filename' => 'viewprofile.php?userid='.$user_id,
							'newwindow' => false,
							'siteid' => 'general_stat',
							'icon' => 'images/actions/stat'
						),
						array(
							'title' => tr('graph_created'),
							'menustring' => tr('graph_created'),
							'visible' => true,
							'filename' => 'ustatsg1.php?userid='.$user_id,
							'newwindow' => false,
							'siteid' => 'createstat',
							'icon' => 'images/actions/stat'
						)
					)
				);

	$content="";
	
	$rsGeneralStat =sql("SELECT  hidden_count, founds_count, log_notes_count, notfounds_count, username FROM `user` WHERE user_id=&1 ",$user_id);

			$user_record = sql_fetch_array($rsGeneralStat);
			tpl_set_var('username',$user_record['username']);		
		if ($user_record['founds_count'] == 0) {
			$content .= '<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="Caches Find" title="Caches Find" />&nbsp;&nbsp;&nbsp;'.tr("graph_find").'</p></div><br /><br /><p> <b>'.tr("there_is_no_caches_found").'</b></p>';
						  }
						  else 
						  { 

	// calculate diif days between date of register on OC  to current date
	  $rdd=sql("select TO_DAYS(NOW()) - TO_DAYS(`date_created`) `diff` from `user` WHERE user_id=&1 ",$user_id);
	  $ddays = mysql_fetch_array($rdd);
	  mysql_free_result($rdd);
	  // calculate days caching
	 // sql ("SELECT COUNT(*) FROM cache_logs WHERE type=1 AND user_id=&1 GROUP BY GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`)",$user_id);

	$rsGeneralStat =sql("SELECT YEAR(`date_created`) usertime,hidden_count, founds_count, log_notes_count, username FROM `user` WHERE user_id=&1 ",$user_id);
	if ($rsGeneralStat !== false){
			$user_record = sql_fetch_array($rsGeneralStat);

			tpl_set_var('username',$user_record['username']);
}

$rsCachesFindYear = sql("SELECT COUNT(`caches`.`type`) `count`, `cache_type`.`&2` AS `type`, `cache_type`.`color` AS `color` FROM `cache_logs`, caches INNER JOIN `cache_type` ON (`caches`.`type`=`cache_type`.`id`) WHERE cache_logs.`deleted`=0 AND cache_logs.user_id=&1 AND cache_logs.`type`='1' AND cache_logs.`cache_id` = caches.cache_id  GROUP BY `caches`.`type` ORDER BY `count` DESC",$user_id,$lang_db);

  if ($rsCachesFindYear !== false) 
	{
		// Get data 
		$rsTypes = sql('SELECT COUNT(`caches`.`type`) `count`, `cache_type`.`&1` AS `type`, `cache_type`.`color` FROM `caches` INNER JOIN `cache_type` ON (`caches`.`type`=`cache_type`.`id`) WHERE `status`=1 GROUP BY `caches`.`type` ORDER BY `count` DESC',$lang_db);
		
		$yData = array();
		$xData = array();
		$colors = array();
		$url = "http://chart.apis.google.com/chart?chs=550x200&chd=t:";
		$sum = 0;
		while ($rTypes = mysql_fetch_array($rsCachesFindYear))
		{
			$yData[] = ' (' . $rTypes['count'] . ') ' . $rTypes['type'];
			$xData[] = $rTypes['count'];
			$colors[] = substr($rTypes['color'], 1);
			$sum += $rTypes['count'];
		}
		mysql_free_result($rsTypes);
		foreach( $xData as $count )
		{
			$url .= $count.",";
		}
		
		$url = substr($url, 0, -1);
		$url .= "&cht=p3&chl=";
		
		foreach( $yData as $label )
		{
			$url .= urlencode($label)."|";
		}
		$url = substr($url, 0, -1);
		
		$url .= "&chco=";
		foreach( $colors as $color )
		{
			$url .= urlencode($color).",";
		}
		$url = substr($url, 0, -1);
		
	}
	mysql_free_result($rsCachesFindYear);
	
				$content .='<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="Caches Find" title="Caches Find" />&nbsp;&nbsp;&nbsp;'.tr("graph_find").'</p></div><br />';	
				$content .= '<p><img src="'.$url.'"  border="0" alt="" width="500" height="300" /></p>';	

$year=date("Y");
$rsCachesFindMonth= sql("SELECT COUNT(*) `count`,YEAR(`date`) `year` , MONTH(`date`) `month` FROM `cache_logs` WHERE type=1 AND cache_logs.deleted='0' AND user_id=&1 AND YEAR(`date`)=&2 GROUP BY MONTH(`date`) , YEAR(`date`) ORDER BY YEAR(`date`) ASC, MONTH(`date`) ASC",$user_id,$year);

 				if ($rsCachesFindMonth !== false){
	//			while ($rcfm = mysql_fetch_array($rsCachesFindYear)){

		$content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=cfm' . $year . '"  border="0" alt="" width="500" height="200" /></p>';		

		if ($user_record['usertime'] != $year){
		$yearr = $year -1;	
		$content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=cfm' . $yearr . '"  border="0" alt="" width="500" height="200" /></p>';					
				}
//			}
		}
		mysql_free_result($rsGeneralStat);
		mysql_free_result($rsCachesFindMonth);


			$rsCachesFindYear = sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` FROM `cache_logs` WHERE type=1 AND user_id=&1 GROUP BY YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC",$user_id);

  				if ($rsCachesFindYear !== false) {

		$content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=cfy"  border="0" alt="" width="500" height="200" /></p>';					

			}




			mysql_free_result($rsCachesFindYear);
		}
			tpl_set_var('content',$content);
	}
}
	tpl_BuildTemplate();
?>
