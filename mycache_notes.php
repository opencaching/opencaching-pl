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
			$tplname = 'mycache_notes';
			require($stylepath . '/editcache.inc.php');
			//get user record
			$userid = $usr['userid'];

			$notes_rs = sql("SELECT `cache_notes`.`cache_id` `cacheid`, `caches`.`name` `cache_name`, `cache_type`.`icon_small` `icon_large` FROM `cache_notes` INNER JOIN caches ON (`caches`.`cache_id` = `cache_notes`.`cache_id`), `cache_type`  WHERE `cache_notes`.`user_id`=&1 AND `cache_type`.`id`=`caches`.`type` GROUP BY `cacheid` ORDER BY `cacheid`,`date` DESC",$userid);
			if (mysql_num_rows($notes_rs) != 0)
			{	
						$notes = '<table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">';
						$notes .= '<tr><td width="22">&nbsp;</td><td><strong>GeoCache</strong></td></tr><tr><td colspan="2"><hr></hr></td></tr>';

						for ($i = 0; $i < mysql_num_rows($notes_rs); $i++)
							{
							
							$notes_record = sql_fetch_array($notes_rs);
							
							$notes .= '<tr><td width="22"><img src="tpl/stdstyle/images/'.$notes_record['icon_large'].'" alt="" /></td><td align="left" valign="middle"><a class="links" href="viewcache.php?cacheid='.$notes_record['cacheid'].'">'.$notes_record['cache_name'].'</a></td></tr>';
							}
							$notes .= '<tr><td colspan="2"><hr></hr></td></tr></table><br /><br />';


						tpl_set_var('notes_content', $notes);
						mysql_free_result($notes_rs);

				} else 	{ tpl_set_var('notes_content', '<br/><span style="font-size: 14px;"'.$no_notes.'</span>'); }
				
				tpl_set_var('notes_links', '&nbsp;');
				tpl_set_var('cache_name', '');
		}			
	}
		
	tpl_BuildTemplate();
?>
