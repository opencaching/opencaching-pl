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

   Unicode Reminder  ąść

		

	****************************************************************************/
	global $lang, $rootpath, $usr;

	if (!isset($rootpath)) $rootpath = '';

	//include template handling
	require_once($rootpath . 'lib/common.inc.php');
	require_once($rootpath . 'lib/calculation.inc.php');
	require_once($rootpath . 'lib/cache_icon.inc.php');
	require_once($stylepath . '/lib/icons.inc.php');

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
		
			//get user record
			$user_id = $usr['userid'];
			tpl_set_var('userid',$user_id);		
		
	if (isset($_REQUEST['logs']))
		{
			$logs = $_REQUEST['logs'];	
		} else {
		$logs =1;}
		
	//get the news
	$tplname = 'myneighborhood2';

       function cleanup_text($str)
        {

          $str = strip_tags($str, "<li>");
	  $from[] = '&nbsp;'; $to[] = ' ';
          $from[] = '<p>'; $to[] = '';
         $from[] = '\n'; $to[] = '';
         $from[] = '\r'; $to[] = '';
          $from[] = '</p>'; $to[] = "";
          $from[] = '<br>'; $to[] = "";
          $from[] = '<br />'; $to[] = "";
	 $from[] = '<br/>'; $to[] = "";
            
          $from[] = '<li>'; $to[] = " - ";
          $from[] = '</li>'; $to[] = "";
          
          $from[] = '&oacute;'; $to[] = 'o';
          $from[] = '&quot;'; $to[] = '"';
          $from[] = '&[^;]*;'; $to[] = '';
          
          $from[] = '&'; $to[] = '';
          $from[] = '\''; $to[] = '';
          $from[] = '"'; $to[] = '';
          $from[] = '<'; $to[] = '';
          $from[] = '>'; $to[] = '';
          $from[] = ']]>'; $to[] = ']] >';
	 $from[] = ''; $to[] = '';
              
          for ($i = 0; $i < count($from); $i++)
            $str = str_replace($from[$i], $to[$i], $str);
                                 
          return filterevilchars($str);
        }
        
	
        function filterevilchars($str)
	{
		return str_replace('[\\x00-\\x09|\\x0A-\\x0E-\\x1F]', '', $str);
	}
function get_zoom($latitude,$lonMin,$lonMax,$latMin,$latMax)
{
/* In the following code, px and py are the width of the map in the
webpage, latCenter represents the latitude of the center, and
latMax etc are the obvious parameters.  Then one reasonable choice
of the zoom (in javascript notation) is 
*/
$s = 1.35;
$px=350;
$py=350;
$latcCnter=$latitude;
$xZoom = -(log(($lonMax - $lonMin)/($px*$s))/log(2));
$yZoom = -(log((($latMax - $latMin)*(1/cos(($latcCnter*PI/180))))/($py*$s))/log(2));
$zoom = min(floor($xZoom),floor($yZoom)); 
return $zoom;
}
	
function get_marker_positions($latitude, $longitude,$radius)
{
	$markerpos = array();
	$markers = array();

	$rs = sql("
		SELECT SQL_BUFFER_RESULT `caches`.`cache_id`, `caches`.`longitude`, `caches`.`latitude`, `caches`.`type`
		FROM	`caches`,`local_caches`
		WHERE `caches`.`cache_id`=`local_caches`.`cache_id` AND
			`caches`.`type` != 6 AND
			`caches`.`status` = 1 AND
			`caches`.`date_hidden` <= NOW() AND
			`caches`.`date_created` <= NOW()
		ORDER BY IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) DESC, `caches`.`cache_id` DESC
		LIMIT 0, 10",$latitude, $longitude,$radius);

	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = sql_fetch_array($rs);
		$lat = $record['latitude'];
		$lon = $record['longitude'];
		$type = $record['type'];
		$markers[] = array('lat' => $lat, 'lon' => $lon, 'type' => $type);
	}

	$markerpos['plain_cache_num'] = count($markers);

	$rs = sql("
		SELECT SQL_BUFFER_RESULT `caches`.`cache_id`, `caches`.`longitude`, `caches`.`latitude`, `caches`.`type`
		FROM	`caches`, `local_caches`
		WHERE `caches`.`cache_id`=`local_caches`.`cache_id` AND
		`caches`.`date_hidden` >= curdate() AND
			`caches`.`type` = 6 AND
			`caches`.`status` = 1
		ORDER BY `caches`.`date_hidden` ASC
		LIMIT 0, 10",$latitude, $longitude,$radius);

	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = sql_fetch_array($rs);
		$lat = $record['latitude'];
		$lon = $record['longitude'];
		$type = $record['type'];
		$markers[] = array('lat' => $lat, 'lon' => $lon, 'type' => $type);
	}

	$markerpos['markers'] = $markers;

	return $markerpos;
}

function create_map_url($markerpos, $index,$latitude,$longitude)
{
	global $googlemap_key;

	$markers = $markerpos['markers'];
	$markers_str = "markers=color:blue|size:small|";
	$markers_ev_str = "&markers=color:orange|size:small|";
	$sel_marker_str = "";
	foreach ($markers as $i => $marker)
	{
		$lat = sprintf("%.3f", $marker['lat']);
		$lon = sprintf("%.3f", $marker['lon']);
		$type = strtoupper(typeToLetter($marker['type']));
		if (strcmp($type, 'E') == 0)
			if ($i != $index)
				$markers_ev_str .= "$lat,$lon|";
			else
				$sel_marker_str = "&markers=color:orange|label:$type|$lat,$lon|";
		else
			if ($i != $index)
				$markers_str .= "$lat,$lon|";
			else
				$sel_marker_str = "&markers=color:blue|label:$type|$lat,$lon|";
	}

	$google_map = "http://maps.google.com/maps/api/staticmap?center=".$latitude.",".$longitude."&size=350x350&maptype=roadmap&key=".$googlemap_key."&sensor=false&".$markers_str.$markers_ev_str.$sel_marker_str;

	return $google_map;
}


	
$latitude =sqlValue("SELECT `latitude` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);
$longitude =sqlValue("SELECT `longitude` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);

if ($longitude==NULL && $latitude==NULL) {tpl_set_var('info','<br><div class="notice" style="line-height: 1.4em;font-size: 120%;"><b>Nie masz ustawionych współrzędnych Twojej okolicy. Możesz to zrobić w swoim <a href="myprofile.php?action=change">profilu</a>. Jeśli chcesz mieć inny promien niż domyślny 25 km ustaw go w swoim profilu opcja: "Powiadamianie". Poniżej przykład dla współrzędnych ustawionych systemowo.</b></div><br>');} else { tpl_set_var('info','');}

if ($latitude==NULL) $lat=52.24522;
if ($longitude==NULL) $lon=21.00442;

$distance =sqlValue("SELECT `notify_radius` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);
if ($distance==0) $distance=25;
$distance_unit = 'km';
$radius=$distance;	

			//get the users home coords
//			$rs_coords = sql("SELECT `latitude` `lat`, `longitude` `lon` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
//			$record_coords = sql_fetch_array($rs_coords);
	
				$lat = $latitude;
				$lon = $longitude;
				$lon_rad = $lon * 3.14159 / 180;   
        			$lat_rad = $lat * 3.14159 / 180; 
							
							
							//all target caches are between lat - max_lat_diff and lat + max_lat_diff
							$max_lat_diff = $distance / 111.12;
							
							//all target caches are between lon - max_lon_diff and lon + max_lon_diff
							//TODO: check!!!
							$max_lon_diff = $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180 )) * 6378  * 3.14159);
							sql('DROP TEMPORARY TABLE IF EXISTS `local_caches`');							
							sql('CREATE TEMPORARY TABLE local_caches ENGINE=MEMORY 
													SELECT 
														(' . getSqlDistanceFormula($lon, $lat, $distance, $multiplier[$distance_unit]) . ') AS `distance`,
														`caches`.`cache_id` AS `cache_id`,
														`caches`.`wp_oc` AS `wp_oc`,
														`caches`.`type` AS `type`,
														`caches`.`name` AS `name`
													FROM `caches` FORCE INDEX (`latitude`)
													WHERE `longitude` > ' . ($lon - $max_lon_diff) . ' 
														AND `longitude` < ' . ($lon + $max_lon_diff) . ' 
														AND `latitude` > ' . ($lat - $max_lat_diff) . ' 
														AND `latitude` < ' . ($lat + $max_lat_diff) . '
													HAVING `distance` < ' . $distance);
							sql('ALTER TABLE local_caches ADD PRIMARY KEY ( `cache_id` )');

			


	// Read coordinates of the newest caches
	$markerpositions = get_marker_positions($latitude, $longitude,$radius);
	// Generate include file for map with new caches
	$file_content = '<img src="' . create_map_url($markerpositions, -1,$latitude,$longitude) . '" basesrc="' . create_map_url($markerpositions, -1,$latitude,$longitude) . '" id="main-cachemap" name="main-cachemap" alt="{{map}}" />';
	$n_file = fopen($dynstylepath . "local_cachemap.inc.php", 'w');
	fwrite($n_file, $file_content);
	fclose($n_file);

	//start_newcaches.include
	$rs =sql("SELECT SQL_BUFFER_RESULT `user`.`user_id` `user_id`,
				`user`.`username` `username`,
				`caches`.`cache_id` `cache_id`,
				`caches`.`name` `name`,
				`caches`.`longitude` `longitude`,
				`caches`.`latitude` `latitude`,
				`caches`.`date_hidden` `date_hidden`,
				`caches`.`date_created` `date_created`,
				IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) AS `date`,
				`caches`.`country` `country`,
				`caches`.`difficulty` `difficulty`,
				`caches`.`terrain` `terrain`,
				`cache_type`.`icon_large` `icon_large`
        FROM `caches`, `user`, `cache_type`,`local_caches`
        WHERE `caches`.`cache_id`=`local_caches`.`cache_id` AND
			`caches`.`user_id`=`user`.`user_id`
			  AND `caches`.`type`!=6
			  AND `caches`.`status`=1
			  AND `caches`.`type`=`cache_type`.`id`
				AND `caches`.`date_hidden` <= NOW() 
				AND `caches`.`date_created` <= NOW() 
			ORDER BY IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) DESC, `caches`.`cache_id` DESC
			LIMIT 0 , 10");

	if (mysql_num_rows($rs) == 0)
	{
		$file_content = "<p>&nbsp;&nbsp;&nbsp;&nbsp;<b>Nie ma najnowiszych skrzynek w tej okolicy</b></p><br>";
	}
	else
	{			
	
	$cacheline =	'<li class="newcache_list_multi" style="margin-bottom:8px;">' .
			'<img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" />&nbsp;{date}&nbsp;' .
			'<a id="newcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}" onmouseover="Lite({nn})" onmouseout="Unlite()" maphref="{smallmapurl}">{cachename}</a>&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" title="user" />&nbsp;&nbsp;<a class="links" href="viewprofile.php?userid={userid}">{username}</a><br/><b><p class="content-title-noshade">{kraj} {dziubek} {woj}</p></b>';
	
	$file_content = '<ul style="font-size: 11px;">';
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = sql_fetch_array($rs);

		$loc = coordToLocation($record['latitude'], $record['longitude']);
		
		$cacheicon = 'tpl/stdstyle/images/'.getSmallCacheIcon($record['icon_large']);
	
		$thisline = $cacheline;
		$thisline = mb_ereg_replace('{nn}', $i, $thisline);
		$thisline = mb_ereg_replace('{kraj}',$loc['kraj'], $thisline);
		$thisline = mb_ereg_replace('{woj}',$loc['woj'], $thisline);
		$thisline = mb_ereg_replace('{miasto}',$loc['miasto'], $thisline);
		$thisline = mb_ereg_replace('{dziubek}',$loc['dziubek'], $thisline);
		$thisline = mb_ereg_replace('{date}', htmlspecialchars(date("d-m-Y", strtotime($record['date'])), ENT_COMPAT, 'UTF-8'), $thisline);
		$thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
		$thisline = mb_ereg_replace('{cache_count}',$i, $thisline);
		$thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
		$thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
		$thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
		$thisline = mb_ereg_replace('{locationstring}', $locationstring, $thisline);
		$thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);
		$thisline = mb_ereg_replace('{smallmapurl}', create_map_url($markerpositions, $i,$latitude,$longitude), $thisline);

		$file_content .= $thisline . "\n";
		
	}
}
	$file_content .= '</ul>';

	tpl_set_var('new_caches',$file_content);		
	mysql_free_result($rs);

	//nextevents.include
	
		$rss =sql("SELECT SQL_BUFFER_RESULT `user`.`user_id` `user_id`,
				`user`.`username` `username`,
				`caches`.`cache_id` `cache_id`,
				`caches`.`name` `name`,
				`caches`.`longitude` `longitude`,
				`caches`.`latitude` `latitude`,
				`caches`.`date_hidden` `date_hidden`,
				`caches`.`date_created` `date_created`,
				`caches`.`country` `country`,
				`caches`.`difficulty` `difficulty`,
				`caches`.`terrain` `terrain`,
				`cache_type`.`icon_large` `icon_large`
        FROM `caches`, `user`, `cache_type`,`local_caches`
        WHERE `caches`.`cache_id`=`local_caches`.`cache_id` AND
		`caches`.`user_id`=`user`.`user_id`
			  AND `caches`.`type`=6
			  AND `caches`.`status`=1
			  AND `caches`.`type`=`cache_type`.`id`
				AND `caches`.`date_hidden` >= curdate()
			ORDER BY `date_hidden` ASC
			LIMIT 0 , 10");



	$file_content = '';
	if (mysql_num_rows($rss) == 0)
	{
		$file_content = "<p>&nbsp;&nbsp;&nbsp;&nbsp;<b>".tr('list_of_events_is_empty')."</b></p><br>";
	}
	else
	{
		$cacheline = '<li class="newcache_list_multi" style="margin-bottom:8px;"><img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" />&nbsp;{date}&nbsp;<a id="newcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}" onmouseover="Lite({nn})" onmouseout="Unlite()" maphref="{smallmapurl}">{cachename}</a>&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" title="user" />&nbsp;&nbsp;<a class="links" href="viewprofile.php?userid={userid}">{username}</a><br/><b><p class="content-title-noshade">{kraj} {dziubek} {woj}</p></b></li>';
		$file_content = '<ul style="font-size: 11px;">';
		for ($i = 0; $i < mysql_num_rows($rss); $i++)
		{
			$record = sql_fetch_array($rss);
			$loc = coordToLocation($record['latitude'], $record['longitude']);
		
			$thisline = $cacheline;
			$thisline = mb_ereg_replace('{nn}', $i + $markerpositions['plain_cache_num'], $thisline);
			$thisline = mb_ereg_replace('{kraj}',$loc['kraj'], $thisline);
			$thisline = mb_ereg_replace('{woj}',$loc['woj'], $thisline);
			$thisline = mb_ereg_replace('{miasto}',$loc['miasto'], $thisline);
			$thisline = mb_ereg_replace('{dziubek}',$loc['dziubek'], $thisline);
			$thisline = mb_ereg_replace('{date}', htmlspecialchars(date("d-m-Y", strtotime($record['date_hidden'])), ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
			$thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
			$thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{locationstring}', $locationstring, $thisline);
			$thisline = mb_ereg_replace('{cacheicon}', 'tpl/stdstyle/images/cache/22x22-event.png', $thisline);
			$thisline = mb_ereg_replace('{smallmapurl}', create_map_url($markerpositions, $i + $markerpositions['plain_cache_num'],$latitude,$longitude), $thisline);

			$file_content .= $thisline . "\n";
		}
		$file_content .= '</ul>';

	}

	tpl_set_var('new_events',$file_content);
	mysql_free_result($rss);
	
	
	//nextevents.include
	
$rsl = sql("SELECT SQL_BUFFER_RESULT cache_logs.id, cache_logs.cache_id AS cache_id,
	                          cache_logs.type AS log_type,
	                          cache_logs.date AS log_date,
				   cache_logs.text AS log_text,
				  cache_logs.text_html AS text_html,
	                          local_caches.name AS cache_name,
	                          user.username AS user_name,
							  user.user_id AS user_id,
							  local_caches.wp_oc AS wp_name,
							  local_caches.type AS cache_type,
							  cache_type.icon_small AS cache_icon_small,
							  log_types.icon_small AS icon_small,
							  IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`,
							COUNT(gk_item.id) AS geokret_in
							FROM 
								(cache_logs INNER JOIN local_caches ON (local_caches.cache_id = cache_logs.cache_id)) 
								INNER JOIN user ON (cache_logs.user_id = user.user_id) 
								INNER JOIN log_types ON (cache_logs.type = log_types.id) 
								INNER JOIN cache_type ON (local_caches.type = cache_type.id) 
								LEFT JOIN `cache_rating` ON (`cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`)
								LEFT JOIN	gk_item_waypoint ON (gk_item_waypoint.wp = local_caches.wp_oc)
								LEFT JOIN	gk_item ON (gk_item.id = gk_item_waypoint.id AND
							gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5)
							WHERE	cache_logs.deleted=0
							GROUP BY cache_logs.id
							ORDER BY cache_logs.date_created DESC LIMIT 0 , 10");

	$file_content = '';


	if (mysql_num_rows($rsl) == 0)
	{
		$file_content = "<p>&nbsp;&nbsp;&nbsp;&nbsp;<b>Nie ma najnowszych wpisów w logach</b></p><br>";
	}
	else
	{
		$cacheline = '<li class="newcache_list_multi" style="margin-bottom:8px;"><img src="{gkicon}" class="icon16" alt="" title="gk" />&nbsp;&nbsp;<img src="{rateicon}" class="icon16" alt="" title="rate" />&nbsp;&nbsp;<img src="{logicon}" class="icon16" alt="" title="log" />&nbsp;&nbsp;<a id="newcache{nn}" class="links" href="viewcache.php?cacheid={cacheid}" onmouseover="Lite({nn})" onmouseout="Unlite()" maphref="{smallmapurl}"><img src="{cacheicon}" class="icon16" alt="Cache" title="Cache" /></a>&nbsp;{date}&nbsp;<a id="newlog{nn}" class="links" href="viewlogs.php?logid={logid}" onmouseover="Tip(\'{log_text}\', PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()">{cachename}</a>&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" title="user" />&nbsp;&nbsp;<a class="links" href="viewprofile.php?userid={userid}">{username}</a><br/></li>';
		$file_content = '<ul style="font-size: 11px;">';
		for ($i = 0; $i < mysql_num_rows($rsl); $i++)
		{
			$log_record = sql_fetch_array($rsl);	
//			$loc = coordToLocation($record['latitude'], $record['longitude']);			
			$thisline = $cacheline;
//			$thisline = mb_ereg_replace('{nn}', $i + $markerpositions['plain_cache_num'], $thisline);
//			$thisline = mb_ereg_replace('{kraj}',$loc['kraj'], $thisline);
//			$thisline = mb_ereg_replace('{woj}',$loc['woj'], $thisline);
//			$thisline = mb_ereg_replace('{miasto}',$loc['miasto'], $thisline);
//			$thisline = mb_ereg_replace('{dziubek}',$loc['dziubek'], $thisline);

			if ( $log_record['geokret_in'] !='0')
					{
								$thisline = mb_ereg_replace('{gkicon}',"images/gk.png", $thisline);
					}
					else
					{
					$thisline = mb_ereg_replace('{gkicon}',"images/rating-star-empty.png", $thisline);
					}					
				
				        //$rating_picture
				if ($log_record['recommended'] == 1 && $log_record['log_type']==1) 
					{
					$thisline = mb_ereg_replace('{rateicon}',"images/rating-star.png", $thisline);					}
					else
					{
					$thisline = mb_ereg_replace('{rateicon}',"images/rating-star-empty.png", $thisline);}	
			$thisline = mb_ereg_replace('{date}', htmlspecialchars(date("d-m-Y", strtotime($log_record['log_date'])), ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{cacheid}', urlencode($log_record['cache_id']), $thisline);
			$thisline = mb_ereg_replace('{cachename}', htmlspecialchars($log_record['cache_name'], ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{userid}', urlencode($log_record['user_id']), $thisline);
			$thisline = mb_ereg_replace('{logid}', htmlspecialchars($log_record['id'], ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{username}', htmlspecialchars($log_record['user_name'], ENT_COMPAT, 'UTF-8'), $thisline);
//			$thisline = mb_ereg_replace('{locationstring}', $locationstring, $thisline);
				$data = '<b>'.$log_record['user_name'].'</b>:<br/>';
				$data .= cleanup_text(str_replace("\r\n", " ", $log_record['log_text']));
				$log_text= str_replace("\n", " ",$data);
			$thisline = mb_ereg_replace('{log_text}', $log_text, $thisline);
			$thisline = mb_ereg_replace('{logicon}', "tpl/stdstyle/images/". $log_record['icon_small'], $thisline);
			$thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);
			$thisline = mb_ereg_replace('{smallmapurl}', create_map_url($markerpositions, $i + $markerpositions['plain_cache_num'],$latitude,$longitude), $thisline);

			$file_content .= $thisline . "\n";
		}
		$file_content .= '</ul>';
		}
		tpl_set_var('new_logs',$file_content);
				
	}	
}
//make the template and send it out
tpl_BuildTemplate();
?>
