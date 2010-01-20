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
		
		$content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE rss PUBLIC \"-//Netscape Communications//DTD RSS 0.91//EN\" \"http://my.netscape.com/publish/formats/rss-0.91.dtd\">\n<rss version=\"2.0\">\n<channel>\n<title>Opencaching.pl</title>\n<description>Nowe wiadomości na OpenCaching.PL</description>\n<link>http://www.opencaching.pl</link>\n\n";
		
		
			$rsNews = sql("SELECT `date_posted`, `content` FROM `news` WHERE `topic`=2 AND `display`=1 ORDER BY `date_posted` DESC LIMIT 0, 20");
			while ($rNews = sql_fetch_array($rsNews))
			{
			$thisnewscontent = "<item>\n<title>{date}</title>\n<description>{message}</description>\n</item>\n";
				$thisnewscontent = $tpl_newstopic_without_topic;
				$thisnewscontent = mb_ereg_replace('{date}',date('d.m.Y H:i:s', strtotime($rNews['date_posted'])), $thisnewscontent);
				$thisnewscontent = mb_ereg_replace('{message}', $rNews['content'], $thisnewscontent);
				$content .= $thisnewscontent . "\n";
			}
			mysql_free_result($rsNews);

		$content .= "</channel>\n</rss>\n";

		echo $content;
	}
?>
