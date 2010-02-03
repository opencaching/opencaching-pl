#!/usr/bin/php -q
<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

  require('../lib/web.inc.php');
  sql('USE `ocpl`');
setlocale(LC_TIME, 'pl_PL.utf-8');


	$f = fopen('NUTS_AT_2006.csv', 'r');
	while (($buffer = fgetcsv($f, 1000, "\t")) !== false)
	{
    if (count($buffer) != 7) die('invalid format' . "\n");
    
    if ($buffer[1] != 'NUTS_ID' && $buffer[1] != '' && $buffer[3] != '')
    {mysql_query("SET NAMES 'UTF8'");
			$sql=sql("INSERT IGNORE INTO `nuts_codes` (`code`, `name`) VALUES ('&1', '&2')", $buffer[1], $buffer[3]);
			mysql_query($sql);
    }
	}
	fclose($f); 
?>
