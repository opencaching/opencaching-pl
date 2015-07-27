<?php
/***************************************************************************
																./viewprofile.php
															-------------------
		begin                : August 21 2004
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

	 view the profile of an other user

 ****************************************************************************/

	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');

	//Preprocessing
	if ($error == false)
	{
		require($stylepath . '/viewprofile.inc.php');
		require($stylepath . '/myprofile.inc.php');

		$tplname = 'viewprofile';

		$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid']+0 : 0;

		$rs = sql("SELECT `user`.`username`, `user`.`pmr_flag`, `user`.`date_created`, `user`.`latitude`, `user`.`longitude`, `countries`.`pl` AS `country`, `user`.`hidden_count`, `user`.`founds_count`, `user`.`uuid` FROM `user` LEFT JOIN `countries` ON (`user`.`country`=`countries`.`short`) WHERE `user`.`user_id`='&1'", $userid);
		$rs1 = sql("SELECT `user`.`username`, `user`.`pmr_flag`, `user`.`date_created`, `user`.`latitude`, `user`.`longitude`, `countries`.`pl` AS `country`, `user`.`hidden_count`, `user`.`founds_count`, `user`.`uuid` FROM `user` LEFT JOIN `countries` ON (`user`.`country`=`countries`.`short`) WHERE `user`.`user_id`='&1'", $userid);

		if (mysql_num_rows($rs) == 0)
		{
			$tplname = 'error';
			tpl_set_var('tplname', 'viewprofile');
			tpl_set_var('error_msg', $err_no_user);
		}
		else
		{
			$record = sql_fetch_array($rs);
			tpl_set_var('username', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('userid', htmlspecialchars($userid, ENT_COMPAT, 'UTF-8'));
			tpl_set_var('founds', htmlspecialchars($record['founds_count'] <= '0' ? '0' : $record['founds_count'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('hidden', htmlspecialchars($record['hidden_count'] <= '0' ? '0' : $record['hidden_count'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('recommended', sqlValue("SELECT COUNT(*) FROM `cache_rating` WHERE `user_id`='" . sql_escape($_REQUEST['userid']) . "'", 0));
			tpl_set_var('maxrecommended', floor($record['founds_count'] * rating_percentage / 100));

			tpl_set_var('country', htmlspecialchars($record['country'], ENT_COMPAT, 'UTF-8'));
			tpl_set_var('registered', strftime($dateformat, strtotime($record['date_created'])));

			$options = '';
			if ($record['pmr_flag'] == 1)
			{
				$options .= $using_pmr_message;
			}

			tpl_set_var('options', $options);
			tpl_set_var('uuid', htmlspecialchars($record['uuid'], ENT_COMPAT, 'UTF-8'));
		}
	}

	tpl_BuildTemplate();
?>