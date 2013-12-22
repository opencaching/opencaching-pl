<?php
/***************************************************************************
																./mylist.php
															-------------------
		begin                : July 17 2004
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

	 display all watches of this user

 ****************************************************************************/

	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require_once __DIR__.'/lib/db.php';
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
			include($stylepath . '/mylist.inc.php');
			$tplname = 'mylist';
			
			$bml_id = 0;
			tpl_set_var('title_text', $standard_title);

			if (count($_SESSION['print_list']) == 0) {
				tpl_set_var('list', $no_list);
				tpl_set_var('print_delete_list', '');
				tpl_set_var('export_list', '');
			} else {
				$cache_list = implode(",", $_SESSION['print_list']);
				$rs = sql("SELECT `caches`.`cache_id` AS `cache_id`, `caches`.`name` AS `name`,   `caches`.`type` AS `type`,  `caches`.`last_found` AS `last_found` FROM `caches` WHERE `caches`.`cache_id` IN (".sql_escape($cache_list).") ORDER BY `caches`.`name`");
				$list = '';
				for ($i = 0; $i < mysql_num_rows($rs); $i++)
				{
					$record = sql_fetch_array($rs);
					$tmp_list = $i % 2 == 0 ? $list_e : $list_o;
				//modified coords
				if ($record ['type'] =='7' && $usr!=false) {  //check if quiz (7) and user is logged 
					if (!isset($dbc)) {$dbc = new dataBase();};	
					$mod_coord_sql = 'SELECT cache_id FROM cache_mod_cords WHERE cache_id = '.$record ['cache_id'].' AND user_id = '.$usr['userid'];
					$dbc->simpleQuery($mod_coord_sql);
					if ($dbc->rowCount() > 0 )
					{
						$tmp_list = str_replace('{mod_suffix}', '[F]', $tmp_list);
					} else {
						$tmp_list = str_replace('{mod_suffix}', '',$tmp_list);
					}
				} else {
					$tmp_list= str_replace('{mod_suffix}', '', $tmp_list);
				}; 							
					
					
					$tmp_list = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $tmp_list);
					if ($record['last_found'] == NULL || $record['last_found'] == '0000-00-00 00:00:00')
					{
						$tmp_list = mb_ereg_replace('{lastfound}', htmlspecialchars($no_found_date, ENT_COMPAT, 'UTF-8'), $tmp_list);
					}
						else
						{
							$tmp_list = mb_ereg_replace('{lastfound}', htmlspecialchars(strftime($dateformat, strtotime($record['last_found'])), ENT_COMPAT, 'UTF-8'), $tmp_list);
						}

						$tmp_list = mb_ereg_replace('{urlencode_cacheid}', htmlspecialchars(urlencode($record['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_list);
						$tmp_list = mb_ereg_replace('{cacheid}', htmlspecialchars($record['cache_id'], ENT_COMPAT, 'UTF-8'), $tmp_list);

						$list .= $tmp_list . "\n";
					}
					
				unset($dbc);
					
					
					tpl_set_var('list', $list);
					tpl_set_var('print_delete_list', $print_delete_list);
					tpl_set_var('export_list', $export_list);
				}
				
			}
		
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
