<?php

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
                                   				                                
	 Starting page of the OpenCaching website and template usage example
	
	 used template(s): start
	 parameter(s):     none
	
 ****************************************************************************/
	//prepare the templates and include all neccessary
	if(!isset($rootpath)) $rootpath = __DIR__.DIRECTORY_SEPARATOR;
	require_once('./lib/common.inc.php');

	//Preprocessing
	if ($error == false)
	{
		//set the template to process
		$tplname = 'start';
		
		// news
		require($stylepath . '/news.inc.php');
		$newscontent = '<div class="line-box">';
		$rs = sql('SELECT `news`.`date_posted` `date`, `news`.`content` `content` FROM `news` WHERE datediff(now(), news.date_posted) <= 31 AND `news`.`display`=1 AND `news`.`topic`=2 ORDER BY `news`.`date_posted` DESC LIMIT 4');
	
	if (mysql_num_rows($rs)!=0) {
			$newscontent .= $tpl_newstopic_header;
		}	

		while ($r = sql_fetch_array($rs))
		{
		$news= '<div class="logs" style="width: 750px;">'.$tpl_newstopic_without_topic;
			$post_date = strtotime($r['date']);	
			$news = mb_ereg_replace('{date}', fixPlMonth(htmlspecialchars(strftime("%d %B %Y", $post_date), ENT_COMPAT, 'UTF-8')), $news);
			$news = mb_ereg_replace('{message}', $r['content'], $news);			
			$newscontent .= $news . "</div>\n";
		}
		$newscontent .= "</div>\n";
	if (mysql_num_rows($rs)!=0) {
			tpl_set_var('display_news', $newscontent);
		} else {
	
		tpl_set_var('display_news','');}

		mysql_free_result($rs);
		$newscontent = '';

		global $dynstylepath;
		include ($dynstylepath . "totalstats.inc.php");
		
		// here is the right place to set up template replacements
		// example: 
		// tpl_set_var('foo', 'myfooreplacement');
		// will replace {foo} in the templates
	}

        // diffrent oc server handling: display proper info depend on server running the code
        $nodeDetect = substr($absolute_server_URI,-3,2);
        tpl_set_var('what_do_you_find_intro',tr('what_do_you_find_intro_'.$nodeDetect));
        
        if ($powerTrailModuleSwitchOn) tpl_set_var('ptDisplay','block'); else tpl_set_var('ptDisplay','none');
		
		if ($BlogSwitchOn) tpl_set_var('blogDisplay','block'); else tpl_set_var('blogDisplay','none');

	//make the template and send it out
	tpl_BuildTemplate(false);

	//not neccessary, call tpl_BuildTemplate with true as argument and the db will be closed there
	db_disconnect();
?>
