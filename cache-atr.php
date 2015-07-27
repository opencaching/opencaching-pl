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
		tpl_set_var('preview_attributes', $language[$lang]['preview_attributes']);
		tpl_set_var('toxic_plants', $language[$lang]['toxic_plants']);
		tpl_set_var('thorns', $language[$lang]['thorns']);
		tpl_set_var('cito', $language[$lang]['cito']);
		tpl_set_var('dogs_allowed', $language[$lang]['dogs_allowed']);
		tpl_set_var('dogs_not_allowed', $language[$lang]['dogs_not_allowed']);
		tpl_set_var('all_year', $language[$lang]['all_year']);
		tpl_set_var('not_all_year', $language[$lang]['not_all_year']);
		tpl_set_var('accessible_winter', $language[$lang]['accessible_winter']);
		tpl_set_var('not_accessible_winter', $language[$lang]['not_accessible_winter']);
		tpl_set_var('depends_weather', $language[$lang]['depends_weather']);
		tpl_set_var('bikes_allowed', $language[$lang]['bikes_allowed']);
		tpl_set_var('bikes_not_allowed', $language[$lang]['bikes_not_allowed']);
		tpl_set_var('cars_allowed', $language[$lang]['cars_allowed']);
		tpl_set_var('cave', $language[$lang]['cave']);
		tpl_set_var('outside_path_allowed', $language[$lang]['outside_path_allowed']);
		tpl_set_var('outside_path_not_allowed', $language[$lang]['outside_path_not_allowed']);
		tpl_set_var('drive_outside_road_allowed', $language[$lang]['drive_outside_road_allowed']);
		tpl_set_var('drive_outside_road_not_allowed', $language[$lang]['drive_outside_road_not_allowed']);
		tpl_set_var('water_cache', $language[$lang]['water_cache']);
		tpl_set_var('wading', $language[$lang]['wading']);
		tpl_set_var('swimming_pool_nearby', $language[$lang]['swimming_pool_nearby']);
		tpl_set_var('warning_mud', $language[$lang]['warning_mud']);
		tpl_set_var('fire_danger', $language[$lang]['fire_danger']);
		tpl_set_var('travel_bug_hotel', $language[$lang]['travel_bug_hotel']);
		tpl_set_var('handicap_allowed', $language[$lang]['handicap_allowed']);
		tpl_set_var('handicap_not_allowed', $language[$lang]['handicap_not_allowed']);
		tpl_set_var('can_drive_in', $language[$lang]['can_drive_in']);
		tpl_set_var('no_shorts', $language[$lang]['no_shorts']);
		tpl_set_var('hunting_area', $language[$lang]['hunting_area']);
		tpl_set_var('danger_area', $language[$lang]['danger_area']);
		tpl_set_var('toilet_nearby', $language[$lang]['toilet_nearby']);
		tpl_set_var('no_toilet_nearby', $language[$lang]['no_toilet_nearby']);
		tpl_set_var('drink_water', $language[$lang]['drink_water']);
		tpl_set_var('hiking', $language[$lang]['hiking']);
		tpl_set_var('take_pencil', $language[$lang]['take_pencil']);
		tpl_set_var('easy_climb', $language[$lang]['easy_climb']);
		tpl_set_var('medium_climb', $language[$lang]['medium_climb']);
		tpl_set_var('extreme_climb', $language[$lang]['extreme_climb']);
		tpl_set_var('horse_path', $language[$lang]['horse_path']);
		tpl_set_var('check_tide', $language[$lang]['check_tide']);
		tpl_set_var('take_compass', $language[$lang]['take_compass']);
		tpl_set_var('sights', $language[$lang]['sights']);
		tpl_set_var('skeeter', $language[$lang]['skeeter']);
		tpl_set_var('tick', $language[$lang]['tick']);
		tpl_set_var('snake', $language[$lang]['snake']);
		tpl_set_var('generate_code', $language[$lang]['generate_code']);
		tpl_set_var('9keycom', $language[$lang]['9keycom']);
	}
	//make the template and send it out

	tpl_BuildTemplate();
?>