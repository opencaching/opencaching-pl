#!/usr/bin/php -q
<?php
 /***************************************************************************
											./util.sec/gns/gns_import.php
											-------------------------
		begin                : Mon October 31 2005
		copyright            : (C) 2005 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

 /***************************************************************************

	Dieses Script liest Dateien von GEOnet Names Server (GNS) ein und importiert 
	diese in die Table gns_locations.
	Homepage:		http://earth-info.nga.mil/gns/html/
	Downloadseite:	http://earth-info.nga.mil/gns/html/cntry_files.html
	Aktuell eingelesene Dateien:
					http://earth-info.nga.mil/gns/html/cntyfile/au.zip
					http://earth-info.nga.mil/gns/html/cntyfile/gm.zip
					http://earth-info.nga.mil/gns/html/cntyfile/sz.zip
	***************************************************************************/

  	require('../../lib/settings.inc.php');
	require('../../lib/clicompatbase.inc.php');

	/* defaults */
	$importfiles = array("pl-latin.txt");

	/* begin db connect */
	$bFail = false;
	$dblink = mysql_connect($dbserver, $dbusername, $dbpasswd);
	if ($dblink != false)
	{
		//database connection established ... set the used database
		if (@mysql_select_db("$dbname", $dblink) == false)					//<!----!!!!!!!!!!!!!!!!!!!!!1
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

	mysql_query("TRUNCATE TABLE gns_locations", $dblink);

	foreach($importfiles as $filename)
		importGns($filename, $dblink);

	function importGns($filename, $dblink)
	{
		echo "Importing '$filename'...\n";
		$file = fopen($filename, "r");
		$cnt = 0;
		while($line = fgets($file, 4096))
		{
			if($cnt++ == 0)	// skip first line
				continue;
	
			$gns =  explode("\t", $line);
			
			$sql = "INSERT INTO gns_locations SET
					rc = '" . addslashes($gns[0]) . "',
					ufi = '" . addslashes($gns[1]) . "',
					uni = '" . addslashes($gns[2]) . "',
					lat = '" . addslashes($gns[3]) . "',
					lon = '" . addslashes($gns[4]) . "',
					dms_lat = '" . addslashes($gns[5]) . "',
					dms_lon = '" . addslashes($gns[6]) . "',
					utm = '" . addslashes($gns[7]) . "',
					jog = '" . addslashes($gns[8]) . "',
					fc = '" . addslashes($gns[9]) . "',
					dsg = '" . addslashes($gns[10]) . "',
					pc = '" . addslashes($gns[11]) . "',
					cc1 = '" . addslashes($gns[12]) . "',
					adm1 = '" . addslashes($gns[13]) . "',
					adm2 = _latin2'" . addslashes($gns[14]) . "',
					dim = '" . addslashes($gns[15]) . "',
					cc2 = '" . addslashes($gns[16]) . "',
					nt = '" . addslashes($gns[17]) . "',
					lc = '" . addslashes($gns[18]) . "',
					SHORT_FORM = _latin2'" . addslashes($gns[19]) . "',
					GENERIC = _latin2'" . addslashes($gns[20]) . "',
					SORT_NAME = _latin2'" . addslashes($gns[21]) . "',
					FULL_NAME = _latin2'" . addslashes($gns[22]) . "',
					FULL_NAME_ND = _latin2'" . addslashes($gns[23]) . "',
					MOD_DATE = '" . addslashes($gns[24]) . "'";
	
			if(!$resp = mysql_query($sql, $dblink))
			{
				echo mysql_error($dblink); echo "\n";
			}
		}
		fclose($file);

		echo "$cnt Records imported\n";
		
		// ein paar Querschläger gleich korrigieren ...
		mysql_query('UPDATE gns_locations SET full_name=\'Zeluce\' WHERE uni=100528 LIMIT 1', $dblink);
		mysql_query('UPDATE gns_locations SET full_name=\'Zitaraves\' WHERE uni=-2780984 LIMIT 1', $dblink);
		mysql_query('UPDATE gns_locations SET full_name=\'Zvabek\' WHERE uni=105075 LIMIT 1', $dblink);
	}
?>
