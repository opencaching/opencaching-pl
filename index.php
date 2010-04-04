<?php
/***************************************************************************
																./index.php
															-------------------
		begin                : Mon June 14 2004
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
                                   				                                
	 Starting page of the OpenCaching website and template usage example
	
	 used template(s): start
	 parameter(s):     none
	
 ****************************************************************************/

	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	
	//Preprocessing
	if ($error == false)
	{
		//if( $_)
		//set here the template to process
		$tplname = 'start';
		// news
		require($stylepath . '/news.inc.php');


		$rs = sql('SELECT `news`.`date_posted` `date`, `news`.`content` `content` FROM `news` WHERE `news`.`display`=1 AND `news`.`topic`=2 ORDER BY `news`.`date_posted` DESC LIMIT 1');
		while ($r = sql_fetch_array($rs))
		{
			$post_date = strtotime($r['date']);
			$newsentry = $tpl_newstopic_header;
			$newsentry .= $tpl_newstopic_without_topics;
			
			$newsentry = mb_ereg_replace('{date}', fixPlMonth(htmlspecialchars(strftime("%d %B %Y", $post_date), ENT_COMPAT, 'UTF-8')), $newsentry);
			$newsentry = mb_ereg_replace('{topic}', htmlspecialchars($r['topic'], ENT_COMPAT, 'UTF-8'), $newsentry);
			$newsentry = mb_ereg_replace('{message}', $r['content'], $newsentry);
			
			$newscontent = $newsentry . "\n";
		}
		mysql_free_result($rs);
		tpl_set_var('news_one', $newscontent);
		$newscontent = '';
//		tpl_set_var('more_news',[{{more_news}}]);

		
		// aktuelle Cache und Logzahlen
		$rs = sql('SELECT COUNT(*) AS `hiddens` FROM `caches` WHERE `status`=1');
		$r = sql_fetch_array($rs);
		tpl_set_var('hiddens', $r['hiddens']);
		mysql_free_result($rs);
		
		$rs = sql('SELECT COUNT(*) AS `founds` FROM `cache_logs` WHERE (`type`=1 OR `type`=7) AND `deleted`=0');
		$r = sql_fetch_array($rs);
		tpl_set_var('founds', $r['founds']);
		mysql_free_result($rs);
		
		$rs = sql('SELECT COUNT(*) AS `users` FROM (SELECT DISTINCT `user_id` FROM `cache_logs` WHERE `deleted`=0 UNION DISTINCT SELECT DISTINCT `user_id` FROM `caches`) AS `t`');
		$r = sql_fetch_array($rs);
		tpl_set_var('users', $r['users']);
		mysql_free_result($rs);

		// here is the right place to set up template replacements
		// example: 
		// tpl_set_var('foo', 'myfooreplacement');
		// will replace {foo} in the templates
	}
	
	//make the template and send it out
	tpl_BuildTemplate(false);

	//not neccessary, call tpl_BuildTemplate with true as argument and the db will be closed there
	db_disconnect();
?>
