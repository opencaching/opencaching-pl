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
		
			$tplname = 'myroutes_add_map';
			tpl_set_var('bodyMod', ' onload="initialize()" onunload="GUnload()"');

			$user_id = $usr['userid'];
			
			
				$name = isset($_POST['name']) ? $_POST['name'] : '';
				tpl_set_var('name', htmlspecialchars($name, ENT_COMPAT, 'UTF-8'));			
			
				$desc = isset($_POST['desc']) ? $_POST['desc'] : '';
				tpl_set_var('desc', htmlspecialchars($desc, ENT_COMPAT, 'UTF-8'));
				
				$radius = isset($_POST['radius']) ? $_POST['radius'] : '0';
				tpl_set_var('radius', $radius);			

				if (isset($_POST['back']))
				{	
				tpl_redirect('myroutes.php');
				exit;
				}

// load xml from google maps







		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
