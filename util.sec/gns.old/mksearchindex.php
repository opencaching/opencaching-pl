#!/usr/bin/php -q
<?php
 /***************************************************************************
													./util.sec/gns/mksearchindex.php
															-------------------
		begin                : Thu November 1 2005
		copyright            : (C) 2005 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

 /***************************************************************************
		
		Ggf. muss die Location des php-Binaries angepasst werden.
		
		Dieses Script erstellt den Suchindex für Ortsnamen aus den Daten der 
		GNS-DB.
		
	***************************************************************************/

  require_once('../../lib/settings.inc.php');
  require_once('../../lib/clicompatbase.inc.php');
  require_once('../../lib/search.inc.php');

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

	$doubleindex['sankt'] = 'st';

	mysql_query('DELETE FROM gns_search', $dblink);
	mysql_query ('SET NAMES latin2'); 
	
	$rs = mysql_query('SELECT `uni`, `full_name` FROM `gns_locations` WHERE `dsg` LIKE \'PPL%\'', $dblink);
	while ($r = mysql_fetch_array($rs))
	{
		$simpletexts = search_text2sort($r['full_name']);
		$simpletextsarray = explode_multi($simpletexts, ' -/,');

		foreach ($simpletextsarray AS $text)
		{
			if ($text != '')
			{
/*				if (nonalpha($text))
					die($r['uni'] . ' ' . $text . "\n");
*/				
				$simpletext = search_text2simple($text);

				mysql_query('INSERT INTO `gns_search` (`uni_id`, `sort`, `simple`, `simplehash`) VALUES (' . $r['uni'] . ', \'' . addslashes($text) . '\', \'' . addslashes($simpletext) . '\', \'' . addslashes(crc32($simpletext)) . '\')', $dblink);
				
				if (isset($doubleindex[$text]))
					mysql_query('INSERT INTO `gns_search` (`uni_id`, `sort`, `simple`, `simplehash`) VALUES (' . $r['uni'] . ', \'' . addslashes($text) . '\', \'' . addslashes($doubleindex[$text]) . '\', \'' . addslashes(crc32($doubleindex[$text])) . '\')', $dblink);
			}
		}
	}
	mysql_free_result($rs);

/* end search index rebuild */

function nonalpha($str)
{
	for ($i = 0; $i < strlen($str); $i++)
		if (!((ord(substr($str, $i, 1)) >= ord('a')) && (ord(substr($str, $i, 1)) <= ord('z'))))
			return true;
	
	return false;
}
?>
