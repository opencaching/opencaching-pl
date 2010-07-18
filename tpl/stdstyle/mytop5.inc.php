<?php
/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*   
	*  UTF-8 ąść
	***************************************************************************/

	$viewtop5_line = '<tr>
				<td bgcolor="{bgcolor}"><a href="viewcache.php?cacheid={cacheid}">{cachename}</a></td>
				<td bgcolor="{bgcolor}">[<a href="mytop5.php?action=delete&amp;cacheid={cacheid}">'.tr('delete').'</a>]</td>
			</tr>
			';

	$notop5 = '<div class="notice">'.tr('dont_have_recommended_caches').'.</div>';
	$msg_delete = '<div class="notice">'.tr('your_recommendations').' "<a href="viewcache.php?cacheid={cacheid}">{cachename}</a>" '.tr('was_removed').'!</div>
			<div class="buffer"></div>';

	$bgcolor1 = '#eeeeee';
	$bgcolor2 = '#e0e0e0';
?>
