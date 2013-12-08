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
	require_once('./lib/db.php');
require_once  __DIR__.'/lib/myn.inc.php';
require_once  __DIR__.'/lib/db.php';

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
			//get user record
			$userid = $usr['userid'];
				
			
			$db = new dataBase(); 			
			if (isset( $_REQUEST["delete"]))
			{
				$note_id = $_REQUEST["delete"];				
				//remove
				$query = "DELETE FROM `cache_notes` WHERE `note_id`=:1";
				$db->multiVariableQuery($query, $note_id );						
			}
			//else
			{

				//$notes_rs = sql("SELECT `cache_notes`.`cache_id` `cacheid`, `caches`.`name` `cache_name`, `cache_type`.`icon_small` `icon_large` FROM `cache_notes` INNER JOIN caches ON (`caches`.`cache_id` = `cache_notes`.`cache_id`), `cache_type`  WHERE `cache_notes`.`user_id`=&1 AND `cache_type`.`id`=`caches`.`type` GROUP BY `cacheid` ORDER BY `cacheid`,`date` DESC",$userid);
				$query = "SELECT `cache_notes`.`cache_id` `cacheid`,
								 `caches`.`name` `cache_name`, 
								 `cache_type`.`icon_small` `icon_large`, 
								 `caches`.`type` `cache_type`,
								 note_id 
								 FROM `cache_notes` INNER JOIN caches ON (`caches`.`cache_id` = `cache_notes`.`cache_id`), `cache_type`  WHERE `cache_notes`.`user_id`=:1 AND `cache_type`.`id`=`caches`.`type` GROUP BY `cacheid` ORDER BY `cacheid`,`date` DESC";
				$db->multiVariableQuery($query, $userid);
				
				//if (mysql_num_rows($notes_rs) != 0)
				$count = $db->rowCount();
				if( $count != 0)
				{	
							//$notes = '<table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">';
							//$notes .= '<tr><td width="22">&nbsp;</td><td><strong>GeoCache</strong></td></tr><tr><td colspan="2"><hr></hr></td></tr>';
							$notes ="";
							$bgcolor1 = '#ffffff';
							$bgcolor2 = '#eeeeee';
							
							//for ($i = 0; $i < mysql_num_rows($notes_rs); $i++)
							for ($i = 0; $i < $count; $i++)
							{
								$bgcolor = ( $i% 2 )? $bgcolor1 : $bgcolor2;
									
								//$notes_record = sql_fetch_array($notes_rs);
								$notes_record = $db->dbResultFetch();
				$cacheicon =  $cache_icon_folder;
				if ($notes_record['cache_type']!="6") {
					$cacheicon .=is_cache_found($notes_record['cacheid'], $userid) ? $foundCacheTypesIcons[$notes_record['cache_type']] : $CacheTypesIcons[$notes_record['cache_type']] ;
				} else {
					$cacheicon .=is_event_attended ($notes_record['cacheid'],$userid) ? $foundCacheTypesIcons["6"] : $CacheTypesIcons["6"] ;
				};	
								
								$notes .= '<tr><td style="background-color: {bgcolor}"><img src="'.$cacheicon.'" alt="" /></td><td align="left"  style="background-color: {bgcolor}"><a  href="viewcache.php?cacheid={cacheid}">'.$notes_record['cache_name'].'</a></td><td style="background-color: {bgcolor}; text-align:center"><a class="links"  href="mycache_notes.php?delete={noteid}" onclick="return confirm(\''.tr("mycache_notes_01").'\');"><img style="vertical-align: middle;" src="tpl/stdstyle/images/log/16x16-trash.png" alt="" title='.tr('delete').' /></a></td></tr>';
								$notes = mb_ereg_replace('{bgcolor}', $bgcolor, $notes);
								$notes = mb_ereg_replace('{cacheid}', $notes_record["cacheid"], $notes);
								$notes = mb_ereg_replace('{noteid}', $notes_record["note_id"], $notes);
							}
							//$notes .= '<tr><td colspan="3"><hr></hr></td></tr></table><br /><br />';
	
	
							tpl_set_var('notes_content', $notes);
							//mysql_free_result($notes_rs);
	
					} else 	{ tpl_set_var('notes_content', '<br/><span style="font-size: 14px;">&nbsp;&nbsp;<strong>'.tr(no_note).'</strong></span><br/></br/>'); }
			}	
			unset( $db );
		}			
	}
		
	tpl_BuildTemplate();
?>
