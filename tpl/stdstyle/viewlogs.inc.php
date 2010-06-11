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
	        
   Unicode Reminder ??
                                 				                                
	 set template specific language variables
	
 ****************************************************************************/

	$functions_start = '<br/><img src="images/trans.gif" alt="" title="" class="icon16" />&nbsp;';
	$functions_middle = '&nbsp;';
	$functions_end = '';

	$decrypt_log = '<img  src="tpl/stdstyle/images/free_icons/lock_open.png" class="icon16" alt="" title=""/>&nbsp;<a href="viewcache-test.php?cacheid={cacheid}&amp;nocryptlog=1#{decrypt_log_id}" onclick="var ch = document.getElementById(\'{decrypt_log_id}\').childNodes;for(var i=0;i < ch.length;++i) {var e = ch[i]; decrypt(e);} return false;">'.tr('decrypt').'</a>';	
	$edit_log = '<a href="editlog.php?logid={logid}"><img src="tpl/stdstyle/images/action/16x16-properties.png" class="icon16" alt="" title=""/></a>&nbsp;<a href="editlog.php?logid={logid}">Edycja</a>&nbsp;';
	$remove_log = '<a href="removelog.php?logid={logid}"><img src="tpl/stdstyle/images/log/16x16-trash.png" class="icon16" alt="{{Trash}}" title=""/></a>&nbsp;<a href="removelog.php?logid={logid}">Usuń</a>&nbsp;';
	$upload_picture = '<a href="newpic.php?objectid={logid}&amp;type=1"><img src="tpl/stdstyle/images/action/16x16-addimage.png" class="icon16" alt="" title=""/></a>&nbsp;<a href="newpic.php?objectid={logid}&amp;type=1">Dodaj obrazek</a>&nbsp;';
	
	$remove_picture = ' <span class="removepic"><img src="tpl/stdstyle/images/log/16x16-trash.png" class="icon16" alt="" title=""/><a href="removepic.php?uuid={uuid}">Usuń</a></span>';

	$rating_picture = '<img src="images/rating-star.png" alt="Rekomendacja" /> '
?>
