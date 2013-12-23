<?php
	/***************************************************************************
															./lib/search.loc.inc.php
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
                                     				                                
		loc search output
		
	****************************************************************************/

	global $content, $bUseZip, $sqldebug, $hide_coords, $usr;
	set_time_limit(1800);
$locHead = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<loc version="1.0" src="'.$absolute_server_URI.'">' . "\n";
	
$locLine = '
<waypoint>
	<name id="{{waypoint}}"><![CDATA[{mod_suffix}{cachename} '.tr('from').' {owner}, {type_text} ({difficulty}/{terrain})]]></name>
	<coord lat="{lat}" lon="{lon}"/>
	<type>Geocache</type>
	<link text="Cache Details">'.$absolute_server_URI.'viewcache.php?cacheid={cacheid}</link>
</waypoint>
';

$locFoot = '</loc>';

$cacheTypeText[1] = "".tr('cacheType_5')."";
$cacheTypeText[2] = "".tr('cacheType_1')."";
$cacheTypeText[3] = "".tr('cacheType_2')."";
$cacheTypeText[4] = "".tr('cacheType_8')."";
$cacheTypeText[5] = "".tr('cacheType_7')."";
$cacheTypeText[6] = "".tr('cacheType_6')."";
$cacheTypeText[7] = "".tr('cacheType_3')."";
$cacheTypeText[8] = "".tr('cacheType_4')."";
$cacheTypeText[10] = "".tr('cacheType_10')."";


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

		// cleanup (old gpxcontent lingers if gpx-download is cancelled by user)		
		sql('DROP TEMPORARY TABLE IF EXISTS `loccontent`');
		// temporäre tabelle erstellen
		sql('CREATE TEMPORARY TABLE `loccontent` ' . $sql . $sqlLimit, $sqldebug);
		
		$rsCount = sql('SELECT COUNT(*) `count` FROM `loccontent`');
		$rCount = sql_fetch_array($rsCount);
		mysql_free_result($rsCount);
		
		if ($rCount['count'] == 1)
		{
			$rsName = sql('SELECT `caches`.`wp_oc` `wp_oc` FROM `loccontent`, `caches` WHERE `loccontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
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
					$sFilebasename = "$short_sitename" . $options['queryid'];
				}
			}
		}
			
		$bUseZip = ($rCount['count'] > 200000000000);
		$bUseZip = $bUseZip || ($_REQUEST['zip'] == '1');
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
				header('Content-Disposition: attachment; filename='. $sFilebasename . '.zip');
			}
			else
			{
				header("Content-type: application/loc");
				header("Content-Disposition: attachment; filename=" . $sFilebasename . ".loc");
			}
		}

		append_output($locHead);
		
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

		$rs = sql('SELECT `loccontent`.`cache_id` `cacheid`, `loccontent`.`longitude` `longitude`, `loccontent`.`latitude` `latitude`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`wp_oc` `waypoint`, `cache_type`.`short` `typedesc`, `cache_type`.`id` `type_id`, `cache_size`.`pl` `sizedesc`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username` FROM `loccontent`, `caches`, `cache_type`, `cache_size`, `user` WHERE `loccontent`.`cache_id`=`caches`.`cache_id` AND `loccontent`.`type`=`cache_type`.`id` AND `loccontent`.`size`=`cache_size`.`id` AND `loccontent`.`user_id`=`user`.`user_id`');
		while($r = sql_fetch_array($rs))
		{
			$thisline = $locLine;
			
			$lat = sprintf('%01.5f', $r['latitude']);
			$thisline = mb_ereg_replace('{lat}', $lat, $thisline);
			
			$lon = sprintf('%01.5f', $r['longitude']);
			$thisline = mb_ereg_replace('{lon}', $lon, $thisline);

			$thisline = mb_ereg_replace('{{waypoint}}', $r['waypoint'], $thisline);
			$thisline = mb_ereg_replace('{cachename}', PLConvert('UTF-8','POLSKAWY',$r['name']), $thisline);

			//modified coords
		if ($r['type_id'] =='7' && $usr!=false) {  //check if quiz (7) and user is logged 
			if (!isset($dbc)) {$dbc = new dataBase();};	
						$mod_coord_sql = 'SELECT cache_id FROM cache_mod_cords
						WHERE cache_id = :v1 AND user_id =:v2';

			$params['v1']['value'] = (integer) $r['cacheid'];
			$params['v1']['data_type'] = 'integer';
			$params['v2']['value'] = (integer) $usr['userid'];
			$params['v2']['data_type'] = 'integer';

			$dbc ->paramQuery($mod_coord_sql,$params);
			Unset($params);	

			if ($dbc->rowCount() > 0 )
			{
				$thisline = str_replace('{mod_suffix}', '<F>', $thisline);
			} else {
				$thisline = str_replace('{mod_suffix}', '', $thisline);
			}
		} else {
			$thisline = str_replace('{mod_suffix}', '', $thisline);
		}; 
			
//			if (($r['status'] == 2) || ($r['status'] == 3))
//			{
//				if ($r['status'] == 2)
//					$thisline = mb_ereg_replace('{archivedflag}', 'Czasowo niedostepna!, ', $thisline);
//				else
//					$thisline = mb_ereg_replace('{archivedflag}', 'Zarchiwizowana!, ', $thisline);
//			}
//			else
//				$thisline = mb_ereg_replace('{archivedflag}', '', $thisline);
			
			$thisline = mb_ereg_replace('{type_text}', $cacheTypeText[$r['type_id']], $thisline);
			$thisline = mb_ereg_replace('{{size}}', PLConvert('UTF-8','POLSKAWY',tr('cacheType_'.$r['type_id'])), $thisline);
			
			$difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
			$thisline = mb_ereg_replace('{difficulty}', $difficulty, $thisline);

			$terrain = sprintf('%01.1f', $r['terrain'] / 2);
			$thisline = mb_ereg_replace('{terrain}', $terrain, $thisline);

			$thisline = mb_ereg_replace('{owner}', $r['username'], $thisline);
			$thisline = mb_ereg_replace('{cacheid}', $r['cacheid'], $thisline);

			append_output($thisline);
			ob_flush();
		}
		mysql_free_result($rs);
		unset($dbc);
		append_output($locFoot);
		
		if ($sqldebug == true) sqldbg_end();
		
		// phpzip versenden
		if ($bUseZip == true)
		{
			$phpzip->add_data($sFilebasename . '.loc', $content);
			echo $phpzip->save($sFilebasename . '.zip', 'b');
		}

		exit;
		}
		
		function xmlentities($str)
		{
			$from[0] = '&'; $to[0] = '&amp;';
			$from[1] = '<'; $to[1] = '&lt;';
			$from[2] = '>'; $to[2] = '&gt;';
			$from[3] = '"'; $to[3] = '&quot;';
			$from[4] = '\''; $to[4] = '&apos;';

			for ($i = 0; $i <= 4; $i++)
				$str = mb_ereg_replace($from[$i], $to[$i], $str);
    				$str = preg_replace('/[[:cntrl:]]/', '', $str);

			return $str;
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

    return str_replace($chars[$source],$chars[$dest],$tekst);
}

?>
