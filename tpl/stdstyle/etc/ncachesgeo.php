<?php
	$rootpath = '../../../';
	require_once($rootpath . 'lib/common.inc.php');
        require_once($rootpath . 'lib/cache_icon.inc.php');

 	// Titel ausgeben

 	echo '<ul>' . "\n";
       mysql_query("SET NAMES 'latin2'"); 
	$rs = mysql_query('SELECT `user`.`user_id` `user_id`, `user`.`username` `username`, `caches`.`cache_id` `cache_id`, `caches`.`name` `name`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, `caches`.`date_created` `date_created`, `caches`.`country` `country`, `caches`.`difficulty` `difficulty`, `caches`.`terrain` `terrain`, `cache_type`.`short` short FROM `caches`, `user`, `cache_type` WHERE `caches`.`user_id`=`user`.`user_id` AND `status`=1 AND `caches`.`type`=`cache_type`.`id` ORDER BY `date_created` DESC LIMIT 0 , 10', $dblink);

//	$rs = mysql_query('SELECT `user`.`username` `username`, `caches`.`cache_id` `cache_id`, `caches`.`name` `name`, `caches`.`date_created` `date_created` FROM `caches`, `user` WHERE `caches`.`user_id`=`user`.`user_id` AND `type`!=6 AND `status`=1 ORDER BY `date_created` DESC LIMIT 0 , 10', $dblink);
	while ($r = mysql_fetch_array($rs))
	{
		$line = '{date} : <a href="http://www.opencaching.pl/viewcache.php?cacheid={cacheid}" target="_blank">{cachename}</a> by {ownername}<br>';
		
		$line = str_replace('{date}', date('d.m.Y', strtotime($r['date_created'])), $line);
		$line = str_replace('{cacheid}', $r['cache_id'], $line);
		$line = str_replace('{cachename}', htmlspecialchars($r['name']), $line);
		$line = str_replace('{ownername}', htmlspecialchars($r['username']), $line);
//		$line = str_replace('{cacheicon}', '/tpl/stdstyle/images/cache/16x16-'.$r['short'], $line);
		
		echo $line . "\n";
	}

	mysql_free_result($rs);

	echo '</ul>' . "\n";
	
?>

