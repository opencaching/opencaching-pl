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
	$no_tpl_build = false;	
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
			$user_id = $usr['userid'];
			//New Waypoint
			if (isset($_REQUEST['cacheid']))
			{
			$cache_id = $_REQUEST['cacheid'];			
			}
			if (isset($_POST['cacheid']))
			{
			$cache_id = $_POST['cacheid'];			
			}
			tpl_set_var("cacheid", $cache_id);			
			
			$cache_rs = sql("SELECT  `name` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
			if (mysql_num_rows($cache_rs) == 1)
			{
				$cache_record = sql_fetch_array($cache_rs);

			tpl_set_var("cache_name",  htmlspecialchars($cache_record['name']));	

			$tplname = 'new_cachenotes';


				require_once($stylepath . '/newcache.inc.php');
				//set template replacements
				tpl_set_var('desc_message', '');
				tpl_set_var('general_message', '');
		
				$note_desc = isset($_POST['desc']) ? stripslashes($_POST['desc']) : '';
//				if ($note_desc != ''){
//				if ($newshtml == 0)
//				$note_desc = htmlspecialchars($note_desc, ENT_COMPAT, 'UTF-8');
//				else
//				{
//				require_once($rootpath . 'lib/class.inputfilter.php');
//				$myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
//				$note_desc = $myFilter->process($note_desc);
//					}
//				}
				tpl_set_var('desc', $note_desc);
				if (isset($_POST['back']))
				{	
							tpl_redirect('viewcache.php?cacheid=' . urlencode($cache_id));
							mysql_free_result($cache_rs);
							exit;
				}
				
				
				if (isset($_POST['submitform']))
				{
				//check the entered data
//				$note_html = isset($_POST['notehtml']) ? $_POST['notehtml'] : 0;
//				if (($note_html != 0) && ($note_html != 1)) $note_html = 0;
					//desc
					if ($note_desc == '')
					{
						tpl_set_var('desc_message', $descwp_not_ok_message);
						$error = true;
						$descnote_not_ok = true;
					}
					else
					{
						$descnote_not_ok = false;
	//					if ($newshtml == 0)
	//					$note_desc = htmlspecialchars($note_desc, ENT_COMPAT, 'UTF-8');
	//					else
	//					{
	//					require_once($rootpath . 'lib/class.inputfilter.php');
	//					$myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
	//					$note_desc = $myFilter->process($note_desc);
	//					}
					}

					//no errors?
					if (!($descnote_not_ok))
					{
						//add record 
						sql("INSERT INTO `cache_notes` (
										`note_id`,
										`cache_id`,
										`user_id`,
										`date`,
										`desc_html`,
										`desc`
										) VALUES (
										'', '&1', '&2',NOW(),'&3', '&4')",
										$cache_id,
										$user_id,
										'1',
										$note_desc);
					
					tpl_redirect('mycache_notes.php?cacheid=' . urlencode($cache_id));
					// end of insert to sql
					}else
					{
						tpl_set_var('general_message', $error_general);
						tpl_set_var('checked', ($note_html == 1) ? ' checked="checked"' : '');
					}
				
					// end submit
				}							
			mysql_free_result($cache_rs);
			}
			else { 	$no_tpl_build = true;}
			}	
		}

	
	if ($no_tpl_build == false)
	{
		//make the template and send it out
		tpl_BuildTemplate();
	}
?>
