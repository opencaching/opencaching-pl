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
			$tplname = 'cache_notes';
			require($stylepath . '/editcache.inc.php');
			//get user record
			$user_id = $usr['userid'];
			tpl_set_var('userid',$user_id);	
			if (isset($_REQUEST['cacheid']))
			{
			$cache_id = $_REQUEST['cacheid'];	
			$cache_param='AND cache_notes.cache_id='.$cache_id;		
			} else { $cache_param="";}
			tpl_set_var('cacheid',$cache_id);	

			$notes_rs = sql("SELECT `cache_notes`.`note_id` `note_id`, `cache_notes`.`cache_id` `cacheid`,`cache_notes`.`date` `date`, `cache_notes`.`desc` `desc`, `caches`.`name` `cache_name`, `cache_type`.`icon_small` `icon_large` FROM `cache_notes` INNER JOIN caches ON (`caches`.`cache_id` = `cache_notes`.`cache_id`), `cache_type`  WHERE (`cache_notes`.`user_id`=&1 $cache_param) AND `cache_type`.`id`=`caches`.`type` GROUP BY `cacheid` ORDER BY `date`,`cacheid`,`note_id`",$user_id);
			if (mysql_num_rows($notes_rs) != 0)
			{	
						$notes = '<table id="gradient" cellpadding="5" width="97%" border="1" style="border-collapse: collapse; font-size: 11px; line-height: 1.6em; color: #000000; ">';
						$notes .= '<tr><th width="22">&nbsp;</th><th width="40"><b>Skrzynka</b></th><th width="40"><b>Data</b></th><th><b>Notatka</b></th><th width="22"><b>Edycja</b></th></tr>';
						for ($i = 0; $i < mysql_num_rows($notes_rs); $i++)
							{
							
							$notes_record = sql_fetch_array($notes_rs);

							$notes .= '<td align="center" valign="middle"><center><img src="tpl/stdstyle/images/'.$notes_record['icon_large'].'" title="" /></center></td><td align="center" valign="middle"><center><a class="links" href="viewcache.php?cacheid='.$notes_record['cacheid'].'">'.$notes_record['cache_name'].'</a></center></td><td align="center" valign="middle"><center></center>'.date("d-m-Y", strtotime($notes_record['date'])). '</td><td>'.$notes_record['desc'].'</td><td align="center" valign="middle"><center><a class="links" href="edit_cachenotes.php?noteid='.$notes_record['note_id'].'"><img src="images/actions/edit-16.png" alt="" title="Edit WP" /></a></center></td></tr>';
							}
							$notes .= '</table>';


						tpl_set_var('notes_content', $notes);
						mysql_free_result($notes_rs);
					}
					else
					{
					tpl_set_var('notes_content', $no_notes);
					}			


			}
		
	}
	tpl_BuildTemplate();
?>
