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
'wp_id','cache_id', 'type', 'status' , 'longitude', 'latitude', 'name', 'describe', 'stage', 

table db waypoints_type
'id','pl','en','icon'

'pl' and 'en'  name of type wp in language, icon with path, images/waypoints/*.png

type:
 1 => Parking area,  2 => Reference point, 3 => Stage of Multicache.  4 => Final location,    5 => Question to answer, ???
Images for WP: images/waypoints/*.png in separate tabel db ? or get icone_name by wp.type db ?

status:
1 => Show all information for this waypoint, including coordinates
2 => Hide this waypoint from view except by the owner or administrator
3 => Show the details of this waypoint but hide the coordinates 
-------------------------------------

in viewcache.php presentation of WayPoints in separate section after Describe section
with possiblity download WP as GPX and send to GPS directly when wp.status = 1

Stage| wp_icone |X Y coordinates | Describe of WP | Show on Map|  Dwonload GPX | Send to GPS |

*/
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
			//Create Waypoint
		if (isset($_REQUEST['cacheid']))
			{
			$cache_id = $_REQUEST['cacheid'];
			}
			$cache_rs = sql("SELECT `user_id`, `name`, `type`,  `longitude`, `latitude`,  `status`, `logpw` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
			if (mysql_num_rows($cache_rs) == 1)
			{
				$cache_record = sql_fetch_array($cache_rs);
			
			if ($cache_record['user_id'] == $usr['userid'] || $usr['admin'])
				{
			$tplname = 'waypoints';
			
			
			tpl_set_var("cache_name", $cache_record['name']);		
				}
			}
			mysql_free_result($cache_rs);

			//Edit Waypoint
			if (isset($_REQUEST['wpid']))
			{
			$wp_id = $_REQUEST['wpid'];			
			}
			
			$wp_rs = sql("SELECT `wp_id`, `cache_id`, `type`, `longitude`, `latitude`,  `desc`, `status`, `waypoint_type`.`pl` `wp_type`, `waypoint_type`.`icon` `wp_icon` FROM `waypoints` INNER JOIN waypoint_type ON (waypoints.type = waypoint_type.id) WHERE `wp_id`='&1'", $wp_id);
			if (mysql_num_rows($wp_rs) == 1)
			{	
			$wp_record = sql_fetch_array($wp_rs);
			$cache_id = $wp_record['cache_id'];
			}
			$cache_rs = sql("SELECT `user_id`, `name`, `type`,  `longitude`, `latitude`,  `status`, `logpw` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
			if (mysql_num_rows($cache_rs) == 1)
			{
				$cache_record = sql_fetch_array($cache_rs);
			
			if ($cache_record['user_id'] == $usr['userid'] || $usr['admin'])
				{
			$tplname = 'waypoints';
			
			tpl_set_var("desc", htmlspecialchars($wp_record['desc']);
			tpl_set_var("type", htmlspecialchars($wp_record['type']);
			tpl_set_var("stage", htmlspecialchars($wp_record['stage']);
			tpl_set_var("status", htmlspecialchars($wp_record['status']);
			tpl_set_var("cache_name",  htmlspecialchars($cache_record['name']);	
			}
			mysql_free_result($cache_rs);
			mysql_free_result($wp_rs);
		}	
			
	
		tpl_set_var("sample", "xxxx");
	

			}
		
	}
	tpl_BuildTemplate();
?>
