<?php
/***************************************************************************
												  ./tpl/stdstyle/viewprofile.inc.php
															-------------------
		begin                : August 21 2004
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
	                                         				                                
	 set template specific language variables
	
 ****************************************************************************/

	$err_no_user = tr('no_userprofile');
	
	$logtype[1] = tr('found');
	$logtype[2] = tr('not_found');
	$logtype[3] = tr('comment');

	$no_logs = '<tr><td colspan="2" class="content-title-noshade">'.tr('no_logs').'</td></tr>';
	//$log_line = '<tr><td>{logimage}&nbsp;{logtype}</td><td>{date}</td><td><a href="viewcache.php?cacheid={cacheid}">{cachename}</a></td></tr>';
	$log_line = '<tr><td class="content-title-noshade">{logimage}&nbsp;{date}</td><td class="content-title-noshade"><a href="viewcache.php?cacheid={cacheid}">{cachename}</a></td></tr>';

?>
