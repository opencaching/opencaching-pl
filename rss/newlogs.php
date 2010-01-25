<?php
/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*
	***************************************************************************/

 
	//prepare the templates and include all neccessary
	$rootpath = '../';
	require_once($rootpath . 'lib/common.inc.php');
	
	//Preprocessing
	if ($error == false)
	{
		//get the news
		$perpage = 20;
		
		$content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<rss version=\"2.0\">\n<channel>\n<title>OC PL - Najnowsze logi</title>\n<ttl>60</ttl><description>Najnowsze logi na OpenCaching.PL </description>\n<link>http://www.opencaching.pl/newlogs.php</link><image>
		<title>OC PL - Najnowsze logi</title>
		<url>http://www.opencaching.pl/images/oc.png</url>
		<link>http://www.opencaching.pl/newlogs.php</link><width>100</width><height>28</height></image>\n\n";
		
		$rs = sql("SELECT  cache_logs.id,
       cache_logs.cache_id AS cache_id,
       cache_logs.type AS log_type,
       cache_logs.date AS log_date,
       (SELECT name FROM caches
       WHERE cache_id = cache_logs.cache_id) AS cache_name,
       (SELECT username FROM user
       WHERE user_id = cache_logs.user_id) AS user_name,
       (SELECT text_combo FROM log_types_text
       WHERE log_types_id = cache_logs.type AND
               lang = 'PL') AS log_name
FROM    cache_logs
WHERE   cache_logs.deleted = 0
ORDER BY cache_logs.date_created DESC
LIMIT 20");
	
	//while ($r = sql_fetch_array($rs))
		for ($i = 0; $i < mysql_num_rows($rs); $i++)
		{
			$r = sql_fetch_array($rs);
			$thisline = "<item>\n<title>{cachename} - Użytkownik: {username} - Wpis: {logtype}</title>\n<description>Użytkownik: {username} - Wpis: {logtype} - Data: {date} </description>\n<link>http://www.opencaching.pl/viewlogs.php?cacheid={cacheid}</link>\n</item>\n";
			
			$thisline = str_replace('{cacheid}', $r['cache_id'], $thisline);
//			$thisline = str_replace('{userid}', $r['userid'], $thisline);
			$thisline = str_replace('{cachename}', htmlspecialchars($r['cache_name']), $thisline);
			$thisline = str_replace('{logtype}', htmlspecialchars($r['log_name']), $thisline);
			$thisline = str_replace('{username}', htmlspecialchars($r['user_name']), $thisline);
			$thisline = str_replace('{date}', date('d-m-Y', strtotime($r['log_date'])), $thisline);
			//$thisline = str_replace('{imglink}', 'tpl/stdstyle/images/'.getSmallCacheIcon($r['icon_large']), $thisline);

			$content .= $thisline . "\n";
		}
		mysql_free_result($rs);
		$content .= "</channel>\n</rss>\n";

		echo $content;
	}
?>
