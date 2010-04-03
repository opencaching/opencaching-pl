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

Add  additional waypoints to cache:

----------------------------------
table db 'waypoints' struture 
'cache_id', 'type', 'status' , 'longitude', 'latitude', 'name', 'describe', 

table db waypoints_type
'id','pl','en','icon'

'pl' and 'en'  name of type wp in language, icon with path, images/waypoints/*.png

type:
1 => Final location, 2 => Parking area, 3 => Question to answer, 4 => Reference point, 5 => Stage of Multicache.
Images for WP: images/waypoints/*.png in separate tabel db ? or get icone_name by wp.type db ?

status:
1 => Show all information for this waypoint, including coordinates
2 => Hide this waypoint from view except by the owner or administrator
3 => Show the details of this waypoint but hide the coordinates ???? make to sens ?
-------------------------------------

in viewcache.php presentation of WayPoints in separate section after Describe section
with possiblity download WP as GPX and send to GPS directly when wp.status = 1

| wp_icone | name of type |X Y coordinates | Describe of WP | Dwonload GPX | Send to GPS |


in editcache.php in section waypoints table with list of wp:

| wp_icone | name of type |status | X Y coordinates | Describe of WP | edit_icone|

......


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

	// 
		if (isset($_REQUEST['cacheid']))
		{
			$cache_id = $_REQUEST['cacheid'];

		
		}

// ToDo:
// Check owner cache user_id must be usr.user_id !!!
// GET action= edit or add 
// if edit GET wp_id where wp_id must be equal from table waypoint.id
		
		
		
		
	
		tpl_set_var("sample", "xxxx");
	

		}
	}
	tpl_BuildTemplate();
?>
