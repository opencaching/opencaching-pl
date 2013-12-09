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

function CleanSpecChars( $log, $flg_html )
{	
	$log_text = $log;
	
	if ( $flg_html == 1)
	{ $log_text = htmlspecialchars( $log_text, ENT_COMPAT, 'UTF-8');}
	
	$log_text = str_replace("\r\n", " ",$log_text);
	$log_text = str_replace("\n", " ",$log_text);
	$log_text = str_replace("'", "-",$log_text);
	$log_text = str_replace("\"", " ",$log_text);
	
	return $log_text;
}	
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
			
			$tr_COG = tr('cog_user_name');
			$no_found_date = '---';	
			
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
								 `cache_notes`.`desc` `notes_desc`,
								 `caches`.`name` `cache_name`, 
								 `cache_type`.`icon_small` `icon_large`, 
								 `caches`.`type` `cache_type`,
								 note_id, 
								 cl.text AS log_text, 
								 cl.type AS log_type,
								 cl.user_id AS luser_id,
								 cl.date AS log_date,
								 cl.deleted AS log_deleted,
								 log_types.icon_small AS icon_small,
								 user.username AS user_name
								 FROM `cache_notes` 
									INNER JOIN `caches` ON (`cache_notes`.`cache_id`=`caches`.`cache_id`)
									INNER JOIN cache_type ON (caches.type = cache_type.id)
									left outer JOIN cache_logs as cl ON (caches.cache_id = cl.cache_id)
									left outer JOIN log_types ON (cl.type = log_types.id) 
									left outer JOIN user ON (cl.user_id = user.user_id)  
									
								 WHERE `cache_notes`.`user_id`=:1 AND `cache_type`.`id`=`caches`.`type` 
								 		AND `cache_notes`.`user_id`= :1 and 
										( cl.id is null or cl.id =
										( SELECT id
											FROM cache_logs cl_id
											WHERE cl.cache_id = cl_id.cache_id and cl_id.date =
						
												( SELECT max( cache_logs.date )
													FROM cache_logs
													WHERE cl.cache_id = cache_id 
												)
												limit 1
											))  
								 GROUP BY `cacheid` 
								 ORDER BY `cacheid`, log_date DESC";
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
								
								$notes .= '<tr>
								<td style="background-color: {bgcolor}"><img src="'.$cacheicon.'" alt="" /></td>
								<td align="left"  style="background-color: {bgcolor}"><a  href="viewcache.php?cacheid={cacheid}" onmouseover="Tip(\'{notes_text}\', OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()">'.$notes_record['cache_name'].'</a></td>
							<td style="background-color: {bgcolor}">&nbsp;</td>
							<td nowrap style="text-align:center; background-color: {bgcolor}">{lastfound}</td>
							<td nowrap style="text-align:center; background-color: {bgcolor}"><img src="tpl/stdstyle/images/{icon_name}" border="0" alt="" onmouseover="Tip(\'{log_text}\', OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()"/></td>						
								<td style="background-color: {bgcolor}; text-align:center"><a class="links"  href="mycache_notes.php?delete={noteid}" onclick="return confirm(\''.tr("mycache_notes_01").'\');"><img style="vertical-align: middle;" src="tpl/stdstyle/images/log/16x16-trash.png" alt="" title='.tr('delete').' /></a></td></tr>';
								$notes = mb_ereg_replace('{bgcolor}', $bgcolor, $notes);
								$notes = mb_ereg_replace('{cacheid}', $notes_record["cacheid"], $notes);
								$notes = mb_ereg_replace('{noteid}', $notes_record["note_id"], $notes);
								if ($notes_record['log_date'] == NULL || $notes_record['log_date'] == '0000-00-00 00:00:00')
							{
								$notes = mb_ereg_replace('{lastfound}', htmlspecialchars($no_found_date, ENT_COMPAT, 'UTF-8'), $notes);
							}
							else
							{
								$notes = mb_ereg_replace('{lastfound}', htmlspecialchars(strftime($dateformat, strtotime($notes_record['log_date'])), ENT_COMPAT, 'UTF-8'), $notes);
							};
							
							if ($notes_record["log_deleted"] == 1) {  // if last record is deleted change icon and text
								$log_text = tr('vl_Record_deleted');
								$notes_record['icon_small']="log/16x16-trash.png";
								
							} else {
								$log_text  = CleanSpecChars( $notes_record[ 'log_text'], 1 );
							};
							

							 if ($notes_record['log_type'] == 12 && !$usr['admin']) {
                    			 $notes_record['user_id']   = '0';
                   				 $notes_record['user_name'] = $tr_COG;
             				  };		
							
							
							$log_text = "<b>".$notes_record['user_name'].":</b><br>".$log_text;	
							$notes = mb_ereg_replace('{log_text}', $log_text, $notes);	
							$notes_text = CleanSpecChars( $notes_record[ 'notes_desc'], 1 );
							if ($notes_text=='') {$notes_text='--';};
							$notes = mb_ereg_replace('{notes_text}', $notes_text, $notes);
							$notes = mb_ereg_replace('{icon_name}', $notes_record['icon_small'], $notes);	
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
