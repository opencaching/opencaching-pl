<?php
/***************************************************************************
																./articles.php
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
                                   				                                
	 Display documents/articles with a minimum of code and no preprocessing
	
	 used template(s): articles/*, sitemap
	 parameter(s):     page        specifies the document which should be 
	                               displayed
	
 ****************************************************************************/
 
	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
		
	//Preprocessing
	if ($error == false)
	{
		//get the article name to display
		$article = '';	
			if (isset($_REQUEST['region']))
		{
			tpl_set_var('region', $_REQUEST['region']);
			$region= $_REQUEST['region'];
		}
		if (isset($_REQUEST['page']) && 
		    (mb_strpos($_REQUEST['page'], '.') === false) && 
		    (mb_strpos($_REQUEST['page'], '/') === false) && 
		    (mb_strpos($_REQUEST['page'], '\\') === false)
		   )
		{
			$article = $_REQUEST['page'];
		}

		if ($article == '')
		{
			//no article specified => sitemap
			$tplname = 'sitemap';
		}
		else if (!file_exists($stylepath . '/articles/' . $article . '.tpl.php'))
		{
			//article doesn't exists => sitemap
			$tplname = 'sitemap';
		}
		else
		{
			//set article inside the articles-directory
			switch($_REQUEST['page'])
			{
				case 'stat':
					require_once('./graphs/cachetypes-oc.php');
					tpl_set_var('oc_statistics_link', genStatPieUrl() );
					break;
				default:
					break;
			}
			$tplname = 'articles/' . $article;
		}
	}
	if ((date('m') == 4) and (date('d') == 1)){
			tpl_set_var('list_of_news', tr('PrimaAprilis') );
			tpl_set_var('news', tr('PMOnly'));
			$tplname = 'news';
		} 
	
		//JG - The script was added on the begin of a main page
		//$ga_scr = " <script type='text/javascript' src = 'lib/js/ga.js'></script> ";
		//tpl_set_var( 'ga_script_header', $ga_scr);		
		
	//make the template and send it out
	tpl_BuildTemplate();
?>
