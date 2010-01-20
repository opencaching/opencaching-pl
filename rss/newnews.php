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
		
		$content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<rss version=\"2.0\">\n<channel>\n<title>OC PL - Najnowsze wiadomości</title>\n<link>http://www.opencaching.pl/news.php</link>\n <description><![CDATA[Najnowsze wiadomości]]></description><image>
		<title>OpenCaching.PL</title>
		<url>http://www.opencaching.pl/images/oc.png</url>
		<link>http://www.opencaching.pl</link><width>100</width><height>28</height></image>\n";
		
		
			$rsNews = sql('SELECT `date_posted`, `content` FROM `news` WHERE `topic`=2 AND `display`=1 ORDER BY `date_posted` DESC LIMIT ' . $perpage);
			
			while ($rNews = sql_fetch_array($rsNews))
			{
			$thisline = "<item>\n<title>{date}</title>\n<description>{message}</description>\n<link>http://www.opencaching.pl/news.php</link><pubDate>{date}</pubDate>\n</item>\n";
			
				$thisline =str_replace('{date}',date('d-m-Y', strtotime($rNews['date_posted'])), $thisline);
				$thisline = str_replace('{message}', htmlspecialchars($rNews['content']), $thisline);

			$content .= $thisline . "\n";
		}
		mysql_free_result($rsNews);
		$content .= "</channel>\n</rss>\n";

		echo $content;
	}
?>
