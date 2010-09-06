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

	$tplname = 'region';
	require_once('./lib/common.inc.php');
	require_once('./lib2/logic/gis.class.php');


	$lat_float = 0;
	if (isset($_REQUEST['lat']))
		$lat_float = (float) $_REQUEST['lat'];

	$lon_float = 0;
	if (isset($_REQUEST['lon']))
		$lon_float = (float) $_REQUEST['lon'];

		/* begin db connect */
		db_connect();
		if ($dblink === false)
		{
			echo 'Unable to connect to database';
			exit;
		}

			$sCode = '';

			$rsLayers = sql("SELECT `level`, `code`, AsText(`shape`) AS `geometry` FROM `nuts_layer` WHERE WITHIN(GeomFromText('&1'), `shape`) ORDER BY `level` DESC", 'POINT(' . $lon_float . ' ' . $lat_float . ')');
			while ($rLayers = mysql_fetch_assoc($rsLayers))
			{
				if (gis::ptInLineRing($rLayers['geometry'], 'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')'))
				{
					$sCode = $rLayers['code'];
					break;
				}
			}
			mysql_free_result($rsLayers);
			
			if ($sCode != '')
			{
				$adm1 = null; $code1 = null;
				$adm2 = null; $code2 = null;
				$adm3 = null; $code3 = null;
				$adm4 = null; $code4 = null;

				if (mb_strlen($sCode) > 5) $sCode = mb_substr($sCode, 0, 5);

				if (mb_strlen($sCode) == 5)
				{
					$code4 = $sCode;
					$adm4 = sqlValue("SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'",0);
					$sCode = mb_substr($sCode, 0, 4);
				}

				if (mb_strlen($sCode) == 4)
				{
					$code3 = $sCode;
					$adm3 = sqlvalue("SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'",0);
					$sCode = mb_substr($sCode, 0, 3);
				}

				if (mb_strlen($sCode) == 3)
				{
					$code2 = $sCode;
					$adm2 = sqlvalue("SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'", 0);
					$sCode = mb_substr($sCode, 0, 2);
				}

				if (mb_strlen($sCode) == 2)
				{
					$code1 = $sCode;
	
					if(checkField('countries','list_default_'.$lang) )
						$lang_db = $lang;
					else
						$lang_db = "en";
				
					// try to get localised name first
					$adm1 = sqlvalue("SELECT `countries`.`pl`
					 FROM `countries`
					WHERE `countries`.`short`='$sCode'",0);

					if ($adm1 == null)
						$adm1 = sqlvalue("SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'", 0);
				}
			tpl_set_var('country', $adm1);
			tpl_set_var('region', $adm3);
			}

		db_disconnect();







	tpl_set_var('lon_float', sprintf('%0.5f', $lon_float));
	tpl_set_var('lon_dir', $lon_dir);
	tpl_set_var('lon_deg_int', $lon_deg_int);
	tpl_set_var('lon_min_int', $lon_min_int);
	tpl_set_var('lon_sec_float', $lon_sec_float);
	tpl_set_var('lon_min_float', $lon_min_float);



	//make the template and send it out
	tpl_BuildTemplate();
?>
