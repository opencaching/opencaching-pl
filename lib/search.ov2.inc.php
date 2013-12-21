<?php
	/***************************************************************************
															./lib/search.ov2.inc.php
																-------------------
			begin                : November 2 2005 
			copyright            : (C) 2005 The OpenCaching Group
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

	/****************************************************************************
		      
		Unicode Reminder ăĄă˘
                                   				                                
		ov2 search output
		
	****************************************************************************/

	global $content, $bUseZip, $sqldebug, $hide_coords, $usr;
	set_time_limit(1800);
$cacheTypeText[1] = 'Unknown Cache';
$cacheTypeText[2] = 'Traditional Cache';
$cacheTypeText[3] = 'Multi-Cache';
$cacheTypeText[4] = 'Virtual Cache';
$cacheTypeText[5] = 'Webcam Cache';
$cacheTypeText[6] = 'Event Cache';
$cacheTypeText[7] = 'Quiz';
$cacheTypeText[8] = 'Moving Cache';
$cacheTypeText[9] = 'PodCastCache';

	if( $usr || !$hide_coords )
	{
		//prepare the output
		$caches_per_page = 20;
		
		$sql = 'SELECT '; 
		
		if (isset($lat_rad) && isset($lon_rad))
		{
			$sql .= getCalcDistanceSqlFormula($usr !== false, $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
		}
		else
		{
			if ($usr === false)
			{
				$sql .= '0 distance, ';
			}
			else
			{
				//get the users home coords
				$rs_coords = sql("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
				$record_coords = sql_fetch_array($rs_coords);
				
				if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0)))
				{
					$sql .= '0 distance, ';
				}
				else
				{
					//TODO: load from the users-profile
					$distance_unit = 'km';

					$lon_rad = $record_coords['longitude'] * 3.14159 / 180;   
					$lat_rad = $record_coords['latitude'] * 3.14159 / 180; 

					$sql .= getCalcDistanceSqlFormula($usr !== false, $record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
				}
				mysql_free_result($rs_coords);
			}
		}
		$sql .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`user_id` `user_id`, ';
			
		if ($usr === false) 
		{
			$sql .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`
					FROM `caches` ';
		}
		else
		{
			$sql .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`, 
							`caches`.`latitude`) `latitude` FROM `caches`
						LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = ' 
							. $usr['userid'];						
		}
					
		$sql .= ' WHERE `caches`.`cache_id` IN (' . $sqlFilter . ')';
		
		$sortby = $options['sort'];
		if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance'))
		{
			$sql .= ' ORDER BY distance ASC';
		}
		else if ($sortby == 'bycreated')
		{
			$sql .= ' ORDER BY date_created DESC';
		}
		else // by name
		{
			$sql .= ' ORDER BY name ASC';
		}

		//startat?
		$startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
		if (!is_numeric($startat)) $startat = 0;
		
		if (isset($_REQUEST['count']))
			$count = $_REQUEST['count'];
		else
			$count = $caches_per_page;
		
		$maxlimit = 1000000000;
		
		if ($count == 'max') $count = $maxlimit;
		if (!is_numeric($count)) $count = 0;
		if ($count < 1) $count = 1;
		if ($count > $maxlimit) $count = $maxlimit;

		$sqlLimit = ' LIMIT ' . $startat . ', ' . $count;

		// temporĂ¤re tabelle erstellen
		sql('CREATE TEMPORARY TABLE `ov2content` ' . $sql . $sqlLimit);

		$rsCount = sql('SELECT COUNT(*) `count` FROM `ov2content`');
		$rCount = sql_fetch_array($rsCount);
		mysql_free_result($rsCount);
		
		if ($rCount['count'] == 1)
		{
			$rsName = sql('SELECT `caches`.`wp_oc` `wp_oc` FROM `ov2content`, `caches` WHERE `ov2content`.`cache_id`=`caches`.`cache_id` LIMIT 1');
			$rName = sql_fetch_array($rsName);
			mysql_free_result($rsName);
			
			$sFilebasename = $rName['wp_oc'];
		}
		else {
			if ($options['searchtype'] == 'bywatched') {
				$sFilebasename = 'watched_caches';
			} elseif ($options['searchtype'] == 'bylist') {
				$sFilebasename = 'cache_list';
			} else {
				$rsName = sql('SELECT `queries`.`name` `name` FROM `queries` WHERE `queries`.`id`= &1 LIMIT 1', $options['queryid']);
				$rName = sql_fetch_array($rsName);
				mysql_free_result($rsName);
				if (isset($rName['name']) && ($rName['name'] != '')) {
					$sFilebasename = trim($rName['name']);
					$sFilebasename = str_replace(" ", "_", $sFilebasename);
				} else {
					$sFilebasename = 'ocpl' . $options['queryid'];
				}
			}
		}
			
		$bUseZip = ($rCount['count'] > 50);
		$bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
		$bUseZip = false;
		if ($bUseZip == true)
		{
			$content = '';
			require_once($rootpath . 'lib/phpzip/ss_zip.class.php');
			$phpzip = new ss_zip('',6);
		}

		// ok, ausgabe starten
		
		if ($sqldebug == false)
		{
			if ($bUseZip == true)
			{
				header("content-type: application/zip");
				header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
			}
			else
			{
				header("Content-type: application/ov2");
				header("Content-Disposition: attachment; filename=" . $sFilebasename . ".ov2");
			}
		}

		// ok, ausgabe ...
		
		/*
			cacheid
			name
			lon
			lat
			
			archivedflag
			type
			size
			difficulty
			terrain
			username
		*/

		$sql = 'SELECT `ov2content`.`cache_id` `cacheid`, `ov2content`.`longitude` `longitude`, `ov2content`.`latitude` `latitude`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`wp_oc` `wp_oc`, `cache_type`.`short` `typedesc`, `cache_size`.`pl` `sizedesc`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username`, `cache_type`.`id` `type_id` FROM `ov2content`, `caches`, `cache_type`, `cache_size`, `user` WHERE `ov2content`.`cache_id`=`caches`.`cache_id` AND `ov2content`.`type`=`cache_type`.`id` AND `ov2content`.`size`=`cache_size`.`id` AND `ov2content`.`user_id`=`user`.`user_id`';
		$rs = sql($sql, $sqldebug);

		while($r = sql_fetch_array($rs))
		{
			$lat = sprintf('%07d', $r['latitude'] * 100000);
			$lon = sprintf('%07d', $r['longitude'] * 100000);
			$name = convert_string($r['name']);
			$username = convert_string($r['username']);
			$type = convert_string($cacheTypeText[$r['type_id']]);
			$size = convert_string($r['sizedesc']);
			$difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
			$terrain = sprintf('%01.1f', $r['terrain'] / 2);
			$cacheid = convert_string($r['wp_oc']);

			$line = "$cacheid - $name by $username, $type ($difficulty/$terrain)";
			$record = pack("CLllA*x", 2, 1 + 4 + 4 + 4 + strlen($line) + 1, (int)$lon, (int)$lat, $line);
			
			append_output($record);
			ob_flush();
		}
		mysql_free_result($rs);
		
		if ($sqldebug == true) sqldbg_end();
		
		// phpzip versenden
		if ($bUseZip == true)
		{
			$phpzip->add_data($sFilebasename . '.ov2', $content);
			echo $phpzip->save($sFilebasename . '.zip', 'b');
		}
	}
	exit;

	function convert_string($str)
	{
		$newstr = iconv("UTF-8", "utf-8", $str);
		if ($newstr == false)
			return "--- charset error ---";
		else
			return $newstr;
	}

	function append_output($str)
	{
		global $content, $bUseZip, $sqldebug;
		if ($sqldebug == true) return;
		
		if ($bUseZip == true)
			$content .= $str;
		else
			echo $str;
	}
?>
