<?php
/***************************************************************************
											./tpl/stdstyle/viewlogs.tpl.php
															-------------------
		begin                : July 9 2004
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
	  
   Unicode Reminder ??
                                       				                                
	 view all logs of a cache

 ****************************************************************************/
?>
			<p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt=""/>
			&nbsp;Wpisy do logów dla skrzynki <a href="viewcache.php?cacheid={cacheid}">{cachename}</a>&nbsp;&nbsp;
			<span id="viewlogs-total">&nbsp;&nbsp;
			{found_icon} {founds}x 
			{notfound_icon} {notfounds}x 
			{note_icon} {notes}x<br /></span></div>
		{logs}

<div id="viewlogs-end">[<a href="viewcache.php?cacheid={cacheid}">Powrót do skrzynki</a>]</div>
