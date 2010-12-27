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

   Unicode Reminder メモ

	 display all watches of this user

 ****************************************************************************/

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
		if (isset($_REQUEST['routeid']))
			{
			$route_id = $_REQUEST['routeid'];			
			}
			$remove = 0;
			if (isset($_REQUEST['delete']))
			{
			$route_id = $_REQUEST['routeid'];				
			$remove = 1;
			}
			if (isset($_POST['delete']))
			{
			$route_id = $_POST['routeid'];				
			$remove = 1;
			}			
			if (isset($_POST['routeid'])){
			$route_id = $_POST['routeid'];}
			
			$tplname = 'myroutes_edit';
			$user_id = $usr['userid'];
			
				if (isset($_POST['back']))
				{	
							tpl_redirect('myroutes.php');
							exit;
				}		

			$route_rs = sql("SELECT `user_id`,`name`, `description`, `radius` FROM `routes` WHERE `route_id`='&1'", $route_id);
			$record = sql_fetch_array($route_rs);	
			
				$rname = isset($_POST['name']) ? $_POST['name'] : '';					
				$rdesc = isset($_POST['desc']) ? $_POST['desc'] : '';			
				$rradius = isset($_POST['radius']) ? $_POST['radius'] :'';

				
			if ($record['user_id'] == $usr['userid'])
				{

						if ($remove == 1)
						{							
							//remove 
							sql("DELETE FROM `routes` WHERE `route_id`='&1'", $route_id);
							sql("DELETE FROM `route_points` WHERE `route_id`='&1'", $route_id);
							tpl_redirect('myroutes.php');
							exit;
						}
				}
	
				// start submit
				if (isset($_POST['submit']))
				{
				
				sql("UPDATE `routes` SET `name`='&1',`description`='&2',`radius`='&3' WHERE `route_id`='&4'",$rname,$rdesc,$rradius,$route_id);
												
						tpl_redirect('myroutes.php');
							exit;
					
				}
				tpl_set_var('name', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'));	
				tpl_set_var('desc', htmlspecialchars($record['description'], ENT_COMPAT, 'UTF-8'));					
				tpl_set_var('radius', $record['radius']);	
				tpl_set_var('routeid', $route_id);	
			}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
