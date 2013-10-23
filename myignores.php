<?php
/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

	 display all ignored caches of this user

 ****************************************************************************/

	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require_once('./lib/db.php');

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

			include($stylepath . '/myignores.inc.php');
			$tplname = 'myignores';
			tpl_set_var('title_text', $title_text);

			$dbc = new dataBase();
			//get all caches ignored
			//$rs = mysql_query('SELECT `cache_ignore`.`cache_id` AS `cache_id`, `caches`.`name` AS `name`, `caches`.`last_found` AS `last_found` FROM `cache_ignore` INNER JOIN `caches` ON (`cache_ignore`.`cache_id` = `caches`.`cache_id`) WHERE `cache_ignore`.`user_id`=\'' . addslashes($usr['userid']) . '\' ORDER BY `caches`.`name`', $dblink);
			$query = "SELECT `cache_ignore`.`cache_id` AS `cache_id`, `caches`.`name` AS `name`, `caches`.`last_found` AS `last_found` FROM `cache_ignore` INNER JOIN `caches` ON (`cache_ignore`.`cache_id` = `caches`.`cache_id`) WHERE `cache_ignore`.`user_id`= :1 ORDER BY `caches`.`name`";
			
			$dbc->multiVariableQuery($query, $usr['userid'] );
			$rowCount = $dbc->rowCount();
			if ( $rowCount == 0)
			{
				tpl_set_var('no_ignores', $no_ignores);
				tpl_set_var('ignores_caches', '');
				tpl_set_var('title_text_tab', '');
			}
			else
			{
				//tpl_set_var('title_text_tab', $title_text_lbl);
				tpl_set_var('no_ignores', '');
				$ignores = '';
				for ($i = 0; $i < $rowCount; $i++)
				{
					$record = $dbc->dbResultFetch();
					//$tmp_ignore = $i % 2 == 0 ? $ignoree : $ignoreo;
					$bgcolor = ( $i% 2 )? $bgcolor1 : $bgcolor2;
					$tmp_ignore = $ignore;
					$tmp_ignore = str_replace('{cachename}', htmlspecialchars($record['name']), $tmp_ignore);

					if ($record['last_found'] == NULL || $record['last_found'] == '0000-00-00 00:00:00')
					{
						$tmp_ignore = str_replace('{lastfound}', htmlspecialchars($no_found_date), $tmp_ignore);
					}
					else
					{
						$tmp_ignore = str_replace('{lastfound}', htmlspecialchars(strftime($dateformat, strtotime($record['last_found']))), $tmp_ignore);
					}

					$tmp_ignore = str_replace('{urlencode_cacheid}', htmlspecialchars(urlencode($record['cache_id'])), $tmp_ignore);
					$tmp_ignore = str_replace('{cacheid}', htmlspecialchars($record['cache_id']), $tmp_ignore);
					$tmp_ignore = mb_ereg_replace('{bgcolor}', $bgcolor, $tmp_ignore);

					$ignores .= $tmp_ignore . "\n";
				}
				tpl_set_var('ignores_caches', $ignores);
			}
			
			unset( $dbc );
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
