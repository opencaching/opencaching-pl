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
	  
   Unicode Reminder メモ
                                       				                                
	 remove a cache log
		
		cachename
		logid_urlencode
		log

 ****************************************************************************/
?>
<form action="removelog.php" method="post" enctype="application/x-www-form-urlencoded" name="removelog_form" dir="ltr">
<input type="hidden" name="commit" value="1"/>
<input type="hidden" name="logid" value="{logid}"/>
<table class="content">
	<tr><td class="header" colspan="2"><img src="tpl/stdstyle/images/description/22x22-logs.png" border="0" width="32" height="32" alt="" title="" align="middle"> <b>Kasowanie wpisu z LOGu dla skrzynki <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>
	<tr><td class="spacer"></td></tr>

	<tr><td>Jesteś pewien że chcesz usunąć wpis z Logu?</td></tr>
	<tr><td class="spacer"></td></tr>

	<tr><TD>{log}</TD></tr>
	<tr><td class="spacer"></td></tr>

	<tr>
		<td class="header-small">
		<input type="submit" name="submit" value="Usunac wpis z Logu" style="width:120px"/>
		</td>
	</tr>
</table>
</form>