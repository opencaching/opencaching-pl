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
	require_once($opt['rootpath'] . 'lib2/logic/coordinate_batch.class.php');
	$opt['db']['warn']['time'] = 0;

	ConvertToWGS84('bio2008_num');
	ConvertToWGS84('ffh2006_num');
	ConvertToWGS84('lsg2006_num');
	ConvertToWGS84('np2008_num');
	ConvertToWGS84('nsg2006_num');
	ConvertToWGS84('ntp2008_num');
	ConvertToWGS84('spa_2007_num');

function ConvertToWGS84($table)
{
	$n = 1;
	$pks = array();
	$val = array();

	$rs = sql("SELECT * FROM `bfn`.`" . sql_escape($table) . "`");
	while ($r = sql_fetch_assoc($rs))
	{
		$pks[] = array('GID' => $r['GID'], 'ESEQ' => $r['ESEQ'], 'SEQ' => $r['SEQ']);
		$val[] = array('X1' => $r['X1'], 'X2' => $r['X2'], 'Y1' => $r['Y1'], 'Y2' => $r['Y2']);

		if (count($pks) >= 1000)
		{
			flushToDB($table, $pks, $val);
			$pks = array();
			$val = array();

			echo $table . ' ' . $n . "\n";
		}

		$n = $n + 1;
	}
	sql_free_result($rs);
	flushToDB($table, $pks, $val);
}

function flushToDB($table, $pks, $val)
{
	$cb = new coordinate_batch();
	$cb->openGK();

	for ($n = 0; $n < count($val); $n++)
	{
		$cb->writeGK($val[$n]['X1'], $val[$n]['Y1']);
		$cb->writeGK($val[$n]['X2'], $val[$n]['Y2']);
	}
	$coords = $cb->analyseOutput();

	for ($n = 0; $n < count($pks); $n++)
	{
		sql("UPDATE `bfn_wgs84`.`" . sql_escape($table) . "` SET `x1`='&1', `x2`='&2', `y1`='&3', `y2`='&4' 
		            WHERE `GID`='&5' AND `ESEQ`='&6' AND `SEQ`='&7'",
			$coords[$n*2]['lon'],
			$coords[$n*2+1]['lon'],
			$coords[$n*2]['lat'],
			$coords[$n*2+1]['lat'],
			$pks[$n]['GID'],
			$pks[$n]['ESEQ'],
			$pks[$n]['SEQ']);
	}
}
?>