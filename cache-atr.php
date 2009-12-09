<?php
/***************************************************************************
																./cache-atr.php
															-------------------
		begin                : Mon June 14 2004
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*
	***************************************************************************/
 
	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	
	//Preprocessing
	if ($error == false)
	{
		$tplname = 'cache-atr';
		tpl_set_var('preview_attributes', tr('preview_attributes'));
		tpl_set_var('toxic_plants', tr('toxic_plants'));
		tpl_set_var('thorns', tr('thorns'));
		tpl_set_var('cito', tr('cito'));
		tpl_set_var('dogs_allowed', tr('dogs_allowed'));
		tpl_set_var('dogs_not_allowed', tr('dogs_not_allowed'));
		tpl_set_var('all_year', tr('all_year'));
		tpl_set_var('not_all_year', tr('not_all_year'));
		tpl_set_var('accessible_winter', tr('accessible_winter'));
		tpl_set_var('not_accessible_winter', tr('not_accessible_winter'));
		tpl_set_var('depends_weather', tr('depends_weather'));
		tpl_set_var('bikes_allowed', tr('bikes_allowed'));
		tpl_set_var('bikes_not_allowed', tr('bikes_not_allowed'));
		tpl_set_var('cars_allowed', tr('cars_allowed'));
		tpl_set_var('cave', tr('cave'));
		tpl_set_var('outside_path_allowed', tr('outside_path_allowed'));
		tpl_set_var('outside_path_not_allowed', tr('outside_path_not_allowed'));
		tpl_set_var('drive_outside_road_allowed', tr('drive_outside_road_allowed'));
		tpl_set_var('drive_outside_road_not_allowed', tr('drive_outside_road_not_allowed'));
		tpl_set_var('water_cache', tr('water_cache'));
		tpl_set_var('wading', tr('wading'));
		tpl_set_var('swimming_pool_nearby', tr('swimming_pool_nearby'));
		tpl_set_var('warning_mud', tr('warning_mud'));
		tpl_set_var('fire_danger', tr('fire_danger'));
		tpl_set_var('travel_bug_hotel', tr('travel_bug_hotel'));
		tpl_set_var('handicap_allowed', tr('handicap_allowed'));
		tpl_set_var('handicap_not_allowed', tr('handicap_not_allowed'));
		tpl_set_var('can_drive_in', tr('can_drive_in'));
		tpl_set_var('no_shorts', tr('no_shorts'));
		tpl_set_var('hunting_area', tr('hunting_area'));
		tpl_set_var('danger_area', tr('danger_area'));
		tpl_set_var('toilet_nearby', tr('toilet_nearby'));
		tpl_set_var('no_toilet_nearby', tr('no_toilet_nearby'));
		tpl_set_var('drink_water', tr('drink_water'));
		tpl_set_var('hiking', tr('hiking'));
		tpl_set_var('take_pencil', tr('take_pencil'));
		tpl_set_var('easy_climb', tr('easy_climb'));
		tpl_set_var('medium_climb', tr('medium_climb'));
		tpl_set_var('extreme_climb', tr('extreme_climb'));
		tpl_set_var('horse_path', tr('horse_path'));
		tpl_set_var('check_tide', tr('check_tide'));
		tpl_set_var('take_compass', tr('take_compass'));
		tpl_set_var('sights', tr('sights'));
		tpl_set_var('skeeter', tr('skeeter'));
		tpl_set_var('tick', tr('tick'));
		tpl_set_var('snake', tr('snake'));
		tpl_set_var('generate_code', tr('generate_code'));
		tpl_set_var('9keycom', tr('9keycom'));
	}
	//make the template and send it out

	tpl_BuildTemplate();
?>
