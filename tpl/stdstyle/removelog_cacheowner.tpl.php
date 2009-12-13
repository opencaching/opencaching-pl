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
		
 ****************************************************************************/
?>
<form action="removelog.php" method="post" enctype="application/x-www-form-urlencoded" name="removelog_form" dir="ltr">
<input type="hidden" name="commit" value="1"/>
<input type="hidden" name="logid" value="{logid}"/>
<table class="table">
	<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" /> <b />Kasowanie wpisu z lohu dla <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>
	<tr><td class="buffer"></td></tr>

	<tr><td>Czy na pewno chcesz usunąć wpis z logu?</td></tr>
	<tr><td class="buffer"></td></tr>

	<tr><TD>{log}</TD></tr>
	<tr><td class="buffer"></td></tr>

	<tr><td >Chciałbyś wysłać dodatkowo uwagę do {log_user_name}?</td></tr>
	<tr>
		<td>
		<textarea class="logs" name="logowner_message"></textarea>
		</td>
	</tr>
	<tr><td class="buffer"></td></tr>

	<tr>
		<td >
		<input type="submit" name="submit" value="Usuń wpis z logu" class="formbuttons"/>
		</td>
	</tr>
</table>
</form>
