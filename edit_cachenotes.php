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

			//Edit Waypoint
			if (isset($_REQUEST['noteid']))
			{
			$note_id = $_REQUEST['noteid'];			
			}
			$remove = 0;
			if (isset($_POST['delete']))
			{
			$note_id = $_POST['noteid'];				
			$remove = 1;
			}
			$note_rs = sql("SELECT `note_id`, `cache_id`, `user_id`,`date`, `desc_html`, `desc` FROM `cache_notes`  WHERE `note_id`='&1'", $note_id);
			if (mysql_num_rows($note_rs) == 1)
			{	
			$note_record = sql_fetch_array($note_rs);
			$cache_id = $note_record['cache_id'];

			}
			$cache_rs = sql("SELECT `user_id`, `name`, `type`,  `longitude`, `latitude`,  `status`, `logpw` FROM `caches` WHERE `cache_id`='&1'", $cache_id);

			$cache_record = sql_fetch_array($cache_rs);

			tpl_set_var("cache_name",  htmlspecialchars($cache_record['name']));	

			if ($note_record['user_id'] == $usr['userid'] || $usr['admin'])
			{
				
				$cache_id = isset($_POST['cacheid']) ? $_POST['cacheid'] : $note_record['cache_id'];
				$note_id = isset($_POST['noteid']) ? $_POST['noteid'] : $note_record['note_id'];	

				if ($remove == 1)
						{							
							//remove 
							sql("DELETE FROM `cache_notes` WHERE `note_id`='&1'", $note_id);
							tpl_redirect('cache_notes.php?cacheid=' . urlencode($cache_id));
							exit;
						}

				$tplname = 'edit_cachenotes';


				require_once($stylepath . '/newcache.inc.php');
				//set template replacements
				tpl_set_var('desc_message', '');
				tpl_set_var('general_message', '');

				
				$note_html = isset($_POST['checked']) ? $_POST['checked']:'';
	echo $note_html;
				if ($note_html==0) {$checked="";}else{$checked="checked";}
				tpl_set_var('checked',$checked);					
				$note_desc = isset($_POST['desc']) ? stripslashes($_POST['desc']) : $note_record['desc'];

				if ($note_desc != ''){
				if ($note_html == 0)
				$note_desc = htmlspecialchars($note_desc, ENT_COMPAT, 'UTF-8');
				else
				{
				require_once($rootpath . 'lib/class.inputfilter.php');
				$myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
				$note_desc = $myFilter->process($note_desc);
					}
				}
				tpl_set_var('desc', $note_desc);
				
				
				if (isset($_POST['submit']))
				{
				//check the entered data

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

							//save to DB
							sql("UPDATE `cache_notes` SET  `desc`='&1', `desc_html`='&2' WHERE `note_id`='&3'",$note_desc, $note_html,$note_id);

							//display cache-page
							tpl_redirect('cache_notes.php?cacheid=' . urlencode($cache_id));
							exit;
						}	
					}				
						if( $descnote_not_ok)
						tpl_set_var('general_message', $error_general);
					else
						tpl_set_var('general_message', "");
						tpl_set_var("cacheid", htmlspecialchars($note_record['cache_id']));
						tpl_set_var("noteid", htmlspecialchars($note_record['note_id']));
						tpl_set_var('newshtml', $note_html);
				}							
			mysql_free_result($cache_rs);

		}	

		
	}

		tpl_BuildTemplate();

?>
