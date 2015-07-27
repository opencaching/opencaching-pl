<?php
  // Unicode Reminder メモ

	$rootpath = '../../../';
	require($rootpath . 'lib/common.inc.php');

	header('Content-type: text/html; charset=utf-8');

	echo '<html>';
	echo '<head><link rel="stylesheet" href="http://home.debitel.net/user/geocaching/formate.css"></head>';
	echo '<body style="margin:0 0 0 0;">';
	
	echo '<span style="line-height:1em;">';

	// Titel ausgeben
	echo '<h1>Die 10 neuesten Caches von <img src="http://www.opencaching.de/images/favicon.gif" alt="" width=16 height=16 border=0>&nbsp;Opencaching.de (' . date('d.m.Y H:i') . ')</h1>' . "\n";
	echo '<ul>' . "\n";

	$rs = sql('SELECT `user`.`username` `username`, `caches`.`cache_id` `cache_id`, `caches`.`name` `name`, `caches`.`date_created` `date_created` FROM `caches`, `user` WHERE `caches`.`user_id`=`user`.`user_id` AND `type`!=6 AND `status`=1 ORDER BY `date_created` DESC LIMIT 0 , 10');
	while ($r = sql_fetch_array($rs))
	{
		$line = '<li>{date} : <a href="http://www.opencaching.de/viewcache.php?cacheid={cacheid}" target="_blank">{cachename}</a> by {ownername} (OC)</li>';
		
		$line = mb_ereg_replace('{date}', date('d.m.Y', strtotime($r['date_created'])), $line);
		$line = mb_ereg_replace('{cacheid}', $r['cache_id'], $line);
		$line = mb_ereg_replace('{cachename}', htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8'), $line);
		$line = mb_ereg_replace('{ownername}', htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8'), $line);
		
		echo $line . "\n";
	}

	mysql_free_result($rs);

	echo '</ul>' . "\n";
	
	echo '</span>';
	echo '</body>';
	echo '</html>';
?>