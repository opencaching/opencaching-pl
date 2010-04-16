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
			
			$cache_rs = sql("SELECT `user_id`, `name`, `type`,  `longitude`, `latitude`,  `status`, `logpw` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
			if (mysql_num_rows($cache_rs) == 1)
			{
				$cache_record = sql_fetch_array($cache_rs);

			tpl_set_var("cache_name",  htmlspecialchars($cache_record['name']));	
			if ($cache_record['user_id'] == $usr['userid'] || $usr['admin'])
			{
			$tplname = 'new_cachenotes';


				require_once($stylepath . '/newcache.inc.php');
				//set template replacements
				tpl_set_var('desc_message', '');
				tpl_set_var('general_message', '');

				$newshtml = isset($_POST['newshtml']) ? $_POST['newshtml'] : 0;
				$note_desc = isset($_POST['desc']) ? stripslashes($_POST['desc']) : '';
				if ($note_desc != ''){
				if ($newshtml == 0)
				$note_desc = htmlspecialchars($note_desc, ENT_COMPAT, 'UTF-8');
				else
				{
				require_once($rootpath . 'lib/class.inputfilter.php');
				$myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
				$note_desc = $myFilter->process($note_desc);
					}
				}
				tpl_set_var('desc', $note_desc);
				
				
				if (isset($_POST['submitform']))
				{
				//check the entered data

					//desc
					if ($note_desc == '')
					{
						tpl_set_var('desc_message', $descnote_not_ok_message);
						$error = true;
						$descnote_not_ok = true;
					}
					else
					{
						$descnote_not_ok = false;
						if ($newshtml == 0)
						$note_desc = htmlspecialchars($note_desc, ENT_COMPAT, 'UTF-8');
						else
						{
						require_once($rootpath . 'lib/class.inputfilter.php');
						$myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
						$note_desc = $myFilter->process($note_desc);
						}
					}

					//no errors?
					if (!($descnote_not_ok))
					{
						//add record 
						sql("INSERT INTO `cache_notes` (
										`note_id`,
										`cache_id`,
										`date`,
										`desc_html`,
										`desc`
										) VALUES (
										'', '&1', NOW(),'&2', '&3')",
										$cache_id,
										$newshtml,
										$note_desc);
					
					tpl_redirect('cache_notes.php?cacheid=' . urlencode($cache_id));
					// end of insert to sql
					}else
					{
						tpl_set_var('general_message', $error_general);
					}
				
					// end submit
				}							
			mysql_free_result($cache_rs);
			}
			else { 	$no_tpl_build = true;}
			}	
		}
		
	}
	
	if ($no_tpl_build == false)
	{
		//make the template and send it out
		tpl_BuildTemplate();
	}
?>
