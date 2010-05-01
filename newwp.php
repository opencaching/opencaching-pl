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
			$wp_rs = sql("SELECT `stage`,`type` FROM `waypoints`  WHERE `cache_id`='&1' AND (type<>4 OR type<>5) ORDER BY `stage` DESC", $cache_id);
			
			$cache_rs = sql("SELECT `user_id`, `name`, `type`,  `longitude`, `latitude`,  `status`, `logpw` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
			if (mysql_num_rows($cache_rs) == 1)
			{
				$cache_record = sql_fetch_array($cache_rs);

			tpl_set_var("cache_name",  htmlspecialchars($cache_record['name']));
			tpl_set_var("cachetype",  htmlspecialchars($cache_record['type']));	
			if (mysql_num_rows($wp_rs) != 0)
			{	
			$wp_record = sql_fetch_array($wp_rs);
			if ($cache_record['type'] == '2' || $cache_record['type'] == '6' || $cache_record['type'] == '8' || $cache_record['type'] == '9')
			{ $next_stage = 0; $wp_stage = 0; tpl_set_var("stage", "0");	tpl_set_var("nextstage", "0"); tpl_set_var("start_stage", '<!--');	tpl_set_var("end_stage", '-->');
			} else {
			$next_stage = ($wp_record['stage'] +1 ); 
			tpl_set_var("nextstage", $next_stage);				
			tpl_set_var("stage", $next_stage);
			tpl_set_var("start_stage", '');	
			tpl_set_var("end_stage", '');}
			} else {
			if ($cache_record['type'] == '2' || $cache_record['type'] == '6' || $cache_record['type'] == '8' || $cache_record['type'] == '9')
			{ $wp_stage = 0; tpl_set_var("stage", "0");	tpl_set_var("nextstage", "0"); tpl_set_var("start_stage", '<!--');	tpl_set_var("end_stage", '-->');
			}else {
			tpl_set_var("start_stage", '');	tpl_set_var("end_stage", '');	}
			
			tpl_set_var("stage", "1");tpl_set_var("nextstage", "1");	}
			
			if ($cache_record['user_id'] == $usr['userid'] || $usr['admin'])
			{
			$tplname = 'newwp';

		    		require_once($rootpath . 'lib/caches.inc.php');
				require_once($stylepath . '/newcache.inc.php');
				//set template replacements
				tpl_set_var('lon_message', '');
				tpl_set_var('lat_message', '');
				tpl_set_var('general_message', '');
				tpl_set_var('desc_message', '');
				tpl_set_var('type_message', '');
				tpl_set_var('stage_message', '');
				
					//build typeoptions					
					$sel_type = isset($_POST['type']) ? $_POST['type'] : -1;	
					if(checkField('waypoint_type',$lang) )
					$lang_db = $lang;
					else
					$lang_db = "en";
					$types = '';
//					if ($cache_record['type'] == '2' || $cache_record['type'] == '6' || $cache_record['type'] == '8' || $cache_record['type'] == '9')

					foreach (get_wp_types_from_database($cache_record['type']) as $type)
					{
					if ($type['id'] == $sel_type)
					{
						$types .= '<option value="' . $type['id'] . '" selected="selected">' . htmlspecialchars($type[$lang_db], ENT_COMPAT, 'UTF-8') . '</option>';
					}
						else
						{
						$types .= '<option value="' . $type['id'] . '">' . htmlspecialchars($type[$lang_db], ENT_COMPAT, 'UTF-8') . '</option>';
						}
					}					
									
				tpl_set_var('typeoptions', $types);

				//coords
				$lonEW = isset($_POST['lonEW']) ? $_POST['lonEW'] : $default_EW;
				if ($lonEW == 'E')
				{
					tpl_set_var('lonEsel', ' selected="selected"');
					tpl_set_var('lonWsel', '');
				}
				else
				{
					tpl_set_var('lonEsel', '');
					tpl_set_var('lonWsel', ' selected="selected"');
				}
				$lon_h = isset($_POST['lon_h']) ? $_POST['lon_h'] : '0';
				tpl_set_var('lon_h', htmlspecialchars($lon_h, ENT_COMPAT, 'UTF-8'));

				$lon_min = isset($_POST['lon_min']) ? $_POST['lon_min'] : '00.000';
				tpl_set_var('lon_min', htmlspecialchars($lon_min, ENT_COMPAT, 'UTF-8'));

				$latNS = isset($_POST['latNS']) ? $_POST['latNS'] : $default_NS;
				if ($latNS == 'N')
				{
					tpl_set_var('latNsel', ' selected="selected"');
					tpl_set_var('latSsel', '');
				}
				else
				{
					tpl_set_var('latNsel', '');
					tpl_set_var('latSsel', ' selected="selected"');
				}
				$lat_h = isset($_POST['lat_h']) ? $_POST['lat_h'] : '0';
				tpl_set_var('lat_h', htmlspecialchars($lat_h, ENT_COMPAT, 'UTF-8'));

				$lat_min = isset($_POST['lat_min']) ? $_POST['lat_min'] : '00.000';
				tpl_set_var('lat_min', htmlspecialchars($lat_min, ENT_COMPAT, 'UTF-8'));

				//stage
				$wp_stage= isset($_POST['stage']) ? $_POST['stage'] : '0';
				
				//status				
					$status1="";
					$status2="";
					$status3="";
				$wp_status = isset($_POST['status']) ? $_POST['status'] : '1';
					if ($wp_status==1) {$status1="checked";}
					if ($wp_status==2) {$status2="checked";}
					if ($wp_status==3) {$status3="checked";}					
					tpl_set_var("checked1", $status1);
					tpl_set_var("checked2", $status2);
					tpl_set_var("checked3", $status3);				
				//desc
				$wp_desc = isset($_POST['desc']) ? $_POST['desc'] : '';
				tpl_set_var('desc', htmlspecialchars($wp_desc, ENT_COMPAT, 'UTF-8'));
				
				if (isset($_POST['back']))
				{	
							tpl_redirect('editcache-test.php?cacheid=' . urlencode($cache_id));
							mysql_free_result($cache_rs);
							mysql_free_result($wp_rs);
							exit;
				}				
				
				if (isset($_POST['submitform']))
				{
					//check the entered data
					if ($sel_type =='4' || $$sel_type == '5') $wp_stage=0;
					//check coordinates
					if ($lat_h!='' || $lat_min!='')
					{
						if (!mb_ereg_match('^[0-9]{1,2}$', $lat_h))
						{
							tpl_set_var('lat_message', $error_coords_not_ok);
							$error = true;
							$lat_h_not_ok = true;
						}
						else
						{
							if (($lat_h >= 0) && ($lat_h < 90))
							{
								$lat_h_not_ok = false;
							}
							else
							{
								tpl_set_var('lat_message', $error_coords_not_ok);
								$error = true;
								$lat_h_not_ok = true;
							}
						}

						if (is_numeric($lat_min))
						{
							if (($lat_min >= 0) && ($lat_min < 60))
							{
								$lat_min_not_ok = false;
							}
							else
							{
								tpl_set_var('lat_message', $error_coords_not_ok);
								$error = true;
								$lat_min_not_ok = true;
							}
						}
						else
						{
							tpl_set_var('lat_message', $error_coords_not_ok);
							$error = true;
							$lat_min_not_ok = true;
						}

						$latitude = $lat_h + $lat_min / 60;
						if ($latNS == 'S') $latitude = -$latitude;

						if ($latitude == 0)
						{
							tpl_set_var('lon_message', $error_coords_not_ok);
							$error = true;
							$lat_min_not_ok = true;
						}
					}
					else
					{
						$latitude = NULL;
						$lat_h_not_ok = false;
						$lat_min_not_ok = false;
					}

					if ($lon_h!='' || $lon_min!='')
					{
						if (!mb_ereg_match('^[0-9]{1,3}$', $lon_h))
						{
							tpl_set_var('lon_message', $error_coords_not_ok);
							$error = true;
							$lon_h_not_ok = true;
						}
						else
						{
							if (($lon_h >= 0) && ($lon_h < 180))
							{
								$lon_h_not_ok = false;
							}
							else
							{
								tpl_set_var('lon_message', $error_coords_not_ok);
								$error = true;
								$lon_h_not_ok = true;
							}
						}

						if (is_numeric($lon_min))
						{
							if (($lon_min >= 0) && ($lon_min < 60))
							{
								$lon_min_not_ok = false;
							}
							else
							{
								tpl_set_var('lon_message', $error_coords_not_ok);
								$error = true;
								$lon_min_not_ok = true;
							}
						}
						else
						{
							tpl_set_var('lon_message', $error_coords_not_ok);
							$error = true;
							$lon_min_not_ok = true;
						}

						$longitude = $lon_h + $lon_min / 60;
						if ($lonEW == 'W') $longitude = -$longitude;

						if ($longitude == 0)
						{
							tpl_set_var('lon_message', $error_coords_not_ok);
							$error = true;
							$lon_min_not_ok = true;
						}
					}
					else
					{
						$longitude = NULL;
						$lon_h_not_ok = false;
						$lon_min_not_ok = false;
					}

					$lon_not_ok = $lon_min_not_ok || $lon_h_not_ok;
					$lat_not_ok = $lat_min_not_ok || $lat_h_not_ok;
					
					// stage only numeric					
					if (is_numeric($wp_stage))
						{
								$stage_not_ok = false;
							}
							else
							{
								tpl_set_var('stage_message', $stage_not_ok);
								$error = true;
								$stage_not_ok = true;
							}
					//desc
					if ($wp_desc == '')
					{
						tpl_set_var('desc_message', $descwp_not_ok_message);
						$error = true;
						$descwp_not_ok = true;
					}
					else
					{
						$descwp_not_ok = false;
					}
					//wp-type
					$type_not_ok = false;
					if ($sel_type == -1 )
					{
						tpl_set_var('type_message', $typewp_not_ok_message);
						$error = true;
						$type_not_ok = true;
					}
					
					//no errors?
					if (!($descwp_not_ok || $lon_not_ok || $lat_not_ok || $stage_not_ok || $type_not_ok))
					{
						//add record 
						sql("INSERT INTO `waypoints` (
													`wp_id`,
													`cache_id`,
													`longitude`,
													`latitude`,
													`type` ,
													`status` ,
													`stage` ,
													`desc`
												) VALUES (
													'', '&1', '&2', '&3', '&4', '&5', '&6', '&7')",
												$cache_id,
												$longitude,
												$latitude,
												$sel_type,
												$wp_status,
												$wp_stage,
												$wp_desc);
					
							tpl_redirect('editcache-test.php?cacheid=' . urlencode($cache_id));
					// end of insert to sql
					}else
					{
						tpl_set_var('general_message', $error_general);
					}
				
					// end submit
				}							
			mysql_free_result($cache_rs);
			mysql_free_result($wp_rs);
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
