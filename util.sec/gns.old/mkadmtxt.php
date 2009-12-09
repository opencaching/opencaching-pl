#!/usr/bin/php -q
<?php
 /***************************************************************************
													./util.sec/gns/mkadmtxt.php
															-------------------
		begin                : Thu November 6 2005
		copyright            : (C) 2005 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

 /***************************************************************************
		
		Ggf. muss die Location des php-Binaries angepasst werden.
		
		Dieses Script erstellt den Suchindex für Ortsnamen aus den Daten der 
		GNS-DB.
		
	***************************************************************************/

	$rootpath = '../../';
  require_once($rootpath . 'lib/settings.inc.php');
  require_once($rootpath . 'lib/clicompatbase.inc.php');
  require_once($rootpath . 'lib/search.inc.php');
  require_once($rootpath . 'tpl/stdstyle/selectlocid.inc.php');

/* begin db connect */
	$bFail = false;
	$dblink = mysql_connect($dbserver, $dbusername, $dbpasswd);
	if ($dblink != false)
	{
		//database connection established ... set the used database
		if (@mysql_select_db($dbname, $dblink) == false)
		{
			$bFail = true;
			mysql_close($dblink);
			$dblink = false;
		}
	}
	else
		$bFail = true;

	if ($bFail == true)
	{
		echo 'Unable to connect to database';
		exit;
	}
/* end db connect */
  
/* begin search index rebuild */
	
	$rsLocations = mysql_query('SELECT `uni`, `lat`, `lon`, `rc`, `cc1`, `adm1` FROM `gns_locations` WHERE `dsg` LIKE \'PPL%\'', $dblink);
	while ($rLocations = mysql_fetch_array($rsLocations))
	{
		$minlat = getMinLat($rLocations['lon'], $rLocations['lat'], 10, 1);
		$maxlat = getMaxLat($rLocations['lon'], $rLocations['lat'], 10, 1);
		$minlon = getMinLon($rLocations['lon'], $rLocations['lat'], 10, 1);
		$maxlon = getMaxLon($rLocations['lon'], $rLocations['lat'], 10, 1);
		
		// den nächsgelegenen Ort in den geodb ermitteln
		$sql = 'SELECT ' . getSqlDistanceFormula($rLocations['lon'], $rLocations['lat'], 10, 1, 'lon', 'lat', 'geodb_coordinates') . ' `distance`, 
							`geodb_coordinates`.`loc_id` `loc_id`
					  FROM `geodb_coordinates` 
					  WHERE `lon` > ' . $minlon . ' AND 
					        `lon` < ' . $maxlon . ' AND 
					        `lat` > ' . $minlat . ' AND 
					        `lat` < ' . $maxlat . '
					  HAVING `distance` < 10 
					  ORDER BY `distance` ASC 
					  LIMIT 1';
		$rs = sql($sql);
		
		if (mysql_num_rows($rs) == 1)
		{
			$r = mysql_fetch_array($rs);
			mysql_free_result($rs);

			$locid = $r['loc_id'];
			
			$admtxt1 = landFromLocid($locid);
			if ($admtxt1 == '0') $admtxt1 = '';

			// bundesland ermitteln
			$rsAdm2 = sql('SELECT `full_name`, `short_form` FROM `gns_locations` WHERE `rc`=' . ($rLocations['rc'] + 0) . ' AND `fc`=\'A\' AND `dsg`=\'ADM1\' AND `cc1`=\'' . addslashes($rLocations['cc1']) . '\' AND `adm1`=\'' . addslashes($rLocations['adm1']) . '\' AND `nt`=\'N\' LIMIT 1');
			if (mysql_num_rows($rsAdm2) == 1)
			{
				$rAdm2 = mysql_fetch_array($rsAdm2);
				$admtxt2 = $rAdm2['short_form'];

				if ($admtxt2 == '')
					$admtxt2 = $rAdm2['full_name'];
			}
			else
				$admtxt3 = '';

			$admtxt3 = regierungsbezirkFromLocid($locid);
			if ($admtxt3 == '0') $admtxt3 = '';

			$admtxt4 = landkreisFromLocid($locid);
			if ($admtxt4 == '0') $admtxt4 = '';

			sql('UPDATE `gns_locations` SET `admtxt1`=\'' . addslashes($admtxt1) . '\', `admtxt2`=\'' . addslashes($admtxt2) . '\', `admtxt3`=\'' . addslashes($admtxt3) . '\', `admtxt4`=\'' . addslashes($admtxt4) . '\' WHERE uni=' . $rLocations['uni']);
		}
		else
		{
			// was tun?
		}

	}
	mysql_free_result($rsLocations);

/* end search index rebuild */
?>

