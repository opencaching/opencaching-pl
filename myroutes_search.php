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

 ****************************************************************************/

	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require_once('./lib/cache_icon.inc.php');
	require_once($rootpath . 'lib/caches.inc.php');
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
			$tplname = 'myroutes_search';
			$user_id = $usr['userid'];
			
			if (isset($_REQUEST['routeid']))
			{
			$route_id = $_REQUEST['routeid'];			
			}
			if (isset($_POST['routeid'])){
			$route_id = $_POST['routeid'];}
			
			if (isset($_POST['distance'])){
			$distance = $_POST['distance'];}
			
			$route_rs = sql("SELECT `user_id`,`name`, `description`, `radius` FROM `routes` WHERE `route_id`='&1'", $route_id);
			$record = sql_fetch_array($route_rs);	
			$distance=$record['radius'];
			tpl_set_var('route_name',$record['name']);
			tpl_set_var('distance',$record['radius']);
			tpl_set_var('routeid',$route_id);
			
			$options['cachetype'] = isset($_POST['cachetype']) ? $_POST['cachetype'] : '111111111';

			$options['cachesize_1'] = isset($_POST['cachesize_1']) ? $_POST['cachesize_1'] : 1;
			$options['cachesize_2'] = isset($_POST['cachesize_2']) ? $_POST['cachesize_2'] : 1;
			$options['cachesize_3'] = isset($_POST['cachesize_3']) ? $_POST['cachesize_3'] : 1;
			$options['cachesize_4'] = isset($_POST['cachesize_4']) ? $_POST['cachesize_4'] : 1;
			$options['cachesize_5'] = isset($_POST['cachesize_5']) ? $_POST['cachesize_5'] : 1;
			$options['cachesize_6'] = isset($_POST['cachesize_6']) ? $_POST['cachesize_6'] : 1;
			$options['cachesize_7'] = isset($_POST['cachesize_7']) ? $_POST['cachesize_7'] : 1;

			$options['cachevote_1'] = isset($_POST['cachevote_1']) ? $_POST['cachevote_1'] : '';
			$options['cachevote_2'] = isset($_POST['cachevote_2']) ? $_POST['cachevote_2'] : '';
			$options['cachenovote'] = isset($_POST['cachenovote']) ? $_POST['cachenovote'] : 1;
			
			$options['cachedifficulty_1'] = isset($_POST['cachedifficulty_1']) ? $_POST['cachedifficulty_1'] : '';
			$options['cachedifficulty_2'] = isset($_POST['cachedifficulty_2']) ? $_POST['cachedifficulty_2'] : '';

			$options['cacheterrain_1'] = isset($_POST['cacheterrain_1']) ? $_POST['cacheterrain_1'] : '';
			$options['cacheterrain_2'] = isset($_POST['cacheterrain_2']) ? $_POST['cacheterrain_2'] : '';
			
			$options['cacherating'] = isset($_POST['cacherating']) ? $_POST['cacherating'] : 0;
			
				// additional options
				if(!isset($options['f_userowner'])) $options['f_userowner']='0';
				if($options['f_userowner'] != 0) { $sql_where[] = '`caches`.`user_id`!=\'' . $usr['userid'] .'\''; }

				if(!isset($options['f_userfound'])) $options['f_userfound']='0';
				if($options['f_userfound'] != 0) 
				{ 
					$sql_where[] = '`caches`.`cache_id` NOT IN (SELECT `cache_logs`.`cache_id` FROM `cache_logs` WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`user_id`=\'' . sql_escape($usr['userid']) . '\' AND `cache_logs`.`type` IN (1, 7))';
				}

				if(!isset($options['f_inactive'])) $options['f_inactive']='0';
				if($options['f_inactive'] != 0)  $sql_where[] = '`caches`.`status`=1';

				if(isset($usr))
				{
					if(!isset($options['f_ignored'])) $options['f_ignored']='0';
					if($options['f_ignored'] != 0)
					{
						$sql_where[] = '`caches`.`cache_id` NOT IN (SELECT `cache_ignore`.`cache_id` FROM `cache_ignore` WHERE `cache_ignore`.`user_id`=\'' . sql_escape($usr['userid']) . '\')';
					}
				}

				if(!isset($options['cachetype'])) $options['cachetype']='111111111';
				$pos = strpos($options['cachetype'], '0');

				//echo $options['cachetype'];

				if($pos !== false)
				{
					$c_type = array();
					for ($i=0; $i<strlen($options['cachetype']);$i++){
						if ($options['cachetype'][$i] == '1') {
							$c_type[] = $i+1;
						}
					}
					
					if (count($c_type) >= 1) {
						$sql_where[] = '`caches`.`type` IN (' . sql_escape(implode(",", $c_type)) . ')';
					}
				}
				if(isset($options['cache_attribs']) && count($options['cache_attribs']) > 0)
				{
					for($i=0; $i < count($options['cache_attribs']); $i++)
					{
						if($options['cache_attribs'][$i] == 99) // special password attribute case
							$sql_where[] = '`caches`.`logpw` != ""';
						else {
							$sql_from[] = '`caches_attributes` `a' . ($options['cache_attribs'][$i]+0) . '`';
							$sql_where[] = '`a' . ($options['cache_attribs'][$i]+0) . '`.`cache_id`=`caches`.`cache_id`';
							$sql_where[] = '`a' . ($options['cache_attribs'][$i]+0) . '`.`attrib_id`=' . ($options['cache_attribs'][$i]+0);
						}
					}
				}

				if(isset($options['cache_attribs_not']) && count($options['cache_attribs_not']) > 0)
				{
					for($i=0; $i < count($options['cache_attribs_not']); $i++)
					{
						if($options['cache_attribs_not'][$i] == 99) // special password attribute case
							$sql_where[] = '`caches`.`logpw` = ""';
						else
							$sql_where[] = 'NOT EXISTS (SELECT `caches_attributes`.`cache_id` FROM `caches_attributes` WHERE `caches_attributes`.`cache_id`=`caches`.`cache_id` AND `caches_attributes`.`attrib_id`=\'' . sql_escape($options['cache_attribs_not'][$i]) . '\')';
					}
				}
				
				$cachesize = array();
				
				if (isset($options['cachesize_1']) && ($options['cachesize_1'] == '1')) { $cachesize[] = '1'; }
				if (isset($options['cachesize_2']) && ($options['cachesize_2'] == '1')) { $cachesize[] = '2'; }
				if (isset($options['cachesize_3']) && ($options['cachesize_3'] == '1')) { $cachesize[] = '3'; }
				if (isset($options['cachesize_4']) && ($options['cachesize_4'] == '1')) { $cachesize[] = '4'; }
				if (isset($options['cachesize_5']) && ($options['cachesize_5'] == '1')) { $cachesize[] = '5'; }
				if (isset($options['cachesize_6']) && ($options['cachesize_6'] == '1')) { $cachesize[] = '6'; }
				if (isset($options['cachesize_7']) && ($options['cachesize_7'] == '1')) { $cachesize[] = '7'; }
				
				if ((sizeof($cachesize) > 0) && (sizeof($cachesize) < 7)) {
					$sql_where[] = '`caches`.`size` IN (' . implode(' , ', $cachesize) . ')';					
				}

				if(!isset($options['cachevote_1']) && !isset($options['cachevote_2'])) {
					$options['cachevote_1']='';	
					$options['cachevote_2']='';	
				}
				if( ( ($options['cachevote_1'] != '') && ($options['cachevote_2'] != '') ) && ( ($options['cachevote_1'] != '0') || ($options['cachevote_2'] != '6') ) && ( (!isset($options['cachenovote'])) || ($options['cachenovote'] != '1') ) )
				{
					$sql_where[] = '`caches`.`score` BETWEEN \'' . sql_escape($options['cachevote_1']) . '\' AND \'' . sql_escape($options['cachevote_2']) . '\' AND `caches`.`votes` > 3';
				} else if ( ($options['cachevote_1'] != '') && ($options['cachevote_2'] != '') && ( ($options['cachevote_1'] != '0') || ($options['cachevote_2'] != '6') ) && isset($options['cachenovote']) && ($options['cachenovote'] == '1') )  {
					$sql_where[] = '((`caches`.`score` BETWEEN \'' . sql_escape($options['cachevote_1']) . '\' AND \'' . sql_escape($options['cachevote_2']) . '\' AND `caches`.`votes` > 3) OR (`caches`.`votes` < 4))';
				}

				if(!isset($options['cachedifficulty_1']) && !isset($options['cachedifficulty_2'])) {
					$options['cachedifficulty_1']='';	
					$options['cachedifficulty_2']='';	
				}
				if((($options['cachedifficulty_1'] != '') && ($options['cachedifficulty_2'] != '')) && (($options['cachedifficulty_1'] != '1') || ($options['cachedifficulty_2'] != '5')))
				{
					$sql_where[] = '`caches`.`difficulty` BETWEEN \'' . sql_escape($options['cachedifficulty_1'] * 2) . '\' AND \'' . sql_escape($options['cachedifficulty_2'] * 2) . '\'';
				}
				
				if(!isset($options['cacheterrain_1']) && !isset($options['cacheterrain_2'])) {
					$options['cacheterrain_1']='';	
					$options['cacheterrain_2']='';	
				}

				if((($options['cacheterrain_1'] != '') && ($options['cacheterrain_2'] != '')) && (($options['cacheterrain_1'] != '1') || ($options['cacheterrain_2'] != '5')))
				{
					$sql_where[] = '`caches`.`terrain` BETWEEN \'' . sql_escape($options['cacheterrain_1'] * 2) . '\' AND \'' . sql_escape($options['cacheterrain_2'] * 2) . '\'';
				}

				if($options['cacherating'] > 0) {
					$sql_where[] = '`caches`.`topratings` >= \'' . $options['cacherating'] .'\'';					
				}
				
				//do the search
				$join = sizeof($sql_join) ? ' LEFT JOIN ' . implode(' AND ', $sql_join) : '';
				$group = sizeof($sql_group) ? ' GROUP BY ' . implode(', ', $sql_group) : '';
				$having = sizeof($sql_having) ? ' HAVING ' . implode(' AND ', $sql_having) : '';

				$sqlFilter = 'SELECT ' . implode(',', $sql_select) .
						' FROM ' . implode(',', $sql_from) .
						$join .
						' WHERE ' . implode(' AND ', $sql_where) .
						$group .
						$having;

			

			
			
			
			
			
			
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
          $from[] = '('; $to[] = ' -';
          $from[] = ')'; $to[] = '- ';
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
			
//*************************************************************************
// Returns information about a route based on $route_id.
//*************************************************************************
function route_info($route_id) {
$query = "SELECT * FROM routes WHERE id=".stripslashes($route_id).";";
$result = sql($query);
if ($result) {
$row = mysql_fetch_array($result,NULL);
$info = $row;
} else {
return FALSE;
}
return $info;
}

/**
    * function cache_distances ($lat1, $lon1, $lat2, $lon2)
    */
function cache_distances($lat1, $lon1, $lat2, $lon2) {
if ( ( $lon1 == $lon2 ) AND ( $lat1 == $lat2 ) ) {
return(0);
} else {
$earth_radius = 6378;
foreach(array("lat1","lon1","lat2","lon2") as $ordinate)
$$ordinate = $$ordinate*(pi()/180);
$dist = acos(cos($lat1)*cos($lon1)*cos($lat2)*cos($lon2) +
cos($lat1)*sin($lon1)*cos($lat2)*sin($lon2) +
sin($lat1)*sin($lat2)
) * $earth_radius;
return($dist);
}
}

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
//$bounds = route_info($route_id);
$smallestLat = sqlValue("SELECT `route_points`.`lat`  FROM `route_points` WHERE `route_id`='" . sql_escape($route_id) . "' ORDER BY `route_points`.`lat` ASC LIMIT 1", 0);
$largestLat = sqlValue("SELECT `route_points`.`lat`  FROM `route_points` WHERE `route_id`='" . sql_escape($route_id) . "' ORDER BY `route_points`.`lat` DESC LIMIT 1 ", 0);
$smallestLon = sqlValue("SELECT `route_points`.`lon`  FROM `route_points` WHERE `route_id`='" . sql_escape($route_id) . "' ORDER BY `route_points`.`lon` ASC LIMIT 1", 0);
$largestLon = sqlValue("SELECT `route_points`.`lon`  FROM `route_points` WHERE `route_id`='" . sql_escape($route_id) . "' ORDER BY `route_points`.`lon` DESC LIMIT 1", 0);

$bounds_min_lat = $smallestLat - $distance/110;
$bounds_max_lat = $largestLat + $distance/110;
$bounds_min_lon = $smallestLon - $distance/110;
$bounds_max_lon = $largestLon + $distance/110;
$query = "SELECT wp_oc waypoint, latitude lat, longitude lon "." FROM caches "."WHERE latitude>'$bounds_min_lat' ".
"AND latitude<'$bounds_max_lat' "."AND longitude>'$bounds_min_lon' "."AND longitude<'$bounds_max_lon' "."AND status = '1';";
$result=sql($query);
if ($result AND $count=mysql_num_rows($result)) {
for ( $i=0; $i<$count; $i++ ) {
$row = mysql_fetch_array($result);
$initial_cache_list[] =array("waypoint"=>$row['waypoint'],"lat"=>$row['lat'],"lon"=>$row['lon']);
}
$points = array();
$query = "SELECT * FROM route_points WHERE route_id ='$route_id' ORDER BY point_nr;";
$result = sql($query);
	if ( $result AND $count=mysql_num_rows($result) ) {
	for ( $i=0; $i<$count; $i++ ) {
	$row = mysql_fetch_array($result);
	$points[] = array("lat"=>$row["lat"],"lon"=>$row["lon"]);}
	}
foreach ($initial_cache_list as $list) {
foreach ($points as $point) {
$route_distance =cache_distances($list["lat"],$list["lon"],$point["lat"],$point["lon"]);
if ( $route_distance <= $distance ) {
if ( !$inter_cache_list[$list['waypoint']] ) {
$final_cache_list[] = $list['waypoint'];
$inter_cache_list[$list['waypoint']] = $list['waypoint'];
break;}}}}}	
return $final_cache_list;
}
// end of function		

				if (isset($_POST['back_list']))
				{	
							tpl_redirect('myroutes.php');
							exit;
				}		
		if (isset($_POST['submit']))
		{
			$route_rs = sql("SELECT `user_id`,`name`, `description`, `radius` FROM `routes` WHERE `route_id`='&1'", $route_id);
			$record = sql_fetch_array($route_rs);	
			$distance=$record['radius'];
			tpl_set_var('route_name',$record['name']);
		
$caches_list=caches_along_route($route_id, $distance);

 $rs=sql("SELECT `caches`.`cache_id` `cacheid`, 
							`user`.`user_id` `userid`, 
							`caches`.`type` `type`,
							`caches`.`name` `cachename`, 
							`caches`.`wp_oc` `wp_name`, 
							`user`.`username` `username`, 
							`caches`.`date_created` `date_created`, 
							`caches`.`date_hidden` `date`, 
							`cache_type`.`icon_large` `icon_large`
					FROM `caches`,`user`, `cache_type` 
					WHERE `caches`.`user_id`=`user`.`user_id` 
						AND `cache_type`.`id`=`caches`.`type`
						AND `caches`.`status` = 1 
						AND `caches`.`wp_oc` IN('".implode("', '", $caches_list)."')");	
	
		while ($r = sql_fetch_array($rs))
		{


				$file_content .= '<tr>';
				$file_content .= '<td style="width: 90px;">'. date('Y-m-d', strtotime($r['date'])) . '</td>';			
				$file_content .= '<td width="22">&nbsp;<img src="tpl/stdstyle/images/' .getSmallCacheIcon($r['icon_large']) . '" border="0" alt=""/></td>';
				$file_content .= '<td><b><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($r['cacheid'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($r['cachename'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
				$file_content .= '<td width="32"><b><a class="links" href="viewprofile.php?userid='.htmlspecialchars($r['userid'], ENT_COMPAT, 'UTF-8') . '">' .htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8'). '</a></b></td>';

	$rs_log = sql("SELECT cache_logs.id AS id, cache_logs.cache_id AS cache_id,
	                          cache_logs.type AS log_type,
	                          DATE_FORMAT(cache_logs.date,'%Y-%m-%d') AS log_date,
				cache_logs.text AS log_text,
	                          user.username AS user_name,
				user.user_id AS user_id,
				log_types.icon_small AS icon_small, COUNT(gk_item.id) AS geokret_in
			FROM (cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id)) INNER JOIN user ON (cache_logs.user_id = user.user_id) INNER JOIN log_types ON (cache_logs.type = log_types.id)
							LEFT JOIN	gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
							LEFT JOIN	gk_item ON gk_item.id = gk_item_waypoint.id AND
							gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5				
			WHERE cache_logs.deleted=0 AND cache_logs.cache_id=&1
			 GROUP BY cache_logs.id ORDER BY cache_logs.date_created DESC LIMIT 1",$r['cacheid']);

			if (mysql_num_rows($rs_log) != 0)
			{
			$r_log = sql_fetch_array($rs_log);

				$file_content .= '<td style="width: 80px;">'. htmlspecialchars(date("Y-m-d", strtotime($r_log['log_date'])), ENT_COMPAT, 'UTF-8') . '</td>';			

				$file_content .= '<td width="22"><b><a class="links" href="viewlogs.php?logid=' . htmlspecialchars($r_log['id'], ENT_COMPAT, 'UTF-8') . '" onmouseover="Tip(\''; 
				$file_content .= '<b>'.$r_log['user_name'].'</b>:<br/>';
				$data = cleanup_text(str_replace("\r\n", " ", $r_log['log_text']));
				$file_content .= str_replace("\n", " ",$data);
				$file_content .= '\',OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()"><img src="tpl/stdstyle/images/' . $r_log['icon_small'] . '" border="0" alt=""/></a></b></td>';
				$file_content .= '<td>&nbsp;&nbsp;<b><a class="links" href="viewprofile.php?userid=' . htmlspecialchars($r_log['user_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($r_log['user_name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';

					}				
			mysql_free_result($rs_log);
			$file_content .= "</tr>";
		}
		mysql_free_result($rs);
	        tpl_set_var('file_content',$file_content);
			$tplname = 'myroutes_result';		
	} //end submit
	
	
	
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
