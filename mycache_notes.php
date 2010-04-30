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

			if (isset($_REQUEST['cacheid']))
			{
			$cache_id = $_REQUEST['cacheid'];	
			$cache_rs = sql("SELECT `caches`.`name` `cache_name` FROM `caches` WHERE cache_id=&1",$cache_id);
			$cache_record = sql_fetch_array($cache_rs);
			tpl_set_var('cache_name', 'dla skrzynki: <font color="black">'.$cache_record['cache_name'].'</font>');

			tpl_set_var('notes_links', '<br /><img src="tpl/stdstyle/images/free_icons/add.png" title=""/>&nbsp;<a class="links" href="new_cachenotes.php?cacheid='.$cache_id.'">Dodaj nową notatkę</a> &nbsp;&nbsp;<img src="tpl/stdstyle/images/free_icons/table_go.png" title=""/>&nbsp;<a class="links" href="viewcache.php?cacheid='.$cache_id.'">Wróć do skrzynki</a><br /><br />');
			$cache_param='AND cache_notes.cache_id='.$cache_id;	
			tpl_set_var('cacheid',$cache_id);		
			$notes_rs = sql("SELECT `cache_notes`.`note_id` `note_id`, `cache_notes`.`cache_id` `cacheid`,`cache_notes`.`date` `date`, `cache_notes`.`desc` `desc`, `cache_notes`.`desc_html` `desc_html`, `caches`.`name` `cache_name`, `cache_type`.`icon_small` `icon_large` FROM `cache_notes` INNER JOIN caches ON (`caches`.`cache_id` = `cache_notes`.`cache_id`), `cache_type`  WHERE (`cache_notes`.`user_id`=&1 $cache_param) AND `cache_type`.`id`=`caches`.`type` ORDER BY `cacheid`,`date` DESC",$userid);
				if (mysql_num_rows($notes_rs) != 0)
				{	

				
						$notes = '<table id="gradient" cellpadding="5" width="97%" border="1" style="border-collapse: collapse; font-size: 11px; line-height: 1.6em; color: #000000; ">';
						$notes .= '<tr><th width="40"><b>Data</b></th><th><b>Notatka</b></th><th width="22"><b>Edycja</b></th><th width="22"><b>Usuń</b></th></tr>';
						for ($i = 0; $i < mysql_num_rows($notes_rs); $i++)
							{
							
							$notes_record = sql_fetch_array($notes_rs);

				$note_desc = $notes_record['desc'];
				if ($note_desc != ''){
				if ($notes_record['desc_html'] == '0')
				$note_desc = htmlspecialchars($note_desc, ENT_COMPAT, 'UTF-8');
				else
				{
				require_once($rootpath . 'lib/class.inputfilter.php');
				$myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
				$note_desc = $myFilter->process($note_desc);
					}
				}

							$notes .= '<tr><td align="center" valign="middle"><center></center>'.date("d-m-Y", strtotime($notes_record['date'])). '</td><td>'.$note_desc.'</td><td align="center" valign="middle"><center><a class="links" href="edit_cachenotes.php?noteid='.$notes_record['note_id'].'"><img src="images/actions/edit-16.png" alt="" title="Edycja notatki" /></a></center></td><td align="center" valign="middle"><center><a class="links" href="edit_cachenotes.php?noteid='.$notes_record['note_id'].'&delete onclick="return confirm(\'Czy chcesz usunąć tę notatke?\')"><img src="tpl/stdstyle/images/log/16x16-trash.png" alt="" title="Usuń" /></a></center></td></tr>';
							}
							$notes .= '</table><br /><br />';


						tpl_set_var('notes_content', $notes);
						mysql_free_result($notes_rs);
						
				} else { tpl_set_var('notes_content', $no_notes);}	

			} else { 
			
			$notes_rs = sql("SELECT COUNT(*) `count`,`cache_notes`.`cache_id` `cacheid`, `caches`.`name` `cache_name`, `cache_type`.`icon_small` `icon_large` FROM `cache_notes` INNER JOIN caches ON (`caches`.`cache_id` = `cache_notes`.`cache_id`), `cache_type`  WHERE `cache_notes`.`user_id`=&1 AND `cache_type`.`id`=`caches`.`type` GROUP BY `cacheid` ORDER BY `cacheid`,`date` DESC",$userid);
			if (mysql_num_rows($notes_rs) != 0)
			{	
						$notes = '<table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">';
						$notes .= '<tr><td width="22"><strong>Notatki</strong></td><td><strong>#</strong></td><td width="22">&nbsp;</td><td><strong>Cache</strong></td></tr><tr><td colspan="4"><hr></hr></td></tr>';

						for ($i = 0; $i < mysql_num_rows($notes_rs); $i++)
							{
							
							$notes_record = sql_fetch_array($notes_rs);
							
							$notes .= '<tr><td><a class="links" href="mycache_notes.php?cacheid='.$notes_record['cacheid'].'"><img src="tpl/stdstyle/images/free_icons/note_edit.png" alt="" title="Pokaż notatki" /></a></td><td width="22">'.$notes_record['count']. '</td><td width="22"><img src="tpl/stdstyle/images/'.$notes_record['icon_large'].'" alt="" /></td><td align="left" valign="middle"><a class="links" href="viewcache.php?cacheid='.$notes_record['cacheid'].'">'.$notes_record['cache_name'].'</a></td></tr>';
							}
							$notes .= '<tr><td colspan="4"><hr></hr></td></tr></table><br /><br />';


						tpl_set_var('notes_content', $notes);
						mysql_free_result($notes_rs);

				} else 	{ tpl_set_var('notes_content', $no_notes); }
				
				tpl_set_var('notes_links', '&nbsp;');
				tpl_set_var('cache_name', '');}
		}			
	}
		
	tpl_BuildTemplate();
?>
