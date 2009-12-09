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
		
		// aktuelle Cache und Logzahlen
		$rs = sql('SELECT COUNT(*) AS `hiddens` FROM `caches` WHERE `status`=1');
		$r = sql_fetch_array($rs);
		tpl_set_var('hiddens', $r['hiddens']);
		mysql_free_result($rs);
		
		$rs = sql('SELECT COUNT(*) AS `founds` FROM `cache_logs` WHERE `type`=1 AND `deleted`=0');
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
		tpl_set_var('incomming_events',tr('incomming_events'));
		tpl_set_var('newest_caches',tr('newest_caches'));
		tpl_set_var('total_of_active_caches',tr('total_of_active_caches'));
		tpl_set_var('number_of_active_users',tr('number_of_active_users'));
		tpl_set_var('number_of_founds',tr('number_of_founds'));
		tpl_set_var('latest_news',tr('latest_news'));
		tpl_set_var('what_do_you_find',tr('what_do_you_find'));
		tpl_set_var('what_do_you_find_intro',tr('what_do_you_find_intro'));
		tpl_set_var('created_by',tr('created_by'));
	}
	
	//make the template and send it out
	tpl_BuildTemplate(false);

	//not neccessary, call tpl_BuildTemplate with true as argument and the db will be closed there
	db_disconnect();
?>
