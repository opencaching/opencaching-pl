<?php
	$rootpath = '../../../';
	require_once($rootpath . 'lib/common.inc.php');
        require_once($rootpath . 'lib/cache_icon.inc.php');

	echo '<html>';
	echo '<head><meta http-equiv="content-type" content="text/xhtml; charset=ISO-8859-2" />';
	echo '<link rel="stylesheet" href="http://home.debitel.net/user/geocaching/formate.css"></head>';
	echo '<body style="margin:0 0 0 0;">';
	
	echo '<span style="line-height:1em;">';

	// Titel ausgeben
	echo '<h1>10 najnowszych skrzynek <img src="http://opencaching.pl/images/favicon.gif" alt="" width=16 height=16 border=0>&nbsp;OC PL (' . date('d.m.Y H:i') . ')</h1>' . "\n";
	echo '<ul>' . "\n";
	$rs = mysql_query('SELECT `user`.`user_id` `user_id`, `user`.`username` `username`, `caches`.`cache_id` `cache_id`, `caches`.`name` `name`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, `caches`.`date_created` `date_created`, `caches`.`country` `country`, `caches`.`difficulty` `difficulty`, `caches`.`terrain` `terrain`, `cache_type`.`short` short FROM `caches`, `user`, `cache_type` WHERE `caches`.`user_id`=`user`.`user_id` AND `type`!=6 AND `status`=1 AND `caches`.`type`=`cache_type`.`id` ORDER BY `date_created` DESC LIMIT 0 , 10', $dblink);

//	$rs = mysql_query('SELECT `user`.`username` `username`, `caches`.`cache_id` `cache_id`, `caches`.`name` `name`, `caches`.`date_created` `date_created` FROM `caches`, `user` WHERE `caches`.`user_id`=`user`.`user_id` AND `type`!=6 AND `status`=1 ORDER BY `date_created` DESC LIMIT 0 , 10', $dblink);
	while ($r = mysql_fetch_array($rs))
	{
		$line = '<img src="http://opencaching.pl{cacheicon}.gif" width=16 height=16 border="0" alt="Cache" title="Cache"/>{date} : <a href="http://www.opencaching.pl/viewcache.php?cacheid={cacheid}" target="_blank">{cachename}</a><br>';
		
		$line = str_replace('{date}', date('d.m.Y', strtotime($r['date_created'])), $line);
		$line = str_replace('{cacheid}', $r['cache_id'], $line);
		$line = str_replace('{cachename}', htmlspecialchars($r['name']), $line);
		$line = str_replace('{ownername}', htmlspecialchars($r['username']), $line);
		$line = str_replace('{cacheicon}', '/tpl/stdstyle/images/cache/16x16-'.$r['short'], $line);
		
		echo $line . "\n";
	}

	mysql_free_result($rs);

	echo '</ul>' . "\n";
	
	echo '</span>';
	echo '</body>';
	echo '</html>';
?>
