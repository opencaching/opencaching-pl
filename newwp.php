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
			//New Waypoint
			if (isset($_REQUEST['cacheid']))
			{
			$cache_id = $_REQUEST['cacheid'];			
			}
			$add_wp = 0;
			if (isset($_POST['add']))
			{
			$wp_id = $_POST['wpid'];				
			$add_wp = 1;
			}			
			$wp_rs = sql("SELECT `wp_id`, `cache_id`, `type`, `longitude`, `latitude`,  `desc`, `status`, `stage`,`waypoint_type`.`pl` `wp_type`, `waypoint_type`.`icon` `wp_icon` FROM `waypoints` INNER JOIN waypoint_type ON (waypoints.type = waypoint_type.id) WHERE `wp_id`='&1'", $wp_id);
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

						if ($add_wp == 1)
						{							
							//remove 
							sql("INSERT TO`waypoints` WHERE `cache_id`='&1'", $cache_id);
							tpl_redirect('editcache-test.php?cacheid=' . urlencode($cache_id));
							exit;
						}


				$tplname = 'editwp';
			require_once($rootpath . 'lib/caches.inc.php');

					$wp_type = isset($_POST['type']) ? $_POST['type'] : $wp_record['type'];
					//build typeoptions
					$types = '';
					foreach ($wp_types as $type)
					{

						if ($type['id'] == $wp_type)
						{
							$types .= '<option value="' . $type['id'] . '" selected="selected">' . htmlspecialchars($type[$lang], ENT_COMPAT, 'UTF-8') . '</option>';
						}
						else
						{
							$types .= '<option value="' . $type['id'] . '">' . htmlspecialchars($type[$lang], ENT_COMPAT, 'UTF-8') . '</option>';
						}
					}
					tpl_set_var('typeoptions', $types);


					if (isset($_POST['latNS']))
					{
						//get coords from post-form
						$coords_latNS = $_POST['latNS'];
						$coords_lonEW = $_POST['lonEW'];
						$coords_lat_h = $_POST['lat_h'];
						$coords_lon_h = $_POST['lon_h'];
						$coords_lat_min = $_POST['lat_min'];
						$coords_lon_min = $_POST['lon_min'];
					}
					else
					{
						//get coords from DB
						$coords_lon = $wp_record['longitude'];
						$coords_lat = $wp_record['latitude'];

						if ($coords_lon < 0)
						{
							$coords_lonEW = 'W';
							$coords_lon = -$coords_lon;
						}
						else
						{
							$coords_lonEW = 'E';
						}

						if ($coords_lat < 0)
						{
							$coords_latNS = 'S';
							$coords_lat = -$coords_lat;
						}
						else
						{
							$coords_latNS = 'N';
						}

						$coords_lat_h = floor($coords_lat);
						$coords_lon_h = floor($coords_lon);

						$coords_lat_min = sprintf("%02.3f", round(($coords_lat - $coords_lat_h) * 60, 3));
						$coords_lon_min = sprintf("%02.3f", round(($coords_lon - $coords_lon_h) * 60, 3));
					}

					//here we validate the data

					//coords
					$lon_not_ok = false;

					if (!mb_ereg_match('^[0-9]{1,3}$', $coords_lon_h))
					{
						$lon_not_ok = true;
					}
					else
					{
						$lon_not_ok = (($coords_lon_h >= 0) && ($coords_lon_h < 180)) ? false : true;
					}

					if (is_numeric($coords_lon_min))
					{
						// important: use here |=
						$lon_not_ok |= (($coords_lon_min >= 0) && ($coords_lon_min < 60)) ? false : true;
					}
					else
					{
						$lon_not_ok = true;
					}

					//same with lat
					$lat_not_ok = false;

					if (!mb_ereg_match('^[0-9]{1,3}$', $coords_lat_h))
					{
						$lat_not_ok = true;
					}
					else
					{
						$lat_not_ok = (($coords_lat_h >= 0) && ($coords_lat_h < 180)) ? false : true;
					}

					if (is_numeric($coords_lat_min))
					{
						// important: use here |=
						$lat_not_ok |= (($coords_lat_min >= 0) && ($coords_lat_min < 60)) ? false : true;
					}
					else
					{
						$lat_not_ok = true;
					}

					tpl_set_var('selLatN', ($coords_latNS == 'N') ? ' selected="selected"' : '');
					tpl_set_var('selLatS', ($coords_latNS == 'S') ? ' selected="selected"' : '');
					tpl_set_var('selLonE', ($coords_lonEW == 'E') ? ' selected="selected"' : '');
					tpl_set_var('selLonW', ($coords_lonEW == 'W') ? ' selected="selected"' : '');
					tpl_set_var('lat_h', htmlspecialchars($coords_lat_h, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('lat_min', htmlspecialchars($coords_lat_min, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('lon_h', htmlspecialchars($coords_lon_h, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('lon_min', htmlspecialchars($coords_lon_min, ENT_COMPAT, 'UTF-8'));

					tpl_set_var('name_message', ($name_not_ok == true) ? $name_not_ok_message : '');
					tpl_set_var('lon_message', ($lon_not_ok == true) ? $error_coords_not_ok : '');
					tpl_set_var('lat_message', ($lat_not_ok == true) ? $error_coords_not_ok : '');




			
			tpl_set_var("desc", htmlspecialchars($wp_record['desc']));
			tpl_set_var("type", htmlspecialchars($wp_record['type']));
			tpl_set_var("stage", htmlspecialchars($wp_record['stage']));
			tpl_set_var("status", htmlspecialchars($wp_record['status']));
			tpl_set_var("wpid", htmlspecialchars($wp_record['wp_id']));
			tpl_set_var("cacheid", htmlspecialchars($wp_record['cache_id']));
			tpl_set_var("cache_name",  htmlspecialchars($cache_record['name']));	
				}
			mysql_free_result($cache_rs);
			mysql_free_result($wp_rs);
			}	
			
	
			tpl_set_var("sample", "xxxx");
	


			}
		
	}
	tpl_BuildTemplate();
?>
