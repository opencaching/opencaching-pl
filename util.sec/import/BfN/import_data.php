#!/usr/bin/php -q
<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Import Layer Data from gis.NUTS_RG_03M_2006
 *
 ***************************************************************************/

	$opt['rootpath'] = '../../../';

	// chdir to proper directory (needed for cronjobs)
	chdir(substr(realpath($_SERVER['PHP_SELF']), 0, strrpos(realpath($_SERVER['PHP_SELF']), '/')));

	require($opt['rootpath'] . 'lib2/cli.inc.php');
	$opt['db']['warn']['time'] = 0;

	ImportAreas('bio', 'bio2008', 'SELECT `GID`, `f_NAME` AS `name` FROM `bfn`.`bio2008` WHERE `f_BIORES`!=0', 0);
	ImportAreas('bio', 'bio2008', 'SELECT `GID`, `f_NAME` AS `name` FROM `bfn`.`bio2008` WHERE `f_BIORES`=0', 1);

	ImportAreas('ffh', 'ffh2006', 'SELECT `GID`, `f_NAME` AS `name` FROM `bfn`.`ffh2006` WHERE `f_FFH`!=0', 0);
	ImportAreas('ffh', 'ffh2006', 'SELECT `GID`, `f_NAME` AS `name` FROM `bfn`.`ffh2006` WHERE `f_FFH`=0', 1);

	ImportAreas('lsg', 'lsg2006', 'SELECT `GID`, `f_NAME` AS `name` FROM `bfn`.`lsg2006` WHERE `f_LSG`!=0', 0);
	ImportAreas('lsg', 'lsg2006', 'SELECT `GID`, `f_NAME` AS `name` FROM `bfn`.`lsg2006` WHERE `f_LSG`=0', 1);

	ImportAreas('np', 'np2008', 'SELECT `GID`, `f_NP_NAME` AS `name` FROM `bfn`.`np2008` WHERE `f_NP`!=0', 0);
	ImportAreas('np', 'np2008', 'SELECT `GID`, `f_NP_NAME` AS `name` FROM `bfn`.`np2008` WHERE `f_NP`=0', 1);

	ImportAreas('nsg', 'nsg2006', 'SELECT `GID`, `f_NAME` AS `name` FROM `bfn`.`nsg2006` WHERE `f_NSG`!=0', 0);
	ImportAreas('nsg', 'nsg2006', 'SELECT `GID`, `f_NAME` AS `name` FROM `bfn`.`nsg2006` WHERE `f_NSG`=0', 1);

	ImportAreas('ntp', 'ntp2008', 'SELECT `GID`, `f_NTP_NAME` AS `name` FROM `bfn`.`ntp2008` WHERE `f_NTP`!=0', 0);
	ImportAreas('ntp', 'ntp2008', 'SELECT `GID`, `f_NTP_NAME` AS `name` FROM `bfn`.`ntp2008` WHERE `f_NTP`=0', 1);

	ImportAreas('spa', 'spa_2007', 'SELECT `GID`, `f_NAME` AS `name` FROM `bfn`.`spa_2007` WHERE `f_SPA`!=0', 0);
	ImportAreas('spa', 'spa_2007', 'SELECT `GID`, `f_NAME` AS `name` FROM `bfn`.`spa_2007` WHERE `f_SPA`=0', 1);

function ImportAreas($sType, $sTable, $sSelectSQL, $bExclude)
{
	$rsArea = sql($sSelectSQL);
	while ($rArea = sql_fetch_assoc($rsArea))
	{
		echo "Import " . $rArea['name'] . "\n";
		$pt = array();
		$sLastPt = array();

		$rsWP = sql("SELECT `x1`, `y1`, `x2`, `y2` FROM `bfn_wgs84`.`" . sql_escape($sTable) . "_num` WHERE `GID`='&1' ORDER BY `SEQ` ASC", $rArea['GID']);
		while ($rWP = sql_fetch_assoc($rsWP))
		{
			$pt[] = $rWP['x1'] . ' ' . $rWP['y1'];;
			$sLastPt[0] = $rWP['x2'];
			$sLastPt[1] = $rWP['y2'];
		}
		sql_free_result($rsWP);

		$pt[] = $sLastPt[0] . ' ' . $sLastPt[1];

		$sLinestring = 'LINESTRING(' . implode(',', $pt) . ')';
		//echo $sLinestring . "\n";
		sql("INSERT INTO `npa_areas` (`type_id`, `name`, `shape`, `exclude`) VALUES ('&1', '&2', LineFromText('&3'), '&4')", $sType, $rArea['name'], $sLinestring, $bExclude);
		$sLineString = '';
	}
	sql_free_result($rsArea);
}	
?>