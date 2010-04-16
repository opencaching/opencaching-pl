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
			//Create Cache Note
		if (isset($_REQUEST['cacheid']))
			{
			$cache_id = $_REQUEST['cacheid'];
			}
			$cache_rs = sql("SELECT `user_id`, `name` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
			if (mysql_num_rows($cache_rs) == 1)
			{
				$cache_record = sql_fetch_array($cache_rs);
			
			if ($cache_record['user_id'] == $usr['userid'] || $usr['admin'])
				{
			$tplname = 'cache_notes';
			
			
			tpl_set_var("cache_name", $cache_record['name']);		
				}
			}
			mysql_free_result($cache_rs);

			//Edit cache Notes
			if (isset($_REQUEST['noteid']))
			{
			$note_id = $_REQUEST['noteid'];			
			}
			
			$note_rs = sql("SELECT `note_id`, `cache_id`, `desc`,FROM `cache_notes` WHERE `note_id`='&1'", $note_id);
			if (mysql_num_rows($note_rs) == 1)
			{	
			$note_record = sql_fetch_array($wp_rs);
			$cache_id = $wp_record['cache_id'];
			}
			$cache_rs = sql("SELECT `user_id`, `name`, `type`,  `longitude`, `latitude`,  `status`, `logpw` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
			if (mysql_num_rows($cache_rs) == 1)
			{
				$cache_record = sql_fetch_array($cache_rs);
			
			if ($cache_record['user_id'] == $usr['userid'] || $usr['admin'])
				{
			$tplname = 'cache_note';


			
			tpl_set_var("desc", htmlspecialchars($wp_record['desc']));
			tpl_set_var("type", htmlspecialchars($wp_record['type']));
			tpl_set_var("stage", htmlspecialchars($wp_record['stage']));
			tpl_set_var("status", htmlspecialchars($wp_record['status']));
			tpl_set_var("wpid", htmlspecialchars($wp_record['wp_id']));
			tpl_set_var("cacheid", htmlspecialchars($wp_record['cache_id']));
			tpl_set_var("cache_name",  htmlspecialchars($cache_record['name']));	
				}
			mysql_free_result($cache_rs);
			mysql_free_result($wp_rs);
			}	
		
	


			}
		
	}
	tpl_BuildTemplate();
?>
