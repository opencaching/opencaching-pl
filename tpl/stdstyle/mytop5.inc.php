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

	$viewtop5_line = '<tr>'
				.'<td style= "background-color: {bgcolor}"><a href="viewcache.php?cacheid={cacheid}">{cachename}</a></td>'
				.'<td style= "background-color: {bgcolor}">&nbsp</td>'			
				.'<td style= "background-color: {bgcolor}"><a href=viewprofile.php?userid={owner_id}>{ownername}</a></td>'
				.'<td style= "background-color: {bgcolor}">&nbsp</td>'								
				.'<td style="width:23px;background-color: {bgcolor}; text-align: center"><a class="links"  href="mytop5.php?action=delete&amp;cacheid={cacheid}" onclick="return confirm(\''.tr("mytop5_1").'\');"><img style="vertical-align: middle;" src="tpl/stdstyle/images/log/16x16-trash.png" alt="" title='.tr('delete').' /></a></td>'
			.'</tr>';
//<td style= "background-color: {bgcolor}">[<a href="mytop5.php?action=delete&amp;cacheid={cacheid}">'.tr('delete').'</a>]</td>
	
	$notop5 = '<div class="notice">'.tr('dont_have_recommended_caches').'.</div>';
	$msg_delete = '<div class="notice">'.tr('your_recommendations').' "<a href="viewcache.php?cacheid={cacheid}">{cachename}</a>" '.tr('was_removed').'!</div>
			<div class="buffer"></div>';

	$bgcolor1 = '#eeeeee';
	$bgcolor2 = '#ffffff';
	
	//
	
	//$bgcolor1 = '#ffffff';
	//$bgcolor2 = '#DBE6F1';
	
	
	///$bgcolor1 = 'rgb(255,255,255)';
	//$bgcolor2 = 'rgb(219,230,241)';
	
?>
