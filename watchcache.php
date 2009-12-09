<?php
/***************************************************************************
																./watchcache.php
															-------------------
		begin                : July 25 2004
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

   Unicode Reminder メモ

	 add a watch to the watchlist
	
 ****************************************************************************/

	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');

	//Preprocessing
	if ($error == false)
	{
		$cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : '';
		$target = isset($_REQUEST['target']) ? $_REQUEST['target'] : 'mywatches.php';
		
		if ($usr !== false)
		{
			//add to caches
			$rs = mysql_query('SELECT watcher FROM caches WHERE cache_id=\'' . sql_escape($cache_id) . '\'');
			if (mysql_num_rows($rs) > 0)
			{
				$record = mysql_fetch_array($rs);
				sql('UPDATE caches SET watcher=\'' . ($record['watcher'] + 1) . '\' WHERE cache_id=\'' . sql_escape($cache_id) . '\'');

				//add watch
				sql('INSERT INTO `cache_watches` (`cache_id`, `user_id`, `last_executed`) VALUES (\'' . sql_escape($cache_id) . '\', \'' . sql_escape($usr['userid']) . '\', NOW())');

				//add to user
				$rs = sql('SELECT cache_watches FROM user WHERE user_id=\'' . sql_escape($usr['userid']) . '\'');
				$record = mysql_fetch_array($rs);
				sql('UPDATE user SET cache_watches=\'' . ($record['cache_watches'] + 1) . '\' WHERE user_id=\'' . sql_escape($usr['userid']) . '\'');
				
				tpl_redirect($target);
			}
		}
	}
	
	tpl_BuildTemplate();
?>
