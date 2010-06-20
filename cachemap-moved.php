<?php
function getMapType($value)
{
	switch( $value ) 
	{
		case 0:
			return "G_NORMAL_MAP";
		case 1:
			return "G_SATELLITE_MAP";
		case 2:
			return "G_HYBRID_MAP";
		case 3:
			return "G_PHYSICAL_MAP";
		default:
			return "G_NORMAL_MAP";
	}
}



require_once('./lib/common.inc.php');
$tplname = 'cachemap-moved';
tpl_set_var('bodyMod', ' onload="initialize()" onunload="GUnload()"');
//tpl_set_var('BodyMod', ' onload="load()" onunload="GUnload()"');
global $usr;
global $get_userid;
global $filter;
global $caches_list;
global $language;
global $lang;

$cache_id = '';

$get_cacheid = $_REQUEST['cacheid'];
//user logged in?
	session_start();

	
	tpl_set_var('sc', intval($_GET['sc']));
	
	if( $get_cacheid == '')
		$cache_id =0;
	else 
		$cache_id = $get_cacheid;

	$cachename = sqlValue("SELECT `name` FROM `caches` WHERE `cache_id`='" . sql_escape($cache_id) . "'",0); 
	tpl_set_var('cachename', $cachename);		
	tpl_set_var('cacheid', $cache_id);

			$rsc = sql("SELECT `cache_moved`.`latitude` `latitude`,
			                   `cache_moved`.`longitude` `longitude`
					FROM `cache_moved` 
					WHERE `cache_moved`.`cache_id`='&1'
					AND `cache_moved`.`longitude` IS NOT NULL AND `cache_moved`.`latitude` IS NOT NULL	
			         ORDER BY `cache_moved`.`date` ASC
			            ", $cache_id);

			 $trasa ="var polyline = new GPolyline([";
			 $latlongl ="var latlongl = [";
			for ($i = 0; $i < mysql_num_rows($rsc); $i++)
			{
				$record = sql_fetch_array($rsc);
				$y=$record['longitude'];
				$x=$record['latitude'];

		$trasa .="new GLatLng(" . $x . "," . $y . "),";
		$latlongl .="new GLatLng(" . $x . "," . $y . "),";
		}
		 $latlongl .="];\n\n";
		 $trasa .="],\"#004080\", 5);\n map0.addOverlay(polyline);\n\n";
		tpl_set_var('latlongl', $latlongl);
		tpl_set_var('route', $trasa);		

			$rscp = sql("SELECT `cache_moved`.`latitude` `latitude`,
			                   `cache_moved`.`longitude` `longitude`
					FROM `cache_moved` 
					WHERE `cache_moved`.`cache_id`='&1'
					AND `cache_moved`.`longitude` IS NOT NULL AND `cache_moved`.`latitude` IS NOT NULL	
			         ORDER BY `cache_moved`.`date` ASC
			            ", $cache_id);
			$point="";
			$nrows=mysql_num_rows($rscp);
			for ($i = 0; $i < mysql_num_rows($rscp); $i++)
			{
				$record = sql_fetch_array($rscp);
				$y=$record['longitude'];
				$x=$record['latitude'];

			$point .=" var point = new GLatLng(" . $x . "," . $y . ");\n";
			$icon="icon1";
			if ($i==0) $icon="icon2";
			if ($i==$nrows-1) {$icon="icon3";}
			$number=$i+1;
			$point .="var marker".$number." = new GMarker(point,".$icon."); map0.addOverlay(marker".$number.");\n\n";
			}

		tpl_set_var('points', $point);	



	$smallestLat = sqlValue("SELECT `cache_moved`.`latitude` `latitude` FROM `cache_moved` WHERE `cache_id`='" . sql_escape($cache_id) . "' ORDER BY `cache_moved`.`latitude` ASC LIMIT 1", 0);
	$largestLat = sqlValue("SELECT `cache_moved`.`latitude` `latitude` FROM `cache_moved` WHERE `cache_id`='" . sql_escape($cache_id) . "' ORDER BY `cache_moved`.`latitude` DESC LIMIT 1 ", 0);
	$smallestLon = sqlValue("SELECT `cache_moved`.`longitude` `longitude` FROM `cache_moved` WHERE `cache_id`='" . sql_escape($cache_id) . "' ORDER BY `cache_moved`.`longitude` ASC LIMIT 1", 0);
	$largestLon = sqlValue("SELECT `cache_moved`.`longitude` `longitude` FROM `cache_moved` WHERE `cache_id`='" . sql_escape($cache_id) . "' ORDER BY `cache_moved`.`longitude` DESC LIMIT 1", 0);
	$mapcenterLat = ($smallestLat + $largestLat)/2;
	$mapcenterLon = ($smallestLon + $largestLon)/2; 

	tpl_set_var('mapcenterLat', $mapcenterLat);
	tpl_set_var('mapcenterLon', $mapcenterLon);


//			tpl_set_var('zoom', 11);

	
//	tpl_set_var('doopen', $_REQUEST['cacheid']?"true":"false");
	tpl_set_var('doopen', "false");
	tpl_set_var('coords', $coordsXY);
	tpl_set_var('username', $record[username]);
	
	tpl_set_var("map_type", "G_NORMAL_MAP");
	
	tpl_set_var('cachemap_mapper', $cachemap_mapper);



	/*SET YOUR MAP CODE HERE*/
	tpl_set_var('cachemap_header', '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$googlemap_key.'" type="text/javascript"></script>');
	tpl_BuildTemplate(); 

?>
