<?php
	/***************************************************************************
															./lib/search.gpx.inc.php
																-------------------
			begin                : November 1 2005 
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
		    
		Unicode Reminder ??
                                     				                                
		GPX search output
		
	****************************************************************************/

	global $content, $bUseZip, $sqldebug, $usr, $hide_coords;
	set_time_limit(1800);
	
	function getPictures($cacheid, $picturescount)
	{
		global $dblink;
		global $thumb_max_width;
		global $thumb_max_height;

		$sql = 'SELECT uuid, title, url, spoiler FROM pictures WHERE object_id=\'' . sql_escape($cacheid) . '\' AND object_type=2 AND display=1 ORDER BY date_created';
		

		$rs = sql($sql);
		while ($r = sql_fetch_array($rs))
		{
			$retval .= '&lt;img src="'.$r['url'].'"&gt;&lt;br&gt;'.cleanup_text($r['title']).'&lt;br&gt;';
		}

		mysql_free_result($rs);
		return $retval;
	}

	$gpxHead = 
'<?xml version="1.0" encoding="utf-8"?>
<gpx xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" version="1.0" creator="Groundspeak, Inc. All Rights Reserved. http://www.groundspeak.com" xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd http://www.groundspeak.com/cache/1/0/1 http://www.groundspeak.com/cache/1/0/1/cache.xsd" xmlns="http://www.topografix.com/GPX/1/0">
	<name>Cache Listing Generated from Opencaching.pl</name>
	<desc>Cache Listing Generated from Opencaching.pl</desc>
	<author>Opencaching.pl</author>
	<email>ocpl@opencaching.pl</email>
	<url>http://www.opencaching.pl</url>
	<urlname>Opencaching.pl - Geocaching w Polsce</urlname>
	<time>{{time}}</time>
	<keywords>cache, geocache</keywords>
';
	
$gpxLine = '
	<wpt lat="{lat}" lon="{lon}">
		<time>{{time}}</time>
		<name>{{waypoint}}</name>
		<desc>{cachename} by {owner}, {type_text} ({difficulty}/{terrain})</desc>
		<url>http://www.opencaching.pl/viewcache.php?cacheid={cacheid}</url>
		<urlname>{cachename} by {owner}, {type_text}</urlname>
		<sym>Geocache</sym>
		<type>Geocache|{type}</type>
		<groundspeak:cache id="{cacheid}" available="{available}" archived="{{archived}}" xmlns:groundspeak="http://www.groundspeak.com/cache/1/0/1">
			<groundspeak:name>{cachename}</groundspeak:name>
			<groundspeak:placed_by>{owner}</groundspeak:placed_by>
			<groundspeak:owner id="{owner_id}">{owner}</groundspeak:owner>
			<groundspeak:type>{type}</groundspeak:type>
			<groundspeak:container>{container}</groundspeak:container>
			<groundspeak:attributes>
			{attributes}
			</groundspeak:attributes>
			<groundspeak:difficulty>{difficulty}</groundspeak:difficulty>
			<groundspeak:terrain>{terrain}</groundspeak:terrain>
			<groundspeak:country>Polska</groundspeak:country>
			<groundspeak:state></groundspeak:state>
			<groundspeak:short_description html="False">{shortdesc}</groundspeak:short_description>
			<groundspeak:long_description html="True">{desc}{rr_comment}&lt;br&gt;{{images}}</groundspeak:long_description>
			<groundspeak:encoded_hints>{hints}</groundspeak:encoded_hints>
			<groundspeak:logs>
			{logs}
			</groundspeak:logs>
			<groundspeak:travelbugs>
			{geokrety}
			</groundspeak:travelbugs>
		</groundspeak:cache>
	</wpt>
';



// Convert from OC Attribut to GC attribute for GPX file (Stefan Jormelius)
//$gpxAttConv[1] = 14;  //OC:Only at night -> GC:Recommended at night;
//$gpxAttConv[6] = ;	//OC:Listed on Opencaching only -> GC:
//$gpxAttConv[8] = ;	//OC:Letterbox - stamp required -> GC:

//$gpxAttConv[10] = ;	//OC:Active railway nearby -> GC:
//$gpxAttConv[11] = 21;	//OC:Cliffs / rocks -> GC:Cliff / falling rocks
//$gpxAttConv[12] = 22;	//OC:Hunting -> GC:Hunting
//$gpxAttConv[13] = 39;	//OC:Thorn -> GC:Thorns
//$gpxAttConv[14] = 19;	//OC:Ticks -> GC:Ticks
//$gpxAttConv[15] = 20;	//OC:The cache leads to a (former) mining region -> GC:Abandoned mines
//$gpxAttConv[16] = 17;	//OC:Poisonous Plants -> GC:Poison plants
//$gpxAttConv[17] = 18;	//OC:Toxic / dangerous animals -> GC:Snakes
//$gpxAttConv[18] = 25;	//OC:Parking area nearby -> GC:Parking available
//$gpxAttConv[19] = 26;	//OC:Public transportation -> GC:Public transportation
//$gpxAttConv[20] = 27;	//OC:Drinking water nearby -> GC:Drinking water nearby
//$gpxAttConv[21] = 28;	//OC:Public restrooms nearby -> GC:Public restrooms nearby
//$gpxAttConv[22] = 29;	//OC:Public phone nearby -> GC:Telephone nearby
//$gpxAttConv[23] = ;	//OC:First aid available -> GC:
//$gpxAttConv[24] = ;	//OC:Near the parking area -> GC:
//$gpxAttConv[25] = 9;	//OC:Long walk -> GC:Significant hike
//$gpxAttConv[26] = 11;	//OC:Swamp or marsh -> GC:May require wading
//$gpxAttConv[27] = ;	//OC:Hilly Terrain -> GC:
//$gpxAttConv[28] = 10;	//OC:Lightweight Climbing - without equipment -> GC:Difficult climbing
//$gpxAttConv[29] = 12;	//OC:Swimming required -> GC:May require swimming
//$gpxAttConv[30] = ;	//OC:Interesting place -> GC:
//$gpxAttConv[31] = ;	//OC:Moving Target -> GC:
//$gpxAttConv[32] = ;	//OC:Webcam  -> GC:
//$gpxAttConv[33] = ;	//OC:Within enclosed rooms (caves, buildings etc.) -> GC:
//$gpxAttConv[34] = ;	//OC:Underwater -> GC:
//$gpxAttConv[35] = ;	//OC:Without GPS (letterboxes, cistes, compass juggling... -> GC:
//$gpxAttConv[36] = 2;	//OC:Access or parking fee -> GC:Access or parking fee
//$gpxAttConv[37] = ;	//OC:Overnight stay necessary -> GC:
//$gpxAttConv[38] = 13;	//OC:Available 24 hours -> GC:Available at all times
//$gpxAttConv[39] = ;	//OC:Only available at specified times -> GC:
//$gpxAttConv[50] = ;	//OC:Cave equipment -> GC:
//$gpxAttConv[51] = 5;	//OC:Diving equipment -> GC:Scuba gear
//$gpxAttConv[52] = 4;	//OC:Watercraft -> GC:Boat
//$gpxAttConv[53] = ;	//OC:Aircraft -> GC:
//$gpxAttConv[54] = ;	//OC:Investigation additional information -> GC:
//$gpxAttConv[55] = ;	//OC:Puzzle / Mystery -> GC:
//$gpxAttConv[56] = ;	//OC:Arithmetical problem -> GC:
//$gpxAttConv[57] = ;	//OC:Other cache type -> GC:
//$gpxAttConv[58] = ;	//OC:Ask owner for start conditions -> GC:
//$gpxAttConv[59] = 6;	//OC:Tides -> GC:
// OC PL attributes
//$gpxAttConv[40] = 40 ;	//OC:Easy cache
$gpxAttConv[41] = 6;	//OC:suited for children -> GC:Recommended for kids OC:Tides -> GC:
//$gpxAttConv[42] = 42 ;	//OC:GPS Free
//$gpxAttConv[43] = 43 ;	//OC:GeoHotel
$gpxAttConv[44] = 24;	//OC:AccessibleFor disabled GC: Wheelchair Accessible
//$gpxAttConv[47] = 47;	//OC:Compass -> GC:
//$gpxAttConv[48] = 48;	//OC:Take something to write
$gpxAttConv[60] = 8;	//OC:Natura
//$gpxAttConv[61] = 61;	//OC:Monumental place
$gpxAttConv[80] = 2;	//OC:Periodical/Paid 
//$gpxAttConv[81] = 81;	//OC:Shovel - special equipment 
//$gpxAttConv[82] = 82;	//OC:flashlight -> GC:Flashlight required
//$gpxAttConv[83] = 83;	//OC:Special equipment
$gpxAttConv[90] = 23;	//OC:Dangerous territory -> GC:Dangerous area

$gpxAttribute = '<groundspeak:attribute id="{attribute_id}" inc="{attribute_inc}">{attribute_text}</groundspeak:attribute>
		';



$gpxLog = '
				<groundspeak:log id="{id}">
        			<groundspeak:date>{date}</groundspeak:date>
					<groundspeak:type>{type}</groundspeak:type>
					<groundspeak:finder id="{finder_id}">{username}</groundspeak:finder>
					<groundspeak:text encoded="False">{{text}}</groundspeak:text>
				</groundspeak:log>
';

$gpxGeoKrety = '<groundspeak:travelbug id="{geokret_id}" ref="{geokret_ref}">
		<groundspeak:name>{geokret_name}</groundspeak:name> 
		</groundspeak:travelbug> 
		';


	$gpxFoot = '</gpx>';

	$gpxTimeFormat = 'Y-m-d\TH:i:s';

	$gpxAvailable[0] = 'False';	//OC: Unavailable
	$gpxAvailable[1] = 'True';	//OC: Available
	$gpxAvailable[2] = 'False';	//OC: Unavailable
	$gpxAvailable[3] = 'False';	//OC: Archived
	
	$gpxArchived[0] = 'False';	//OC: Unavailable
	$gpxArchived[1] = 'False';	//OC: Available
	$gpxArchived[2] = 'False';	//OC: Unavailable
	$gpxArchived[3] = 'True';	//OC: Archived

	$gpxContainer[0] = 'Unknown';	//OC: Other
	$gpxContainer[2] = 'Micro';		//OC: Micro
	$gpxContainer[3] = 'Small';		//OC: Small
	$gpxContainer[4] = 'Regular';	//OC: Regular
	$gpxContainer[5] = 'Large';		//OC: Large
	$gpxContainer[6] = 'Large';		//OC: Large
	$gpxContainer[7] = 'Virtual';	//OC: Virtual

	// known by gpx
	$gpxType[1] = 'Unknown Cache'; 		//OC: Other;
	$gpxType[2] = 'Traditional Cache'; 	//OC: Traditional
	$gpxType[3] = 'Multi-cache'; 		//OC: Multi
	$gpxType[4] = 'Virtual Cache';		//OC: Virtual
	$gpxType[5] = 'Webcam Cache';		//OC: Webcam
	$gpxType[6] = 'Event Cache';		//OC: Event
	
	$gpxType[7] = 'Unknown Cache';		//OC: Quiz
	$gpxType[8] = 'Unknown Cache';		//OC: Moving
	$gpxType[9] = 'Unknown Cache';		//OC: PodCache
	$gpxType[10] = 'Unknown Cache';		//OC: Educache
	$gpxType[11] = 'Unknown Cache';		//OC: Challenge cache
	// other
	//$gpxType[] = 'Unknown Cache';
	//$gpxType[] = 'Earthcache';
	//$gpxType[] = 'Cache In Trash Out Event';
	//$gpxType[] = 'Letterbox Hybrid';
	//$gpxType[] = 'Locationless (Reverse) Cache';

	// nazwy skrzynek do description
	$gpxGeocacheTypeText[1] = 'Unknown Cache';
	$gpxGeocacheTypeText[2] = 'Traditional Cache';
	$gpxGeocacheTypeText[3] = 'Multi-Cache';
	$gpxGeocacheTypeText[4] = 'Virtual Cache';
	$gpxGeocacheTypeText[5] = 'Webcam Cache';
	$gpxGeocacheTypeText[6] = 'Event Cache';
	$gpxGeocacheTypeText[7] = 'Quiz';
	$gpxGeocacheTypeText[8] = 'Moving Cache';
	$gpxGeocacheTypeText[10] = 'Unknown Cache';
	$gpxGeocacheTypeText[11] = 'PodCast Cache';
	
	$gpxLogType[0] = 'Write note';			//OC: Other
	$gpxLogType[1] = 'Found it'; 			//OC: Found
	$gpxLogType[2] = 'Didn\'t find it';		//OC: Not Found
	$gpxLogType[3] = 'Write note'; 			//OC: Note

	if( $usr || !$hide_coords )
		{
		//prepare the output
		$caches_per_page = 20;
		
		$sql = 'SELECT '; 
		
		if (isset($lat_rad) && isset($lon_rad))
		{
			$sql .= getSqlDistanceFormula($lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
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

					$sql .= getSqlDistanceFormula($record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
				}
				mysql_free_result($rs_coords);
			}
		}
		$sql .= '`caches`.`cache_id` `cache_id`, `caches`.`wp_oc` `cache_wp`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, `caches`.`user_id` `user_id`
					FROM `caches`
					WHERE `caches`.`cache_id` IN (' . $sqlFilter . ')';
		
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

		// cleanup (old gpxcontent lingers if gpx-download is cancelled by user)		
		sql('DROP TEMPORARY TABLE IF EXISTS `gpxcontent`');

		// temporäre tabelle erstellen
		sql('CREATE TEMPORARY TABLE `gpxcontent` ' . $sql . $sqlLimit);

		$rsCount = sql('SELECT COUNT(*) `count` FROM `gpxcontent`');
		$rCount = sql_fetch_array($rsCount);
		mysql_free_result($rsCount);
		
		if ($rCount['count'] == 1)
		{
			$rsName = sql('SELECT `caches`.`wp_oc` `wp_oc` FROM `gpxcontent`, `caches` WHERE `gpxcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
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
				header("Content-type: application/gpx");
				header("Content-Disposition: attachment; filename=" . $sFilebasename . ".gpx");
			}
		}
		
		$gpxHead = str_replace('{{time}}', date($gpxTimeFormat, time()), $gpxHead);
		append_output($gpxHead);

		// ok, ausgabe ...
		$rs = sql('SELECT `gpxcontent`.`cache_id` `cacheid`, `gpxcontent`.`longitude` `longitude`, `gpxcontent`.`latitude` `latitude`, `caches`.`wp_oc` `waypoint`, `caches`.`date_hidden` `date_hidden`, `caches`.`picturescount` `picturescount`, `caches`.`name` `name`, `caches`.`country` `country`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `caches`.`desc_languages` `desc_languages`, `caches`.`size` `size`, `caches`.`type` `type`, `caches`.`status` `status`, `user`.`username` `username`, `gpxcontent`.`user_id` `owner_id`, `cache_desc`.`desc` `desc`, `cache_desc`.`short_desc` `short_desc`, `cache_desc`.`hint` `hint`, `cache_desc`.`rr_comment`, `caches`.`logpw` FROM `gpxcontent`, `caches`, `user`, `cache_desc` WHERE `gpxcontent`.`cache_id`=`caches`.`cache_id` AND `caches`.`cache_id`=`cache_desc`.`cache_id` AND `caches`.`default_desclang`=`cache_desc`.`language` AND `gpxcontent`.`user_id`=`user`.`user_id`');
		while($r = sql_fetch_array($rs))
		{
			$thisline = $gpxLine;
			$lat = sprintf('%01.5f', $r['latitude']);
			$thisline = str_replace('{lat}', $lat, $thisline);
			
			$lon = sprintf('%01.5f', $r['longitude']);
			$thisline = str_replace('{lon}', $lon, $thisline);

			$time = date($gpxTimeFormat, strtotime($r['date_hidden']));
			$thisline = str_replace('{{time}}', $time, $thisline);
			$thisline = str_replace('{{waypoint}}', $r['waypoint'], $thisline);
			$thisline = str_replace('{cacheid}', $r['cacheid'], $thisline);
			$thisline = str_replace('{cachename}', cleanup_text($r['name']), $thisline);
//			$thisline = str_replace('{country}', $r['country'], $thisline);
			$thisline = str_replace('{state}', '', $thisline);
			
			if ($r['hint'] == '')
				$thisline = str_replace('{hints}', '', $thisline);
			else
				$thisline = str_replace('{hints}', cleanup_text($r['hint']), $thisline);
			
			$logpw = ($r['logpw']==""?"":"UWAGA! W skrzynce znajduje się hasło - pamiętaj o jego zapisaniu!<br />");
			
			$thisline = str_replace('{shortdesc}', cleanup_text($r['short_desc']), $thisline);
			$thisline = str_replace('{desc}', cleanup_text($logpw.$r['desc']), $thisline);
			if( $r['rr_comment'] == '' )
				$thisline = str_replace('{rr_comment}', '', $thisline);
			else
				$thisline = str_replace('{rr_comment}', cleanup_text("<br /><br />--------<br />".$r['rr_comment']."<br />"), $thisline);
			
			$thisline = str_replace('{{images}}', getPictures($r['cacheid'], false, $r['picturescount']), $thisline);

			if (isset($gpxType[$r['type']]))
				$thisline = str_replace('{type}', $gpxType[$r['type']], $thisline);
			else
				$thisline = str_replace('{type}', $gpxType[1], $thisline);

			if (isset($gpxGeocacheTypeText[$r['type']]))
				$thisline = str_replace('{type_text}', $gpxGeocacheTypeText[$r['type']], $thisline);
			else
				$thisline = str_replace('{type_text}', $gpxGeocacheTypeText[1], $thisline);

			if (isset($gpxContainer[$r['size']]))
				$thisline = str_replace('{container}', $gpxContainer[$r['size']], $thisline);
			else
				$thisline = str_replace('{container}', $gpxContainer[0], $thisline);
			
			if (isset($gpxAvailable[$r['status']]))
				$thisline = str_replace('{available}', $gpxAvailable[$r['status']], $thisline);
			else
				$thisline = str_replace('{available}', $gpxAvailable[1], $thisline);
			
			if (isset($gpxArchived[$r['status']]))
				$thisline = str_replace('{{archived}}', $gpxArchived[$r['status']], $thisline);
			else
				$thisline = str_replace('{{archived}}', $gpxArchived[1], $thisline);

			$difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
			$difficulty = str_replace('.0', '', $difficulty); // garmin devices cannot handle .0 on integer values
			$thisline = str_replace('{difficulty}', $difficulty, $thisline);

			$terrain = sprintf('%01.1f', $r['terrain'] / 2);
			$terrain = str_replace('.0', '', $terrain);
			$thisline = str_replace('{terrain}', $terrain, $thisline);

			$thisline = str_replace('{owner}', xmlentities($r['username']), $thisline);
			$thisline = str_replace('{owner_id}', xmlentities($r['owner_id']), $thisline);



			// logs ermitteln
			$logentries = '';
			$rsLogs = sql("SELECT `cache_logs`.`id`, `cache_logs`.`type`, `cache_logs`.`date`, `cache_logs`.`text`, `user`.`username`, `cache_logs`.`user_id` `userid` FROM `cache_logs`, `user` WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`user_id`=`user`.`user_id` AND `cache_logs`.`cache_id`=&1 ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`id` DESC", $r['cacheid']); // adam: removed LIMIT 20
			while ($rLog = sql_fetch_array($rsLogs))
			{
				$thislog = $gpxLog;
				
				$thislog = str_replace('{id}', $rLog['id'], $thislog);
				$thislog = str_replace('{date}', date($gpxTimeFormat, strtotime($rLog['date'])), $thislog);
				$thislog = str_replace('{username}', xmlentities($rLog['username']), $thislog);
				$thislog = str_replace('{finder_id}', xmlentities($rLog['userid']), $thislog);				
				if (isset($gpxLogType[$rLog['type']]))
					$logtype = $gpxLogType[$rLog['type']];
				else
					$logtype = $gpxLogType[0];
					
				$thislog = str_replace('{type}', $logtype, $thislog);
													$thislog = str_replace('{{text}}', cleanup_text($rLog['text']), $thislog);
				$logentries .= $thislog . "\n";
				
			}
			$thisline = str_replace('{logs}', $logentries, $thisline);
			// Attributes
			$attributes = '';
			$rsAttributes = sql("SELECT `caches_attributes`.`attrib_id`, `cache_attrib`.`text_long` FROM `caches_attributes`, `cache_attrib` WHERE `caches_attributes`.`cache_id`=&1 AND `caches_attributes`.`attrib_id` = `cache_attrib`.`id` AND `cache_attrib`.`language` = 'PL' ORDER BY `caches_attributes`.`attrib_id`", $r['cacheid']);
			while ($rAttribute = sql_fetch_array($rsAttributes))
			{
				$thisAttribute = $gpxAttribute;
				
				if (isset($gpxAttConv[$rAttribute['attrib_id']]))
				{
					$thisAttribute = str_replace('{attribute_id}', $gpxAttConv[$rAttribute['attrib_id']], $thisAttribute);
					//$thisAttribute = str_replace('{attribute_id}', $rAttribute['attrib_id'], $thisAttribute);
					$thisAttribute = str_replace('{attribute_inc}', '1', $thisAttribute);
					$thisAttribute = str_replace('{attribute_text}', xmlentities($rAttribute['text_long']), $thisAttribute);
									
					$attributes .= $thisAttribute;// . "\n";
				}
				
			}
			$thisline = str_replace('{attributes}', $attributes, $thisline);

			// Travel Bug GeoKrety
			$waypoint = $r['waypoint'];
			$geokrety = '';
			$geokret_sql = "SELECT id, name FROM gk_item WHERE id IN (SELECT id FROM gk_item_waypoint WHERE wp = '".sql_escape($waypoint)."') AND stateid<>1 AND stateid<>4 AND stateid <>5 AND typeid<>2";
			$geokret_query = sql($geokret_sql);

				while( $geokret = sql_fetch_array($geokret_query) )
				{

				$thisGeoKret = $gpxGeoKrety;
				$gk_wp = strtoupper(dechex($geokret['id']));
				while (mb_strlen($gk_wp) < 4) $gk_wp = '0' . $gk_wp;
				$gkWP = 'GK' . mb_strtoupper($gk_wp);
					$thisGeoKret = str_replace('{geokret_id}',xmlentities($geokret['id']) , $thisGeoKret);
					$thisGeoKret = str_replace('{geokret_ref}',$gkWP, $thisGeoKret);
					$thisGeoKret = str_replace('{geokret_name}', xmlentities($geokret['name']), $thisGeoKret);
									
					$geokrety .= $thisGeoKret;// . "\n";
				
			}
			$thisline = str_replace('{geokrety}', $geokrety, $thisline);




			append_output($thisline);
			ob_flush();
		}
		mysql_free_result($rs);

		append_output($gpxFoot);

		if ($sqldebug == true) sqldbg_end();
		
		// phpzip versenden
		if ($bUseZip == true)
		{
			$phpzip->add_data($sFilebasename . '.gpx', $content);
			echo $phpzip->save($sFilebasename . '.zip', 'b');
		}
	}
	
	exit;
	
	function xmlentities($str)
	{
		$from[0] = '&'; $to[0] = '&amp;';
		$from[1] = '<'; $to[1] = '&lt;';
		$from[2] = '>'; $to[2] = '&gt;';
		$from[3] = '"'; $to[3] = '&quot;';
		$from[4] = '\''; $to[4] = '&apos;';
		$from[5] = ']]>'; $to[5] = ']] >';

		for ($i = 0; $i <= 4; $i++)
			$str = str_replace($from[$i], $to[$i], $str);

		return filterevilchars($str);
	}

        function cleanup_text($str)
        {
          $str = PLConvert('UTF-8','POLSKAWY',$str);
          $str = strip_tags($str, "<p><br /><li>");
          // <p> -> nic
          // </p>, <br /> -> nowa linia
          $from[] = '<p>'; $to[] = '';
          $from[] = '</p>'; $to[] = "\n";
          $from[] = '<br>'; $to[] = "\n";
          $from[] = '<br />'; $to[] = "\n";
	 $from[] = '<br/>'; $to[] = "\n";
            
          $from[] = '<li>'; $to[] = " - ";
          $from[] = '</li>'; $to[] = "\n";
          
          $from[] = '&oacute;'; $to[] = 'o';
          $from[] = '&quot;'; $to[] = '"';
          $from[] = '&[^;]*;'; $to[] = '';
          
          $from[] = '&'; $to[] = '&amp;';
          $from[] = '<'; $to[] = '&lt;';
          $from[] = '>'; $to[] = '&gt;';
          $from[] = ']]>'; $to[] = ']] >';
					$from[] = ''; $to[] = '';
              
          for ($i = 0; $i < count($from); $i++)
            $str = str_replace($from[$i], $to[$i], $str);
                                 
          return filterevilchars($str);
        }
        
	
        function filterevilchars($str)
	{
		return str_replace('[\\x00-\\x09|\\x0B-\\x0C|\\x0E-\\x1F]', '', $str);
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
	/*
Funkcja do konwersji polskich znakow miedzy roznymi systemami kodowania.
Zwraca skonwertowany tekst.

Argumenty:
$source - string - źródłowe kodowanie
$dest - string - źródłowe kodowanie
$tekst - string - tekst do konwersji

Obsługiwane formaty kodowania to:
POLSKAWY (powoduje zamianę polskich liter na ich łacińskie odpowiedniki)
ISO-8859-2
WINDOWS-1250
UTF-8
ENTITIES (zamiana polskich znaków na encje html)

Przyklad:
echo(PlConvert('UTF-8','ISO-8859-2','Zażółć gęślą jaźń.'));
*/
function PlConvert($source,$dest,$tekst)
{
    $source=strtoupper($source);
    $dest=strtoupper($dest);
    if($source==$dest) return $tekst;

    $chars['POLSKAWY']    =array('a','c','e','l','n','o','s','z','z','A','C','E','L','N','O','S','Z','Z');
    $chars['ISO-8859-2']  =array("\xB1","\xE6","\xEA","\xB3","\xF1","\xF3","\xB6","\xBC","\xBF","\xA1","\xC6","\xCA","\xA3","\xD1","\xD3","\xA6","\xAC","\xAF");
    $chars['WINDOWS-1250']=array("\xB9","\xE6","\xEA","\xB3","\xF1","\xF3","\x9C","\x9F","\xBF","\xA5","\xC6","\xCA","\xA3","\xD1","\xD3","\x8C","\x8F","\xAF");
    $chars['UTF-8']       =array('ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż');
    $chars['ENTITIES']    =array('ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż');

    if(!isset($chars[$source])) return false;
    if(!isset($chars[$dest])) return false;
    
	$tekst = str_replace('a', 'a', $tekst);
	$tekst = str_replace('é', 'e', $tekst);

    return str_replace($chars[$source],$chars[$dest],$tekst);
}
			
			?>
