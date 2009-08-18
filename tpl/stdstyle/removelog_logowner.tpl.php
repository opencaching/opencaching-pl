<?php
/***************************************************************************
											./tpl/stdstyle/removelogs.tpl.php
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
                                       				                                
	 remove a cache log
		
		cachename
		logid_urlencode
		log

 ****************************************************************************/
?>
<form action="removelog.php" method="post" enctype="application/x-www-form-urlencoded" name="removelog_form" dir="ltr">
<input type="hidden" name="commit" value="1"/>
<input type="hidden" name="logid" value="{logid}"/>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/description/22x22-logs.png" border="0" width="22" height="22" alt="" title="" align="middle"/>&nbsp;Kasowanie wpisu z logu dla skrzynki <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></div>
<div class="buffer"></div>
<p>Czy na pewno chcesz usunąć wpis z logu?</p>
<p>{log}</p>
<p><input type="submit" name="submit" value="Usuń wpis z logu" class="formbuttons"/></p>
</form>