<?php


	$cacheline = '<tr><td rowspan="2"><img src="{cacheicon}" border="0" width="32" height="32" alt="Cache" title="Cache" />&nbsp;</td><td>{date}&nbsp;<a href="viewcache.php?cacheid={cacheid}">{cachename}</a> von <a href="viewprofile.php?userid={userid}">{username}</a></td></tr>
								<tr><td style="padding-bottom:3px;"><font size="1" color="#001BBC">{locationstring}</font></td></tr>';

	global $lang, $rootpath;

	if (!isset($rootpath)) $rootpath = '/devel/';

	//include template handling
	require_once($rootpath . 'lib/common.inc.php');
	require_once($rootpath . 'lib/cache_icon.inc.php');

	//start_newcaches.include
	$rs = sql("	SELECT	`user`.`user_id` `user_id`,
				`user`.`username` `username`,
				`caches`.`cache_id` `cache_id`,
				`caches`.`name` `name`,
				`caches`.`longitude` `longitude`,
				`caches`.`latitude` `latitude`,
				`caches`.`date_created` `date_created`,
				`caches`.`country` `country`,
				`caches`.`difficulty` `difficulty`,
				`caches`.`terrain` `terrain`,
				`cache_type`.`icon_large` `icon_large`
			FROM `caches`, `user`, `cache_type`
			WHERE `caches`.`user_id`=`user`.`user_id`
			  AND `type`!=6
			  AND `status`=1
			  AND `caches`.`type`=`cache_type`.`id`
			ORDER BY `date_created`
			DESC LIMIT 0 , 10");

	$file_content = '<table border="0" cellspacing="0" cellpadding="0">';
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$record = sql_fetch_array($rs);

		$locationstring = htmlspecialchars(db_CountryFromShort($record['country']), ENT_COMPAT, 'UTF-8');
		if ($record['country'] == 'DE')
		{
			$locid = locidFromCoords($record['longitude'], $record['latitude']);
			if ($locid != 0)
			{
				$rb = regierungsbezirkFromLocid($locid);
				if ($rb != '')
					$locationstring .= ' &gt; ' . $rb;

				$lk = landkreisFromLocid($locid);
				if ($lk != '')
					$locationstring .= ' &gt; ' . $lk;
			}
		}
		else
			$locationstring = htmlspecialchars(db_CountryFromShort($record['country']), ENT_COMPAT, 'UTF-8');

		$thisline = $cacheline;
		$thisline = mb_ereg_replace('{date}', htmlspecialchars(date("d.m.Y", strtotime($record['date_created'])), ENT_COMPAT, 'UTF-8'), $thisline);
		$thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
		$thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
		$thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
		$thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
		$thisline = mb_ereg_replace('{locationstring}', $locationstring, $thisline);
		$thisline = mb_ereg_replace('{cacheicon}', 'tpl/stdstyle/images/'.$record['icon_large'], $thisline);

		$file_content .= $thisline . "\n";
	}

	$file_content .= '</table>';

	$n_file = fopen($stylepath . "/html/start_newcaches.inc.php", 'w');
	fwrite($n_file, $file_content);
	fclose($n_file);

	//newcaches.include
	$rs = sql("	SELECT	`caches`.`cache_id` `cache_id`,
				`caches`.`user_id` `userid`,
				`user`.`username` `username`,
				`caches`.`country` `countryshort`,
				`caches`.`longitude` `longitude`,
				`caches`.`latitude` `latitude`,
				`caches`.`name` `name`,
				`caches`.`date_created` `date_created`,
				`countries`.`&1` `country`,
				`cache_type`.`icon_large` `icon_large`
			FROM `caches`, `user`, `countries`, `cache_type`
			WHERE `caches`.`user_id`=`user`.`user_id`
			  AND `countries`.`short`=`caches`.`country`
			  AND `type` != 6
			  AND `caches`.`status` != 5
			  AND `caches`.`country` != 'DE'
			  AND `caches`.`type`=`cache_type`.`id`
			ORDER BY `caches`.`date_created`
			DESC LIMIT 0, 200", $lang);

	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		//group by country
		$record = sql_fetch_array($rs);

		/*if ($record['countryshort'] == 'DE')
		{
			$locid = locidFromCoords($record['longitude'], $record['latitude']);
			if ($locid != 0)
			{
				$rb = regierungsbezirkFromLocid($locid);
				if ($rb != '')
					$record['country'] .= ' - ' . $rb;
			}
		}*/

		$newcaches[$record['country']][] = array(
			'name' => $record['name'],
			'userid' => $record['userid'],
			'username' => $record['username'],
			'cache_id' => $record['cache_id'],
			'country' => $record['countryshort'],
			'longitude' => $record['longitude'],
			'latitude' => $record['latitude'],
			'date_created' => $record['date_created'],
			'icon_large' => $record['icon_large']
		);
	}

	//sort by country name
	uksort($newcaches, 'cmp');

	$file_content = '
		<table class="content">
			<colgroup>
				<col width="100">
			</colgroup>
			<tr><td class="header"><img src="tpl/stdstyle/images/cache/traditional.png" border="0" width="32" height="32" alt="Cachesuche" title="Cachesuche" align="middle"><font size="4">  <b>Die neuesten Caches ohne Deutschland</b></font></td></tr>
			<tr><td class="spacer"></td></tr>
			';
	if (isset($newcaches))
	{
		foreach ($newcaches AS $countryname => $country_record)
		{
			$file_content .= '<tr><td class="header-small"><b>' . htmlspecialchars($countryname, ENT_COMPAT, 'UTF-8') . '</b></td></tr>';

			foreach ($country_record AS $cache_record)
			{
				$cacheicon = 'tpl/stdstyle/images/'.getSmallCacheIcon($cache_record['icon_large']);

				$file_content .= "<tr><td>";
				$file_content .= htmlspecialchars(date("d.m.Y", strtotime($cache_record['date_created'])), ENT_COMPAT, 'UTF-8');
				$file_content .= ' - <img src="'.$cacheicon.'" border="0" width="16" height="16" alt="Cache" title="Cache"/>';
				$file_content .= '<a href="viewcache.php?cacheid=' . htmlspecialchars($cache_record['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8') . '</a>';
				$file_content .= ' von <a href="viewprofile.php?userid=' . $cache_record['userid'] . '">' . htmlspecialchars($cache_record['username'], ENT_COMPAT, 'UTF-8') . '</a>' . "\n";
				$file_content .= "</td></tr>";
			}
		}
	}

	$file_content .= '</table>';
	$n_file = fopen($stylepath . "/html/newcachesrest.tpl.php", 'w');
	fwrite($n_file, $file_content);
	fclose($n_file);
	unset($newcaches);

	//nextevents.include
	$rs = sql('	SELECT	`user`.`user_id` `user_id`,
				`user`.`username` `username`,
				`caches`.`cache_id` `cache_id`,
				`caches`.`name` `name`,
				`caches`.`longitude` `longitude`,
				`caches`.`latitude` `latitude`,
				`caches`.`date_created` `date_created`,
				`caches`.`country` `country`,
				`caches`.`difficulty` `difficulty`,
				`caches`.`terrain` `terrain`,
				`caches`.`date_hidden`
			FROM `caches`, `user`
			WHERE `user`.`user_id`=`caches`.`user_id`
			  AND `caches`.`date_hidden` >= curdate()
			  AND `caches`.`type` = 6
			  AND `caches`.`status` != 5
			ORDER BY `date_hidden` ASC
			LIMIT 0 , 10', $dblink);

	$file_content = '';
	if (mysql_num_rows($rs) == 0)
	{
		$file_content = 'leider keine Events';
	}
	else
	{
		$file_content = '<table border="0" cellspacing="0" cellpadding="0">';
		for ($i = 0; $i < mysql_num_rows($rs); $i++)
		{
			$record = sql_fetch_array($rs);

			$locationstring = htmlspecialchars(db_CountryFromShort($record['country']), ENT_COMPAT, 'UTF-8');
			if ($record['country'] == 'DE')
			{
				$locid = locidFromCoords($record['longitude'], $record['latitude']);
				if ($locid != 0)
				{
					$rb = regierungsbezirkFromLocid($locid);
					if ($rb != '')
						$locationstring .= ' &gt; ' . $rb;

					$lk = landkreisFromLocid($locid);
					if ($lk != '')
						$locationstring .= ' &gt; ' . $lk;
				}
			}
			else
				$locationstring = htmlspecialchars(db_CountryFromShort($record['country']), ENT_COMPAT, 'UTF-8');

			$thisline = $cacheline;
			$thisline = mb_ereg_replace('{date}', htmlspecialchars(date("d.m.Y", strtotime($record['date_hidden'])), ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
			$thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
			$thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
			$thisline = mb_ereg_replace('{locationstring}', $locationstring, $thisline);
		  $thisline = mb_ereg_replace('{cacheicon}', 'tpl/stdstyle/images/cache/22x22-event.png', $thisline);

			$file_content .= $thisline . "\n";
		}
		$file_content .= '</table>';
	}

	$n_file = fopen($stylepath . "/html/nextevents.inc.php", 'w');
	fwrite($n_file, $file_content);
	fclose($n_file);

	//user definied sort function
	function cmp($a, $b)
	{
		if ($a == $b)
		{
			return 0;
		}
		return ($a > $b) ? 1 : -1;
	}

	function kfzFromCoords($lon, $lat)
	{
		global $dblink;

		if (!is_numeric($lon)) return '';
		if (!is_numeric($lat)) return '';
		$lon = $lon + 0;
		$lat = $lat + 0;

		$rs = sql('	SELECT `geodb_textdata`.`text_val` `kfz`,
					((' . $lon . ' - `geodb_coordinates`.`lon`) * (' . $lon . ' - `geodb_coordinates`.`lon`) +
					 (' . $lat . ' - `geodb_coordinates`.`lat`) * (' . $lat . ' - `geodb_coordinates`.`lat`)) `dist`
				FROM `geodb_coordinates`, `geodb_textdata`
				WHERE `geodb_coordinates`.loc_id=`geodb_textdata`.`loc_id`
				  AND `geodb_textdata`.`text_type`=500500000
				  AND `geodb_coordinates`.`lon` > ' . ($lon - 0.15) . '
				  AND `geodb_coordinates`.`lon` < ' . ($lon + 0.15) . '
				  AND `geodb_coordinates`.`lat` > ' . ($lat - 0.15) . '
				  AND `geodb_coordinates`.`lat` < ' . ($lat + 0.15) . '
				ORDER BY `dist` ASC
				LIMIT 1');

		if ($r = sql_fetch_array($rs))
			return $r['kfz'];
		else
			return '';
	}

	function locidFromCoords($lon, $lat)
	{
		global $dblink;

		if (!is_numeric($lon)) return '';
		if (!is_numeric($lat)) return '';
		$lon = $lon + 0;
		$lat = $lat + 0;

		$rs = sql('	SELECT	`geodb_coordinates`.`loc_id` `loc_id`,
					(( ' . $lon . ' - `geodb_coordinates`.`lon` ) * ( ' . $lon . ' - `geodb_coordinates`.`lon` ) +
					 ( ' . $lat . ' - `geodb_coordinates`.`lat` ) * ( ' . $lat . ' - `geodb_coordinates`.`lat` )) `dist`
				FROM `geodb_coordinates`
				WHERE `geodb_coordinates`.`lon` > ' . ($lon - 0.15) . '
				  AND `geodb_coordinates`.`lon` < ' . ($lon + 0.15) . '
				  AND `geodb_coordinates`.`lat` > ' . ($lat - 0.15) . '
				  AND `geodb_coordinates`.`lat` < ' . ($lat + 0.15) . '
				ORDER BY `dist` ASC
				LIMIT 1', $dblink);
		if ($r = sql_fetch_array($rs))
			return $r['loc_id'];
		else
			return 0;
	}

	function regierungsbezirkFromLocid($locid)
	{
		if (!is_numeric($locid)) return '';
		$locid = $locid + 0;

		$rs = sql("SELECT `rb`.`text_val` `regierungsbezirk` FROM `geodb_textdata` `ct`, `geodb_textdata` `rb`, `geodb_hierarchies` `hr` WHERE `ct`.`loc_id`=`hr`.`loc_id` AND `hr`.`id_lvl4`=`rb`.`loc_id` AND `ct`.`text_type`=500100000 AND `rb`.`text_type`=500100000 AND `ct`.`loc_id`='&1' AND `hr`.`id_lvl4`!=0", $locid);
		if ($r = sql_fetch_array($rs))
			return $r['regierungsbezirk'];
		else
			return 0;
	}

	function landkreisFromLocid($locid)
	{
		global $dblink;

		if (!is_numeric($locid)) return '';
		$locid = $locid + 0;

		$rs = sql("SELECT `rb`.`text_val` `regierungsbezirk` FROM `geodb_textdata` `ct`, `geodb_textdata` `rb`, `geodb_hierarchies` `hr` WHERE `ct`.`loc_id`=`hr`.`loc_id` AND `hr`.`id_lvl5`=`rb`.`loc_id` AND `ct`.`text_type`=500100000 AND `rb`.`text_type`=500100000 AND `ct`.`loc_id`='&1' AND `hr`.`id_lvl5`!=0", $locid);
		if ($r = sql_fetch_array($rs))
			return $r['regierungsbezirk'];
		else
			return 0;
	}
?>
