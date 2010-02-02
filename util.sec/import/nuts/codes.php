#!/usr/bin/php -q
<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	$opt['rootpath'] = '../../../';

	// chdir to proper directory (needed for cronjobs)
	chdir(substr(realpath($_SERVER['PHP_SELF']), 0, strrpos(realpath($_SERVER['PHP_SELF']), '/')));

	require($opt['rootpath'] . 'lib2/cli.inc.php');

	$f = fopen('data/NUTS_at_2006.txt', 'r');
	while (($buffer = fgetcsv($f, 1000, "\t")) !== false)
	{
    if (count($buffer) != 6) die('invalid format' . "\n");
    
    if ($buffer[1] != 'NUTS_ID' && $buffer[1] != '' && $buffer[5] != '')
    {
			sql("INSERT IGNORE INTO `nuts_codes` (`code`, `name`) VALUES ('&1', '&2')", $buffer[1], $buffer[5]);
    }
	}
	fclose($f); 
?>