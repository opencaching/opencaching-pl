<?php
	$rootpath = '../../../';
	require($rootpath . 'lib/common.inc.php');


	// Titel ausgeben
	echo '<ul>' . "\n";

	$rs = mysql_query('SELECT `user`.`username` `username`, `caches`.`cache_id` `cache_id`, `caches`.`name` `name`, `caches`.`date_created` `date_created` FROM `caches`, `user` WHERE `caches`.`user_id`=`user`.`user_id` AND `type`!=6 AND `status`=1 ORDER BY `date_created` DESC LIMIT 0 , 10', $dblink);
	while ($r = mysql_fetch_array($rs))
	{
		$line = '<li>{date} : <a href="http://www.opencaching.pl/viewcache.php?cacheid={cacheid}" target="_blank">{cachename}</a></li>';
		
		$line = str_replace('{date}', date('d.m.Y', strtotime($r['date_created'])), $line);
		$line = str_replace('{cacheid}', $r['cache_id'], $line);
		$line = str_replace('{cachename}', htmlspecialchars($r['name']), $line);
		$line = str_replace('{ownername}', htmlspecialchars($r['username']), $line);
		
		echo $line . "\n";
	}

	mysql_free_result($rs);

	echo '</ul>' . "\n";
	

?>

