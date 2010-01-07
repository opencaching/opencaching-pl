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
/*
Add  additional waypoints to cache
table db 'waypoints' struture 
'id', 'cache_id', 'type', 'longitude', 'latitude',  'describe', 'status'  ????

type:
1 => Final location, 2 => Parking area, 3 => Question to answer, 4 => Reference point, 5 => Stages of Multicaches.
Images for WP: images/waypoints/*.png

status:
1 => Show all information for this waypoint, including coordinates
2 => Show the details of this waypoint but hide the coordinates ???? make to sens ?
3 => Hide this waypoint from view except by the owner or administrator

*/
//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	
	$tplname = 'waypoints';

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

	// check for old-style parameters
		if (isset($_REQUEST['cacheid']))
		{
			$cache_id = $_REQUEST['cacheid'];
		
		}

		
		
		
		
	
		tpl_set_var("sample", "xxxx");
	

		}
	}
	tpl_BuildTemplate();
?>
