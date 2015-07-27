<?php
/***************************************************************************
																./newlogs.php
															-------------------
		begin                : July 7 2004
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
                                 				                                
	 include the newlogs HTML file
	
 ****************************************************************************/
 
	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	
	//Preprocessing
	if ($error == false)
	{
		//get the news
		$tplname = 'html/newlogs';
	}
	
	//make the template and send it out
	tpl_BuildTemplate();
?>