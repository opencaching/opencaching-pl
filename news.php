<?php
/***************************************************************************
																./news.php
															-------------------
		begin                : Mon June 14 2004
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************
	      
   Unicode Reminder ăĄă˘
                                   				                                
	 include the news HTML file
	
 ****************************************************************************/
 
	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	
	//Preprocessing
	if ($error == false)
	{
		//get the news
		$tplname = 'news';
		$newscontent = '
				<div class="content2-pagetitle">
				<img src="tpl/stdstyle/images/description/22x22-description.png" border="0" width="22" height="22" alt="News" title="news" align="middle">Aktualności</div>
				
				';
		require($stylepath . '/news.inc.php');
		
		$rsNewsTopics = sql('SELECT `name`, `id` FROM `news_topics` ORDER BY `id` ASC');
		while ($rNewsTopics = sql_fetch_array($rsNewsTopics))
		{
			$newscontent .= mb_ereg_replace('{topic}', htmlspecialchars($rNewsTopics['name'], ENT_COMPAT, 'UTF-8'), $tpl_newstopic_header) . "\n";
		
			$rsNews = sql("SELECT `date_posted`, `content` FROM `news` WHERE `topic`='&1' AND `display`=1 ORDER BY `date_posted` DESC LIMIT 0, 20", $rNewsTopics['id']);
			while ($rNews = sql_fetch_array($rsNews))
			{
				$thisnewscontent = $tpl_newstopic_without_topic;
				$thisnewscontent = mb_ereg_replace('{date}',date('d.m.Y H:i:s', strtotime($rNews['date_posted'])), $thisnewscontent);
				$thisnewscontent = mb_ereg_replace('{message}', $rNews['content'], $thisnewscontent);
				$newscontent .= $thisnewscontent . "\n";
			}
			mysql_free_result($rsNews);
		}
		mysql_free_result($rsNewsTopics);
		
		//$newscontent .= "</table>";
		tpl_set_var('news', $newscontent);
	}
	
	//make the template and send it out
	tpl_BuildTemplate();
?>
