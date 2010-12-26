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
			$tplname = 'myroutes_search';
			$user_id = $usr['userid'];

//*************************************************************************
// Find all the caches that appear with $distance from each point in the defined $route_id.
//*************************************************************************
function caches_along_route($route_id, $distance) {
$initial_cache_list = array();
$inter_cache_list = array();
$final_cache_list = array();

// Get caches where within the minimum bounding box of the route
// Actually, add the distance to the minimum bounding box
// In Oz, 1 degree is around 110km (close enough)
$bounds = route_info($route_id);
$bounds_min_lat = $bounds['lat_min'] - $distance/110;
$bounds_max_lat = $bounds['lat_max'] + $distance/110;
$bounds_min_lon = $bounds['lon_min'] - $distance/110;
$bounds_max_lon = $bounds['lon_max'] + $distance/110;
$query = "SELECT wp_oc waypoint, latitude lat, longitude lon "."FROM caches "."WHERE latitude>'$bounds_min_lat' ".
"AND latitude<'$bounds_max_lat' "."AND longitude>'$bounds_min_lon' "."AND longitude<'$bounds_max_lon' "."AND status = '1';";
$result=sql($query);
if ($result AND $count=gca_numrows($result)) {
for ( $i=0; $i<$count; $i++ ) {
$row = gca_fetch($result,$i);
$initial_cache_list[] =array("waypoint"=>$row['waypoint'],"lat"=>$row['lat'],"lon"=>$row['lon']);
}
$points = array();
$query = "SELECT * FROM route_points WHERE route_id ='$route_id' ORDER BY point_nr;";
$result = sql($query);
	if ( $result AND $count=gca_numrows($result) ) {
	for ( $i=0; $i<$count; $i++ ) {
	$row = fetch($result,$i);
	$points[] = array("lat"=>$row["lat"],"lon"=>$row["lon"]);}
	}
foreach ($initial_cache_list as $list) {
foreach ($points as $point) {
$route_distance =cache_distances($list["lat"],$list["lon"],$point["lat"],$point["lon"]);
if ( $route_distance <= $distance ) {
if ( !$inter_cache_list[$list['waypoint']] ) {
$final_cache_list[] = $list['waypoint'];
$inter_cache_list[$list['waypoint']] = $list['waypoint'];
break;
}
}
}
}
}
return $final_cache_list;
}
// end			
			
			
			
		
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
